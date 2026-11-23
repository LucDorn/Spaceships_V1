<?
// =====================================================================
// VERSIE 2 — OOP met INTERFACE (de nette manier)
// =====================================================================

/**
 * Het contract: alles wat je kan kopen in het spel.
 * Elke class die dit implementeert, BELOOFT deze drie methodes te hebben.
 */


$allowed = ['clicker', 'grandma', 'farm'];
if (!in_array($naam, $allowed, true)) {
    jsonResponse(['error' => 'unknown item'], 400);
}
$result = $SafeFile->buy($naam);   // ← hier zit een switch, gok ik
// de functies van deze pagina
interface Koopbaar
{
    public function getNaam(): string;
    public function getKost(): float;
    public function pasToe(Spel $spel): void;
}


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

    public function pasToe(Spel $spel): void
    {
        $spel->verdubbelClickKracht();
    }
}


final class Grandma implements Koopbaar
{
    public function getNaam(): string
    {
        return 'grandma';
    }
    public function getKost(): float
    {
        return 100.0;
    }

    public function pasToe(Spel $spel): void
    {
        $spel->verhoogProductie(1.0);
    }
}


final class Farm implements Koopbaar
{
    public function getNaam(): string
    {
        return 'farm';
    }
    public function getKost(): float
    {
        return 1000.0;
    }

    public function pasToe(Spel $spel): void
    {
        $spel->verhoogProductie(10.0);
        $spel->vermenigvuldigProductie(1.10);
    }
}


final class Spel
{
    private float $geld        = 2000.0;
    private float $productie   = 0.0;
    private float $clickKracht = 1.0;

    /** @var Koopbaar[] */
    private array $winkel = [];

    public function __construct()
    {
        $this->winkel = [
            new Clicker(),
            new Grandma(),
            new Farm(),
        ];
    }

    /**
     * Nieuw item toevoegen? Nieuwe class maken en hier aanroepen.
     * Bestaande code hoeft NIET aangepast te worden (Open/Closed).
     */
    public function voegItemToe(Koopbaar $item): void
    {
        $this->winkel[] = $item;
    }

    public function koop(string $naam): void
    {
        foreach ($this->winkel as $item) {
            if ($item->getNaam() !== $naam) {
                continue;
            }

            if ($this->geld < $item->getKost()) {
                echo "  ✗ Niet genoeg geld voor $naam\n";
                return;
            }

            $this->geld -= $item->getKost();
            $item->pasToe($this);   // ← de class zelf weet wat hij moet doen

            echo "  ✓ $naam gekocht | {$this->status()}\n";
            return;
        }

        throw new InvalidArgumentException("Onbekend item: $naam");
    }

    // --- Methodes die items mogen aanroepen ---
    public function verdubbelClickKracht(): void
    {
        $this->clickKracht *= 2;
    }
    public function verhoogProductie(float $x): void
    {
        $this->productie += $x;
    }
    public function vermenigvuldigProductie(float $f): void
    {
        $this->productie *= $f;
    }

    public function status(): string
    {
        return sprintf(
            "geld=%.2f productie=%.2f clickKracht=%.2f",
            $this->geld,
            $this->productie,
            $this->clickKracht
        );
    }
}


// =====================================================================
// EXTRA: een nieuw item toevoegen
// =====================================================================
// In VERSIE 1 zou je de class moeten aanpassen (const + switch-case).
// In VERSIE 2 maak je gewoon een nieuwe class — bestaande code blijft dicht.

final class Bakkerij implements Koopbaar
{
    public function getNaam(): string
    {
        return 'bakkerij';
    }
    public function getKost(): float
    {
        return 500.0;
    }

    public function pasToe(Spel $spel): void
    {
        $spel->verhoogProductie(50.0);
    }
}


// =====================================================================
// DEMO: beide versies draaien en vergelijken
// =====================================================================


echo "\n>>> VERSIE 2 (interface + classes)\n";
$spel2 = new Spel();
$spel2->koop('clicker');
$spel2->koop('grandma');
$spel2->koop('farm');

echo "\n>>> VERSIE 2 + nieuw item (zonder oude code aan te passen)\n";
$spel2->voegItemToe(new Bakkerij());
$spel2->koop('bakkerij');
