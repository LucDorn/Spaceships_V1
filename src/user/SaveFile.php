<?php

declare(strict_types=1);

namespace user;

final class SaveFile
{
    private float $amount;

    public function __construct()
    {
        $this->amount = (float) ($_SESSION['cookies'] ?? 0);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = max(0.0, $amount);
        $_SESSION['cookies'] = $this->amount;
    }

    public function addAmount(float $amount): void
    {
        $this->setAmount($this->amount + $amount);
    }

    public function subtractAmount(float $amount): void
    {
        $this->setAmount($this->amount - $amount);
    }

    public function kanBetalen(float $kost): bool
    {
        return $this->amount >= $kost;
    }
}
