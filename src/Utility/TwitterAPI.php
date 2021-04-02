<?php

namespace App\Utility;



use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterAPI
{

    /**
     * Upload des photos sur un hébergeur et stockage local en vue d'une publication sur Twitter
     * @param array $images Liste d'images à stocker et heberger (donner $form->get('images'))
     * @return array $names Retourne la liste des noms des images stockées localement
     */
    public function stockImages($images)
    {
        try {
            $names = [];
            // Vérif nombres d'images
            if (sizeof($images) > 4) throw new \Exception('Echec de l\'envoi du post sur Twitter, 4 images maximum autorisées');

            foreach ($images as $image) {
                $ext = $image->guessExtension();
                // Stockage en local
                $folder = $this->parameterBag->get('kernel.project_dir') . '/public/post_images/';
                $imgName = uniqid() . '.' . $ext;
                $imgPath = $folder . $imgName;
                $image->move($folder, $imgPath);
                // Vérif ext image
                if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif' && $ext != 'webp') {
                    unlink($imgPath);
                    foreach ($names as $name) {
                        unlink($folder . $name);
                    }
                    throw new \Exception('Echec de l\'envoi du post sur Twitter : format d\'image ' . $ext . 'non supporté, formats acceptés = JPG, PNG, GIF, WEBP');
                };
                // Vérif poids image
                $fileSize = filesize($imgPath);
                if ($fileSize > 5 * (10 ** 6)) {
                    unlink($imgPath);
                    foreach ($names as $name) {
                        unlink($folder . $name);
                    }
                    throw new \Exception('Echec de la publication de la photo sur Twitter : image trop volumineuse, limite de taille = 5MB, taille de l\'image = ' . $fileSize . 'B');
                };
                // Stockage du path dans la valeur retour                
                $names[] = $imgName;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $names;
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

