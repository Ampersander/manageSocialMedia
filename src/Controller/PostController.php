<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\SocialMediaAccount;
use App\Entity\Artiste;
use App\Entity\TemplatePost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Utility\FacebookAPI;
use App\Utility\InstagramAPI;
use App\Utility\TwitterAPI;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Validator\Constraints\Timezone;

class PostController extends AbstractController
{

    private $FbAPI;
    private $InstaAPI;
    private $TwitterAPI;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->FbAPI = new FacebookAPI($client, $parameterBag);
        $this->InstaAPI = new InstagramAPI($client, $parameterBag);
        $this->TwitterAPI = new TwitterAPI($parameterBag);
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
            //'day' => date_format(new \DateTime(), 'Y-m-d'),
            'day' => new \DateTime()
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

        $repository = $this->getDoctrine()->getRepository(TemplatePost::class);
        $templatePosts = $repository->findByUser($user);
        $arrayTemplate['Pas de template'] = 0;
        foreach ($templatePosts as $templatePost) {
            $arrayTemplate[$templatePost->getTitle()] = $templatePost->getId();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createFormBuilder($post)
            ->add('description', TextareaType::class, [
                'attr' => array('class' => 'm-2'),
                'constraints' => new NotBlank(),
            ])
            ->add('image', FileType::class, [
                'attr' => ['class' => 'imageInput'],
                'mapped' => false,
                'required' => false,
                'multiple' => true,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'attr' => ['class' => 'form-control', 'style' => 'line-height: 20px;'],
            ])
            ->add('templatePost', ChoiceType::class, [
                'mapped' => false,
                'choices'  => $arrayTemplate,
            ])
            ->getForm();

        $selectTemplatePost = $repository->findById($request->request->get('templatePost'));
        if ($selectTemplatePost != null)
            $post->setTemplatePost($selectTemplatePost);

        $post->setUser($user);

        $planif = $request->request->get('planif');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('image')->getData();
            $imageNames = [];
            if (!is_NULL($images)) {
                // Stockage en local de toutes les images
                foreach ($images as $image) {
                    $ext = $image->guessExtension();
                    $folder = $parameterBag->get('kernel.project_dir') . '/public/post_images/';
                    $imgName = uniqid() . '.' . $ext;
                    $image->move($folder, $imgName);
                    $imageNames[] = $imgName;
                }
            }
            $post->setImage($imageNames);
            $manager->persist($post);
            $manager->flush();
            if ($planif != "planif") {

                return $this->redirectToRoute('post_watch', [
                    'id' => $post->getId()
                ]);
            } else {
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

    public function watch(Post $post, Request $request, EntityManagerInterface $manager, $id)
    {
        if ($post == null) {
            $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        }
        $images = $post->getImage();
        if (empty($images)) $images = false;
        $date = $post->getDate();
        $description = $post->getDescription();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $day = new \DateTime();

        // Message après l'envoi du post
        $sendingMsg = [
            'facebook' => [],
            'instagram' => [],
            'twitter' => []
        ];

        $repository = $this->getDoctrine()->getRepository(SocialMediaAccount::class);
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
                    $mot = explode(" ", $description);
                    $at = 0;
                    $nbat = 1;
                    for ($i = 0; $i < count($mot); $i++) {
                        //$mot = substr($description, strpos($description, "@"), strpos($description, " ")-strlen($description));
                        if (strpos($mot[$i], "@") === false) {
                           $at++;
                        } else {
                            //echo $mot[$i];
                            $other = $this->getDoctrine()->getRepository(Artiste::class);
                            $artiste = $other->findByName(substr($mot[$i], 1));

                            //var_dump(count($artiste));
                            $nF = $artiste[0]->getNameFacebook();
                            $nT = $artiste[0]->getNameTwitter();
                            $nI = $artiste[0]->getNameInsta();
                            
                            //var_dump($artiste);
                            $descriptionF = str_replace($mot[$i], "@" . $nF, $description);
                            $descriptionT = str_replace($mot[$i], "@" . $nT, $description);
                            $descriptionI = str_replace($mot[$i], "@" . $nI, $description);

                            $nbat++;
                        }


                    }
                    if($at === count($mot)-$nbat){
                        $descriptionF = $description;
                        $descriptionI = $description;
                        $descriptionT = $description;
                    }

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
                                if ($images) {
                                    $isValid = true;
                                    // Check et host images
                                    $hostedImages = $this->FbAPI->checkAndHostImages($images);
                                    foreach ($hostedImages as $imageResult) {
                                        // var_dump($imageResult);
                                        if ($imageResult['isValid']) {
                                            $imageUrls[] = $imageResult['url'];
                                        } else {
                                            $isValid = false;
                                            $sendingMsg['facebook'] = $imageResult['errors'];
                                        }
                                    }
                                    // Post
                                    if ($isValid) {
                                        $postId = $this->FbAPI->postPhotosOnPage($pageAccessToken, $pageId, $imageUrls, $descriptionF);
                                        $sendingMsg['facebook'][] = 'Le post a été effectué avec succès sur la page ' . $social_media->getName() . ' sur Facebook !';
                                    }
                                } else {
                                    $postId = $this->FbAPI->postMessageOnPage($pageAccessToken, $pageId, $descriptionF);
                                    $sendingMsg['facebook'][] = 'Le post a été effectué avec succès sur la page ' . $social_media->getName() . ' sur Facebook !';
                                }
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
                                // Check et host imagess
                                if ($images) {
                                    $isValid = true;
                                    $hostedImage = $this->InstaAPI->checkAndHostImage($images[0]);
                                    if ($hostedImage['isValid']) {
                                        $imageUrl = $hostedImage['url'];
                                    } else {
                                        $isValid = false;
                                        $sendingMsg['instagram'] = $hostedImage['errors'];
                                    }
                                    // Post
                                    if ($isValid) {
                                        $postId = $this->InstaAPI->publishPhotoOnPage($accountId, $imageUrl, $accessToken, $descriptionI);
                                        $sendingMsg['instagram'][] = 'Le post a été effectué avec succès sur la page ' . $social_media->getName() . ' sur Instagram !';
                                    }
                                }
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
                            // Check images
                            if ($images) {
                                $isValid = true;
                                $checkImages = $this->TwitterAPI->checkImages(array_slice($images, 0, 4));
                                foreach ($checkImages as $imageResult) {
                                    if (!$imageResult['isValid']) {
                                        $isValid = false;
                                        $sendingMsg['twitter'] = $imageResult['errors'];
                                    }
                                }
                                if ($isValid) {
                                    $postId = $this->TwitterAPI->postStatusOnPage(
                                        $consumer_key,
                                        $consumer_secret,
                                        $access_token,
                                        $access_token_secret,
                                        $descriptionT,
                                        $images ? array_slice($images, 0, 4) : false
                                    );
                                    $sendingMsg['twitter'][] = 'Le post a été effectué avec succès sur la page ' . $social_media->getName() . ' sur Twitter !';
                                }
                            }else{
                                $postId = $this->TwitterAPI->postStatusOnPage(
                                    $consumer_key,
                                    $consumer_secret,
                                    $access_token,
                                    $access_token_secret,
                                    $descriptionT,
                                    false
                                );
                                $sendingMsg['twitter'][] = 'Le post a été effectué avec succès sur la page ' . $social_media->getName() . ' sur Twitter !';
                            }
                            // Post
                           
                            break;
                    }
                }
            }

