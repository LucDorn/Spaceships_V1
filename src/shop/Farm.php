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
    public function getKost(): float
    {
        return 1000.0;
    }

    public function pasToe(ClickPower $clickPower, SaveFile $save): void
    {
        // Een boerderij levert flink wat koekjes op.
        $clickPower->verhoogPerSecond(10.0);
    }
}
