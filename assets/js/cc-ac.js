if (window.__ccACInstalled) return;
window.__ccACInstalled = true;

const ENDPOINT = '/ajax_autoclick_detection.php';
const PAGE_HINT = location.pathname;
const batch = new Map();
const FLUSH_MS = 3000;

function aTB(reason, extra = {}) {
    const cur = batch.get(reason) || { count: 0, last: null };
    cur.count += 1;
    cur.last = extra;
    batch.set(reason, cur);
}

function flushWithBeacon() {
    if (batch.size === 0) return;
    const items = [];
    for (const [reason, { count, last }] of batch.entries()) {
        items.push({ reason, count, last, page_hint: PAGE_HINT });
    }
    batch.clear();
    const blob = new Blob([JSON.stringify({ batch: items })], { type: 'application/json' });
    navigator.sendBeacon(ENDPOINT, blob);
}

window.addEventListener('pagehide', flushWithBeacon);
window.addEventListener('beforeunload', flushWithBeacon);

async function fB() {
    if (batch.size === 0) return;
    const items = [];
    for (const [reason, { count, last }] of batch.entries()) {
        items.push({ reason, count, last, page_hint: PAGE_HINT });
    }
    batch.clear();
    try {
        await fetch(ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ batch: items }),
            keepalive: true
        });
    } catch (_) { /* ignore */ }
}

setInterval(fB, FLUSH_MS);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') fB();
});
window.addEventListener('pagehide', fB);

let clickCount = 0;
document.addEventListener('click', () => {
    clickCount++;
    if (clickCount > 300) {
        aTB('click_count', { count: clickCount });
        clickCount = 0;
    }
}, { capture: true });

['pointerdown', 'click', 'auxclick', 'contextmenu'].forEach(type => {
    document.addEventListener(type, evt => {
        if (evt.isTrusted === false) {
            aTB('click_not_trusted', { type });
            return;
        }
        if (type === 'auxclick' && (typeof evt.button !== 'number' || evt.button > 2)) {
            aTB('aux_click', { button: evt.button });
        }
    }, { capture: true });
});

let dtWO = false;
function cDT() {
    const threshold = 160;
    const widthDiff = window.outerWidth - window.innerWidth;
    const heightDiff = window.outerHeight - window.innerHeight;
    return (widthDiff > threshold || heightDiff > threshold);
}
setInterval(() => {
    const open = cDT();
    if (open && !dtWO) aTB('dev_tools_is_open');
    dtWO = open;
}, 5000);