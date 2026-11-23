<?php

declare(strict_types=1);

/** hoe het werkt met functie oproepingg
 *  namespace → bestandspad.
 *
 *   cookies\ClickPower   →  src/cookies/ClickPower.php
 *   user\SaveFile        →  src/user/SaveFile.php
 *   Shop\Registry        →  src/Shop/Registry.php
 */
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
