// cc-countdown.js
// Handles countdowns for events in the header

function formatDuration(seconds) {
    const w = Math.floor(seconds / (7 * 24 * 3600));
    const d = Math.floor((seconds % (7 * 24 * 3600)) / (24 * 3600));
    const h = Math.floor((seconds % (24 * 3600)) / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = Math.floor(seconds % 60);

    const parts = [];

    if (w > 0) parts.push(`${w} week${w !== 1 ? 's' : ''}`);
    if (d > 0) parts.push(`${d} day${d !== 1 ? 's' : ''}`);
    if (h > 0) parts.push(`${h} hour${h !== 1 ? 's' : ''}`);
    if (m > 0) parts.push(`${m} minute${m !== 1 ? 's' : ''}`);
    if (s > 0 || parts.length === 0) parts.push(`${s} second${s !== 1 ? 's' : ''}`);

    return parts.join(', ');
}

function updateCountdowns() {
    const now = Math.floor(Date.now() / 1000);
    document.querySelectorAll('.event-countdown').forEach(el => {
        const end = parseInt(el.getAttribute('data-end'), 10);
        const countdownEl = el.querySelector('.countdown-text');
        const remaining = end - now;

        countdownEl.textContent = remaining > 0
            ? formatDuration(remaining)
            : "0 seconds, event has ended";
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
