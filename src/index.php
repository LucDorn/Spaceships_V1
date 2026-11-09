<?php
session_start();
include_once 'Spaceship.php';
$_SESSION['cookies'] = $_SESSION['cookies'] ?? 0;


// Handle the AJAX click BEFORE any HTML is output
if (($_POST['action'] ?? '') === 'click') {
    $ship = new spaceship();
    $ship->click();

    header('Content-Type: application/json');
    echo json_encode(['amount' => $ship->getAmount()]);
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>

<body>
    <button id="cookie-click">click on cookie</button>
    <div id="cookie-amount"><?= (new spaceship())->getAmount() ?></div>

    <script>
        document.getElementById('cookie-click').addEventListener('click', () => {
            const body = new FormData();
            body.append('action', 'click');

            fetch('', {
                    method: 'POST',
                    body
                }) // '' = current page
                .then(r => r.json())
                .then(data => {
                    document.getElementById('cookie-amount').textContent = data.amount;
                })
                .catch(err => console.error('Error:', err));
        });
    </script>
</body>

</html>