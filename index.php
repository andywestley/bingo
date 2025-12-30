<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<title>Bingo Party</title>

<link rel="stylesheet" href="WebContent/bingo.css"> 
<style>
    body {
        height: 100vh;
        display: flex;
        align-items: center;
        width: 100%;
        background-color: #fcebd1; 
    }
    .landing-card {
        max-width: 500px;
        margin: auto;
        padding: 40px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
    }
</style>
</head>
<body>

	<div class="container">
        <div class="landing-card">
            <h1 class="mb-5">Bingo Party!</h1>
            
            <a href="host.php" class="btn btn-primary btn-lg btn-block mb-3 p-4">Start a Game (Host)</a>
            <a href="player.php" class="btn btn-success btn-lg btn-block p-4">Join a Game (Player)</a>
        </div>
	</div>

</body>
</html>
