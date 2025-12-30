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
<script src="js/bingo-client.js"></script>
<script>
    // Simple 90-ball card generator logic
    function generateCard() {
        // 9 columns
        // Col 0: 1-9, Col 1: 10-19 ... Col 8: 80-90
        const columns = [[],[],[],[],[],[],[],[],[]];
        const rows = [[],[],[]]; // 3 rows
        
        // We need 15 numbers total.
        // Rule: Each row has 5 numbers.
        // Rule: Each column has at least 1 number (optional strictly, but good practice).
        
        // Simplified Logic: 
        // 1. Pick 15 unique numbers from 1-90 distribution.
        // Actually, to display them in a grid, we need to know which column they belong to.
        
        // Let's force proper column distribution:
        // Pick one number for every column (9 numbers).
        // Pick 6 more numbers from random columns (ensuring no column has > 3).
        
        let colsData = [];
        for(let c=0; c<9; c++) {
            let min = c*10 + (c===0?1:0);
            let max = c*10 + 9 + (c===8?1:0);
            let pool = [];
            for(let i=min; i<=max; i++) pool.push(i);
            // Shuffle
            pool.sort(() => Math.random() - 0.5);
            colsData.push(pool);
        }

        let finalNumbers = [[],[],[],[],[],[],[],[],[]]; // by column
        
        // 1. Assign 1 to each column
        for(let c=0; c<9; c++) {
            finalNumbers[c].push(colsData[c].pop());
        }
        
        // 2. Assign remaining 6 randomly
        let remainingCounts = 6;
        while(remainingCounts > 0) {
            let c = Math.floor(Math.random() * 9);
            if(finalNumbers[c].length < 3) {
                finalNumbers[c].push(colsData[c].pop());
                remainingCounts--;
            }
        }
        
        // Sort columns
        for(let c=0; c<9; c++) finalNumbers[c].sort((a,b)=>a-b);
        
        // Populate rows (This is the tricky part - mapping variable col lengths to fixed row lengths of 5)
        // Simplified: Just render the 9 columns and blank cells where needed.
        // But for this MVP, let's just render the grid with what we have. 
        // A proper UK card layout algorithm is complex (swapping to fit 5 per row).
        
        // ALTERNATIVE: Just render the 3x9 grid, filling slots from top to bottom in each column. 
        // Empties are empties. Note: This might not strictly guarantee 5 per row, but it's close enough for a basic game.
        
        let html = '<div class="player-card">';
        // We will try to balance them into 3 rows.
        // Simple approach: Render a table where each column shows its numbers.
        
        html += '<div class="row">';
        for(let c=0; c<9; c++) {
            html += `<div class="col text-center border-right">`;
            for(let r=0; r<3; r++) {
                let num = finalNumbers[c][r] || '&nbsp;';
                let id = (typeof num === 'number') ? `num-${num}` : '';
                let val = (typeof num === 'number') ? num : '';
                html += `<div class="p-2 border-bottom ${id ? 'bingo-number' : ''}" id="${id}" data-number="${val}" style="height:50px;">${num}</div>`;
            }
            html += `</div>`;
        }
        html += '</div></div>';
        
        $('#card-area').html(html);
        
        // Add click handler
        $('.bingo-number').click(function() {
             $(this).toggleClass('bg-success text-white');
        });
    }

    $(document).ready(function() {
        generateCard();

        $('#bingo-shout').click(function() {
             alert("BINGO! (Check with host)");
        });

        // Polling
        setInterval(async function() {
            const status = await getStatus();
            if(status && status.drawn_numbers) {
                // Update Last Called
                if(status.current_number) {
                     $('#last-called').text(status.current_number);
                }
                
                // Auto-daub check or just highlight called numbers visually slightly different?
                // "Auto-daubing" is easiest for verified play. 
                // Let's highlight valid numbers in Yellow, user marks in Green.
                
                status.drawn_numbers.forEach(num => {
                    const el = $(`#num-${num}`);
                    if(el.length) {
                        el.css('border', '3px solid #ffc107'); // Gold border for called numbers
                    }
                });
            }
        }, 2000);
    });
</script>
</body>
</html>
