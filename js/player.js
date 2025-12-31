/**
 * Bingo Player Logic
 * Handles card generation, game state polling, and telemetry.
 */

// Simple 90-ball card generator logic
function generateCard() {
    // 9 columns
    // Col 0: 1-9, Col 1: 10-19 ... Col 8: 80-90

    let colsData = [];
    for (let c = 0; c < 9; c++) {
        let min = c * 10 + (c === 0 ? 1 : 0);
        let max = c * 10 + 9 + (c === 8 ? 1 : 0);
        let pool = [];
        for (let i = min; i <= max; i++) pool.push(i);
        // Shuffle
        pool.sort(() => Math.random() - 0.5);
        colsData.push(pool);
    }

    let finalNumbers = [[], [], [], [], [], [], [], [], []]; // by column

    // 1. Assign 1 to each column
    for (let c = 0; c < 9; c++) {
        finalNumbers[c].push(colsData[c].pop());
    }

    // 2. Assign remaining 6 randomly
    let remainingCounts = 6;
    while (remainingCounts > 0) {
        let c = Math.floor(Math.random() * 9);
        if (finalNumbers[c].length < 3) {
            finalNumbers[c].push(colsData[c].pop());
            remainingCounts--;
        }
    }

    // Sort columns
    for (let c = 0; c < 9; c++) finalNumbers[c].sort((a, b) => a - b);

    // Generate HTML using CSS Grid (Flat list of cells, row by row)
    let html = '<div class="player-card">';
    html += '<div class="bingo-grid">';

    // Iterate Rows (0..2)
    for (let r = 0; r < 3; r++) {
        // Iterate Cols (0..8)
        for (let c = 0; c < 9; c++) {
            let num = finalNumbers[c][r]; // May be undefined if empty slot

            if (num) {
                html += `<div class="bingo-cell bingo-number" id="num-${num}" data-number="${num}">${num}</div>`;
            } else {
                html += `<div class="bingo-cell empty"></div>`;
            }
        }
    }

    html += '</div></div>'; // Close grid and card

    $('#card-area').html(html);

    // Store for shouting
    window.finalCardMatrix = finalNumbers;

    // Capture numbers for telemetry
    window.myCardNumbers = new Set();
    $('.bingo-number').each(function () {
        let val = $(this).data('number');
        if (val) window.myCardNumbers.add(parseInt(val));
    });
    Logger.info("Card Generated with numbers:", Array.from(window.myCardNumbers));

    // Add click handler
    $('.bingo-number').click(function () {
        $(this).toggleClass('marked'); // Use 'marked' class instead of bootstrap util
    });
}

