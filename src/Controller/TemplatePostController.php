<?php

namespace App\Controller;

use App\Entity\TemplatePost;
use App\Form\TemplatePostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TemplatePostController extends AbstractController
{
    /**
     * @Route("/template", name="template")
     */
    public function index(Request $request): Response
    {
        unset($form);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(TemplatePost::class);
        $entityManager = $this->getDoctrine()->getManager();
        
        $templatePost = new TemplatePost();
        $form = $this->createForm(TemplatePostFormType::class,$templatePost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $title= $form->get('title')->getData();
            $description= $form->get('description')->getData();

            $templatePost->setTitle( $title);
            $templatePost->setDescription($description);
            $templatePost->setUser($user);

            $entityManager->persist($templatePost);
            $entityManager->flush();
            
        }



        return $this->render('templatePost/index.html.twig', [
            'templatePostFormType'=>$form->createView(),
            'templateposts' => $repository->findByUser($user),     
        ]);
        
    }



     /**
     * @Route("/template/{id}", name="template.edit", methods="GET|POST")
     */
    public function editTwitterAccount(TemplatePost $templatePost, Request $request)
    {

        if ($this->isCsrfTokenValid('edit' . $templatePost->getId(), $request->get('_token'))) {
            $name = $request->get('templatePostTitle'.$templatePost->getId());
            $description = $request->get('templatePostDescription'.$templatePost->getId());
            $templatePost->setTitle($name);
            $templatePost->setDescription($description);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($templatePost);
            $entityManager->flush();
            $this->addFlash('success', 'La ligne a bien été modifié!');
            }  
            return $this->redirectToRoute('template');
        
    }
 
    /**
     *@Route("/template/{id}", name= "template.delete", methods="DELETE")
     */
    public function deleteFbAccount(TemplatePost $templatePost, Request $request)
    {
        
           
        if ($this->isCsrfTokenValid('delete' . $templatePost->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($templatePost);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La ligne a bien été supprimé!');
        }
 
       
            return $this->redirectToRoute('template');
       
    }
}
