<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookAPI
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Publie un statut avec un lien éventuel sur la page Facebook
    public function postMessageOnPage($pageAccessToken, $pageId, $message, $link = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/feed/';
        try {
            $params = [
                'message' => $message,
                'access_token' => $pageAccessToken
            ];
            if ($link != false) {
                $params['link'] = $link;
            }
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi du post sur Facebook : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Publie une photo avec un message éventuel sur la page Facebook
    public function postPhotoOnPage($pageAccessToken, $pageId, $photoPath, $message = false, $published = true)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/photos/';
        try {
            $params = [
                'url' => $photoPath,
                'access_token' => $pageAccessToken
            ];
            // Message éventuel
            if ($message != false) {
                $params['message'] = $message;
            }
            // Publier la photo ou upload seulement ?
            if ($published == false) {
                $params['published'] = 'false';
            }
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi de la photo sur Facebook : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Publie plusieurs photos en un post avec un message éventuel sur la page Facebook
    public function postPhotosOnPage($pageAccessToken, $pageId, $photoPaths, $message = false)
    {
        $pageAccessToken = $pageAccessToken;
        $unpublishedPhotoIds = [];

        try {
            // Upload des photos sans les publier
            var_dump($pageAccessToken);
            var_dump($pageId);
            var_dump($photoPaths);
            var_dump($message);
            foreach ($photoPaths as $path) {
                $unpublishedPhotoIds[] = $this->postPhotoOnPage($pageAccessToken, $pageId, $path, false, false);
            }
            $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/feed/';
            $params = [
                'access_token' => $pageAccessToken
            ];
            // Ajout d'un éventuel message
            if ($message != false) {
                $params['message'] = $message;
            }
            // Inclue chaque id de photo a publier
            foreach ($unpublishedPhotoIds as $key => $photoId) {
                // Ex : attached_media[0]={"media_fbid":"1002088839996"}
                $params['attached_media' . '[' . $key . ']'] = '{"media_fbid":"'.$photoId.'"}';
            }

            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);
            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi des photos sur Facebook : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Renvoie le Page Access Token
    public function getPageAccessToken($longLivedUserToken, $pageId)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId;
        try {
            $params = [
                'fields' => 'access_token',
                'access_token' => $longLivedUserToken
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec obtention Page Access Token Facebook : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['access_token'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Renvoie le Long-lived User Access Token
    public function getLongLivedUserToken($shortLivedToken, $accountId, $clientSecret)
    {
        $url = 'https://graph.facebook.com/oauth/access_token';
        try {
            $params = [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $accountId,
                'client_secret' => $clientSecret,
                'fb_exchange_token' => $shortLivedToken
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec obtention Long-lived User Access Token Facebook : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['access_token'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
