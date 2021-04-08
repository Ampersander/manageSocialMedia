<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImgbbAPI
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Upload une image sur ImgBB et retourne son url
     * @param string $image Image à upload, format base64
     */
    public function uploadImage($image)
    {
        $url = 'https://api.imgbb.com/1/upload';
        $apiKey = 'b7add5b85d58762625f0c640991a9689';
        try {
            // Params
            $params = [
                'key' => $apiKey
            ];
            // Request
            $response = $this->client->request('POST', $url, [
                'query' => $params,
                'body' => ['image' => $image]
            ]);
            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi du post sur ImgBB : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['data']['url'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $url;
    }
}
