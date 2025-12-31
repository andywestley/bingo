/**
 * Bingo Host Logic
 * Handles game control (Next Ball, Reset), verification, and player monitoring.
 */

$(document).ready(function () {
    Logger.info("Host View Initialized");

    // New Game
    $('#new-game').click(async function () {
        if (confirm("Are you sure you want to start a new game? This will reset everyone's board.")) {
            const result = await startGame();
            if (result.status === 'success') {
                location.reload();
            } else {
                alert("Error: " + result.message);
            }
        }
    });

    // Pick Ball
    $('#pick-ball').click(async function () {
        try {
            // Disable button during animation
            $(this).prop('disabled', true);

            const result = await drawBall();
            if (result.status === 'success') {
                await updateBoardAnim(result.current_number);
                // Update count
                if (result.drawn_numbers) {
                    $('#called-count-badge').text(`${result.drawn_numbers.length} / 90`);
                }
            } else if (result.status === 'error') {
                alert(result.message);
            }
        } catch (e) {
            console.error("Pick Ball Error:", e);
            alert("An error occurred while picking the next ball. Please try again.");
        } finally {
            $(this).prop('disabled', false);
        }
    });

    async function updateBoardAnim(current) {
        // Mark board
        $(`#ball-${current}`).addClass('called');

        let currentEl = $('#current-number');
        let historyContainer = $('#host-recent-calls');

        // Slang
        $('#slang').text(getNumberSlang(current));

        // Animate
        await BingoUI.animateTransition(current, currentEl, historyContainer);
    }

    // Initial Load Logic (No animation)
    function updateDisplayInitial(current, drawnHistory) {
        if (current) {
            $('#current-number').text(current);
            $('#slang').text(getNumberSlang(current));
        }

        // Populate history
        let histContainer = $('#host-recent-calls');
        histContainer.empty();

        if (drawnHistory && drawnHistory.length > 0) {
            // Filter out current, take last 5, reverse for display
            let historyItems = drawnHistory.filter(n => n != current).slice(-5).reverse();

            if (historyItems.length === 0 && !current) {
                histContainer.append('<span class="text-muted p-2">--</span>');
            } else {
                historyItems.forEach(h => {
                    histContainer.append(`<span class="bingo-ball-history">${h}</span>`);
                });
            }
        } else {
            histContainer.append('<span class="text-muted p-2">--</span>');
        }
    }

    // Poll for status initially to populate board if page refreshed
    async function refreshBoard() {
        const status = await getStatus();
        // Winner Check
        if (status && status.winner_info) {
            let info = status.winner_info;
            // Check if we already showed this timestamp to avoid spamming modal
            let lastWinTime = window.lastWinTime || 0;
            if (info.timestamp > lastWinTime) {
                window.lastWinTime = info.timestamp;
                showVerificationModal(info);
            }
        }

        if (status && status.drawn_numbers) {
            let current = status.current_number;

            // Initial update (no animation)
            updateDisplayInitial(current, status.drawn_numbers);

            // Update count
            $('#called-count-badge').text(`${status.drawn_numbers.length} / 90`);

            // Highlight all
            status.drawn_numbers.forEach(num => {
                $(`#ball-${num}`).addClass('called');
            });
        }
    }

    function showVerificationModal(info) {
        $('#winner-name').text(info.player);

        // Render Card
        let html = '<div class="bingo-grid" style="transform: scale(0.8);">'; // Reuse player grid, scaled down
        let card = info.card; // 9 columns array

        // Get drawn numbers set
        let drawnSet = new Set();
        $('.host-cell.called').each(function () {
            drawnSet.add(parseInt($(this).text()));
        });

        // Iterate Rows (0..2)
        for (let r = 0; r < 3; r++) {
            for (let c = 0; c < 9; c++) {
                let num = card[c][r]; // From [col][row]
                if (num) {
                    let isMatch = drawnSet.has(parseInt(num));
                    let styleClass = isMatch ? 'bg-success text-white' : '';

                    html += `<div class="bingo-cell bingo-number ${styleClass}" style="width:50px; height:50px; line-height:50px; font-size:1.2rem;">${num}</div>`;
                } else {
                    html += `<div class="bingo-cell empty" style="width:50px; height:50px;"></div>`;
                }
            }
        }
        html += '</div>';

        $('#winner-card-container').html(html);
        $('#verificationModal').modal('show');
    }

    // Player Polling
    setInterval(async function () {
        Logger.debug("Polling for players...");
        const data = await getConnectedPlayers();
        if (data && data.players) {
            Logger.debug(`Received ${data.players.length} players`);
            $('#player-count').text(data.players.length);
            $('#player-count').text(data.players.length);
            const list = $('#player-list-body');
            list.empty();
            data.players.forEach(p => {
                let score = p.score || 0; // This is essentially matches count calculated by server/client
                let matchedArr = p.matched_str ? p.matched_str.split(',') : [];
                let markedArr = p.marked_str ? p.marked_str.split(',') : [];

                // Filter empty strings if any
                matchedArr = matchedArr.filter(n => n.length > 0);
                markedArr = markedArr.filter(n => n.length > 0);

                let matchCount = matchedArr.length;
                let markedCount = markedArr.length;

                // Highlight if close to winning (e.g. > 4 matches? Standard bingo is 15 but let's just keep logic simple)
                let rowClass = (matchCount >= 4) ? 'font-weight-bold' : '';
                let badgeClass = (matchCount > 0) ? 'badge-primary' : 'badge-secondary';

                list.append(`<tr class="${rowClass}">
                    <td class="pl-3 align-middle">${p.name}</td>
                    <td class="text-center align-middle"><span class="badge ${badgeClass}" style="min-width: 25px;">${matchCount}</span></td>
                    <td class="text-center align-middle"><span class="badge badge-light border" style="min-width: 25px;">${markedCount}</span></td>
                </tr>`);
            });
        } else {
            Logger.warn("Failed to get player list or empty response");
        }
    }, 4000);

    // Game State Polling (Numbers & Winner)
    setInterval(refreshBoard, 2000);

    // Initial call
    refreshBoard();
});
