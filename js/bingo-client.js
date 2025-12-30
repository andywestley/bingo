
const API_URL = 'api/game.php';

async function apiCall(action) {
    // Only log write actions or if debug is really needed, otherwise polling spams console.
    // Let's log everything for now as per request.
    const isPolling = (action === 'get_status' || action === 'get_players');

    // if(!isPolling) 
    Logger.debug(`API Request: ${action}`);

    try {
        const response = await fetch(`${API_URL}?action=${action}`, { method: 'POST' });
        const data = await response.json();

        // if(!isPolling) 
        Logger.debug(`API Response [${action}]:`, data);

        if (data.error) {
            Logger.error(`API Error [${action}]:`, data.error);
        }

        return data;
    } catch (e) {
        Logger.error(`API Network Error [${action}]:`, e);
        return { error: e.message };
    }
}

async function startGame() {
    return await apiCall('start_game');
}

async function drawBall() {
    return await apiCall('draw_ball');
}

async function getStatus() {
    return await apiCall('get_status');
}

async function sendHeartbeat(id, name) {
    Logger.debug(`Sending Heartbeat: ${name} (${id})`);
    try {
        const formData = new FormData();
        formData.append('action', 'register_heartbeat');
        formData.append('id', id);
        formData.append('name', name);
        const response = await fetch(API_URL, { method: 'POST', body: formData });
        const data = await response.json();

        if (data.error) Logger.error("Heartbeat Error:", data.error);
        else Logger.debug("Heartbeat OK");

        return data;
    } catch (e) {
        Logger.error("Heartbeat Network Error:", e);
        return { error: e.message };
    }
}

async function getConnectedPlayers() {
    return await apiCall('get_players');
}

function getNumberSlang(number) {
    // Copied from original bingo.js
    var slang = "";
    switch (parseInt(number)) {
        case 1: slang = "Kelly's Eye"; break;
        case 2: slang = "One little duck."; break;
        case 3: slang = "You and me"; break;
        case 4: slang = "Knock at the door"; break;
        case 5: slang = "Man alive"; break;
        case 6: slang = "Tom Mix"; break;
        case 7: slang = "Lucky"; break;
        case 8: slang = "Garden gate"; break;
        case 9: slang = "Doctor's Orders"; break;
        case 10: slang = "Boris's Den"; break;
        case 11: slang = "Legs eleven"; break;
        case 12: slang = "One dozen"; break;
        case 13: slang = "Unlucky for some"; break;
        case 14: slang = "The Lawnmower"; break;
        case 15: slang = "Young and Keen"; break;
        case 16: slang = "Never been kissed"; break;
        case 17: slang = "Dancing Queen"; break;
        case 18: slang = "Coming of Age"; break;
        case 19: slang = "Goodbye Teens"; break;
        case 20: slang = "One Score"; break;
        case 21: slang = "Key of the Door"; break;
        case 22: slang = "Two little ducks"; break;
        case 23: slang = "The Lord is My Shepherd"; break;
        case 24: slang = "Double dozen"; break;
        case 25: slang = "Duck and dive"; break;
        case 26: slang = "A to Z"; break;
        case 27: slang = "Duck and a crutch."; break;
        case 28: slang = "In a state."; break;
        case 29: slang = "Rise and Shine"; break;
        case 30: slang = "Burlington Bertie"; break;
        case 31: slang = "Get Up and Run"; break;
        case 32: slang = "Buckle My Shoe"; break;
        case 33: slang = "All the threes"; break;
        case 34: slang = "Ask for More"; break;
        case 35: slang = "Jump and Jive"; break;
        case 36: slang = "Triple dozen"; break;
        case 38: slang = "Christmas cake"; break;
        case 39: slang = "Steps"; break;
        case 40: slang = "Life Begins"; break;
        case 43: slang = "down on your knees"; break;
        case 44: slang = "Droopy drawers"; break;
        case 45: slang = "Halfway there"; break;
        case 46: slang = "up to tricks"; break;
        case 48: slang = "Four Dozen"; break;
        case 50: slang = "It's a bullseye!"; break;
        case 52: slang = "Deck of Cards"; break;
        case 53: slang = "Here comes Herbie"; break;
        case 54: slang = "Man at the door"; break;
        case 55: slang = "All the fives"; break;
        case 56: slang = "Shotts Bus"; break;
        case 57: slang = "Heinz Varieties"; break;
        case 59: slang = "The Brighton Line"; break;
        case 60: slang = "Grandma's getting frisky"; break;
        case 62: slang = "Tickety-boo"; break;
        case 64: slang = "Almost retired"; break;
        case 65: slang = "Retirement age, Stop work"; break;
        case 66: slang = "Clickety click"; break;
        case 67: slang = "Stairway to Heaven"; break;
        case 68: slang = "Pick a Mate"; break;
        case 71: slang = "Bang on the Drum"; break;
        case 72: slang = "Danny La Rue"; break;
        case 73: slang = "Queen Bee. Under The Tree. Lucky 3"; break;
        case 74: slang = "Hit the Floor"; break;
        case 76: slang = "Was she worth it?"; break;
        case 77: slang = "Sunset Strip"; break;
        case 78: slang = "39 more steps"; break;
        case 80: slang = "Gandhi's Breakfast"; break;
        case 81: slang = "Fat Lady with a walking stick"; break;
        case 82: slang = "I'm gonna get more right than you"; break;
        case 83: slang = "Time for Tea"; break;
        case 84: slang = "Seven dozen"; break;
        case 85: slang = "Staying alive"; break;
        case 86: slang = "Between the sticks"; break;
        case 87: slang = "Torquay in Devon"; break;
        case 88: slang = "Two Fat Ladies"; break;
        case 89: slang = "Almost there"; break;
        case 90: slang = "Top of the shop"; break;
        default: break;
    }
    return slang;
}
