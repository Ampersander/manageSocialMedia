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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TestController extends AbstractController
{

    private $FbAPI;
    private $InstaAPI;
    private $TwitterAPI;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $bag)
    {
        $this->FbAPI = new FacebookAPI($client, $bag);
        $this->InstaAPI = new InstagramAPI($client, $bag);
        $this->TwitterAPI = new TwitterAPI($client, $bag);
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
     *  @Route ("/test2", name="test2")
     */
    public function test2()
    {
        $consumer_key = '8zz3WouFDnNW0vJ3r5BpPZfxX';
        $consumer_secret = 'yY6PC7CUEJYP2gg1X9uusxEdCfUMmW5UgIIowAWCunOXZDFM1F';
        $access_token = '1371453453432655872-PGVA3ttM6nDTcRmfS5TqSEfRxuU48O';
        $access_token_secret = 'RdhuU5csqLUhXzFrGrMuGo5Jl4cDG1AQQuWiFGDkhEOcS';
        $photoPaths = [
            '/home/yuyari/Code/Projets/manageSocialMedia/public/images/canard.jpg'
        ];

        $response = $this->TwitterAPI->postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, 'Test API N°2', $photoPaths);

        return $this->render('test.html.twig', [
            'response' => $response
        ]);
    }

    /**
     *  @Route ("/test3", name="test3")
     */
    public function test3()
    {
        $accountId = '17841446705960906';
        $photoUrl = 'https://picsum.photos/100/100';
        $access_token = 'EAAoDC3xI7SABALcR2x05FteO72YKjv7wZAoXLGlFnLPF3LRURup8FK3GC126OsRgWf45u8QsTjq5j6FSNxiTj257F6CiSGoYwBVDAbdE8ZCibNWx5gDSUpEBwIVzGqPkQmgZBZBPqiYJKVAhA6ADL0IY9KS1ofze9R3cIhyYhRdT9YsVA7pAC9ucvbNXB8usZB6oTuBGCAcooko8RG8tIQt57TwcFdkQZD';

        $response = $this->InstaAPI->publishPhotoOnPage($accountId, $photoUrl, $access_token, 'Test API N°3');

        return $this->render('test.html.twig', [
            'response' => $response
        ]);
    }
}
