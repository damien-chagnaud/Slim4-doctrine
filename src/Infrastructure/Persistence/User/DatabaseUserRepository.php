<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;


class DatabaseUserRepository implements UserRepository
{

    private EntityRepository $repository;
    private EntityManager $entity_manager;

    public function __construct(EntityManager $entity_manager)
    {
        $this->repository = $entity_manager->getRepository(User::class);
        $this->entity_manager = $entity_manager;
    }

     /**
     * {@inheritdoc}
     */
    public function findUserOfId(string $uiid): User
    {
        /** @var User $user */
        $user = $this->repository->find((string) $uiid);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $username): string
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['username' => $username]);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        $newToken = hash('sha256', bin2hex(random_bytes(26)));

        $user->setUidkey($newToken);

        $this->entity_manager->persist($user);
        $this->entity_manager->flush();

        return $user->getUidkey();
    }

    


}