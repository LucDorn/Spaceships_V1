<?php

declare(strict_types=1);
// session_destroy();
ob_start();
session_start();

require_once __DIR__ . '/src/AllSrc.php';

use cookies\ClickPower;
use user\SaveFile;
use shop\Registry;

function jsonResponse(array $data, int $code = 200): void
{
    while (ob_get_level() > 0) ob_end_clean();
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$SaveFile   = new SaveFile();
$ClickPower = new ClickPower($SaveFile);
$Shop       = new Registry();

// Passieve inkomsten syncen op basis van verstreken tijd
// $ClickPower->syncTime();

$action = $_POST['action'] ?? '';

// -------- AJAX: click --------
if ($action === 'click') {
    $ClickPower->cookieClick();
    jsonResponse([
        'amount'     => $SaveFile->getAmount(),
        'clickPower' => $ClickPower->getAmount(),
        'perSecond'  => $ClickPower->getPerSecond(),
    ]);
}

// -------- AJAX: tick (elke seconde, voor live UI) --------
if ($action === 'tick') {
    jsonResponse([
        'amount'     => $SaveFile->getAmount(),
        'clickPower' => $ClickPower->getAmount(),
        'perSecond'  => $ClickPower->getPerSecond(),
    ]);
}

// -------- AJAX: buy --------
if ($action === 'buy') {
    $naam = $_POST['name'] ?? '';
    $item = $Shop->vind($naam);

    if ($item === null) {
        jsonResponse(['error' => 'Onbekend item'], 400);
    }

    if (!$SaveFile->kanBetalen($item->getKost())) {
        jsonResponse([
            'error'  => 'Te weinig cookies',
            'amount' => $SaveFile->getAmount(),
        ], 400);
    }

    $SaveFile->subtractAmount($item->getKost());
    $item->pasToe($ClickPower, $SaveFile);

    jsonResponse([
        'ok'         => true,
        'amount'     => $SaveFile->getAmount(),
        'clickPower' => $ClickPower->getAmount(),
        'perSecond'  => $ClickPower->getPerSecond(),
    ]);
}

// -------- Page render --------
while (ob_get_level() > 0) ob_end_clean();

$items = $Shop->alle();
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Cookie Clicker</title>
    <link rel="stylesheet" href="public/style.css">
</head>

<body>

    <h1>🍪 Cookie Clicker</h1>

    <div class="stats">
        <p>Cookies: <strong id="amount"><?= htmlspecialchars((string) $SaveFile->getAmount()) ?></strong></p>
        <p>Per klik: <strong id="clickPower"><?= htmlspecialchars((string) $ClickPower->getAmount()) ?></strong></p>
        <p>Per seconde: <strong id="perSecond"><?= htmlspecialchars((string) $ClickPower->getPerSecond()) ?></strong></p>
    </div>

    <p id="status" aria-live="polite"></p>

    <button id="cookieClick" class="click-btn">🍪 Klik op de cookie</button>

    <h2>Winkel</h2>
    <div class="shop">
        <?php foreach ($items as $item): ?>
            <button class="buy" data-item="<?= htmlspecialchars($item->getNaam()) ?>">
                <?= htmlspecialchars($item->getNaam()) ?> — <?= htmlspecialchars((string) $item->getKost()) ?> 🍪
            </button>
        <?php endforeach; ?>
    </div>
    <script src="public/goldenCookie.js"></script>
    <script src="public/app.js"></script>

</body>

</html>