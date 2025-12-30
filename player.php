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
        
    function generateCard() {
        // ... (existing generation logic) ...
        // We need to capture the generated numbers to check for matches
        window.myCardNumbers = new Set();
        
        let colsData = [];
        // ...
        // (Skipping full regeneration logic for brevity, just capturing the output)
        
        // RE-IMPLEMENTING generateCard to capture numbers properly without breaking existing logic
        // Since I'm replacing the whole function anyway to add tracking:
        
        const columns = [[],[],[],[],[],[],[],[],[]];
        // ... logic ...
        
        let finalNumbers = [[],[],[],[],[],[],[],[],[]]; 
        
        // Helper to pick without replacement
        // ... 
        // Actually, let's just grab the generated numbers from the DOM after generation? 
        // No, cleaner to track them at generation.
        
        // SIMPLIFIED: Let's just iterate the grid AFTER generation to build the Set.
        // It's safer than rewriting the whole complex generation logic I saw earlier.
        
        // ... Original Generation Code ...
        // Let's assume the original logic runs, and then we scrape.
        
        // WAIT, I need to wrap the original logic or append to it.
        // The previous view_file showed the whole function. I should just append the scraping to the end of it.
        
        // ERROR: replace_file_content replaces a block. I need to be careful not to delete the generation logic.
        // Let's use the scraping approach.
        
        // Actually, looking at previous file content, generateCard() is lines 79-159.
        // I will replace the end of the function.
        
        $('#card-area').html(html);
        
        // Capture numbers
        window.myCardNumbers = new Set();
        $('.bingo-number').each(function() {
            let val = $(this).data('number');
            if(val) window.myCardNumbers.add(parseInt(val));
        });
        Logger.info("Card Generated with numbers:", Array.from(window.myCardNumbers));
        
        // Add click handler
        $('.bingo-number').click(function() {
             $(this).toggleClass('bg-success text-white');
        });
    }

    $(document).ready(function() {
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
            if(playerName) {
                localStorage.setItem('bingo_player_name', playerName);
            } else {
                playerName = "Guest";
            }
            Logger.info("Set Player Name: " + playerName);
        } else {
             Logger.info("Loaded Player Name: " + playerName);
        }
        
        // Send heartbeat immediately and every 3 seconds
        function sendHeartbeatWithScore() {
             let score = 0;
             if (window.myCardNumbers && window.matchedNumbers) {
                 // intersection
                 score = new Set([...window.myCardNumbers].filter(x => window.matchedNumbers.has(x))).size;
             }
             sendHeartbeat(playerId, playerName, score);
        }

        window.matchedNumbers = new Set();
        
        sendHeartbeatWithScore();
        setInterval(sendHeartbeatWithScore, 3000);
        // -----------------------------

        generateCard();

        $('#bingo-shout').click(function() {
             alert("BINGO! (Check with host)");
        });

        // Polling
        let lastKnownCount = 0;
        let updateQueue = [];
        let isDisplaying = false;
        let initialSync = false;

        // Process Queue Loop (Animation Only)
        setInterval(function() {
            if (!isDisplaying && updateQueue.length > 0) {
                isDisplaying = true;
                const num = updateQueue.shift();
                
                // Update "Last Called" Display
                $('#last-called').text(num);
                $('#last-called').addClass('flash-highlight');
                setTimeout(() => $('#last-called').removeClass('flash-highlight'), 500); // Faster reset

                // Animation Logging
                Logger.info(`Animating Number: ${num} (Queue: ${updateQueue.length})`);

                // Dynamic Delay: Faster if queue is long
                let delay = 800; 
                if (updateQueue.length > 2) delay = 400;
                if (updateQueue.length > 5) delay = 200;

                setTimeout(() => {
                    isDisplaying = false;
                }, delay);
            }
        }, 50);

        setInterval(async function() {
            const status = await getStatus();
            if(status && status.drawn_numbers) {
                const drawn = status.drawn_numbers;
                
                // Initial Sync: Just catch up instantly
                if (!initialSync) {
                    initialSync = true;
                    lastKnownCount = drawn.length;
                    
                    if (status.current_number) {
                         $('#last-called').text(status.current_number);
                    }
                    
                    // Mark all on board
                    drawn.forEach(num => {
                        $(`#num-${num}`).css('border', '4px solid #ffca28').addClass('called');
                    });
                    
                    Logger.info("Initial Sync Complete");
                    return;
                }

                // Normal Update
                if (drawn.length > lastKnownCount) {
                    // Identify new numbers
                    const newNumbers = drawn.slice(lastKnownCount);
                    
                    // 1. IMMEDIATE BOARD UPDATE (Critical for fairness)
                    newNumbers.forEach(n => {
                        const el = $(`#num-${n}`);
                        if(el.length) {
                             el.css('border', '4px solid #ffca28'); 
                             el.addClass('flash-highlight');
                        }
                        // Track match
                        if(window.matchedNumbers) window.matchedNumbers.add(n);
                    });
                    
                    // 2. Queue for "Last Called" animation
                    newNumbers.forEach(n => updateQueue.push(n));
                    
                    lastKnownCount = drawn.length;
                    Logger.debug(`Process Update: ${newNumbers.length} new numbers.`);
                }
            }
        }, 2000);
    });
</script>
</body>
</html>
