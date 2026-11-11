<?php
ob_start();               // <- capture EVERYTHING from here on
session_start();

include_once 'SaveFile.php';
include_once 'ClickPower.php';

$_SESSION['cookies'] = $_SESSION['cookies'] ?? 0;
$ClickPower = new ClickPower();
$SafeFile   = new SaveFile();

/**
 * Send a clean JSON response, throwing away any accidental output
 * (BOM, whitespace from included files, PHP notices/warnings, ...).
 */
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
    <button id="cookieClick">click on cookie</button>
    <br><br>
    <h3>buy</h3>
    <button id="buyClicker" class="buy" data-item="clicker">clicker: cost 10</button>
    <button id="buyGrandma" class="buy" data-item="grandma">grandma: cost 100</button>
    <button id="buyFarm" class="buy" data-item="farm">farm: cost 1000</button>

    <script>
        const amountEl = document.getElementById('cookie-amount');
        const gainEl = document.getElementById('GainEverySecond');

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
                return JSON.parse(text);
            } catch (e) {
                // Show the offending raw output so you can find it
                console.error('Non-JSON response from server:\n', text);
                throw e;
            }
        }

        document.getElementById('cookieClick').addEventListener('click', () => {
            send('click')
                .then(data => {
                    amountEl.textContent = data.amount;
                })
                .catch(err => console.error('Error:', err));
        });

        document.querySelectorAll('.buy').forEach(btn => {
            btn.addEventListener('click', () => {
                send('buy', {
                        name: btn.dataset.item
                    })
                    .then(data => {
                        if (data.error) {
                            console.warn(data.error);
                            return;
                        }
                        amountEl.textContent = data.amount;
                        if (data.gain !== undefined) gainEl.textContent = data.gain;
                    })
                    .catch(err => console.error('Error:', err));
            });
        });
    </script>
</body>

</html>