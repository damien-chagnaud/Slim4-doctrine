<?php

declare(strict_types=1);

namespace App\Domain\Token;

interface TokenRepository
{
    /**
     * @throws TokenNotFoundException
     */
    public function findTokenOfUiid(string $uiid): Token;

    public function generateToken(User $user, string $id): string;

    public function add(Token $token): void;


}