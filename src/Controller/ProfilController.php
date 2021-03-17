<?php

namespace App\Controller;

use App\Entity\SocialMediaAccount;
use App\Form\ManageAccountSocialMediaFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


class ProfilController extends AbstractController
{
    
    /**
     * @Route("/profil", name="profil")
     */
    public function index(Request $request): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(SocialMediaAccount::class);
        

        /*$accounts2 = $repository->findAll($userID);
        var_dump($accounts2);*/

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $socialMediaAccount = new SocialMediaAccount();
        $form = $this->createForm(ManageAccountSocialMediaFormType::class,$socialMediaAccount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name= $form->get('name')->getData();
            $apiKey = $form->get('apiKey')->getData();
            $social_media = $form->get('social_media')->getData();

            $socialMediaAccount->setName( $name);
            $socialMediaAccount->setApikey($apiKey);
            $socialMediaAccount->setSocialMedia($social_media);
            $socialMediaAccount->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($socialMediaAccount);
            $entityManager->flush();
                 
        }
        return $this->render('profil/profil.html.twig', [
            'ManageAccountSocialMedia' => $form->createView(),
            'accounts' => $repository->findByUser($user),     
        ]);
    }

/**
     * @Route("/profil/SocialMediaAccount/{id}", name="profil.SocialMediaAccount.edit", methods="GET|POST")
     */
    public function edit(SocialMediaAccount $SocialMediaAccount, Request $request)
    {
        
        if ($this->isCsrfTokenValid('edit' . $SocialMediaAccount->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($SocialMediaAccount);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La ligne a bien été modifié!');
        }
 
        return $this->redirectToRoute('profil');
    }
 
    /**
     *@Route("/profil/SocialMediaAccount/{id}", name= "profil.SocialMediaAccount.delete", methods="DELETE")
     */
    public function delete(SocialMediaAccount $SocialMediaAccount, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $SocialMediaAccount->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($SocialMediaAccount);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le chapitre a bien été supprimé!');
        }
 
        return $this->redirectToRoute('profil');
    }

}
