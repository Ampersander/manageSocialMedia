<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController{
    /**
     * @Route("/", name="accueil")
     */
    public function accueil()
    {
        return $this->render('test-api.html.twig');
    }
}

?>