<?php

namespace App\Controller;

use App\Entity\SocialMediaAccount;
use App\Entity\FbAccount;
use App\Entity\TwitterAccount;
use App\Entity\FbPage;
use App\Entity\InstaAccount;
use App\Form\FbAccountFormType;
use App\Form\TwitterAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Utility\FacebookAPI;
use App\Utility\InstagramAPI;
use App\Utility\TwitterAPI;

class ProfilController extends AbstractController
{


    private $FbAPI;

    public function __construct(HttpClientInterface $client)
    {
        $this->FbAPI = new FacebookAPI($client);
    }

    /**
     * @Route("/profil/facebook", name="manageFacebook")
     */
    public function manageFB(Request $request): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(FbAccount::class);
        $entityManager = $this->getDoctrine()->getManager();
        
        

        $fbAccount = new FbAccount();
        $form1 = $this->createForm(FbAccountFormType::class,$fbAccount);
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {

            $accountId= $form1->get('accountId')->getData();
            $shortLivedToken= $form1->get('shortLivedToken')->getData();
            $clientSecret= $form1->get('clientSecret')->getData();
            $getLongLivedUserToken = $this->FbAPI->getLongLivedUserToken($shortLivedToken, $accountId, $clientSecret);
                
            $name= $form1->get('name')->getData();
            $socialMediaAccount = new SocialMediaAccount();
            $socialMediaAccount->setName( $name);
            $socialMediaAccount->setSocialMedia('facebook_account');
            $socialMediaAccount->setUser($user);

            $accountId= $form1->get('accountId')->getData();
            $shortLivedToken= $form1->get('shortLivedToken')->getData();
            $fbAccount->setAccountId( $accountId);
            $fbAccount->setShortLivedToken($shortLivedToken);
            $fbAccount->setLonglivedtoken($getLongLivedUserToken);
            $fbAccount->setSocialMediaAccount($socialMediaAccount);

            $entityManager->persist($fbAccount);
            $entityManager->persist($socialMediaAccount);
            $pagesAccountsData= $this->FbAPI->getPages($shortLivedToken);
            $pagesAccounts=  $pagesAccountsData['data'];
            foreach($pagesAccounts as $page){
                $socialMediaAccount1 = new SocialMediaAccount();

                $FbPage = new FbPage();
                $pageName = $page['name'];
                $pageId=$page['id'];
                $pageAccessToken = $this->FbAPI->getPageAccessToken($getLongLivedUserToken, $pageId);
                $socialMediaAccount1->setName( $pageName);
                $socialMediaAccount1->setSocialMedia('fb_page');
                $socialMediaAccount1->setUser($user);
                $idInsta = $this->FbAPI->getIdInstagram($shortLivedToken, $pageId);
                if($idInsta != null){
                    $InstaAccount = new InstaAccount();
                    $instaName = $this->FbAPI->getName($shortLivedToken, $idInsta);
                    $InstaAccount->setIdAccount($idInsta);
                    $InstaAccount->setFbPage($FbPage);
                    $InstaAccount->setName($instaName);
                    $socialMediaAccount2 = new SocialMediaAccount();
                    $socialMediaAccount2->setName( $instaName);
                    $socialMediaAccount2->setSocialMedia('instagram_account');
                    $socialMediaAccount2->setUser($user);
                    $InstaAccount->setSocialMediaAccount( $socialMediaAccount2);
                    
                    $entityManager->persist($InstaAccount);
                    $entityManager->persist($socialMediaAccount2);
                }
                    
                $FbPage->setPageID($pageId);
                $FbPage->setNamePage($pageName);
                $FbPage->setPageAccessToken($pageAccessToken);
                $FbPage->setFbAccount( $fbAccount);
                $FbPage->setSocialMediaAccount($socialMediaAccount1);
                $entityManager->persist($FbPage);
                $entityManager->persist($socialMediaAccount1);
            }
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
        $repository = $this->getDoctrine()->getRepository(TwitterAccount::class);

        $socialMediaAccount = new SocialMediaAccount();
        
        $twitterAccount = new TwitterAccount();
        $form2 = $this->createForm(TwitterAccountFormType::class,$twitterAccount);

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            $name= $form2->get('name')->getData();
            $socialMediaAccount->setName( $name);
            $socialMediaAccount->setSocialMedia('twitter_account');
            $socialMediaAccount->setUser($user);

            $consumerKey= $form2->get('consumerKey')->getData();
            $consumerSecret= $form2->get('consumerSecret')->getData();
            $accessToken= $form2->get('accessToken')->getData();
            $accessTokenSecret= $form2->get('accessTokenSecret')->getData();
            $twitterAccount->setConsumerKey( $consumerKey);
            $twitterAccount->setConsumerSecret($consumerSecret);
            $twitterAccount->setAccessToken( $accessToken);
            $twitterAccount->setAccessTokenSecret($accessTokenSecret);

            $twitterAccount->setSocialMediaAccount($socialMediaAccount);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($twitterAccount);
            $entityManager->persist($socialMediaAccount);
            $entityManager->flush();
                 
        }


