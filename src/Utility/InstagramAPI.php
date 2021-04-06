<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InstagramAPI
{
    private $client;
    private $ImgbbAPI;
    protected $parameterBag;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->ImgbbAPI = new ImgbbAPI($client);
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
        $validExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (!in_array(strtolower($ext), $validExts)) {
            $result['isValid'] = false;
            $result['errors'][] = 'Echec de l\'envoi du post sur Instagram : format d\'image ' . $ext . ' non supporté, formats acceptés = JPG, PNG, GIF, WEBP, SVG';
        } else {
            // Vérif ratio image
            list($width, $height) = getimagesize($imagePath);
            if ($width / $height < 0.8) {
                $result['isValid'] = false;
                $result['errors'][] = 'Echec de l\'envoi du post sur Instagram : photo trop longue, ratio minimum = 4:5';
            } elseif ($width / $height > 1.91) {
                $result['isValid'] = false;
                $result['errors'][] = 'Echec de l\'envoi du post sur Instagram : photo trop large, ratio maximum = 1.91:1';
            }
            // Vérif poids image
            $fileSize = filesize($imagePath);
            if ($fileSize > 8 * (2 ** 20)) {
                $result['isValid'] = false;
                $result['errors'][] = 'Echec de la publication de la photo sur Instagram : image trop volumineuse, limite de taille = 8MiB, taille de l\'image = ' . $fileSize . 'B';
            }
            // Envoi de la photo sur le site de l'hébergeur
            if (empty($result['errors'])) {
                $data = file_get_contents($imagePath);
                $image = base64_encode($data);
                $url = $this->ImgbbAPI->uploadImage($image);
                $result['url'] = $url;
            }
        }
        return $result;
    }

    // Publie une photo sur Instagram, des tags peuvent être ajoutés
    public function publishPhotoOnPage($accountId, $imageUrl, $access_token, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $accountId . '/media_publish';
        try {

            // Params

            $params = [
                'creation_id' => $this->sendPhotoOnPage($accountId, $imageUrl, $access_token, $message),
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
    public function sendPhotoOnPage($accountId, $imageUrl, $access_token, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $accountId . '/media';
        try {
            // Vérifications
            // Message trop long
            if ($message !== false && strlen($message) > 2200) {
                throw new \Exception('Echec de la publication de la photo sur Instagram : message trop long, limite de caractères = 2200');
            };

            // Params
            $params = [
                'image_url' => $imageUrl,
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
