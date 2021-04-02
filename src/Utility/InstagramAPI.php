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
     * Upload des photos sur un hébergeur et stockage local en vue d'une publication sur Instagram
     * @param $image Image à stocker et heberger (donner $form->get('images')->getData())
     * @return array Retourne une array, à utiliser comme suit :
     * $nomImage = array['name']
     * $urlImage = array['url']
     */
    public function stockAndHostImage($image)
    {
        try {
            // Vérif ext image
            $ext = $image->guessExtension();
            if ($ext != 'jpg' && $ext != 'jpeg') {
                throw new \Exception('Echec de l\'envoi du post sur Facebook : format d\'image ' . $ext . 'non supporté, formats acceptés = JPEG / JPG');
            };
            // Stockage en local
            $folder = $this->parameterBag->get('kernel.project_dir') . '/public/post_images/';
            $imgName = uniqid() . '.' . $ext;
            $imgPath = $folder . $imgName;
            $image->move($folder, $imgPath);
            list($width, $height) = getimagesize($imgPath);
            // Vérif ratio image
            if ($width / $height < 0.8) {
                unlink($imgPath);
                throw new \Exception('Echec de l\'envoi du post sur Instagram : photo trop longue, ratio minimum = 4:5');
            } elseif ($width / $height > 1.91) {
                unlink($imgPath);
                throw new \Exception('Echec de l\'envoi du post sur Instagram : photo trop large, ratio maximum = 1.91:1');
            }
            // Vérif poids image
            $fileSize = filesize($imgPath);
            if ($fileSize > 8 * (2 ** 20)) {
                unlink($imgPath);
                throw new \Exception('Echec de la publication de la photo sur Instagram : image trop volumineuse, limite de taille = 8MiB, taille de l\'image = ' . $fileSize . 'B');
            };
            // Envoi de la photo sur le site de l'hébergeur
            $image = base64_encode($image);
            $url = $this->ImgbbAPI->uploadImage($image);
            $imgInfos = [
                'name' => $imgName,
                'url' => $$url
            ];
        } catch (\Exception $e) {
            throw $e;
        }
        return $url;


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

