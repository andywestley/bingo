<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<title>Bingo Host</title>

<link rel="stylesheet" href="WebContent/bingo.css"> 
<link rel="stylesheet" href="css/host.css?v=<?php echo time(); ?>">

</head>
<body>

	<div class="container mt-4">
		<h1 class="mb-4 text-center" style="font-weight:800; color:#343a40;">Bingo Host <span style="font-weight:300; color:#adb5bd;">(Caller)</span></h1>

		<div class="row host-section" id="top">
			<div class="col-md-3 mb-3 order-2 order-md-2">
				<div class="card host-card">
					<div class="card-header text-center">Current</div>
					<div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-2">
						<div class="current-ball" id="current-number" style="font-size: 5rem;">--</div>
						<p id="slang" class="slang-text mt-0 mb-0" style="font-size: 1rem;">&nbsp;</p>
					</div>
				</div>
			</div>

            <!-- Previous Ball (Mobile: 3, Desktop: 3) -->
			<div class="col-md-2 mb-3 order-3 order-md-3">
				<div class="card host-card">
					<div class="card-header text-center">Prev</div>
					<div class="card-body text-center d-flex align-items-center justify-content-center p-2">
						<div style="font-size: 3rem; color: #ced4da; font-weight:700;" id="previous-number">--</div>
					</div>
				</div>
			</div>

            <!-- Connected Players (Mobile: 4, Desktop: 4) -->
            <div class="col-md-4 mb-3 order-4 order-md-4">
                <div class="card host-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Players
                        <span id="player-count" class="badge badge-pill badge-info">0</span>
                    </div>
                    <div class="card-body p-0">
                        <div style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-striped mb-0" style="font-size: 0.9rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="pl-3">Name</th>
                                        <th class="text-center">Matches</th>
                                        <th class="text-center">Marked</th>
                                    </tr>
                                </thead>
                                <tbody id="player-list-body">
                                    <!-- Players go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls (Mobile: 1, Desktop: 1) -->
			<div class="col-md-3 mb-3 order-1 order-md-1">
				<div class="card host-card">
					<div class="card-header d-flex justify-content-between align-items-center">
                        Controls
                        <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#helpModal">?</button>
                    </div>
					<div class="card-body">
						<button type="button" class="btn btn-primary btn-block mb-3 btn-control shadow-sm" id="pick-ball">Next Ball</button>
						<button type="button" class="btn btn-outline-danger btn-block btn-control" id="new-game">Reset</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row host-section" id="card">
			<div class="col-12">

				<div class="card host-card">
					<div class="card-header" id="remaining-numbers-label">Master Board</div>
					<div class="card-body">
						<div class="host-grid">
                            <?php for($i=1; $i<=90; $i++): ?>
							    <div class="host-cell" id="ball-<?php echo $i; ?>"><?php echo $i; ?></div>
                            <?php endfor; ?>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="helpModalLabel">Host Instructions</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul>
                <li><strong>Pick Next Ball</strong>: Draws a random number (1-90).</li>
                <li><strong>Reset Game</strong>: Clears the board for everyone. Warning: This cannot be undone!</li>
                <li><strong>Connected Players</strong>: Shows who is currently ready to play.</li>
                <li><strong>Master Board</strong>: Use this to verify numbers when a player shouts "Bingo!".</li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-labelledby="verifyWinLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="verifyWinLabel">BINGO! - Verify Win</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <h2 id="winner-name" class="mb-4">Player Name</h2>
            <div id="winner-card-container" class="d-flex justify-content-center">
                <!-- Winning card rendered here -->
            </div>
            <div class="mt-3 text-muted">
                <p>Legend: <span class="badge badge-success">Valid Match</span> <span class="badge badge-danger">False Claim</span></p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close & Continue</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirm-win">Details Confirmed</button>
          </div>
        </div>
      </div>
    </div>

	<!-- Optional JavaScript -->
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
 
    <script src="js/logger.js?v=<?php echo time(); ?>"></script>
	<script src="js/bingo-client.js?v=<?php echo time(); ?>"></script>
    <script>
        $(document).ready(function() {
            Logger.info("Host View Initialized");

            // New Game
            $('#new-game').click(async function() {
                if(confirm("Are you sure you want to start a new game? This will reset everyone's board.")) {
                    const result = await startGame();
                    if(result.status === 'success') {
                        location.reload();
                    } else {
                        alert("Error: " + result.message);
                    }
                }
            });

            // Pick Ball
            $('#pick-ball').click(async function() {
                const result = await drawBall();
                if(result.status === 'success') {
                    updateBoard(result.current_number, result.drawn_numbers);
                } else if(result.status === 'error') {
                    alert(result.message);
                }
            });

            // Poll for status initially to populate board if page refreshed
             async function refreshBoard() {
                const status = await getStatus();
                // Winner Check
                if (status && status.winner_info) {
                    let info = status.winner_info;
                    // Check if we already showed this timestamp to avoid spamming modal
                    let lastWinTime = window.lastWinTime || 0;
                    if(info.timestamp > lastWinTime) {
                        window.lastWinTime = info.timestamp;
                        showVerificationModal(info);
                    }
                }

                if(status && status.drawn_numbers) {
                     // Find previous number (if any)
                     let current = status.current_number;
                     let previous = null;
                     if(status.drawn_numbers.length > 1) {
                         // The drawn_numbers array order isn't guaranteed to be time-sorted from the DB JSON, 
                         // but for now let's assume the last drawn is current. 
                         // Actually the DB saves them in order.
                         let len = status.drawn_numbers.length;
                         if (status.drawn_numbers[len-1] == current) {
                             previous = status.drawn_numbers[len-2];
                         }
                     }
                     updateDisplay(current, previous);
                     
                     // Highlight all
                     status.drawn_numbers.forEach(num => {
                         $(`#ball-${num}`).addClass('called');
                     });
                }
            }
            
            function updateBoard(current, allDrawn) {
                 // Update numbers
                 $(`#ball-${current}`).addClass('called');
                 
                 let previous = $('#current-number').text();
                 if(previous === '--') previous = null;
                 
                 updateDisplay(current, previous);
            }

            function updateDisplay(current, previous) {
                if(current) {
                    $('#current-number').text(current);
                    $('#slang').text(getNumberSlang(current));
                }
                if(previous) {
                    $('#previous-number').text(previous);
                }
            }

            function showVerificationModal(info) {
                $('#winner-name').text(info.player);
                
                // Render Card
                let html = '<div class="bingo-grid" style="transform: scale(0.8);">'; // Reuse player grid, scaled down
                let card = info.card; // 9 columns array
                
                // Get drawn numbers set
                let drawnSet = new Set();
                $('.host-cell.called').each(function() {
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
            setInterval(async function() {
                Logger.debug("Polling for players...");
                const data = await getConnectedPlayers();
                if(data && data.players) {
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

            refreshBoard();
        });
    </script>
</body>
</html>
