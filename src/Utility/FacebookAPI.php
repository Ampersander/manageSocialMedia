<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookAPI
{

    private $client;
    // private $pageId = '102213561957064'; //Page id test

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Publie un statut avec un lien éventuel sur la page Facebook
    public function postMessageOnPage($shortLivedToken, $accountId, $clientSecret, $pageId, $message, $link = false)
    {
        $url = 'https://graph.facebook.com/' . $pageId . '/feed/';
        try {
            $params = [
                'message' => $message,
                'access_token' => $this->getPageAccessToken($pageId, $shortLivedToken, $accountId, $clientSecret)
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
                throw new \Exception('Echec de l\'envoi du post sur Facebook : '.$message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Publie un statut avec un lien éventuel sur la page Facebook
    public function postPhotoOnPage($shortLivedToken, $accountId, $clientSecret, $pageId, $photoPath)
    {
        $url = 'https://graph.facebook.com/' . $pageId . '/photos/';
        try {
            $params = [
                'url' => $photoPath,
                'access_token' => $this->getPageAccessToken($pageId, $shortLivedToken, $accountId, $clientSecret)
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi de la photo sur Facebook : '.$message);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Renvoie le Page Access Token
    public function getPageAccessToken($pageId, $shortLivedToken, $accountId, $clientSecret)
    {
        $url = 'https://graph.facebook.com/' . $pageId;
        try {
            $params = [
                'fields' => 'access_token',
                'access_token' => $this->getLongLivedUserToken($shortLivedToken, $accountId, $clientSecret)
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec obtention Page Access Token Facebook : '.$message);
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
                throw new \Exception('Echec obtention Long-lived User Access Token Facebook : '.$message);
            } else {
                $content = $response->toArray();
                return $content['access_token'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
