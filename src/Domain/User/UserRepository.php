<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{

     /**
     * @param ivoid
     * @return string
     * @throws UserNotFoundException
     */
    public function generateToken(string $username): string;
}
