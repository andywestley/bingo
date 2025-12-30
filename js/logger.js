/**
 * Centralized Logger for Bingo Game
 */
const Logger = {
    // Configuration
    debugEnabled: true,

    _log: function (level, ...args) {
        const timestamp = new Date().toISOString().substring(11, 19);
        const prefix = `[Bingo] [${timestamp}] [${level}]`;

        if (level === 'DEBUG' && !this.debugEnabled) return;

        switch (level) {
            case 'ERROR':
                console.error(prefix, ...args);
                break;
            case 'WARN':
                console.warn(prefix, ...args);
                break;
            case 'INFO':
                console.info(prefix, ...args);
                break;
            case 'DEBUG':
                console.debug(prefix, ...args);
                break;
            default:
                console.log(prefix, ...args);
        }
    },

    info: function (...args) { this._log('INFO', ...args); },
    warn: function (...args) { this._log('WARN', ...args); },
    error: function (...args) { this._log('ERROR', ...args); },
    debug: function (...args) { this._log('DEBUG', ...args); }
};

// Expose globally
window.Logger = Logger;
console.log("[Bingo] Logger initialized");