$(document).ready(function () {
    Logger.info("Player View Initialized");

    // --- Player Identification ---
    let playerId = localStorage.getItem('bingo_player_id');
    if (!playerId) {
        playerId = 'player_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('bingo_player_id', playerId);
        Logger.info("Generated new Player ID: " + playerId);
    } else {
        Logger.info("Loaded Player ID: " + playerId);
    }

    let playerName = localStorage.getItem('bingo_player_name');
    if (!playerName || playerName === 'null') {
        playerName = prompt("Please enter your name for the Bingo Host:", "Guest");
        if (playerName) {
            localStorage.setItem('bingo_player_name', playerName);
        } else {
            playerName = "Guest";
        }
        Logger.info("Set Player Name: " + playerName);
    } else {
        Logger.info("Loaded Player Name: " + playerName);
        // Telemetry & Heartbeat
        window.matchedNumbers = new Set();
        function sendHeartbeatWithScore() {
            let matched = [];
            let marked = [];

            if (window.myCardNumbers && window.matchedNumbers) {
                // Calculate matched (intersection of Card and Called)
                matched = [...window.myCardNumbers].filter(x => window.matchedNumbers.has(x)).sort((a, b) => a - b);
            }

            // Calculate marked (what the user has actually clicked on their card)
            $('.bingo-number.marked').each(function () {
                let val = $(this).data('number');
                if (val) marked.push(parseInt(val));
            });
            marked.sort((a, b) => a - b);

            let score = matched.length;
            // Join as strings for DB
            let matched_str = matched.join(',');
            let marked_str = marked.join(',');

            sendHeartbeat(playerId, playerName, score, matched_str, marked_str);
        }
    }

    sendHeartbeatWithScore();
    setInterval(sendHeartbeatWithScore, 3000);

    // Generate Card
    generateCard();

    $('#bingo-shout').click(async function () {
        let result = await shoutBingo(playerName, window.finalCardMatrix);
        if (result.status === 'success') {
            $(this).text("SHOUTED!").removeClass('btn-warning').addClass('btn-success').prop('disabled', true);
        } else {
            alert("Error shouting: " + result.message);
        }
    });

    // Polling Variables
    let lastKnownCount = 0;
    let updateQueue = [];
    let isDisplaying = false;
    let initialSync = false;

    // Process Queue Loop (Animation)
    setInterval(async function () {
        if (updateQueue.length > 0) {
            let nextNum = updateQueue.shift();
            let currentEl = $('#last-called');
            let historyContainer = $('#recent-calls');

            // Mark card immediately (so player can click while animation happens)
            $(`#num-${nextNum}`).addClass('called');

            // Run Animation
            await BingoUI.animateTransition(nextNum, currentEl, historyContainer);
        }
    }, 800); // Process every 800ms

    // Status Polling Loop
    setInterval(async function () {
        const status = await getStatus();

        if (status && status.status === 'LOBBY') {
            $('#lobby-overlay').fadeIn();
            $('#card-area').css('opacity', '0.2'); // Dim the board
            // Don't process other updates if in lobby
        } else {
            $('#lobby-overlay').fadeOut();
            $('#card-area').css('opacity', '1');
        }

        // Beginner Mode
        if (status && status.beginner_mode == 1) {
            $('body').addClass('beginner-mode-active');
        } else {
            $('body').removeClass('beginner-mode-active');
        }

        // Winner Notification
        if (status && status.status !== 'LOBBY' && status.winner_info) {
            let info = status.winner_info;
            if (info.player !== playerName) { // Don't annoy the winner themselves
                // Simple full screen overlay or just an alert? Let's do a sticky header
                if ($('#winner-banner').length === 0) {
                    $('body').prepend(`<div id="winner-banner" class="alert alert-success text-center fixed-top" style="z-index:9999; font-size: 1.5rem; font-weight: bold;">
                        🎉 ${info.player} HAS WON BINGO! 🎉
                     </div>`);
                }
            }
        } else {
            $('#winner-banner').remove();
        }

        if (status && status.drawn_numbers) {
            const drawn = status.drawn_numbers;

            // Initial Sync
            if (!initialSync) {
                initialSync = true;
                lastKnownCount = drawn.length;

                if (status.current_number) {
                    $('#last-called').text(status.current_number);

                    // Populate History
                    $('#recent-calls').empty();
                    // Get last 5 numbers EXCLUDING current
                    let history = drawn.filter(n => n != status.current_number).slice(-5).reverse();
                    history.forEach(h => {
                        $('#recent-calls').append(`<span class="bingo-ball-history">${h}</span>`);
                    });
                }

                drawn.forEach(num => {
                    $(`#num-${num}`).addClass('called');
                    if (window.matchedNumbers) window.matchedNumbers.add(num);
                });

                Logger.info("Initial Sync Complete");
                return;
            }

            // Normal Update
            if (drawn.length > lastKnownCount) {
                const newNumbers = drawn.slice(lastKnownCount);

                // 1. Immediate Board Update
                newNumbers.forEach(n => {
                    const el = $(`#num-${n}`);
                    if (el.length) {
                        el.addClass('called');
                        el.addClass('flash-highlight');
                    }
                    if (window.matchedNumbers) window.matchedNumbers.add(n);
                });

                // 2. Queue for Animation
                newNumbers.forEach(n => updateQueue.push(n));

                lastKnownCount = drawn.length;
                Logger.debug(`Process Update: ${newNumbers.length} new numbers.`);
            }
        }
        // Poll for connected players
        setInterval(async function () {
            // Only update if not hidden or if in lobby? 
            // Actually good to see even during game
            const data = await getConnectedPlayers();
            if (data && data.players) {
                $('#player-count').text(data.players.length);
                const list = $('#player-list');
                list.empty();
                data.players.forEach(p => {
                    let isMe = (p.name === playerName) ? 'font-weight-bold LIST-GROUP-ITEM-PRIMARY' : '';
                    let badge = '';

                    // Show winner badge if they won?
                    // Or just simpler logic: Name + ToGo
                    let matchedArr = p.matched_str ? p.matched_str.split(',').filter(n => n) : [];
                    let toGo = 15 - matchedArr.length;
                    if (toGo < 0) toGo = 0;

                    let toGoBadge = `<span class="badge badge-secondary float-right">Need ${toGo}</span>`;
                    if (toGo <= 1) toGoBadge = `<span class="badge badge-success float-right pulse">Need ${toGo}</span>`;

                    list.append(`<li class="list-group-item py-1 ${isMe}">${p.name} ${toGoBadge}</li>`);
                });
            }
        }, 5000); // Poll every 5s

    }, 2000);
});
