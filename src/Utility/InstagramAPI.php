<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InstagramAPI
{
    private $client;
    protected $parameterBag;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Upload une photo sur un hébergeur après vérifications en vue d'une publication sur Instagram
     * @param $imageName Nom de l'image à heberger
     * @return $result Retourne nom, url, validité et erreurs de l'image
     */
    public function checkAndHostImage($imageName)
    {
        $result = [
            'name' => $imageName,
            'url' => '',
            'isValid' => true,
            'errors' => []
        ];
        $folder = $this->parameterBag->get('kernel.project_dir') . '/public/post_images/';
        $imagePath = $folder . $imageName;
        // Vérif ext image
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        if ($ext != 'jpg' && $ext != 'jpeg') {
            $result['errors'][] = 'Echec de l\'envoi du post sur Facebook : format d\'image ' . $ext . 'non supporté, formats acceptés = JPEG / JPG';
        }
        // Vérif ratio image
        list($width, $height) = getimagesize($imagePath);
        if ($width / $height < 0.8) {
            $result['errors'][] = 'Echec de l\'envoi du post sur Instagram : photo trop longue, ratio minimum = 4:5';
        } elseif ($width / $height > 1.91) {
            $result['errors'][] = 'Echec de l\'envoi du post sur Instagram : photo trop large, ratio maximum = 1.91:1';
        }
        // Vérif poids image
        $fileSize = filesize($imagePath);
        if ($fileSize > 8 * (2 ** 20)) {
            $result['errors'][] = 'Echec de la publication de la photo sur Instagram : image trop volumineuse, limite de taille = 8MiB, taille de l\'image = ' . $fileSize . 'B';
        }
        // Envoi de la photo sur le site de l'hébergeur
        $data = file_get_contents($imagePath);
        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $image = base64_encode($data);
        $url = $this->ImgbbAPI->uploadImage($image);
        $result['url'] = $url;
        return $result;
    }

    // Publie une photo sur Instagram, des tags peuvent être ajoutés
    public function publishPhotoOnPage($accountId, $photoUrl, $access_token, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $accountId . '/media_publish';
        try {
            // Params
            $params = [
                'creation_id' => $this->sendPhotoOnPage($accountId, $photoUrl, $access_token, $message),
                'access_token' => $access_token
            ];

            // Request
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);
            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $error = $content['error']['message'];
                throw new \Exception('Echec de la publication de la photo sur Instagram : ' . $error);
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
            // Vérifications
            // Message trop long
            if ($message !== false && strlen($message) > 2200) {
                throw new \Exception('Echec de la publication de la photo sur Instagram : message trop long, limite de caractères = 2200');
            };
            // Stockage temporaire de l'image
            $folder = $this->parameterBag->get('kernel.project_dir') . '/public/images/instagram/';
            $imgPath = $folder . uniqid() . '.jpg';
            file_put_contents($imgPath, file_get_contents($photoUrl));
            list($width, $height) = getimagesize($imgPath);
            // Ratio non accepté
            if ($width / $height < 0.8) {
                throw new \Exception('Echec de l\'envoi du post sur Instagram : photo trop longue, ratio minimum = 4:5');
            } elseif ($width / $height > 1.91) {
                throw new \Exception('Echec de l\'envoi du post sur Instagram : photo trop large, ratio maximum = 1.91:1');
            }
            // Image trop volumineuse
            $fileSize = filesize($imgPath);
            if ($fileSize > 8 * (2 ** 20)) {
                throw new \Exception('Echec de la publication de la photo sur Instagram : image trop volumineuse, limite de taille = 8MiB, taille de l\'image = ' . $fileSize . 'B');
            };
            // Suppression image temporaire
            unlink($imgPath);

            // Params
            $params = [
                'image_url' => $photoUrl,
                'access_token' => $access_token
            ];
            if ($message) $params['caption'] = $message;

            // Request
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);
            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $error = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi du post sur Instagram : ' . $error);
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
