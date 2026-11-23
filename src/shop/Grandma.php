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
    public function getKost(): float
    {
        return 100.0;
    }

    public function pasToe(ClickPower $clickPower, SaveFile $save): void
    {
        // Oma bakte vroeger koekjes: +1 per seconde.
        $clickPower->verhoogPerSecond(1.0);
    }
}
