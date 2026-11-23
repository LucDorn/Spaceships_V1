<?php

declare(strict_types=1);

namespace Shop;

use cookies\ClickPower;
use user\SaveFile;

final class Clicker implements Koopbaar
{
    public function getNaam(): string
    {
        return 'clicker';
    }
    public function getKost(): float
    {
        return 10.0;
    }

    public function pasToe(ClickPower $clickPower, SaveFile $save): void
    {
        // Een clicker verhoogt click kracht.
        $clickPower->verhoogClickPower(1);
    }
}
