<?php

declare(strict_types=1);

#User:
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;

#Tampon:
use App\Domain\Tampon\TamponRepository;
use App\Infrastructure\Persistence\Tampon\DatabaseTamponRepository;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
        TamponRepository::class => \DI\autowire(DatabaseTamponRepository::class),
    ]);
};
