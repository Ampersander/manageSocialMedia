<?php

namespace App\Controller;

use App\Entity\Artiste;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;




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

/**
 * @Route ("/artiste", name="artiste_create")
 */
public function artiste(Request $request, EntityManagerInterface $manager){
  $artiste = new Artiste();

  $form = $this->createFormBuilder($artiste)
      ->add('nom')
      ->add('nameFacebook')
      ->add('nameTwitter')
      ->add('nameInsta')
      ->getForm();

  $form->handleRequest($request);
  if ($form->isSubmitted() && $form->isValid()) {
      $manager->persist($artiste);
      $manager->flush();
      
  }
  $repository = $this->getDoctrine()->getRepository(Artiste::class);
  //var_dump($artiste);
  return $this->render('artiste/artiste.html.twig', [
      'formArtiste' => $form->createView(),
      'accounts' => $repository->findAll(), 
  ]);
  }

      /**
     *@Route("/artiste/del/{id}", name= "artiste.del", methods="DELETE")
     */
    public function deleteArtisteAccount(Artiste $artiste, Request $request)
    {
        
           
        if ($this->isCsrfTokenValid('delete' . $artiste->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($artiste);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La ligne a bien été supprimé!');
        }
 
       
            return $this->redirectToRoute('artiste_create');
       
    }

    /**
     * @Route("/artiste/edit/{id}", name="artiste.edit", methods="GET|POST")
     */
    public function editArtisteAccount(Artiste $artiste, Request $request)
    {
        if ($this->isCsrfTokenValid('edit' . $artiste->getId(), $request->get('_token'))) {
            $name = $request->get('nom'.$artiste->getId());
            var_dump($name);
            $artiste->setNom($name);
            $entityManager = $this->getDoctrine()->getManager(); 
            $nameFacebook = $request->get('nameFacebook'.$artiste->getId());
            $nameTwitter = $request->get('nameTwitter'.$artiste->getId());
            $nameInsta = $request->get('nameInsta'.$artiste->getId());
            $artiste->setNameFacebook($nameFacebook);
            $artiste->setNameTwitter($nameTwitter);
            $artiste->setNameInsta($nameInsta);
            $entityManager->persist($artiste);
            $entityManager->flush();
            $this->addFlash('success', 'La ligne a bien été modifié!');
        }
            return $this->redirectToRoute('artiste_create'); 
    }

}