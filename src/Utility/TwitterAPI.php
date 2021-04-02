<?php

namespace App\Utility;



use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterAPI
{

    /**
     * Upload des photos sur un hébergeur après vérification en vue d'une publication sur Twitter
     * @param array[string] $images Liste des noms des images à vérifier
     * @return $result Retourne une liste de listes associatives avec nom, validité et erreurs des images
     */
    public function checkImages($imagesNames)
    {
        $results = [];
        // Vérif nombres d'images
        try{
            if (sizeof($imagesNames) > 4){
                throw new \Exception('Echec de l\'envoi du post sur Twitter, 4 images maximum autorisées');
            }
        } catch (\Exception $e) {
            throw $e;
        }
        
        foreach ($imagesNames as $imageName) {
            $imageResult = [
                'name' => $imageName,
                'isValid' => true,
                'errors' => []
            ];
            $folder = $this->parameterBag->get('kernel.project_dir') . '/public/post_images/';
            $imagePath = $folder . $imageName;
            // Vérif ext image
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif' && $ext != 'webp') {
                $imageResult['isValid'] = false;
                $imageResult['errors'][] = 'Echec de l\'envoi du post sur Twitter : format d\'image ' . $ext . 'non supporté, formats acceptés = JPG, PNG, GIF, WEBP';
            };
            // Vérif poids image
            $fileSize = filesize($imagePath);
            if ($fileSize > 5 * (10 ** 6)) {
                $imageResult['isValid'] = false;
                $imageResult['errors'][] = 'Echec de la publication de la photo sur Twitter : image trop volumineuse, limite de taille = 5MB, taille de l\'image = ' . $fileSize . 'B';
            };
            // Stockage du path dans la valeur retour                
            $result[] = $imageResult;
        }
        return $results;
    }

    /**
     * Publie un statut avec ou sans photos sur la page Twitter
     * @param array $imagePaths Liste des chemins (absolus) des images, ne rien mettre si pas d'images
     */
    public function postStatusOnPage($consumer_key, $consumer_secret, $access_token, $access_token_secret, $message = false, $imagePaths = false)
    {
        try {
            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
            // Verifications
            // Message trop long
            if ($message && strlen($message) > 280) {
                throw new \Exception('Echec de la publication de la photo sur Twitter : message trop long, limite de caractères = 280');
            };

            // Params            
            if ($message !== false) {
                $parameters = [
                    'status' => $message
                ];
            }
            // Si le tweet contient des photos
            if ($imagePaths != false) {
                $mediaIds = [];
                foreach ($imagePaths as $path) {
                    $media = $connection->upload('media/upload', ['media' => $path]);

                    $mediaIds[] = $media->media_id_string;
                }
                $parameters['media_ids'] = implode(',', $mediaIds);
            }


            // Request
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

