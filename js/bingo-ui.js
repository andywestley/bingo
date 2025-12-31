/**
 * Shared Bingo UI Logic
 * Handles animations and frequent DOM manipulations shared between Host and Player.
 */

class BingoUI {
    /**
     * Animates the transition of a number from the "Current" display to the "History" list.
     * @param {string|number} nextNum - The new number to display as current.
     * @param {jQuery} currentEl - The jQuery element displaying the current number.
     * @param {jQuery} historyContainer - The jQuery container for the history list.
     */
    static async animateTransition(nextNum, currentEl, historyContainer) {
        return new Promise((resolve) => {
            let currentDisplay = currentEl.text();

            // Initial State check
            if (currentDisplay === '--' || currentDisplay === '') {
                currentEl.text(nextNum);
                resolve();
                return;
            }

            // 1. Prepare target slot in history
            // Create the badge but make it invisible (opacity 0) to reserve space and calculate position
            let historyHtml = `<span class="badge badge-light border p-2 m-1 history-item" style="font-size: 1.2rem; opacity: 0;">${currentDisplay}</span>`;
            historyContainer.prepend(historyHtml);

            // Limit history length immediately to keep layout stable during calculation
            if (historyContainer.children().length > 5) {
                historyContainer.children().last().remove();
            }

            let targetBadge = historyContainer.children().first();

            // Safety check
            if (targetBadge.length === 0) {
                currentEl.text(nextNum);
                resolve();
                return;
            }

            // 2. Calculate Coordinates
            let startPos = currentEl.offset();
            let endPos = targetBadge.offset();

            // 3. Create Flyer
            let flyer = $('<div class="flyer">' + currentDisplay + '</div>');
            $('body').append(flyer);

            flyer.css({
                position: 'absolute',
                top: startPos.top,
                left: startPos.left,
                fontSize: currentEl.css('font-size'), // Match start size
                fontWeight: 'bold',
                color: '#333',
                zIndex: 1000,
                transition: 'all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)' // Bouncy effect
            });

            // 4. Trigger Animation
            // Small timeout to allow browser render
            setTimeout(() => {
                flyer.css({
                    top: endPos.top,
                    left: endPos.left,
                    fontSize: '1.2rem', // Match target size
                    opacity: 1
                });
            }, 10);

            // 5. Update Current Number (Crossfade)
            currentEl.animate({ opacity: 0 }, 100, function () {
                $(this).text(nextNum).animate({ opacity: 1 }, 100);
            });

            // 6. Cleanup
            setTimeout(() => {
                flyer.remove();
                targetBadge.css('opacity', 1); // Reveal real badge
                resolve();
            }, 550); // Slightly longer than transition to be safe
        });
    }
}

// Attach to window for global access if not using modules
window.BingoUI = BingoUI;
