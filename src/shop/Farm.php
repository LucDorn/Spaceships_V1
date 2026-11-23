<?php

declare(strict_types=1);

namespace Shop;

use cookies\ClickPower;
use user\SaveFile;

final class Farm implements Koopbaar
{
    public function getNaam(): string
    {
        return 'farm';
    }
    public function getKost(): int
    {
        return 1000;
    }

    public function pasToe(ClickPower $clickPower, SaveFile $save): void
    {
        $clickPower->verhoogPerSecond(10);
    }
}
