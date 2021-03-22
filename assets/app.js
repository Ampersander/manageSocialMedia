/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// Renvoie le user short-lived token à partid du appId, puis déconnecte la session
async function sendFacebookToken(appId) {
    console.log('Connexion à Facebook, appId : ' + appId);
    FB.init({ appId: appId, status: true, xfbml: false, version: 'v10.0' });
    FB.login(function (response) {
        if (response.authResponse) {
            console.log('Connecté à Facebook ! Access token : ' + response.authResponse.accessToken);
            // Afficher l'access token
            $('#access-token').text(response.authResponse.accessToken);
            // Envoi du short-lived token au serveur pour le traiter
            // let data = {
            //     appId: appId,
            //     token: response.authResponse.accessToken
            // }
            // fetch('url', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //     },
            //     body: JSON.stringify(data),
            // });
        } else {
            console.log('Connection échouée');
        }
    });
};

window.fbAsyncInit = function() {
    console.log("Facebook SDK Loaded");
    FB.getLoginStatus(function(response) {        
        if (response.status === 'connected') {
            console.log('Utilisateur déjà connecté');
          } else if (response.status === 'not_authorized') {
            console.log('Utilisateur déjà connecté, pas d autorisation');
          } else {
            console.log('Utilisateur non connecté');
          }
    });
}

$(function () {

    $('#fb-log-btn').on('click', async function () {
        let appId = $('#fb-app-id').val();
        sendFacebookToken(appId);
    });
});