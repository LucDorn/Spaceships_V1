<?php

declare(strict_types=1);

namespace Shop;

final class Registry
{
    /** @var Koopbaar[] */
    private array $items;

    public function __construct()
    {
        $this->items = [
            new Clicker(),
            new Grandma(),
            new Farm(),
            new Bakkerij(),
        ];
    }

    public function vind(string $naam): ?Koopbaar
    {
        foreach ($this->items as $item) {
            if ($item->getNaam() === $naam) {
                return $item;
            }
        }
        return null;
    }

    /** @return Koopbaar[] */
    public function alle(): array
    {
        return $this->items;
    }

    public function random(): ?Koopbaar
    {
        if ($this->items === []) {
            return null;
        }
        return $this->items[array_rand($this->items)];
    }
}
