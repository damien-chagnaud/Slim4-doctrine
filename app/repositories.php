<?php

declare(strict_types=1);

#User:
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;

#Token:
use App\Domain\Token\TokenRepository;
use App\Infrastructure\Persistence\Token\DatabaseTokenRepository;

#Tampon:
use App\Domain\Tampon\TamponRepository;
use App\Infrastructure\Persistence\Tampon\DatabaseTamponRepository;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(DatabaseUserRepository::class),
        TokenRepository::class => \DI\autowire(DatabaseTokenRepository::class),
        TamponRepository::class => \DI\autowire(DatabaseTamponRepository::class),
    ]);
};
