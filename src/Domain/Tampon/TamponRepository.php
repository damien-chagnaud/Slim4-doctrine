<?php

declare(strict_types=1);

namespace App\Domain\Tampon;

interface TamponRepository
{
    /**
     * @return Tampon[]
     */
    public function findAll(): array;

    //public function add(Tampon $tampon): void;


}