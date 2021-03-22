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
            // Mettre ici le token d'entrée (a recup sur API graph tools par ex)
            $shortLivedToken = 'EAABZCHn2BZAjMBAMWgcaRhifxTbrCqw3JQBBLnQW2GIsF0TSx2ZAEFY9iLEb8tHFRJxd75nMUKPGdmolBSiSMqAol4EE4WIb8BMoK3RkE9ZAUevtxwYSYWC8n6NsMDcVoOQavE5V0AZC2yXkABSh37ZBZBrVVcyZCachLePjdddNHtN5L34TYRxM6iLxWw1J9uNx6VZBC5jhwZAx62rVIG3M3eg11oit8Mkoysg4wN58ezkncx1SHkZCj6zQ9kSR5IggyAZD';

            $postId = $this->FbAPI->postPhotosOnPage(
                $shortLivedToken,
                $accountId,
                $clientSecret,
                $pageId,
                [
                    'https://vignette.wikia.nocookie.net/battlefordreamislandfanfiction/images/7/70/Doge_Body.jpg/revision/latest?cb=20170606234719',
                    'https://cdn.vox-cdn.com/thumbor/rQbUYlFaKRlqeUxIwnAddnGYBKM=/0x0:1280x720/1200x800/filters:focal(536x206:740x410)/cdn.vox-cdn.com/uploads/chorus_image/image/66770128/nintendo_direct_mario.0.0.0.0.jpg'
                ],
                'JE SUIS PAS FOUTU DE LIRE CORRECTEMENT LA DOC !'
            );

            // $postId1 = $this->FbAPI->postPhotoOnPage(
            //     $shortLivedToken,
            //     $accountId,
            //     $clientSecret,
            //     $pageId,
            //     'https://vignette.wikia.nocookie.net/battlefordreamislandfanfiction/images/7/70/Doge_Body.jpg/revision/latest?cb=20170606234719',
            //     false,
            //     false
            // );

            // $postId2 = $this->FbAPI->postPhotoOnPage(
            //     $shortLivedToken,
            //     $accountId,
            //     $clientSecret,
            //     $pageId,
            //     'https://cdn.vox-cdn.com/thumbor/rQbUYlFaKRlqeUxIwnAddnGYBKM=/0x0:1280x720/1200x800/filters:focal(536x206:740x410)/cdn.vox-cdn.com/uploads/chorus_image/image/66770128/nintendo_direct_mario.0.0.0.0.jpg',
            //     false,
            //     false
            // );

            // $token = $this->FbAPI->getPageAccessToken(
            //     $pageId,
            //     $shortLivedToken,
            //     $accountId,
            //     $clientSecret
            // );

            // return $this->render('test-api.html.twig',['postId1'=>$postId1, 'postId2'=>$postId2, 'token'=>$token]);
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

    /**
     * @Route("/logfb", name="logb")
     */
    public function logfb()
    {
        return $this->render('test-api.html.twig',['postId'=>'ok']);
    }

}

?>