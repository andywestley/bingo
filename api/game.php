<?php
// api/game.php

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        started_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Ensure there is one row
    $count = $db->query("SELECT COUNT(*) FROM game_state")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO game_state (id, current_number, drawn_numbers, status) VALUES (1, NULL, '[]', 'stopped')");
    }

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
        $stmt = $db->prepare("UPDATE game_state SET current_number = NULL, drawn_numbers = '[]', status = 'playing', started_at = CURRENT_TIMESTAMP WHERE id = 1");
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
        $stmt = $db->query("SELECT current_number, drawn_numbers, status FROM game_state WHERE id = 1");
        $row = $stmt->fetch();
        $row['drawn_numbers'] = json_decode($row['drawn_numbers'], true);
        echo json_encode($row);
        break;

    case 'register_heartbeat':
         initDb($db);
         $id = $_POST['id'] ?? '';
         $name = $_POST['name'] ?? 'Unknown';
         if(!$id) {
             echo json_encode(['error' => 'Missing ID']);
             exit;
         }
         
         // Upsert player
         $stmt = $db->prepare("INSERT INTO players (id, name, last_seen) VALUES (:id, :name, CURRENT_TIMESTAMP) 
                               ON CONFLICT(id) DO UPDATE SET name = :name, last_seen = CURRENT_TIMESTAMP");
         $stmt->execute([':id' => $id, ':name' => $name]);
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
