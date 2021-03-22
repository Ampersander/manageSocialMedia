<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class InstagramAPI
{

    private $client;
    // private $pageId = '102213561957064'; //Page id test

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Publie une photo sur Instagram, des tags peuvent être ajoutés
    public function publishPhotoOnPage($accountId, $photoUrl, $access_token, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $accountId . '/media_publish';
        try {
            $params = [
                'creation_id' => $this->sendPhotoOnPage($accountId, $photoUrl, $access_token, $message),
                'access_token' => $access_token
            ];
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de la publication de la photo sur Instagram : '.$message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Envoie une photo sur Instagram sans la publier et renvoie son id de création
    public function sendPhotoOnPage($accountId, $photoUrl, $access_token, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $accountId . '/media';
        try {
            $params = [
                'image_url' => $photoUrl,
                'access_token' => $access_token
            ];
            if ($message != false) $params['caption'] = $message;
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi du post sur Instagram : '.$message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}