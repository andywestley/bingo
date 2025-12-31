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
<style>
    .player-card {
        border: 2px solid #333;
        border-radius: 10px;
        padding: 10px;
        background-color: #f8f9fa;
        max-width: 800px;
        margin: 0 auto;
    }
    .bingo-row {
        display: flex;
        height: 60px;
        border-bottom: 1px solid #ccc;
    }
    .bingo-row:last-child {
        border-bottom: none;
    }
    .bingo-cell {
        flex: 1;
        border-right: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .bingo-cell:last-child {
        border-right: none;
    }
    .daubed {
        background-color: #28a745;
        color: white;
        border-radius: 50%;
        width: 80%;
        height: 80%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
    }
</style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Bingo Player</h1>
    <div class="row mb-3">
        <div class="col text-center">
            <span class="badge badge-info status-badge">Waiting for numbers...</span>
            <h3>Last Called: <span id="last-called">--</span></h3>
        </div>
    </div>

    <div id="card-area" class="mt-4">
        <!-- Card will be generated here -->
    </div>
    
    <div class="text-center mt-4">
        <button id="bingo-shout" class="btn btn-warning btn-lg btn-block">BINGO!</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="js/logger.js?v=<?php echo time(); ?>"></script>
<script src="js/bingo-client.js?v=<?php echo time(); ?>"></script>
<script src="js/player.js?v=<?php echo time(); ?>"></script>
</body>
</html>
