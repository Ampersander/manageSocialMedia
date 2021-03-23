<?php

namespace App\Controller;

use App\Entity\SocialMediaAccount;
use App\Entity\FbAccount;
use App\Entity\TwitterAccount;
use App\Form\FbAccountFormType;
use App\Form\TwitterAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


class ProfilController extends AbstractController
{

 /**
     * @Route("/test", name="test")
     */
    public function test(Request $request): Response
    {
        return $this->render('test.html.twig');
    }

    /**
     * @Route("/profil/facebook", name="manageFacebook")
     */
    public function manageFB(Request $request): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(SocialMediaAccount::class);

        $socialMediaAccount = new SocialMediaAccount();

        $fbAccount = new FbAccount();
        $form1 = $this->createForm(FbAccountFormType::class,$fbAccount);
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {

            $name= $form1->get('name')->getData();
            $social_media = 'facebook_account';
            $socialMediaAccount->setName( $name);
            $socialMediaAccount->setSocialMedia($social_media);
            $socialMediaAccount->setUser($user);

            $accountId= $form1->get('accountId')->getData();
            $shortLivedToken= $form1->get('shortLivedToken')->getData();
            $fbAccount->setAccountId( $accountId);
            $fbAccount->setShortLivedToken($shortLivedToken);

            $socialMediaAccount->setFbAccount($fbAccount);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fbAccount);
            $entityManager->persist($socialMediaAccount);
            $entityManager->flush();
                 
        }

        return $this->render('profil/manageFbAccount.html.twig', [
            'FbAccountFormType'=>$form1->createView(),
            'accounts' => $repository->findByUser($user),     
        ]);
    }


     
    /**
     * @Route("/profil/twitter", name="manageTwitter")
     */
    public function manageTwitter(Request $request): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(SocialMediaAccount::class);

        $socialMediaAccount = new SocialMediaAccount();
        
        $twitterAccount = new TwitterAccount();
        $form2 = $this->createForm(TwitterAccountFormType::class,$twitterAccount);

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            var_dump('yes');
            $name= $form2->get('name')->getData();
            $social_media = 'twitter_account';
            $socialMediaAccount->setName( $name);
            $socialMediaAccount->setSocialMedia($social_media);
            $socialMediaAccount->setUser($user);

            $consumerKey= $form2->get('consumerKey')->getData();
            $consumerSecret= $form2->get('consumerSecret')->getData();
            $accessToken= $form2->get('accessToken')->getData();
            $accessTokenSecret= $form2->get('accessTokenSecret')->getData();
            $twitterAccount->setConsumerKey( $consumerKey);
            $twitterAccount->setConsumerSecret($consumerSecret);
            $twitterAccount->setAccessToken( $accessToken);
            $twitterAccount->setAccessTokenSecret($accessTokenSecret);

            $socialMediaAccount->setTwitterAccount($twitterAccount);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($twitterAccount);
            $entityManager->persist($socialMediaAccount);
            $entityManager->flush();
                 
        }


        return $this->render('profil/manageTwitterAccount.html.twig', [
            'TwitterAccountFormType'=>$form2->createView(),
            'accounts' => $repository->findByUserAndSocialMedia($user,'twitter_account'),     
        ]);
    }

/**
     * @Route("/profil/SocialMediaAccount/{id}", name="profil.SocialMediaAccount.edit", methods="GET|POST")
     */
    public function edit(SocialMediaAccount $SocialMediaAccount, Request $request)
    {

        $returnFB = false;
        if($SocialMediaAccount->getFbAccount() == null)
        $returnFB= true;
        if ($this->isCsrfTokenValid('edit' . $SocialMediaAccount->getId(), $request->get('_token'))) {
            $name = $request->get('accountName'.$SocialMediaAccount->getId());
            $SocialMediaAccount->setName($name);
            $entityManager = $this->getDoctrine()->getManager();

            if($returnFB == false){
                $accountId = $request->get('accountId'.$SocialMediaAccount->getId());
                $shortlivedtoken = $request->get('shortlivedtoken'.$SocialMediaAccount->getId());
                $FbAccount = $SocialMediaAccount->getFbAccount();
                $FbAccount->setAccountId( $accountId);
                $FbAccount->setShortlivedtoken( $shortlivedtoken);
                $entityManager->persist($FbAccount);
            }else{
                $accountConsumerKey = $request->get('accountConsumerKey'.$SocialMediaAccount->getId());
                $accountConsumerSecret = $request->get('accountConsumerSecret'.$SocialMediaAccount->getId());
                $accountAccessToken = $request->get('accountAccessToken'.$SocialMediaAccount->getId());
                $accountAccessTokenSecret = $request->get('accountAccessTokenSecret'.$SocialMediaAccount->getId());
                $TwitterAccount = $SocialMediaAccount->getTwitterAccount();
                $TwitterAccount->setConsumerKey( $accountConsumerKey);
                $TwitterAccount->setConsumerSecret( $accountConsumerSecret);
                $TwitterAccount->setAccessToken( $accountAccessToken);
                $TwitterAccount->setAccessTokenSecret( $accountAccessTokenSecret);
                $entityManager->persist($TwitterAccount);
            }


            $entityManager->persist($SocialMediaAccount);
            $entityManager->flush();
            $this->addFlash('success', 'La ligne a bien été modifié!');
        }
        
        if($returnFB == false){
            return $this->redirectToRoute('manageFacebook');
        }else{
            return $this->redirectToRoute('manageTwitter');
        }
    }
 
    /**
     *@Route("/profil/SocialMediaAccount/{id}", name= "profil.SocialMediaAccount.delete", methods="DELETE")
     */
    public function delete(SocialMediaAccount $SocialMediaAccount, Request $request)
    {
        $returnFB = false;
        if($SocialMediaAccount->getFbAccount() == null)
            $returnFB= true;
        if ($this->isCsrfTokenValid('delete' . $SocialMediaAccount->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($SocialMediaAccount);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La ligne a bien été supprimé!');
        }
 
        if($returnFB == false){
            return $this->redirectToRoute('manageFacebook');
        }else{
            return $this->redirectToRoute('manageTwitter');
        }
    }

}
