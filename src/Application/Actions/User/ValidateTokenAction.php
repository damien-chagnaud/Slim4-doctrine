<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use App\Domain\Token\TokenRepository;
use Psr\Log\LoggerInterface;
use function uuid_create;

class ValidateTokenAction extends UserAction
{
    protected UserRepository $userRepository;
    protected TokenRepository $tokenRepository;
    protected LoggerInterface $logger;

    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $userName = (string) $this->request->getAttribute('user');

        $this->logger->info("user:".$userName);

        //Retrive User data
        $user = $this->userRepository->findOneBy(['username' => $username]);

       

        

        return $this->respondWithData($responseData);
    }
}
