<?php

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookAPI {

    private $client;
    // private $pageId = '102213561957064'; //Page id test

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Publie un statut avec un lien éventuel sur la page Facebook
    public function postMessageOnPage($pageAccessToken, $pageId, $message, $link=false){
        $url = 'https://graph.facebook.com/'.$pageId.'/feed/';
        try{
            $headers = [
                'message' => $message,
                'access_token' => $pageAccessToken
            ];
            if($link != false){
                $headers['link'] = $link;
            }
            $response = $this->client->request('POST', $url, [
                'headers' => $headers,
            ]);
        
            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Echec de l\'envoi du post sur Facebook');
            } else {
                $content = $response->toArray();
                return $content['id'];
            }      
        }catch (Exception $e){
            throw $e;
        }
    }

    // Publie un statut avec un lien éventuel sur la page Facebook
    public function postPhotoOnPage($pageAccessToken, $pageId, $photoPath){
        $url = 'https://graph.facebook.com/'.$pageId.'/photos/';
        try{
            $headers = [
                'url' => $photoPath,
                'access_token' => $pageAccessToken
            ];
            $response = $this->client->request('POST', $url, [
                'headers' => $headers,
            ]);
        
            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Echec de l\'envoi de la photo sur Facebook');
            } else {
                $content = $response->toArray();
                return $content['id'];
            }      
        }catch (Exception $e){
            throw $e;
        }
    }

    // Renvoie le Page Access Token
    public function getPageAccessToken($longLivedToken, $pageId){
        $url = 'https://graph.facebook.com/'.$pageId;
        try{
            $headers = [
                'fields' => 'access_token',
                'access_token' => $longLivedToken
            ];

            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
            ]);
        
            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Echec obtention Page Access Token Facebook');
            } else {
                $content = $response->toArray();
                return $content['access_token'];
            }      
        }catch (Exception $e){
            throw $e;
        }
    }

    // Renvoie le Long-lived User Access Token
    public function getLongLivedUserToken($shortLivedToken, $client_id, $client_secret){
        $url = 'https://graph.facebook.com/oauth/access_token';
        try{
            $headers = [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'fb_exchange_token' => $shortLivedToken
            ];

            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
            ]);
        
            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Echec obtention Long-lived User Access Token Facebook');
            } else {
                $content = $response->toArray();
                return $content['access_token'];
            }      
        }catch (Exception $e){
            throw $e;
        }
    }

}

