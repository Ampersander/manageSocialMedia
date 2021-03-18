<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Utility\FacebookAPI;
use App\Utility\InstagramAPI;


class TestApiController extends AbstractController{

    private $FbAPI;
    private $InstaAPI;

    public function __construct(HttpClientInterface $client)
    {
        $this->FbAPI = new FacebookAPI($client);
        $this->InstaAPI = new InstagramAPI($client);
    }

    /**
     * @Route("/fb", name="fb_test")
     */
    public function fb_test()
    {
        try {
            $pageId = '102213561957064';
            $accountId = '139768931378739';
            $clientSecret = 'ac660241b09b4640889456be63f3f7da';
            $shortLivedToken = 'EAABZCHn2BZAjMBAH1ao9eOHgKQcLhy2CFK10NKPqbt8i9QAg2y54hsvZCRquf6zuRTKPAF1iVdGnWpf8XqWZBpHAZAJnHrHzTJRAkvBeTZBgxwghqI4w53YhgO3KoWBzVfq31MCO86FyWoeiYZBOWgKeYK6tqDpYSSFKO1eksOTCj2khrKk0qDOPZCGumGgzVOVZC9SeC9HZCFyBFIMUk5XER5La2AVEFC7B1J6lBgzoVjdxYZBL9bEdvzt5Fp7VTyCuj8ZD';

            $postId = $this->FbAPI->postMessageOnPage(
                $shortLivedToken,
                $accountId,
                $clientSecret,
                $pageId,
                'TEST',
                'https://www.instagram.com/'
            );

            return $this->render('test-api.html.twig',['postId'=>$postId]);
        } catch (\Throwable $th) {
            throw $th;
        }
        // return $this->render('test-api.html.twig',['postId'=>'ok']);
    }

    /**
     * @Route("/insta", name="insta_test")
     */
    public function insta_test()
    {
        try {
            $accountId = '17841446705960906';
            $photoUrl = 'https://vignette.wikia.nocookie.net/battlefordreamislandfanfiction/images/7/70/Doge_Body.jpg/revision/latest?cb=20170606234719';
            $accessToken = 'EAABZCHn2BZAjMBAHQuA7fzoJrAEF537s26YcHOUQfwjtIfqZBt70a8NjZB1m2gY5JuZBO5CoLim63xTjZCoFrkRjFWctRxN9vMnuAWT2FFok5pbTIcRu2gfi5JQW5jTeXbHrkacSVI4PTyk4NTtKebw6SQIkSPOjzwzZAKtzbtdyjZCwttN9tpZArw7o4fK4uV5ini1pmAZAhZCgqcjzoG3i791svOBdK8ZC4fiIwZACddAzxcSQMRsAJoZAqlxvS55s0eHYcZD';
            $message = 'Coucou';          
            $postId = $this->InstaAPI->publishPhotoOnPage($accountId, $photoUrl, $accessToken, $message);

            return $this->render('test-api.html.twig',['postId'=>$postId]);
        } catch (\Throwable $th) {
            throw $th;
        }
        // return $this->render('test-api.html.twig',['postId'=>'ok']);
    }
}

?>