            if ($date < $day) {
                sleep(20);
                 $this->getDoctrine()->getManager()->remove($post);
                 $this->getDoctrine()->getManager()->flush();
            }
        }

        $socialMediaAccountsInstagram = $repository->findByUserAndSocialMedia($user, 'instagram_account');
        $socialMediaAccountsTwitter = $repository->findByUserAndSocialMedia($user, 'twitter_account');
        $socialMediaAccountsFacebook = $repository->findByUserAndSocialMedia($user, 'facebook_account');
        $socialMediaPagesFacebook = $repository->findByUserAndSocialMedia($user, 'fb_page');

        return $this->render('post/watch.html.twig', [
            'post' => $post,
            'day' => $day,
            'socialMediaAccountsInstagram' => $socialMediaAccountsInstagram,
            'socialMediaAccountsTwitter' => $socialMediaAccountsTwitter,
            'socialMediaAccountsFacebook' => $socialMediaAccountsFacebook,
            'socialMediaPagesFacebook' => $socialMediaPagesFacebook,
            'sendingMsg' => $sendingMsg
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
    $user = $this->get('security.token_storage')->getToken()->getUser();
    $repository = $this->getDoctrine()->getRepository(Post::class);
    $posts = $repository->findByUser($user);

    // In case our GET was a success we need to return a 200 HTTP OK response with the collection of article object
    return $posts;
}

 /**
     *@Route("/post/delete/{id}", name= "post.delete", methods="DELETE")
     */
    public function deletePost(Post $Post, Request $request)
    {
        
           
        if ($this->isCsrfTokenValid('delete' . $Post->getId(), $request->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($Post);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le post a bien été supprimé!');
        }
 
       
            return $this->redirectToRoute('posts');
       
    }

}



