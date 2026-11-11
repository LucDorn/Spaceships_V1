<?php

class SaveFile
{

    // make it an array 
    public int $clicker = 0;
    public int $grandma = 0;
    public int $farm = 0;

    public function __construct()
    {
        // Initialize or load Clickers
        if (isset($_SESSION['clicker'])) {
            $this->clicker = $_SESSION['clicker'];
        } else {
            $_SESSION['clicker'] = 0;
        }

        // Initialize or load Grandmas
        if (isset($_SESSION['grandma'])) {
            $this->grandma = $_SESSION['grandma'];
        } else {
            $_SESSION['grandma'] = 0;
        }

        // Initialize or load Farms
        if (isset($_SESSION['farm'])) {
            $this->farm = $_SESSION['farm'];
        } else {
            $_SESSION['farm'] = 0;
        }
    }

    // Now correctly returns your actual cookie balance
    public function getAmount(): int
    {
        return $_SESSION['cookies'] ?? 0;
    }

    // buy will get the name of the thing you want to buy and look for the price of it 
    // clicker = 10 
    // grandma = 100
    // farm = 1000
    public function buy($name)
    {

        // Fixed cost & reward per item
        $items = [
            'clicker' => ['cost' => 10,   'amount' => 0.1],
            'grandma' => ['cost' => 100,  'amount' => 1],
            'farm'    => ['cost' => 1000, 'amount' => 10],
        ];

        // Guard: unknown item
        if (!isset($items[$name])) {
            return false;
        }

        $cost   = $items[$name]['cost'];
        $amount = $items[$name]['amount'];

        // Guard: not enough cookies
        if ($_SESSION['cookies'] < $cost) {
            return false;
        }

        // Buy it
        $_SESSION['cookies'] -= $cost;
        $_SESSION['cp'] += $amount;

        return true;
    }
}
