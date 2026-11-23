<?php

declare(strict_types=1);

namespace cookies;

use user\SaveFile;

final class ClickPower
{
    private float $clickPower;
    private float $perSecond;

    public function __construct(private SaveFile $save)
    {
        $this->clickPower = (float) ($_SESSION['clickPower'] ?? 1.0);
        $this->perSecond  = (float) ($_SESSION['perSecond']  ?? 0.0);
    }

    /** Eén klik: voeg clickPower cookies toe aan het totaal. */
    public function cookieClick(): void
    {
        $this->save->addAmount($this->clickPower);
    }

    /**
     * Passieve inkomsten syncen op basis van verstreken tijd.
     * Roep dit bovenaan elk request aan (load, click, buy, tick).
     */
    public function syncTime(): void
    {
        $now  = microtime(true);
        $last = (float) ($_SESSION['last_tick'] ?? $now);

        $elapsed = max(0.0, $now - $last);

        if ($this->perSecond > 0) {
            $this->save->addAmount($this->perSecond * $elapsed);
        }

        $_SESSION['last_tick'] = $now;
    }

    public function getAmount(): float
    {
        return $this->clickPower;
    }
    public function getPerSecond(): float
    {
        return $this->perSecond;
    }

    public function verdubbelClickPower(): void
    {
        $this->clickPower *= 2;
        $_SESSION['clickPower'] = $this->clickPower;
    }

    public function verhoogClickPower(float $x): void
    {
        $this->clickPower += $x;
        $_SESSION['clickPower'] = $this->clickPower;
    }

    public function verhoogPerSecond(float $x): void
    {
        $this->perSecond += $x;
        $_SESSION['perSecond'] = $this->perSecond;
    }
}
