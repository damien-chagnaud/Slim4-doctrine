<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use function uuid_create;
use Psr\Log\LoggerInterface;


class NewUserAction extends UserAction
{
    const LEVEL = 2;
    protected UserRepository $userRepository;
    protected LoggerInterface $logger;
    
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $data = (array) $this->request->getParsedBody();
        $uuid = uuid_create(UUID_TYPE_RANDOM);

        $this->logger->info('User_Id: '.$uuid);

        $this->logger->info(print_r($uuid, true));


    

    }

    private function checkLevel(): bool
    {


    }



}