<?php
class ClickPower
{
    public int $cp = 1; // Click power default

    public function __construct()
    {
        if (isset($_SESSION['cp'])) {
            $this->cp = $_SESSION['cp'];
        } else {
            $_SESSION['cp'] = 1;
            $this->cp = 1;
        }
    }

    public function getAmount(): int
    {
        return (int) $_SESSION['cp'];
    }


    public function cookieClick(): void
    {
        if (!isset($_SESSION['cookies'])) {
            $_SESSION['cookies'] = 0;
        }
        $_SESSION['cookies'] += $this->cp;
    }
}
