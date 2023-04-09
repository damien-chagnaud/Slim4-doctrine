<?php 

declare(strict_types=1);

namespace App\Domain\Tampon;

use JsonSerializable;

class Tampon implements JsonSerializable
{
    private ?int $id;

    private string $name;

    private string $ref;

    private int $instock;

    public function __construct(?int $id, string $name, string $ref, int $instock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ref = $ref;
        $this->instock = $instock;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function getInstock(): int
    {
        return $this->instock;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ref' => $this->ref,
            'instock' => $this->instock,
        ];
    }
}