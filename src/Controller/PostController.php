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
        }
        return $this->redirectToRoute('posts');
    }

        return $this->render('post/watch.html.twig', [
            'post' => $post,
        ]);
    }
}
