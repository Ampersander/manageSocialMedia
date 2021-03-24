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

    /**
     * Publie un statut avec ou sans photos sur la page Twitter
     * @param array $imagePaths Liste des chemins (absolus) des images, ne rien mettre si pas d'images
     */
    public function postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, $message = false, $imagePaths = false)
    {
        try {
            // Verifications
            // Message trop long
            if ($message && strlen($message) > 280) {
                throw new \Exception('Echec de la publication de la photo sur Twitter : message trop long, limite de caractères = 280');
            };
            // Trop de photos
            if ($imagePaths && sizeof($imagePaths) > 4) {
                throw new \Exception('Echec de l\'envoi du post sur Twitter : trop d\'images, maximum = 4');
            }
            // Image trop volumineuse
            foreach ($imagePaths as $imgPath) {
                $fileSize = filesize($imgPath);
                if ($fileSize > 10 * (10 ** 6)) {
                    throw new \Exception('Echec de la publication de l\'image sur Facebook : image trop volumineuse, limite de taille = 10MB, taille de l\'image = ' . $fileSize . 'B');
                };
            }

            // Request
            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
            $parameters = [
                'status' => $message
            ];
            // Si le tweet contient des photos
            if ($imagePaths != false) {
                $mediaIds = [];
                foreach ($imagePaths as $path) {
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
