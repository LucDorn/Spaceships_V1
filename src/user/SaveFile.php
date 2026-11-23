<?php

declare(strict_types=1);

namespace user;

final class SaveFile
{
    private int $amount;

    public function __construct()
    {
        $this->amount = (int) ($_SESSION['cookies'] ?? 0);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = max(0, $amount);
        $_SESSION['cookies'] = $this->amount;
    }

    public function addAmount(int $amount): void
    {
        $this->setAmount($this->amount + $amount);
    }

    public function subtractAmount(int $amount): void
    {
        $this->setAmount($this->amount - $amount);
    }

    public function kanBetalen(int $kost): bool
    {
        return $this->amount >= $kost;
    }
}
