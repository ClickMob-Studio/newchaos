// cc-countdown.js
// Handles countdowns for events in the header

function formatDuration(seconds) {
    const parts = [];

    const SECONDS_IN_YEAR = 365 * 24 * 3600;
    const SECONDS_IN_MONTH = 30 * 24 * 3600;
    const SECONDS_IN_WEEK = 7 * 24 * 3600;
    const SECONDS_IN_DAY = 24 * 3600;
    const SECONDS_IN_HOUR = 3600;
    const SECONDS_IN_MINUTE = 60;

    const y = Math.floor(seconds / SECONDS_IN_YEAR);
    seconds -= y * SECONDS_IN_YEAR;

    const mo = Math.floor(seconds / SECONDS_IN_MONTH);
    seconds -= mo * SECONDS_IN_MONTH;

    const w = Math.floor(seconds / SECONDS_IN_WEEK);
    seconds -= w * SECONDS_IN_WEEK;

    const d = Math.floor(seconds / SECONDS_IN_DAY);
    seconds -= d * SECONDS_IN_DAY;

    const h = Math.floor(seconds / SECONDS_IN_HOUR);
    seconds -= h * SECONDS_IN_HOUR;

    const m = Math.floor(seconds / SECONDS_IN_MINUTE);
    seconds -= m * SECONDS_IN_MINUTE;

    const s = seconds;

    if (y > 0) parts.push(`${y} y${y !== 1 ? 's' : ''}`);
    if (mo > 0) parts.push(`${mo} mo${mo !== 1 ? 's' : ''}`);
    if (w > 0) parts.push(`${w} w${w !== 1 ? 's' : ''}`);
    if (d > 0) parts.push(`${d} d${d !== 1 ? 's' : ''}`);
    if (h > 0) parts.push(`${h} h${h !== 1 ? 's' : ''}`);
    if (m > 0) parts.push(`${m} m${m !== 1 ? 's' : ''}`);
    if (s > 0 || parts.length === 0) parts.push(`${s} s`);

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
            : "0 seconds";
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});