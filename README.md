# Bingo Game

A simple HTML/JavaScript bingo game application.

## Structure

The project code is located in the `WebContent` directory.

- **`index.html`**: The main interface for the "Bingo Machine" (the caller).
  - Allows drawing the next number.
  - Displays the current and previous numbers.
  - Shows the board of remaining/called numbers (1-90).
  - Displays traditional Bingo slang for selected numbers.
- **`cards.html`**: An interface intended for generating Bingo cards for players.
  - *Note: The logic for generating cards does not appear to be implemented in `bingo.js` currently.*
- **`bingo.js`**: Contains the client-side logic.
  - Handles the randomization of balls (1-90).
  - Manages the state of the "machine" (balls remaining).
  - Updates the DOM elements (current number, board, slang).
- **`bingo.css`**: Custom styling for the application (in addition to Bootstrap).

## Usage

1. Open `WebContent/index.html` in a web browser.
2. Click **Pick next number** to draw a ball.
3. The board will update to show called numbers.
4. Click **Start new game** to reset.