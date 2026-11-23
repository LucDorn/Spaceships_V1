<?php
ob_start();
session_start();

require_once './src/AllSrc.php';

$_SESSION['cookies'] = $_SESSION['cookies'] ?? 0;
$ClickPower = new \cookies\ClickPower();
$SafeFile   = new \user\SaveFile();


// Send a clean JSON response
function jsonResponse(array $data, int $code = 200): void
{
    while (ob_get_level() > 0) ob_end_clean();
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ---- AJAX: click ----
if (($_POST['action'] ?? '') === 'click') {
    $ClickPower->cookieClick();
    jsonResponse(['amount' => $SafeFile->getAmount()]);
}

// ---- AJAX: buy ----
if (($_POST['action'] ?? '') === 'buy') {
    $allowed = ['clicker', 'grandma', 'farm'];
    $name    = $_POST['name'] ?? '';

    if (!in_array($name, $allowed, true)) {
        jsonResponse(['error' => 'unknown item', 'amount' => $SafeFile->getAmount()], 400);
    }

    $result = $SafeFile->buy($name);   // make sure this method exists!

    jsonResponse([
        'ok'     => $result !== false,
        'amount' => $SafeFile->getAmount(),
        'gain'   => $ClickPower->getAmount(),
    ]);
}

// ---- Normal page render ----
// Drop the buffer (strips BOM/warnings) and start fresh.
while (ob_get_level() > 0) ob_end_clean();

echo "<div id='cookie-amount'>" . $SafeFile->getAmount() . "</div>";
echo "<div id='GainEverySecond'>" . $ClickPower->getAmount() . "</div>";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>

<body>
    <p id="status" aria-live="polite"></p>
    <div id="cookie-amount" aria-live="polite">0</div>
    <div id="GainEverySecond" aria-live="polite">0</div>
    <button id="cookieClick">click on cookie</button>
    <br><br>
    <h3>buy</h3>
    <button id="buyClicker" class="buy" data-item="clicker">clicker: cost 10</button>
    <button id="buyGrandma" class="buy" data-item="grandma">grandma: cost 100</button>
    <button id="buyFarm" class="buy" data-item="farm">farm: cost 1000</button>

    <script>
        const amountEl = document.getElementById('cookie-amount');
        const gainEl = document.getElementById('GainEverySecond');
        const statusEl = document.getElementById('status');

        async function send(action, extra = {}) {
            const body = new FormData();
            body.append('action', action);
            for (const [k, v] of Object.entries(extra)) body.append(k, v);

            const res = await fetch('', {
                method: 'POST',
                body
            });
            const text = await res.text();

            try {
                return {
                    ok: res.ok,
                    data: JSON.parse(text)
                };
            } catch (e) {
                console.error('Non-JSON response:\n', text);
                throw new Error('Server gaf geen geldige JSON terug');
            }
        }

        function toonStatus(tekst, soort = 'info') {
            statusEl.textContent = tekst;
            statusEl.dataset.soort = soort; // voor CSS-kleurtje
        }

        async function metKnopUit(btn, fn) {
            btn.disabled = true;
            try {
                return await fn();
            } finally {
                btn.disabled = false;
            }
        }

        // --- Click ---
        document.getElementById('cookieClick').addEventListener('click', (e) =>
            metKnopUit(e.currentTarget, async () => {
                try {
                    const {
                        data
                    } = await send('click');
                    amountEl.textContent = data.amount;
                } catch (err) {
                    toonStatus(err.message, 'error');
                }
            })
        );

        // --- Buy ---
        document.querySelectorAll('.buy').forEach(btn => {
            btn.addEventListener('click', () => metKnopUit(btn, async () => {
                try {
                    const {
                        ok,
                        data
                    } = await send('buy', {
                        name: btn.dataset.item
                    });
                    if (!ok || data.error) {
                        toonStatus(data.error ?? 'Aankoop mislukt', 'error');
                        return;
                    }
                    amountEl.textContent = data.amount;
                    if (data.gain !== undefined) gainEl.textContent = data.gain;
                    toonStatus(`${btn.dataset.item} gekocht!`, 'success');
                } catch (err) {
                    toonStatus(err.message, 'error');
                }
            }));
        });
    </script>
</body>

</html>