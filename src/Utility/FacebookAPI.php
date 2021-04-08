<?php

namespace App\Utility;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Utility\ImgbbAPI;


class FacebookAPI
{
    private $client;
    private $ImgbbAPI;
    private $parameterBag;


    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->ImgbbAPI = new ImgbbAPI($client);
        $this->parameterBag = $parameterBag;
    }

    /**
     * Upload des photos sur un hébergeur après vérifications en vue d'une publication sur Facebook
     * @param array[string] $images Liste des noms des images à vérifier
     * @return $results Retourne une liste de listes associatives avec nom, url, validité et erreurs des images
     */
    public function checkAndHostImages($imageNames)
    {
        $results = [];
        foreach ($imageNames as $index => $imageName) {
            $imageResult = [
                'name' => $imageName,
                'url' => '',
                'isValid' => true,
                'errors' => []
            ];
            $folder = $this->parameterBag->get('kernel.project_dir') . '/public/post_images/';
            $imagePath = $folder . $imageName;
            // Vérif ext image
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $validExts = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array(strtolower($ext), $validExts)) {
                $imageResult['isValid'] = false;
                $imageResult['errors'][] = 'Echec de l\'envoi du post sur Facebook : format d\'image ' . $ext . ' non supporté, formats acceptés = JPG, PNG, WEBP';
            } else {
                // Vérif ratio image
                list($width, $height) = getimagesize($imagePath);
                if ($width / $height < 0.8) {
                    $imageResult['isValid'] = false;
                    $imageResult['errors'][] = 'Echec de l\'envoi du post sur Facebook : image N°' . $index . ' trop longue, ratio minimum = 4:5';
                } elseif ($width / $height > 1.91) {
                    $imageResult['isValid'] = false;
                    $imageResult['errors'][] = 'Echec de l\'envoi du post sur Facebook : image N°' . $index . ' trop large, ratio maximum = 1.91:1';
                }
                // Vérif poids image
                $fileSize = filesize($imagePath);
                if ($fileSize > 10 * (10 ** 6)) {
                    $imageResult['isValid'] = false;
                    $imageResult['errors'][] = 'Echec de la publication de la photo sur Facebook : image N°' . $index . ' trop volumineuse, limite de taille = 10MB, taille de l\'image = ' . $fileSize . 'B';
                }
                // Envoi de la photo sur le site de l'hébergeur
                if (empty($imageResult['errors'])) {
                    $data = file_get_contents($imagePath);
                    $image = base64_encode($data);
                    $url = $this->ImgbbAPI->uploadImage($image);
                    $imageResult['url'] = $url;
                }
            }
            $results[] = $imageResult;
        }
        return $results;
    }

    /**
     * Publie un statut avec un lien éventuel sur la page Facebook
     */
    public function postMessageOnPage($pageAccessToken, $pageId, $message, $link = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/feed/';
        try {

            // Vérifications
            // Message vide
            if (!$message) {
                throw new \Exception('Echec de la publication de la photo sur Facebook : message vide !');
            };
            // Taille message
            if (strlen($message) > 63206) {
                throw new \Exception('Echec de la publication de la photo sur Facebook : message trop long, limite de caractères = 63206');
            };

            // Params

            $params = [
                'message' => $message,
                'access_token' => $pageAccessToken
            ];
            if ($link !== false) {
                $params['link'] = $link;
            }

            // Request
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



    /**
     * Envoie une photo avec un message éventuel sur la page Facebook
     * @param bool $publish Définir sur true pour publier la photo, false pour l'upload seulement
     */
    public function postPhotoOnPage($pageAccessToken, $pageId, $imageUrl, $message = false, $publish = true)

    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/photos/';
        try {
            // Vérifications
            // Message trop long
            if ($message && strlen($message) > 63206) {
                throw new \Exception('Echec de la publication de la photo sur Facebook : message trop long, limite de caractères = 63206');
            };

            // Params
            $params = [
                'url' => $imageUrl,
                'access_token' => $pageAccessToken
            ];
            // Message éventuel
            if ($message !== false) {
                $params['message'] = $message;
            }
            // Publier la photo ou upload seulement ?
            if (!$publish) {
                $params['published'] = 'false';
            }

            // Request
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec de l\'envoi de la photo sur Facebook : ' . $message );
            } else {
                $content = $response->toArray();
                return $content['id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * Publie plusieurs photos en un post avec un message éventuel sur la page Facebook
     * @param array $imageUrls Liste des url de photo à publier dans le post
     */
    public function postPhotosOnPage($pageAccessToken, $pageId, $imageUrls, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/feed/';
        try {
            // Upload des photos sans les publier
            $unpublishedPhotoIds = [];
            foreach ($imageUrls as $imageUrl) {
                $unpublishedPhotoIds[] = $this->postPhotoOnPage($pageAccessToken, $pageId, $imageUrl, false, false);
            }

            // Params
            $params = [
                'access_token' => $pageAccessToken
            ];
            if ($message !== false) {
                $params['message'] = $message;
            }
            // Inclue chaque id de photo a publier
            foreach ($unpublishedPhotoIds as $key => $photoId) {
                // Format du param : attached_media[0]={"media_fbid":"1002088839996"}
                $params['attached_media' . '[' . $key . ']'] = '{"media_fbid":"' . $photoId . '"}';
            }

            // Request

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

    // Renvoie les pages liées aux comptes
    public function getPages($shortLivedToken)
    {
        $url = 'https://graph.facebook.com/me';
        try {
            $params = [
                'fields' => 'id,name,accounts',
                'access_token' => $shortLivedToken
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec obtention Pages Facebook : ' . $message);
            } else {
                $content = $response->toArray();
                return $content['accounts'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Renvoie l'id instagram lié à la page
    public function getIdInstagram($shortLivedToken, $id_page_fb)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $id_page_fb;
        try {
            $params = [
                'fields' => 'instagram_business_account',
                'access_token' => $shortLivedToken,
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec obtention Id Account Instagram : ' . $message);
            } else {
                $content = $response->toArray();
                $idInsta = null;
                if (isset($content['instagram_business_account'])) {
                    $idInstaContent = $content['instagram_business_account'];
                    $idInsta =  $idInstaContent['id'];
                }
                return $idInsta;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Renvoie le nom de la page/compteFb/CompteInstagram
    public function getName($shortLivedToken, $id)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $id;
        try {
            $params = [
                'fields' => 'name',
                'access_token' => $shortLivedToken,
            ];
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            if (200 !== $response->getStatusCode()) {
                $content = $response->toArray(false);
                $message = $content['error']['message'];
                throw new \Exception('Echec obtention Id Account Instagram : ' . $message);
            } else {
                $content = $response->toArray();

                return $content['name'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
