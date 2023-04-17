<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

class TokenUsersAction extends UserAction
{
    protected UserRepository $userRepository;
    protected LoggerInterface $logger;

    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $userName = (string) $this->request->getAttribute('user');

        $this->logger->info("user:".$userName);

        $token = $this->userRepository->generateToken($userName);

        $this->logger->info("Users list was viewed.");

        $responseData = ['token'=>$token];

        return $this->respondWithData($responseData);
    }
}
