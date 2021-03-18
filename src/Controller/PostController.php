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
use Doctrine\ORM\EntityManagerInterface;

class PostController extends AbstractController
{
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
        
            foreach($social_medias as $e)
            {             
                $checkboxValue = $request->request->get('checkbox'.$e->getId());
                if($checkboxValue != NULL){
                    $post = new Post();
                    $post->setUser($user);
                    $post->setDescription($description);
                    $post->setDate($date);
                    $post->setImage($image);
                    $post->setSocialMediaAccounts($e->getSocialMedia());
                    $manager->persist($post);
                    $manager->flush();
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
