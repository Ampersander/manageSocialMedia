<?php

namespace App\Controller;

use App\Entity\Post;
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

//require "vendor/autoload.php";

class PostController extends AbstractController
{

    private $FbAPI;
    private $InstaAPI;

    public function __construct(HttpClientInterface $client)
    {
        $this->FbAPI = new FacebookAPI($client);
        $this->InstaAPI = new InstagramAPI($client);
    }

    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findBySocialMediaAccounts('DEFAULT');

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
            $post->setSocialMediaAccounts('DEFAULT');

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

        $image= $post->getImage();
        $date= $post->getDate();
        $description= $post->getDescription();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $facebook = $request->request->get('facebook');
        $insta = $request->request->get('insta');
        $twitter = $request->request->get('twitter');

        if($facebook != '' || $insta != '' || $twitter != ''){
        if($facebook == "facebook"){
            $postFB = new Post();
            $postFB->setUser($user);
            $postFB->setDescription($description);
            $postFB->setDate($date);
            $postFB->setImage($image);
            $postFB->setSocialMediaAccounts('facebook');
            $manager->persist($postFB);
            $manager->flush();

            try {
                $pageId = '102213561957064';
                $accountId = '139768931378739';
                $clientSecret = 'ac660241b09b4640889456be63f3f7da';
                // Mettre ici le token d'entrée (a recup sur API graph tools par ex)
                $shortLivedToken = 'EAABZCHn2BZAjMBAHBwr4byZBXGCRjvyTEqDPT1rZBPKJP9mQdeGyq89xYjZCBeSlZClhZBZBdpT32rsJbsVRIxO9Qtj9QJiYBWwBDR912EwSdwXaFIcYPVu2UFdSKf4hGGpEnFLDBlvEh42gqsNUybuqTpbh4q9cqFNtiDTxdFOxpCAbc4uk3hOA23kEojbLtzacLAjHoqgJTWZAZABzAddM38wt08TRU1e2UtIepf174En7qPuVMn7YxyrItZBtl6kTx8ZD';
    
                $postId = $this->FbAPI->postPhotosOnPage(
                    $shortLivedToken,
                    $accountId,
                    $clientSecret,
                    $pageId,
                    [
                        $image
                    ],
                    $description
                );
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        if($insta == "insta"){
            $postInst = new Post();
            $postInst->setUser($user);
            $postInst->setDescription($description);
            $postInst->setDate($date);
            $postInst->setImage($image);
            $postInst->setSocialMediaAccounts('instagram');
            $manager->persist($postInst);
            $manager->flush();

            try {
                $accountId = '17841446705960906';
                $photoUrl = $image;
                $accessToken = 'EAABZCHn2BZAjMBAHBwr4byZBXGCRjvyTEqDPT1rZBPKJP9mQdeGyq89xYjZCBeSlZClhZBZBdpT32rsJbsVRIxO9Qtj9QJiYBWwBDR912EwSdwXaFIcYPVu2UFdSKf4hGGpEnFLDBlvEh42gqsNUybuqTpbh4q9cqFNtiDTxdFOxpCAbc4uk3hOA23kEojbLtzacLAjHoqgJTWZAZABzAddM38wt08TRU1e2UtIepf174En7qPuVMn7YxyrItZBtl6kTx8ZD';
                $message = $description;          
                $postId = $this->InstaAPI->publishPhotoOnPage($accountId, $photoUrl, $accessToken, $message);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        if($twitter == "twitter"){
            $postTwitter = new Post();
            $postTwitter->setUser($user);
            $postTwitter->setDescription($description);
            $postTwitter->setDate($date);
            $postTwitter->setImage($image);
            $postTwitter->setSocialMediaAccounts('twitter');
            $manager->persist($postTwitter);
            $manager->flush();

            $consumer_key = '8zz3WouFDnNW0vJ3r5BpPZfxX';
            $consumer_secret = 'yY6PC7CUEJYP2gg1X9uusxEdCfUMmW5UgIIowAWCunOXZDFM1F';
            $access_token = '1371453453432655872-PGVA3ttM6nDTcRmfS5TqSEfRxuU48O';
            $access_token_secret = 'RdhuU5csqLUhXzFrGrMuGo5Jl4cDG1AQQuWiFGDkhEOcS';
 

            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
            $content = $connection->get("account/verify_credentials");

            $new_status = $connection->post("statuses/update", ['status' => $description]);
            $status = $connection->get("statuses/home_timeline", ['count' => 25, "exclde_replies" => true]);
        }
        return $this->redirectToRoute('posts');
    }

        return $this->render('post/watch.html.twig', [
            'post' => $post,
        ]);
    }


}
