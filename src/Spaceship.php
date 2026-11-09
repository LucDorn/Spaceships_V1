<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


class Spaceship
{
    // Properties
    public int $click;
    public int $ClickPower;


    public function __construct(
        $click = 100,
        $ClickPower = 1,
    ) {
        $this->ClickPower =  $ClickPower;
        $this->click = $click;
    }

    // public function click()
    // {
    //     // Start the session to remember the click count between clicks

    //     // If this is the first click, initialize the click at a base value (e.g., 0)

    //     if (!isset($this->ClickPower)) {
    //         $this->ClickPower = 1;
    //     }
    //     include_once 'Spaceship.php';
    //     // Add cookies
    //     $this->click && $_SESSION['ship1_click'] += $this->ClickPower;

    //     // Echo your exact required output line back to JavaScript
    //     echo $this->click . "<br>";
    // }


    public function click(): void
    {
        if (!isset($_SESSION['cookies'])) {
            $_SESSION['cookies'] = 0;
        }
        $_SESSION['cookies'] += $this->ClickPower;
    }

    public function getAmount(): int
    {
        return $_SESSION['cookies'];
    }

    public function hit($damage)
    {
        // Mogelijkheid 1:
        //        $this->hitPoints -= $damage;
        //        if($this->hitPoints <= 0)
        //        {
        //            $this->isAlive = false;
        //        }

        // Mogelijkheid 2 (gekozen om duidelijkheid):
        if ($this->hitPoints - $damage > 0) {
            $this->hitPoints -= $damage;
        } else {
            $this->isAlive = false;
        }
    }

    public function move()
    {
        $fuelUsage = 2;
        if ($this->fuel - $fuelUsage > 0) {
            $this->fuel -= $fuelUsage;
        } else {
            $this->fuel = 0;
        }
    }
}
