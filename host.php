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

</head>
<body>

	<div class="container">
		<h1>Bingo Host (Caller)</h1>

		<div class="row" id="top">
			<div class="col-4">
				<div class="card h-100">
					<div class="card-header">Controls</div>
					<div class="card-body">
						<button type="button" class="btn btn-primary btn-block mb-3" id="pick-ball">Pick next number</button>
						<button type="button" class="btn btn-danger btn-block" id="new-game">Start new game</button>
                        <hr>
                        <h5>Connected Players: <span id="player-count">0</span></h5>
                        <ul id="player-list" class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                            <!-- Players go here -->
                        </ul>
					</div>
				</div>

			</div>
			<div class="col-4">
				<div class="card h-100">
					<div class="card-header">Current number</div>
					<div class="card-body text-center">
						<h2 class="card-title ball" style="font-size: 4rem;" id="current-number">--</h2>
						<p id="slang" class="text-muted"></p>
					</div>
				</div>

			</div>
			<div class="col-4">
				<div class="card h-100">
					<div class="card-header">Previous number</div>
					<div class="card-body text-center">
						<h2 class="card-title ball" id="previous-number">--</h2>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="card">
			<div class="col-12 mt-3">

				<div class="card h-100">
					<div class="card-header" id="remaining-numbers-label">Called Numbers</div>
					<div class="card-body">
						<div class="row">
                            <?php for($i=1; $i<=90; $i++): ?>
							    <div class="col-1 number-card" id="ball-<?php echo $i; ?>"><?php echo $i; ?></div>
                            <?php endfor; ?>
						</div>
					</div>
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

            // Player Polling
            setInterval(async function() {
                Logger.debug("Polling for players...");
                const data = await getConnectedPlayers();
                if(data && data.players) {
                    Logger.debug(`Received ${data.players.length} players`);
                    $('#player-count').text(data.players.length);
                    const list = $('#player-list');
                    list.empty();
                    data.players.forEach(p => {
                        list.append(`<li class="list-group-item py-1">${p.name}</li>`);
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
