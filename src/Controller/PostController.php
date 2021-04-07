<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\SocialMediaAccount;
use App\Entity\Artiste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class PostController extends AbstractController
{

    private $FbAPI;
    private $InstaAPI;
    private $TwitterAPI;
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
        $day = new \DateTime();


        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $posts,
            'day' => $day,
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
            ->add('description', TextareaType::class, [
                'attr'   =>  array(
                'class'   => 'm-2'),
                'constraints' => new NotBlank(),
                    
            ])
            ->add('image', TextType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'attr' => ['class' => 'form-control', 'style' => 'line-height: 20px;'],
            ])
            ->getForm();
            $post->setUser($user);

        $form->handleRequest($request);
        $planif = $request->request->get('planif');

        if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($post);
                $manager->flush();
            if($planif != "planif"){
                return $this->redirectToRoute('post_watch', [
                    'id' => $post->getId(),
                ]);
            }else{
                return $this->redirectToRoute('posts');
            }

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
        $id = $post->getId();
        $description= $post->getDescription();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $day = new \DateTime();

        $facebook = $request->request->get('facebook');
        $insta = $request->request->get('insta');
        $twitter = $request->request->get('twitter');
        $social_medias = $repository->findByUser($user);

        $nombre_incre = mb_substr_count($description, "@");
        $biblie = [];
        $mot = explode(" ", $description);
        $j = 0;
        for($i=0; $i<count($mot); $i++){
            //$mot = substr($description, strpos($description, "@"), strpos($description, " ")-strlen($description));

            if(strpos($mot[$i], "@") === false  ){
                //echo 'yes';
                $descriptionF = $description;
                $descriptionI = $description;
                $descriptionT = $description;

            }else{
                //echo $mot[$i];
                $other = $this->getDoctrine()->getRepository(Artiste::class);
                $artiste = $other->findByName(substr($mot[$i], 1));
                //var_dump(count($artiste));
                $nF = $artiste[0]->nameFacebook;
                $nT = $artiste[0]->nameTwitter;
                $nI = $artiste[0]->nameInsta;
                //var_dump($artiste);
                $descriptionF = str_replace($mot[$i], "@".$nF, $description);
                $descriptionT = str_replace($mot[$i], "@".$nT, $description);
                $descriptionI = str_replace($mot[$i], "@".$nI, $description);
            }
        }
        
        
        if($facebook != null || $insta != null || $twitter != null){
        
            foreach($social_medias as $social_media)
            {             
                $checkboxValue = $request->request->get('checkbox'.$social_media->getId());
                if($checkboxValue != NULL){
                    $post->addSocialMediaAccount($social_media);
                    $manager->persist($post);
                    $manager->flush();
                    
                    $case = $social_media->getSocialMedia();
                    
                    switch ( $case) {
                        case 'facebook_account':
                            try {
                                $FbAccount = $social_media->getFbAccount();
                                $accountId = $FbAccount->getAccountId(); 
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                            break;
                        case 'fb_page':
                            try {
                                $FbPage = $social_media->getFbPage();
                                $pageId = $FbPage->getPageID();
                                $pageAccessToken = $FbPage->getPageAccessToken();
                                 
                                $postId = $this->FbAPI->postPhotoOnPage(
                                    $pageAccessToken,
                                    $pageId,
                                    $image,
                                    $descriptionF
                                );
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                            break;
                        case 'instagram_account':
                            try {
                                $InstaAccount = $social_media->getInstaAccount();
                                $accountId = $InstaAccount->getIdAccount();
                                $FbAccount = $InstaAccount->getFbPage()->getFbAccount();
                                $accessToken = $FbAccount->getLonglivedtoken();
                                $photoUrl = $image;
                                $message = $descriptionI;          
                                $postId = $this->InstaAPI->publishPhotoOnPage($accountId, $photoUrl, $accessToken, $message);
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                            break;
                        case 'twitter_account':

                            $TwitterAccount = $social_media->getTwitterAccount();

                            $consumer_key = $TwitterAccount->getConsumerKey();
                            $consumer_secret = $TwitterAccount->getConsumerSecret();
                            $access_token = $TwitterAccount->getAccessToken();
                            $access_token_secret = $TwitterAccount->getAccessTokenSecret();
                            

                            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
                            $photoPaths = [
                                'C:\Users\romai\OneDrive\Documents\LP\SocialMedia\manageSocialMedia\assets\images\canard.jpg'
                            ];
                    
                            $response = $this->TwitterAPI->postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, 'Test API N°2', $photoPaths);
                            
                            $content = $connection->get("account/verify_credentials");
                            $new_status = $connection->post("statuses/update", ['status' => $descriptionT]);
                            $status = $connection->get("statuses/home_timeline", ['count' => 25, "exclde_replies" => true]);
                            break;
                    }
                }
            }
        $this->getDoctrine()->getManager()->remove($post);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('posts');
    }
    
    $socialMediaAccountsInstagram = $repository->findByUserAndSocialMedia($user,'instagram_account');
    $socialMediaAccountsTwitter = $repository->findByUserAndSocialMedia($user,'twitter_account');
    $socialMediaAccountsFacebook = $repository->findByUserAndSocialMedia($user,'facebook_account');
    $socialMediaPagesFacebook = $repository->findByUserAndSocialMedia($user,'fb_page');


        return $this->render('post/watch.html.twig', [
            'post' => $post,
            'day' => $day,
            'socialMediaAccountsInstagram' => $socialMediaAccountsInstagram,
            'socialMediaAccountsTwitter' => $socialMediaAccountsTwitter,
            'socialMediaAccountsFacebook' => $socialMediaAccountsFacebook,
            'socialMediaPagesFacebook' => $socialMediaPagesFacebook,
        ]);
    }

      /**
 * Retrieves a collection of Post resource
 * @Get(
 *     path = "/api/post",
 * )
 * @View
 */
public function getPost()
{
    
    $post = $this->getDoctrine()->getRepository(Post::class)->findAll();
    // In case our GET was a success we need to return a 200 HTTP OK response with the collection of article object
    return $post;
}
}
