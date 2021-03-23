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

class TestController extends AbstractController
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
     *  @Route ("/test", name="test")
     */
    public function test()
    {
        $consumer_key = '8zz3WouFDnNW0vJ3r5BpPZfxX';
        $consumer_secret = 'yY6PC7CUEJYP2gg1X9uusxEdCfUMmW5UgIIowAWCunOXZDFM1F';
        $access_token = '1371453453432655872-PGVA3ttM6nDTcRmfS5TqSEfRxuU48O';
        $access_token_secret = 'RdhuU5csqLUhXzFrGrMuGo5Jl4cDG1AQQuWiFGDkhEOcS';

        $response = $this->TwitterAPI->postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, 'Test API N°1', false);

        return $this->render('test.html.twig', [
            'response' => $response
        ]);
    }

    /**
     *  @Route ("/test2", name="test")
     */
    public function test2()
    {
        $consumer_key = '8zz3WouFDnNW0vJ3r5BpPZfxX';
        $consumer_secret = 'yY6PC7CUEJYP2gg1X9uusxEdCfUMmW5UgIIowAWCunOXZDFM1F';
        $access_token = '1371453453432655872-PGVA3ttM6nDTcRmfS5TqSEfRxuU48O';
        $access_token_secret = 'RdhuU5csqLUhXzFrGrMuGo5Jl4cDG1AQQuWiFGDkhEOcS';
        $photoPaths = [
            '/home/yuyari/Code/Projets/manageSocialMedia/public/images/test.jpg',
            '/home/yuyari/Code/Projets/manageSocialMedia/public/images/test.jpg'
        ];

        $response = $this->TwitterAPI->postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, 'Test API N°2', $photoPaths);

        return $this->render('test.html.twig', [
            'response' => $response
        ]);
    }
}
