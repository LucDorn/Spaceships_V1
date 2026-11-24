(function () {
    'use strict';

    /* ==================== CONFIG ==================== */
    const CONFIG = {
        intervalMs: 10000,   // how often a cookie appears (ms)
        lifetimeMs: 15000,   // how long it stays if not clicked (0 = forever)
        size: 80,            // cookie diameter in px
        margin: 24           // min distance from screen edges
    };

    /* ============== YOUR CODE GOES HERE ==============
       Runs every time the cookie is clicked.
       `count` = total cookies collected so far. */
    function onCookieClicked(count) {
        console.log('🍪 Golden cookie collected! Total:', count);

        // Add your own stuff here, e.g.:
        // document.body.animate([{filter: 'brightness(1.6)' }, {filter: 'brightness(1)' }], {duration: 300 });
        // playSound('coin.mp3');
        // addPoints(10);
    }
    /* ================================================= */

    const STYLE_ID = 'golden-cookie-styles';
    const MAX_Z = 2147483647;

    let cookieEl = null;
    let spawnTimer = null;
    let expireTimer = null;
    let clickCount = 0;

    const api = {
        config: CONFIG,
        onClick: onCookieClicked,   // override me any time: goldenCookie.onClick = fn
        start,
        stop,
        spawn: spawnCookie,
        despawn: removeCookie,
        get clicks() { return clickCount; }
    };

    /* ---------------- styles ---------------- */
    function injectStyles() {
        if (document.getElementById(STYLE_ID)) return;
        const style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = `
    .golden-cookie {
        position: fixed;
    border-radius: 50%;
    cursor: pointer;
    z-index: ${MAX_Z};
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    background:
    radial-gradient(circle 8px at 66% 28%, rgba(140,70,0,.55) 0 60%, transparent 65%),
    radial-gradient(circle 9px at 34% 62%, rgba(140,70,0,.50) 0 60%, transparent 65%),
    radial-gradient(circle 6px at 62% 74%, rgba(140,70,0,.45) 0 60%, transparent 65%),
    radial-gradient(circle 5px at 44% 22%, rgba(140,70,0,.40) 0 60%, transparent 65%),
    radial-gradient(circle at 32% 28%, #fff8c0 0%, #ffd94d 28%, #efa31c 62%, #a86200 100%);
    box-shadow:
    0 0 18px 4px  rgba(255, 210, 80, .75),
    0 0 55px 18px rgba(255, 165, 0, .35),
    inset -6px -8px 16px rgba(120, 60, 0, .45),
    inset  6px  6px 12px rgba(255, 255, 220, .60);
    animation:
    gc-pop  .28s cubic-bezier(.2, 1.6, .5, 1),
    gc-glow 1.4s ease-in-out .28s infinite alternate;
    transition: transform .08s ease-out;
      }
    .golden-cookie:hover  {transform: scale(1.08); }
    .golden-cookie:active {transform: scale(.92); }

    .golden-cookie.gc-collected {
        pointer - events: none;
    animation: gc-collect .3s ease-out forwards;
      }

    @keyframes gc-pop     {from {transform: scale(0);   opacity: 0; } to {transform: scale(1); opacity: 1; } }
    @keyframes gc-glow    {from {filter: brightness(1); } to {filter: brightness(1.3) saturate(1.15); } }
    @keyframes gc-collect {to   {transform: scale(1.7); opacity: 0; filter: brightness(2); } }
    `;
        document.head.appendChild(style);
    }

    /* ---------------- spawn / remove ---------------- */
    function spawnCookie() {
        if (cookieEl) return;             // only one cookie at a time
        injectStyles();

        const s = CONFIG.size;
        const maxX = Math.max(0, window.innerWidth - s - CONFIG.margin * 2);
        const maxY = Math.max(0, window.innerHeight - s - CONFIG.margin * 2);

        const el = document.createElement('div');
        el.className = 'golden-cookie';
        el.style.width = s + 'px';
        el.style.height = s + 'px';
        el.style.left = (CONFIG.margin + Math.random() * maxX) + 'px';
        el.style.top = (CONFIG.margin + Math.random() * maxY) + 'px';

        el.addEventListener('click', handleClick);
        // don't let the page underneath react to clicks on the cookie
        ['mousedown', 'mouseup', 'pointerdown', 'pointerup', 'dblclick']
            .forEach(t => el.addEventListener(t, e => e.stopPropagation()));

        document.body.appendChild(el);
        cookieEl = el;

        if (CONFIG.lifetimeMs > 0) {
            expireTimer = setTimeout(removeCookie, CONFIG.lifetimeMs);
        }
    }

    function removeCookie() {
        clearTimeout(expireTimer);
        expireTimer = null;
        if (cookieEl) {
            cookieEl.remove();
            cookieEl = null;
        }
    }

    function handleClick(event) {
        event.preventDefault();
        event.stopPropagation();

        const el = cookieEl;
        cookieEl = null;
        clearTimeout(expireTimer);
        expireTimer = null;

        if (el) {                       // little pop-out animation, then delete
            el.classList.add('gc-collected');
            setTimeout(() => el.remove(), 300);
        }

        clickCount += 1;
        try {
            api.onClick(clickCount);      // <-- your code runs here
        } catch (err) {
            console.error('[goldenCookie] onClick handler threw:', err);
        }
    }

    /* ---------------- loop control ---------------- */
    function start() {
        if (spawnTimer !== null) return;
        spawnCookie();
        spawnTimer = setInterval(spawnCookie, CONFIG.intervalMs);
    }

    function stop() {
        clearInterval(spawnTimer);
        spawnTimer = null;
        removeCookie();
    }

    /* ---------------- go! ---------------- */
    window.goldenCookie = api;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();