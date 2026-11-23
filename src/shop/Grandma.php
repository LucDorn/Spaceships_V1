<?php

declare(strict_types=1);

namespace Shop;

use cookies\ClickPower;
use user\SaveFile;

final class Grandma implements Koopbaar
{
    public function getNaam(): string
    {
        return 'grandma';
    }
    public function getKost(): int
    {
        return 100;
    }

    public function pasToe(ClickPower $clickPower, SaveFile $save): void
    {
        $clickPower->verhoogPerSecond(1);
    }
}
