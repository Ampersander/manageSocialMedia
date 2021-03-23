<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\SocialMediaAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Utility\FacebookAPI;
use App\Utility\InstagramAPI;
use App\Utility\TwitterAPI;

class PostController extends AbstractController
{

    private $FbAPI;
    private $InstaAPI;

    public function __construct(HttpClientInterface $client)
    {
        $this->FbAPI = new FacebookAPI($client);
        $this->InstaAPI = new InstagramAPI($client);
        $this->TwitterAPI = new TwitterAPI($client);
    }

    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findByUser($user);

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $posts,
        ]);
    }

    /**
     *  @Route ("/post/new", name="post_create")
     *  @Route ("/post/{id}/edit", name="post_edit")
     */
    public function form(Post $post = null, Request $request, EntityManagerInterface $manager) {
        if (!$post) {
            $post = new Post();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createFormBuilder($post)
            ->add('description', TextareaType::class)
            ->add('image', TextType::class)
            ->add('date')
            ->getForm();
            $post->setUser($user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('post_watch', [
                'id' => $post->getId(),
            ]);
        }

        return $this->render('post/create.html.twig', [
            'formPost' => $form->createView(),
            'editMode' => $post->getId() !== null,
        ]);
    }

    /**
     *  @Route ("/post/{id}", name="post_show")
     */
    public function show(Post $post)
    { 
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     *  @Route ("/post/watch/{id}", name="post_watch")
     */
    public function watch(Post $post, Request $request, EntityManagerInterface $manager)
    {
        
        $repository = $this->getDoctrine()->getRepository(SocialMediaAccount::class);
        $image= $post->getImage();
        $date= $post->getDate();
        $description= $post->getDescription();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $facebook = $request->request->get('facebook');
        $insta = $request->request->get('insta');
        $twitter = $request->request->get('twitter');
        $social_medias = $repository->findByUser($user);

        if($facebook != null || $insta != null || $twitter != null){
        
            foreach($social_medias as $social_media)
            {             
                $checkboxValue = $request->request->get('checkbox'.$social_media->getId());
                if($checkboxValue != NULL){
                    $post->addSocialMediaAccount($social_media);
                    $manager->persist($post);
                    $manager->flush();
                    
                    $case = $social_media->getSocialMedia();
                    if($case == 'facebook_account' || $case== 'facebook_page')
                    {
                        $case= 'facebook';
                    }
                    switch ( $case) {
                        case 'facebook':
                            try {
                                $pageId = '102213561957064';
                                $accountId = '139768931378739';

                                $clientSecret = 'ac660241b09b4640889456be63f3f7da';
                                // Mettre ici le token d'entrée (a recup sur API graph tools par ex)

                                $shortLivedToken = 'EAABZCHn2BZAjMBAOOz2G6xqjKakRvlU64xtvAtaxlQeZBQEirCoUR1h6IDYi9UIpAF4bYehwWu1m94D99o27yW1mYs4X3jLim5ugXQ8dibYPFzbcNhXw93r63E9G0mLquZCAUUIYBBgpORA87hOFOgMxfpEB12W451khazrzN0hiiA4oDewsXppD36K2PkBZARV7P0vTwEdykarIC6gW0bH1hCtUO6gTmxFjNZCyVBfjrIvZB0PidvDbCwsZAXsockkZD';
                                $getLongLivedUserToken = $this->FbAPI->getLongLivedUserToken($shortLivedToken, $accountId, $clientSecret);
                                $pageAccessToken = $this->FbAPI->getPageAccessToken($getLongLivedUserToken, $pageId);
                                $postId = $this->FbAPI->postPhotoOnPage(
                                    $pageAccessToken,
                                    $pageId,
                                    $image,
                                    $description
                                );
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                            break;
                        case 'instagram':
                            try {
                                $accountId = '17841446705960906';
                                $photoUrl = $image;
                                $accessToken = 'EAABZCHn2BZAjMBAOOz2G6xqjKakRvlU64xtvAtaxlQeZBQEirCoUR1h6IDYi9UIpAF4bYehwWu1m94D99o27yW1mYs4X3jLim5ugXQ8dibYPFzbcNhXw93r63E9G0mLquZCAUUIYBBgpORA87hOFOgMxfpEB12W451khazrzN0hiiA4oDewsXppD36K2PkBZARV7P0vTwEdykarIC6gW0bH1hCtUO6gTmxFjNZCyVBfjrIvZB0PidvDbCwsZAXsockkZD';
                                $message = $description;          
                                $postId = $this->InstaAPI->publishPhotoOnPage($accountId, $photoUrl, $accessToken, $message);
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                            break;
                        case 'twitter':
                            $consumer_key = '8zz3WouFDnNW0vJ3r5BpPZfxX';
                            $consumer_secret = 'yY6PC7CUEJYP2gg1X9uusxEdCfUMmW5UgIIowAWCunOXZDFM1F';
                            $access_token = '1371453453432655872-PGVA3ttM6nDTcRmfS5TqSEfRxuU48O';
                            $access_token_secret = 'RdhuU5csqLUhXzFrGrMuGo5Jl4cDG1AQQuWiFGDkhEOcS';
                

                            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
                            $content = $connection->get("account/verify_credentials");

                            $new_status = $connection->post("statuses/update", ['status' => $description]);
                            $status = $connection->get("statuses/home_timeline", ['count' => 25, "exclde_replies" => true]);
                            break;
                    }
                }
            }
        return $this->redirectToRoute('posts');
    }
    
    $socialMediaAccountsInstagram = $repository->findByUserAndSocialMedia($user,'instagram');
    $socialMediaAccountsTwitter = $repository->findByUserAndSocialMedia($user,'twitter');
    $socialMediaAccountsFacebook = $repository->findByUserAndSocialMedia($user,'facebook_account');
    $socialMediaPagesFacebook = $repository->findByUserAndSocialMedia($user,'facebook_page');
   
    

        return $this->render('post/watch.html.twig', [
            'post' => $post,
            'socialMediaAccountsInstagram' => $socialMediaAccountsInstagram,
            'socialMediaAccountsTwitter' => $socialMediaAccountsTwitter,
            'socialMediaAccountsFacebook' => $socialMediaAccountsFacebook,
            'socialMediaPagesFacebook' => $socialMediaPagesFacebook,
        ]);
    }
}
