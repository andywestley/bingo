<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<title>Bingo Host</title>


<link rel="stylesheet" href="css/bingo-theme.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="css/bingo-grid.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="css/host.css?v=<?php echo time(); ?>">

</head>
<body>

	<div class="container mt-4">
		<h1 class="mb-4 text-center" style="font-weight:800; color:#343a40;">Bingo Host <span style="font-weight:300; color:#adb5bd;">(Caller)</span></h1>

		<div class="row host-section" id="top">
            <!-- Calling Area (Mobile: 2, Desktop: 2) -->
            <div class="col-md-5 mb-3 order-2 order-md-2">
                <div class="row no-gutters">
                    <!-- Current Number -->
                    <div class="col-6 pr-1">
                        <div class="card bingo-card host-card h-100">
                             <div class="card-header text-center">Current</div>
                             <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-2">
                                 <div class="current-ball" id="current-number" style="font-size: 5rem;">--</div>
                                 <p id="slang" class="slang-text mt-0 mb-0" style="font-size: 1rem;">&nbsp;</p>
                             </div>
                        </div>
                    </div>
                    <!-- Previous Numbers History -->
                    <div class="col-6 pl-1">
                        <div class="card bingo-card host-card h-100">
                            <div class="card-header text-center">Previous</div>
                            <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                <div id="host-recent-calls" class="d-flex flex-wrap justify-content-start align-content-start" style="gap: 5px; width: 100%; min-height: 50px;">
                                    <span class="text-muted p-2">--</span>
                                </div>
                            </div>
                        </div>
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
                            <table class="table table-sm table-striped mb-0" style="font-size: 1.1rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="pl-3">Name</th>
                                        <th class="text-center">To Go</th>
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
						<button id="start-game" class="btn btn-success btn-lg btn-block mb-3" style="display:none;">Start Game</button>
						<button id="pick-ball" class="btn btn-primary btn-lg btn-block">Next Ball</button>
						<button type="button" class="btn btn-outline-danger btn-block btn-control" id="new-game">Reset</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row host-section" id="card">
			<div class="col-12">

				<div class="card host-card">
					<div class="card-header d-flex justify-content-between align-items-center">
                        Master Board
                        <span id="called-count-badge" class="badge badge-pill badge-secondary" style="font-size: 0.9em;">0 / 90</span>
                    </div>					<div class="card-body">
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
 
    <script src="js/logger.js?v=<?php echo time(); ?>"></script>
	<script src="js/bingo-client.js?v=<?php echo time(); ?>"></script>
    <script src="js/bingo-ui.js?v=<?php echo time(); ?>"></script>
    <script src="js/host.js?v=<?php echo time(); ?>"></script>
</body>
</html>