        return $this->render('profil/manageTwitterAccount.html.twig', [
            'TwitterAccountFormType'=>$form2->createView(),
            'accounts' => $repository->findByUser($user),     
        ]);
    }

/**
     * @Route("/profil/FbAccount/{id}", name="profil.FbAccount.edit", methods="GET|POST")
     */
    public function editFbAccount(FbAccount $FbAccount, Request $request)
    {
        if ($this->isCsrfTokenValid('edit' . $FbAccount->getId(), $request->get('_token'))) {
            $name = $request->get('accountName'.$FbAccount->getId());
            $FbAccount->getSocialMediaAccount()->setName($name);
            $entityManager = $this->getDoctrine()->getManager(); 
            $accountId = $request->get('accountId'.$FbAccount->getId());
            $clientsecret = $request->get('clientsecret'.$FbAccount->getId());
            $FbAccount->setAccountId( $accountId);
            $FbAccount->setClientSecret($clientsecret);
            $entityManager->persist($FbAccount);
            $entityManager->flush();
            $this->addFlash('success', 'La ligne a bien été modifié!');
        }
            return $this->redirectToRoute('manageFacebook'); 
    }

    /**
     * @Route("/profil/TwitterAccount/{id}", name="profil.TwitterAccount.edit", methods="GET|POST")
     */
    public function editTwitterAccount(TwitterAccount $TwitterAccount, Request $request)
    {

        if ($this->isCsrfTokenValid('edit' . $TwitterAccount->getId(), $request->get('_token'))) {
            $name = $request->get('accountName'.$TwitterAccount->getId());
            $TwitterAccount->getSocialMediaAccount()->setName($name);
            $entityManager = $this->getDoctrine()->getManager();
            $accountConsumerKey = $request->get('accountConsumerKey'.$TwitterAccount->getId());
            $accountConsumerSecret = $request->get('accountConsumerSecret'.$TwitterAccount->getId());
            $accountAccessToken = $request->get('accountAccessToken'.$TwitterAccount->getId());
            $accountAccessTokenSecret = $request->get('accountAccessTokenSecret'.$TwitterAccount->getId());
            $TwitterAccount->setConsumerKey( $accountConsumerKey);
            $TwitterAccount->getConsumerSecret( $accountConsumerSecret);
            $TwitterAccount->setAccessToken( $accountAccessToken);
            $TwitterAccount->setAccessTokenSecret( $accountAccessTokenSecret);
            $entityManager->persist($TwitterAccount);
            $entityManager->flush();
            $this->addFlash('success', 'La ligne a bien été modifié!');
            }  
            return $this->redirectToRoute('manageTwitter');
        
    }
 
    /**
     *@Route("/profil/FbAccount/{id}", name= "profil.FbAccount.delete", methods="DELETE")
     */
    public function deleteFbAccount(FbAccount $FbAccount, Request $request)
    {
        
           
        if ($this->isCsrfTokenValid('delete' . $FbAccount->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($FbAccount);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La ligne a bien été supprimé!');
        }
 
       
            return $this->redirectToRoute('manageFacebook');
       
    }



/**
     *@Route("/profil/TwitterAccount/{id}", name= "profil.TwitterAccount.delete", methods="DELETE")
     */
    public function deleteTwitterAccount(TwitterAccount $TwitterAccount, Request $request)
    {
        
           
        if ($this->isCsrfTokenValid('delete' . $TwitterAccount->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($TwitterAccount);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La ligne a bien été supprimé!');
        }
 
       
            return $this->redirectToRoute('manageTwitter');
       
    }

}
