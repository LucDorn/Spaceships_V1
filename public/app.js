const amountEl = document.getElementById('amount');
const clickPowEl = document.getElementById('clickPower');
const perSecEl = document.getElementById('perSecond');
const statusEl = document.getElementById('status');


async function send(action, extra = {}) {
    const body = new FormData();
    body.append('action', action);
    for (const [k, v] of Object.entries(extra)) body.append(k, v);

    const res = await fetch('', { method: 'POST', body });
    const text = await res.text();

    try {
        return { ok: res.ok, data: JSON.parse(text) };
    } catch (e) {
        console.error('Geen geldige JSON:\n', text);
        throw new Error('Server gaf geen geldige JSON terug');
    }
}

function updateUI(data) {
    if (data.amount !== undefined) amountEl.textContent = Math.floor(data.amount);
    if (data.clickPower !== undefined) clickPowEl.textContent = data.clickPower;
    if (data.perSecond !== undefined) perSecEl.textContent = Math.floor(data.perSecond);
}


function toonStatus(tekst, soort = 'info') {
    statusEl.textContent = tekst;
    statusEl.dataset.soort = soort;
    setTimeout(() => { statusEl.textContent = ''; }, 2500);
}

async function metKnopUit(btn, fn) {
    btn.disabled = true;
    try { return await fn(); }
    finally { btn.disabled = false; }
}

// --- Click ---
document.getElementById('cookieClick').addEventListener('click', (e) =>
    metKnopUit(e.currentTarget, async () => {
        try {
            const { data } = await send('click');
            updateUI(data);
        } catch (err) {
            toonStatus(err.message, 'error');
        }
    })
);

// --- Buy ---
document.querySelectorAll('.buy').forEach(btn => {
    btn.addEventListener('click', () => metKnopUit(btn, async () => {
        try {
            const { ok, data } = await send('buy', { name: btn.dataset.item });
            if (!ok || data.error) {
                toonStatus(data.error ?? 'Aankoop mislukt', 'error');
                updateUI(data);
                return;
            }
            updateUI(data);
            toonStatus(`${btn.dataset.item} gekocht!`, 'success');
        } catch (err) {
            toonStatus(err.message, 'error');
        }
    }));
});

// --- Live update (elke seconde passieve inkomsten ophalen) ---
setInterval(async () => {
    try {
        const { data } = await send('tick');
        updateUI(data);
    } catch (e) {
        // stil falen — anders spamt de console
    }
}, 1000);