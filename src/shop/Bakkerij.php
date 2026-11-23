<?php

declare(strict_types=1);

namespace Shop;

use cookies\ClickPower;
use user\SaveFile;

final class Bakkerij implements Koopbaar
{
    public function getNaam(): string
    {
        return 'bakkerij';
    }
    public function getKost(): int
    {
        return 5000;
    }

    public function pasToe(ClickPower $clickPower, SaveFile $save): void
    {
        $clickPower->verhoogPerSecond(50);
    }
}
