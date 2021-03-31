<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterAPI
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Publie un statut avec ou sans photos sur la page Twitter
    public function postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, $message, $photoPaths = false)
    {
        try {
            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
            $parameters = [
                'status' => $message
            ];
            // Si le tweet contient des photos
            if ($photoPaths != false) {
                if (sizeof($photoPaths) > 4){
                    throw new \Exception('Echec de l\'envoi du post sur Twitter : trop de photos, maximum = 4');
                }
                $mediaIds = [];
                foreach ($photoPaths as $path) {
                    $media = $connection->upload('media/upload', ['media' => $path]);
                    
                    $mediaIds[] = $media->media_id_string;
                }
                $parameters['media_ids'] = implode(',', $mediaIds);
            }
            $result = $connection->post('statuses/update', $parameters);

            if ($connection->getLastHttpCode() != 200) {
                $body = $connection->getLastBody();
                $body = json_decode(json_encode($body), true);
                $message = $body['errors'][0]['message'];
                throw new \Exception('Echec de l\'envoi du post sur Twitter : ' . $message);
            } else {
                $body = $connection->getLastBody();
                $body = json_decode(json_encode($body), true);
                $postId = $body['id_str'];
                return $postId;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

}