<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<title>Bingo Player</title>
<link rel="stylesheet" href="WebContent/bingo.css"> 
<link rel="stylesheet" href="css/player.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-center align-items-center position-relative mb-4">
        <h1 class="text-center m-0">Bingo Player</h1>
        <button class="btn btn-outline-info btn-sm position-absolute" style="right: 0;" data-toggle="modal" data-target="#playerHelpModal">?</button>
    </div>

    <div id="card-area" class="mt-4">
        <!-- Card will be generated here -->
    </div>
    
    <div class="text-center mt-4 mb-4">
        <button id="bingo-shout" class="btn btn-warning btn-lg btn-block">BINGO!</button>
    </div>

    <div class="row mb-3">
        <div class="col-12 text-center mb-2">
            <span class="badge badge-info status-badge">Waiting for numbers...</span>
        </div>
        
        <div class="col-6 pr-1">
            <div class="card shadow-sm h-100">
                <div class="card-header py-1 text-center font-weight-bold bg-primary text-white">Current</div>
                <div class="card-body d-flex justify-content-center align-items-center p-2">
                    <div id="last-called" class="display-3 font-weight-bold text-dark">--</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 pl-1">
             <div class="card shadow-sm h-100">
                <div class="card-header py-1 text-center font-weight-bold bg-secondary text-white">Previous</div>
                <div class="card-body p-2 d-flex align-items-center justify-content-center">
                    <div id="recent-calls" class="d-flex flex-wrap justify-content-center" style="gap: 5px;">
                        <!-- History items will appear here -->
                        <span class="badge badge-light border p-2">--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Player Help Modal -->
<div class="modal fade" id="playerHelpModal" tabindex="-1" role="dialog" aria-labelledby="playerHelpLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="playerHelpLabel">How to Play</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ol>
            <li><strong>Listen</strong> for the numbers called by the host.</li>
            <li><strong>Watch</strong> for "Last Called" updates on your screen.</li>
            <li><strong>Click</strong> numbers on your card to mark them.
                <ul>
                    <li><span class="text-success">Green</span> = Marked by you.</li>
                    <li><span class="text-warning">Gold Border</span> = Called by Host.</li>
                </ul>
            </li>
            <li><strong>Win</strong> by marking all numbers on your card and shouting <strong>BINGO!</strong></li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Got it!</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="js/logger.js?v=<?php echo time(); ?>"></script>
<script src="js/bingo-client.js?v=<?php echo time(); ?>"></script>
<script src="js/player.js?v=<?php echo time(); ?>"></script>
</body>
</html>
