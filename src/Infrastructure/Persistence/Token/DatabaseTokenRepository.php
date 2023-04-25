<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Token;

use App\Domain\Token\Token;
use App\Domain\Token\TokenNotFoundException;
use App\Domain\Token\TokenRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;


class DatabaseTokenRepository implements TokenRepository
{

    private EntityRepository $repository;
    private EntityManager $entity_manager;

    public function __construct(EntityManager $entity_manager)
    {
        $this->repository = $entity_manager->getRepository(Token::class);
        $this->entity_manager = $entity_manager;
    }

    /**
     * {@inheritdoc}
     */
    public function findTokenOfUiid(string $uiid): Token
    {
        /** @var Token $Token */
        $token = $this->repository->find((string) $uiid);

        if ($token  === null) {
            throw new TokenNotFoundException();
        }
        return $token ;
    }

    public function add(Token $token): void
    {
        $this->entity_manager->persist($token);
        $this->entity_manager->flush();
    }

     /**
     * {@inheritdoc}
     */
    public function generateToken(User $user, string $id): string
    {
        $uiid = $user->getId();

        //if user exist in token table:
        $token = $this->entity_manager->findOneBy(['uiid' => $uiid]);

        $hash = hash('sha256', bin2hex(random_bytes(26)));

        $now = time();
        $expiration = $now+86400;
        
        if ($token  === null) {//create new token:
            $token = new Token($id, $uiid, $hash, $now, $expiration);
        }else{//update token:
            $token->setToken($hash);
            $token->setCreated($now);
            $token->setExpiration($expiration);
        }

        $this->entity_manager->persist($token);
        $this->entity_manager->flush();

        return $hash;
    }

     /**
     * {@inheritdoc}
     */
    public function validateToken(string $tokenHash, string $uiid): bool
    {
        $token = $this->entity_manager->findOneBy(['token' => $tokenHash]);

        $result = true;

        //token not found:
        if ($token  === null) {
            $result = false;
        }

        //token not same Uiid
        $TokenUiid = $token->getUiid();
        if($TokenUiid != $uiid){
            $result = false;
        }

        //token expired
        $exp = $token->getExpiration();
        if($exp <= time()){
            $result = false;
        }

        return $result;
    }


}