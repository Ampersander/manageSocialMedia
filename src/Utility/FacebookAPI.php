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

    /**
     * Upload des photos sur un hébergeur en vue d'une publication sur Facebook
     * @param array $images Liste des images (data des images)
     */
    public function stockAndHostImage($images)
    {
        try {
            foreach ($images as $image) {
                // Vérifications
                $ext = $image->guessExtension();
                if ($ext != 'jpg' || $ext != 'jpeg' || $ext != 'png') {
                    throw new \Exception('Echec de l\'envoi du post sur Facebook : format d\'image ' . $ext . 'non supporté, formats acceptés = JPEG ou PNG');
                };
                // Stockage en local
                $folder = $this->parameterBag->get('kernel.project_dir') . '/public/image_verification/';
                $imgPath = $folder . uniqid() . '.jpg';
                $image->move($folder, $imgPath);
                // Envoi de la photo sur le site de l'hébergeur
                $url = '';
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $url;
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
            if ($link) {
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
    public function postPhotoOnPage($pageAccessToken, $pageId, $photoPath, $message = false, $publish = true)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/photos/';
        try {
            // Vérifications
            // Message trop long
            if ($message && strlen($message) > 63206) {
                throw new \Exception('Echec de la publication de la photo sur Facebook : message trop long, limite de caractères = 63206');
            };
            // Stockage temporaire de l'image
            $folder = $this->parameterBag->get('kernel.project_dir') . '/public/image_verification/';
            $imgPath = $folder . uniqid() . '.jpg';
            file_put_contents($imgPath, file_get_contents($photoPath));
            list($width, $height) = getimagesize($imgPath);
            // Ratio non accepté
            if ($width / $height < 0.8) {
                throw new \Exception('Echec de l\'envoi du post sur Facebook : photo trop longue, ratio minimum = 4:5');
            } elseif ($width / $height > 1.91) {
                throw new \Exception('Echec de l\'envoi du post sur Facebook : photo trop large, ratio maximum = 1.91:1');
            }
            // Image trop volumineuse
            $fileSize = filesize($imgPath);
            if ($fileSize > 10 * (10 ** 6)) {
                throw new \Exception('Echec de la publication de la photo sur Facebook : image trop volumineuse, limite de taille = 10MB, taille de l\'image = ' . $fileSize . 'B');
            };
            // Suppression image temporaire
            unlink($imgPath);

            // Params
            $params = [
                'url' => $photoPath,
                'access_token' => $pageAccessToken
            ];
            // Message éventuel
            if ($message != false) {
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
                throw new \Exception('Echec de l\'envoi de la photo sur Facebook : ' . $message);
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
     * @param array $photoPath Liste des url de photo à publier dans le post
     */
    public function postPhotosOnPage($pageAccessToken, $pageId, $photoPaths, $message = false)
    {
        $url = 'https://graph.facebook.com/v10.0/' . $pageId . '/feed/';
        try {
            // Upload des photos sans les publier
            $unpublishedPhotoIds = [];
            foreach ($photoPaths as $path) {
                $unpublishedPhotoIds[] = $this->postPhotoOnPage($pageAccessToken, $pageId, $path, false, false);
            }

            // Params
            $params = [
                'access_token' => $pageAccessToken
            ];
            if ($message != false) {
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
     public function getIdInstagram($shortLivedToken,$id_page_fb)
     {
         $url = 'https://graph.facebook.com/v10.0/'.$id_page_fb;
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
                 if(isset($content['instagram_business_account'])){
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
public function getName($shortLivedToken,$id)
{
    $url = 'https://graph.facebook.com/v10.0/'.$id;
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
