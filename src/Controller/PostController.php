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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Utility\FacebookAPI;
use App\Utility\InstagramAPI;
use App\Utility\TwitterAPI;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\File;

class PostController extends AbstractController
{

    private $FbAPI;
    private $InstaAPI;
    private $TwitterAPI;
    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->FbAPI = new FacebookAPI($client, $parameterBag);
        $this->InstaAPI = new InstagramAPI($client, $parameterBag);
        $this->TwitterAPI = new TwitterAPI($client, $parameterBag);
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
    public function form(Post $post = null, Request $request, EntityManagerInterface $manager, ParameterBagInterface $parameterBag)
    {
        if (!$post) {
            $post = new Post();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createFormBuilder($post)
            ->add('description', TextareaType::class, [
                'attr'   =>  array(
                    'class'   => 'm-2'
                ),
                'constraints' => new NotBlank(),

            ])
            ->add('image', FileType::class, [
                'attr' => ['class' => 'imageInput'],
                'mapped' => false,
                'required' => false,
                'multiple' => true,
            ])
            ->add('date')
            ->getForm();

        $post->setUser($user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('image')->getData();
            $imageNames = [];
            foreach ($images as $image) {
                // Stockage en local
                $ext = $image->guessExtension();
                $folder = $parameterBag->get('kernel.project_dir') . '/public/images/preview/';
                $imgName = uniqid() . '.' . $ext;
                $imgPath = $folder . $imgName;
                $image->move($folder, $imgPath);
                $imageNames[] = $imgName;
            }

            $manager->persist($post);
            $manager->flush();

            return $this->forward('App\Controller\PostController::watch', [
                'id' => $post->getId(),
                'imageNames' => $imageNames
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
    public function watch(Post $post, Request $request, EntityManagerInterface $manager, $imageNames)
    {

        $repository = $this->getDoctrine()->getRepository(SocialMediaAccount::class);
        $image = $post->getImage();
        $date = $post->getDate();
        $description = $post->getDescription();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $facebook = $request->request->get('facebook');
        $insta = $request->request->get('insta');
        $twitter = $request->request->get('twitter');
        $social_medias = $repository->findByUser($user);

        if ($facebook != null || $insta != null || $twitter != null) {

            foreach ($social_medias as $social_media) {
                $checkboxValue = $request->request->get('checkbox' . $social_media->getId());
                if ($checkboxValue != NULL) {
                    $post->addSocialMediaAccount($social_media);
                    $manager->persist($post);
                    $manager->flush();

                    $case = $social_media->getSocialMedia();

                    switch ($case) {
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
                                    $description
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
                                $message = $description;
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

                            //$content = $connection->get("account/verify_credentials");
                            //$new_status = $connection->post("statuses/update", ['status' => $description]);
                            //$status = $connection->get("statuses/home_timeline", ['count' => 25, "exclde_replies" => true]);
                            break;
                    }
                }
            }
            return $this->redirectToRoute('posts');
        }

        $socialMediaAccountsInstagram = $repository->findByUserAndSocialMedia($user, 'instagram_account');
        $socialMediaAccountsTwitter = $repository->findByUserAndSocialMedia($user, 'twitter_account');
        $socialMediaAccountsFacebook = $repository->findByUserAndSocialMedia($user, 'facebook_account');
        $socialMediaPagesFacebook = $repository->findByUserAndSocialMedia($user, 'fb_page');

        return $this->render('post/watch.html.twig', [
            'post' => $post,
            'images' => $imageNames,
            'socialMediaAccountsInstagram' => $socialMediaAccountsInstagram,
            'socialMediaAccountsTwitter' => $socialMediaAccountsTwitter,
            'socialMediaAccountsFacebook' => $socialMediaAccountsFacebook,
            'socialMediaPagesFacebook' => $socialMediaPagesFacebook,
        ]);
    }
}
