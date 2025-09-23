if (window.__ccACInstalled) return;
window.__ccACInstalled = true;

const ENDPOINT = '/ajax_autoclick_detection.php';
const PAGE_HINT = location.pathname;
const FLUSH_MS = 3000;

// Turn verbose logs on/off from console:
// localStorage.ccac_debug = '1'  (enable)
// localStorage.ccac_debug = '0'  (disable)
let DEBUG = (localStorage.getItem('ccac_debug') === '1');

const batch = new Map();
let flushSoonTimer = null;

function log(...args) {
    if (!DEBUG) return;
    try { console.log('[cc-ac]', ...args); } catch { }
}

// Expose a tiny debug API
window.ccac = {
    get DEBUG() { return DEBUG; },
    set DEBUG(v) { DEBUG = !!v; localStorage.setItem('ccac_debug', v ? '1' : '0'); },
    peek() {
        const out = [];
        for (const [reason, val] of batch.entries()) out.push([reason, { ...val }]);
        return { PAGE_HINT, out };
    },
    add: (r, extra = {}) => aTB(r, extra),
    flush: () => fB(true),
};

log('loaded', { PAGE_HINT, FLUSH_MS });

function aTB(reason, extra = {}) {
    const cur = batch.get(reason) || { count: 0, last: null };
    cur.count += 1;
    cur.last = extra;
    batch.set(reason, cur);
    log('batch+=', { reason, count: cur.count, last: cur.last });

    // Quick "flush soon" so we don’t rely only on the 3s interval
    if (!flushSoonTimer) {
        flushSoonTimer = setTimeout(() => {
            flushSoonTimer = null;
            fB(false);
        }, 300);
    }
}

function flushWithBeacon() {
    if (batch.size === 0) return;
    const items = [];
    for (const [reason, { count, last }] of batch.entries()) {
        items.push({ reason, count, last, page_hint: PAGE_HINT });
    }
    batch.clear();
    const blob = new Blob([JSON.stringify({ batch: items })], { type: 'application/json' });
    const ok = navigator.sendBeacon(ENDPOINT, blob);
    log('sendBeacon', { ok, itemsCount: items.length, items });
}

window.addEventListener('pagehide', flushWithBeacon);
window.addEventListener('beforeunload', flushWithBeacon);

async function fB(manual = false) {
    if (batch.size === 0) {
        if (manual) log('flush noop (empty)');
        return;
    }
    const items = [];
    for (const [reason, { count, last }] of batch.entries()) {
        items.push({ reason, count, last, page_hint: PAGE_HINT });
    }
    batch.clear();
    log('flushing', { itemsCount: items.length, items, manual });

    try {
        const res = await fetch(ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ batch: items }),
            keepalive: true
        });
        const text = await res.text();
        log('flush result', { status: res.status, text });
    } catch (e) {
        log('flush error', e);
    }
}

setInterval(() => fB(false), FLUSH_MS);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') {
        log('visibilitychange: hidden');
        fB(true);
    }
});
window.addEventListener('pagehide', () => {
    log('pagehide -> fetch flush (in addition to beacon)');
    fB(true);
});

// -------- Click counter (debug prints every 25 clicks) --------
let clickCount = 0;
document.addEventListener('click', () => {
    clickCount++;
    if (clickCount % 25 === 0) log('clickCount', clickCount);
    if (clickCount > 300) {
        log('click_count TRIP', clickCount);
        aTB('click_count', { count: clickCount });
        clickCount = 0;
    }
}, { capture: true });

// -------- Event signals --------
['pointerdown', 'click', 'auxclick', 'contextmenu'].forEach(type => {
    document.addEventListener(type, evt => {
        // Sample logs so we don’t spam (log 1 in 50)
        if (DEBUG && (Math.random() < 0.02)) {
            log('evt', { type, isTrusted: evt.isTrusted, button: evt.button, target: evt.target?.tagName });
        }

        if (evt.isTrusted === false) {
            aTB('click_not_trusted', { type });
            return;
        }
        if (type === 'auxclick' && (typeof evt.button !== 'number' || evt.button > 2)) {
            aTB('aux_click', { button: evt.button });
        }
    }, { capture: true });
});

// -------- DevTools signal (log only on state change) --------
let dtWO = false;
function cDT() {
    const threshold = 160;
    const widthDiff = window.outerWidth - window.innerWidth;
    const heightDiff = window.outerHeight - window.innerHeight;
    return (widthDiff > threshold || heightDiff > threshold);
}
setInterval(() => {
    const open = cDT();
    if (open && !dtWO) {
        log('dev_tools_is_open');
        aTB('dev_tools_is_open');
    }
    dtWO = open;
}, 5000);
