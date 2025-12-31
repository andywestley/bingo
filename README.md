# Bingo Game (Realtime Web App)

A modern, web-based multiplayer Bingo game built with PHP, SQLite, and jQuery. It separates the "Host" (Caller) and "Player" experiences, allowing for a seamless game flow on any device.

## Features

### For the Host (Caller)
*   **Game Lobby**: Manage the game session. Players join a waiting room until you hit "Start Game".
*   **Beginner Mode**: Optional setting to give players hints (gold outlines) for called numbers.
*   **Realtime Dashboard**: View connected players, their match count, and exactly how many numbers they need to win ("To Go").
*   **Verification**: When a player shouts "BINGO!", view their card instantly to verify the win.
*   **Automated Calling**: Random number generation (1-90) with traditional Bingo slang for each number.

### For Players
*   **Mobile-First Design**: Responsive interface ideal for playing on phones or tablets.
*   **Interactive Card**: Click to mark numbers.
*   **Realtime Updates**: The board updates instantly as the host draws balls.
*   **Connected Players List**: See who you are playing against and who is gaining on you.
*   **"To Go" Counters**: Track your progress and see the tension build as you get closer to 1 number remaining.
*   **Bingo Shout**: One-click button to shout "BINGO!" and notify the host.

## System Requirements
*   **PHP**: 7.4 or higher.
*   **SQLite**: PHP SQLite extension enabled.
*   **Web Server**: Apache/Nginx or built-in PHP server.

## Installation & Setup

1.  **Clone the repository**:
    ```bash
    git clone <repository_url>
    cd bingo
    ```

2.  **Permissions**:
    Ensure the `data/` directory is writable by the web server user. The game creates a `bingo.sqlite` database here.
    ```bash
    mkdir -p data
    chmod 777 data
    ```

3.  **Run the Server**:
    Using PHP's built-in server for testing:
    ```bash
    php -S 0.0.0.0:8000
    ```

4.  **Access the Game**:
    *   **Host**: http://localhost:8000/host.php
    *   **Players**: http://localhost:8000/player.php

## Game Operation Guide

### 1. The Lobby
*   **Host**: Open `host.php`. If a game was previously running, click **Reset** to return to the Lobby.
*   **Players**: Open `player.php`. Enter a name. You will see a "Waiting for Host..." screen.
*   **Options**: The Host can toggle **"Beginner Mode"** (hints) before starting.
*   **Start**: When all players are connected (visible in the Host's player list), click **Start Game**.

### 2. Playing
*   **Host**: Click **Next Ball** to draw numbers.
*   **Players**:
    *   Numbers will appear on screen.
    *   If you have a matching number, click it to mark it (Green).
    *   **Beginner Mode**: If enabled, matching numbers will flash with a Gold border.
*   **Information**: Players can see a list of competitors and how many numbers everyone needs to win.

### 3. Winning
*   **Shouting**: When a player marks all 15 numbers, they click **BINGO!**.
*   **Verification**: The Host receives a popup with the winner's card.
    *   Green Cells = Correctly marked.
    *   Red Cells = False claim (marked but not called).
*   **End Game**: The Host clicks **"Confirm Game End"** to announce the winner and return everyone to the Lobby for the next round.

## Implementation Overview

### Architecture
*   **Frontend**: HTML5, Bootstrap 4, jQuery.
    *   `host.php`: The Caller's interface.
    *   `player.php`: The Player's interface.
    *   `js/bingo-client.js`: Handles API communication (Long-polling).
    *   `js/bingo-ui.js`: Shared UI logic (Animations).
*   **Backend**: PHP.
    *   `api/game.php`: REST-like API handling Game State and Player Telemetry.
*   **Database**: SQLite (`data/bingo.sqlite`). Single file, zero configuration.

### Key Logic
*   **State Management**: The game has three states: `STOPPED` (Legacy), `LOBBY`, and `PLAYING`.
*   **Sync**: Players poll `get_status` every 2 seconds to receive drawn numbers.
*   **Telemetry**: Players send "Heartbeats" every 3 seconds containing their score (matches) and marked numbers. This allows the Host to see "To Go" counts in near real-time.

## Future Improvements
*   **Room Codes**: Support multiple concurrent games by adding a `room_code` to the game state and player sessions.
*   **Secure Auth**: Replace simple localstorage names with a more robust session system.
*   **Sound Effects**: Add audio for "Ball Call" (Text-to-Speech) and "Bingo Shout".
*   **Leaderboards**: Track wins over time in the database.