<?php declare(strict_types=1);


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function listAction(): Response
    {
        return $this->render('/pages/products.html.twig', ['serverAddress' => $this->getParameter('backend_server_address')]);
    }
}