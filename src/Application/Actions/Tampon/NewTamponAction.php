<?php declare(strict_types=1);

namespace App\Application\Actions\Tampon;

use Psr\Http\Message\ResponseInterface as Response;


class AddTamponAction extends TamponAction
{ 
    
    /**
    * {@inheritdoc}
    */
   protected function action(): Response
   {

       $data = (array) $this->request->getParsedBody();

       Assert::lazy()
       ->that($data)
       ->keyExists('name')
       ->that($data)
       ->keyExists('ref')
       ->that($data)
       ->keyExists('instock')
       ->verifyNow();

       Assert::lazy()
       ->that($data['name'])
       ->notEmpty()
       ->that($data['ref'])
       ->notEmpty()
       ->that($data['instock'])
       ->notEmpty()
       ->verifyNow();

       $tampon = new Tampon(
        $data['name'],
        $data['ref'],
        $data['instock'],
       );

       $this->tampon_repository->add($tampon);
       $this->tampon_repository->flush();

       $this->logger->info('Tampon was created.');

       return $this->respondWithData($tampon, 201);
   }
}
