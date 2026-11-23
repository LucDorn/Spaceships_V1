<?php

declare(strict_types=1);

namespace shop;

use cookies\ClickPower;
use user\SaveFile;

/**
 * Het contract: alles wat je in de winkel kan kopen.
 * Elke implementatie MOET deze drie methodes hebben.
 */
interface Koopbaar
{
    public function getNaam(): string;
    public function getKost(): int;
    public function pasToe(ClickPower $clickPower, SaveFile $save): void;
}
