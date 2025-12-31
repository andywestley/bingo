<?php
// api/game.php

// Enable error reporting for debugging (disable in production)
// Enable error reporting for debugging (disable in production)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$dbPath = __DIR__ . '/../data/bingo.sqlite';

try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Helper to initialize the database
function initDb($db) {
    // Game State Table
    $db->exec("CREATE TABLE IF NOT EXISTS game_state (
        id INTEGER PRIMARY KEY,
        current_number INTEGER DEFAULT NULL,
        drawn_numbers TEXT DEFAULT '[]',
        status TEXT DEFAULT 'stopped',
        winner_info TEXT DEFAULT NULL,
        started_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Ensure there is one row
    $count = $db->query("SELECT COUNT(*) FROM game_state")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO game_state (id, current_number, drawn_numbers, status) VALUES (1, NULL, '[]', 'stopped')");
    }
    
    // Add winner_info column if missing
    try {
        $db->exec("ALTER TABLE game_state ADD COLUMN winner_info TEXT DEFAULT NULL");
    } catch(Exception $e) { /* Ignore */ }

    // Players Table
    $db->exec("CREATE TABLE IF NOT EXISTS players (
        id TEXT PRIMARY KEY,
        name TEXT,
        last_seen DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

// Get Action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'start_game':
        initDb($db);
        // Reset the game state
        $stmt = $db->prepare("UPDATE game_state SET current_number = NULL, drawn_numbers = '[]', status = 'playing', winner_info = NULL, started_at = CURRENT_TIMESTAMP WHERE id = 1");
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'New game started']);
        break;

    case 'draw_ball':
        initDb($db);
        // Get current state
        $stmt = $db->query("SELECT drawn_numbers FROM game_state WHERE id = 1");
        $row = $stmt->fetch();
        $drawnNumbers = json_decode($row['drawn_numbers'], true) ?? [];

        // All numbers 1-90
        $allNumbers = range(1, 90);
        $remainingNumbers = array_values(array_diff($allNumbers, $drawnNumbers));

        if (count($remainingNumbers) === 0) {
             echo json_encode(['status' => 'error', 'message' => 'No more balls']);
             exit;
        }

        // Pick random number
        $nextNumber = $remainingNumbers[array_rand($remainingNumbers)];
        
        // Update state
        $drawnNumbers[] = $nextNumber;
        $stmt = $db->prepare("UPDATE game_state SET current_number = :num, drawn_numbers = :drawn WHERE id = 1");
        $stmt->execute([':num' => $nextNumber, ':drawn' => json_encode($drawnNumbers)]);

        echo json_encode(['status' => 'success', 'current_number' => $nextNumber, 'drawn_numbers' => $drawnNumbers]);
        break;

    case 'get_status':
        initDb($db);
        $stmt = $db->query("SELECT current_number, drawn_numbers, status, winner_info FROM game_state WHERE id = 1");
        $row = $stmt->fetch();
        $row['drawn_numbers'] = json_decode($row['drawn_numbers'], true);
        $row['winner_info'] = json_decode($row['winner_info'], true);
        echo json_encode($row);
        break;

    case 'shout_bingo':
        initDb($db);
        $player = $_POST['name'] ?? 'Unknown';
        $card = $_POST['card'] ?? '[]'; // JSON string of matrix
        
        $winnerInfo = json_encode([
            'player' => $player,
            'card' => json_decode($card),
            'timestamp' => time()
        ]);
        
        $stmt = $db->prepare("UPDATE game_state SET winner_info = :info WHERE id = 1");
        $stmt->execute([':info' => $winnerInfo]);
        
        echo json_encode(['status' => 'success']);
        break;

    case 'register_heartbeat':
         initDb($db);
         $id = $_POST['id'] ?? '';
         $name = $_POST['name'] ?? 'Unknown';
         $score = $_POST['score'] ?? 0;
         if(!$id) {
             echo json_encode(['error' => 'Missing ID']);
             exit;
         }
         
         // Ensure schema has score column (poor man's migration for SQLite)
         // We'll just ignore error if it exists or create table with it from scratch if not.
         // Better: just add it to the CREATE statement and alter if needed? 
         // For simplicity in this dev loop, let's assume table might need alter or just recreate.
         // Actually, let's just try to add the column if missing? No, SQLite ALTER is limited.
         // Simplest for this env: Just update the CREATE IF NOT EXISTS in initDb and hope user creates fresh on deploy, 
         // OR, just try to run ALTER TABLE and catch exception.
         try {
            $db->exec("ALTER TABLE players ADD COLUMN score INTEGER DEFAULT 0");
         } catch(Exception $e) { /* Ignore if exists */ }
         
         try {
            $db->exec("ALTER TABLE players ADD COLUMN matched_str TEXT DEFAULT ''");
         } catch(Exception $e) { /* Ignore */ }
         
         try {
            $db->exec("ALTER TABLE players ADD COLUMN marked_str TEXT DEFAULT ''");
         } catch(Exception $e) { /* Ignore */ }

         // Upsert player
         $stmt = $db->prepare("INSERT INTO players (id, name, score, matched_str, marked_str, last_seen) 
                               VALUES (:id, :name, :score, :matched, :marked, CURRENT_TIMESTAMP) 
                               ON CONFLICT(id) DO UPDATE SET name = :name, score = :score, matched_str = :matched, marked_str = :marked, last_seen = CURRENT_TIMESTAMP");
         $stmt->execute([
             ':id' => $id, 
             ':name' => $name, 
             ':score' => $score,
             ':matched' => $matched,
             ':marked' => $marked
         ]);
         echo json_encode(['status' => 'success']);
         break;

    case 'get_players':
         initDb($db);
         // Get players seen in last 15 seconds
         $stmt = $db->query("SELECT * FROM players WHERE last_seen > datetime('now', '-15 seconds') ORDER BY name ASC");
         $players = $stmt->fetchAll();
         echo json_encode(['players' => $players]);
         break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
