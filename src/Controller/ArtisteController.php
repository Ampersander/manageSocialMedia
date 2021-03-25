<?php

namespace App\Controller;

use App\Entity\Artiste;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;




class ArtisteController extends AbstractController
{
    
    

  /**
 * Retrieves a collection of Article resource
 * @Get(
 *     path = "/api/artistes",
 * )
 * @View
 */
public function getArticles()
{
    
    $articles = $this->getDoctrine()->getRepository(Artiste::class)->findAll();
    // In case our GET was a success we need to return a 200 HTTP OK response with the collection of article object
    return $articles;
}
}