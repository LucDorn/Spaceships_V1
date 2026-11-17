<?php

namespace user;

class SaveFile
{
    // cost   = cookies to buy
    // amount = cookies-per-second it adds
    private const ITEMS = [
        'clicker' => ['cost' => 10,   'amount' => 0.1],
        'grandma' => ['cost' => 100,  'amount' => 1],
        'farm'    => ['cost' => 1000, 'amount' => 10],
    ];

    // All that the user owns ['clicker' => 0, 'grandma' => 0, 'farm' => 0]
    public array $items = [];

    public function __construct()
    {
        // if empty make it 0
        $defaults = array_map(fn() => 0, self::ITEMS);

        //the session will become the default 
        if (isset($_SESSION['items']) && is_array($_SESSION['items'])) {
            $this->items = array_merge($defaults, $_SESSION['items']);
        } else {
            $this->items = $defaults;
        }
        $_SESSION['items'] = $this->items;

        // Make sure the shared counters exist
        $_SESSION['cookies'] = $_SESSION['cookies'] ?? 0;
        $_SESSION['cp']      = $_SESSION['cp']      ?? 0;
    }

    // Current cookie balance
    public function getAmount(): int
    {
        return $_SESSION['cookies'] ?? 0;
    }

    // Count of one item, e.g. $save->get('grandma')
    public function get(string $name): int
    {
        return $this->items[$name] ?? 0;
    }

    // buys item if you have more than anough
    public function buy(string $name): bool
    {
        // is item real?
        if (!isset(self::ITEMS[$name])) {
            return false;
        }
        // looks up cost and amount
        $cost   = self::ITEMS[$name]['cost'];
        $amount = self::ITEMS[$name]['amount'];

        // not enough cookies?
        if (($_SESSION['cookies'] ?? 0) < $cost) {
            return false;
        }

        // Buy it
        $_SESSION['cookies'] -= $cost;
        $_SESSION['cp']      = ($_SESSION['cp'] ?? 0) + $amount;

        $this->items[$name]++;
        $_SESSION['items'] = $this->items;

        return true;
    }
}
