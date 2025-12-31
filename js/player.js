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

    $('#bingo-shout').click(function () {
        alert("BINGO! (Check with host)");
    });

    // Polling Variables
    let lastKnownCount = 0;
    let updateQueue = [];
    let isDisplaying = false;
    let initialSync = false;

    // Process Queue Loop (Animation)
    setInterval(function () {
        if (!isDisplaying && updateQueue.length > 0) {
            isDisplaying = true;
            const num = updateQueue.shift();

            // Push current to History
            let previous = $('#last-called').text();
            if (previous && previous !== '--') {
                $('#recent-calls').prepend(`<span class="badge badge-secondary mx-1 p-2" style="font-size: 1.2rem;">${previous}</span>`);
                if ($('#recent-calls').children().length > 5) {
                    $('#recent-calls').children().last().remove();
                }
            }

            // Update "Last Called" Display
            $('#last-called').text(num);
            $('#last-called').addClass('flash-highlight');
            setTimeout(() => $('#last-called').removeClass('flash-highlight'), 500);

            Logger.info(`Animating Number: ${num}`);

            // Dynamic Delay
            let delay = 800;
            if (updateQueue.length > 2) delay = 400;
            if (updateQueue.length > 5) delay = 200;

            setTimeout(() => {
                isDisplaying = false;
            }, delay);
        }
    }, 50);

    // Status Polling Loop
    setInterval(async function () {
        const status = await getStatus();
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
                        $('#recent-calls').append(`<span class="badge badge-secondary mx-1 p-2" style="font-size: 1.2rem;">${h}</span>`);
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
    }, 2000);
});
