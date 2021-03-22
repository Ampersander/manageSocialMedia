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
async function getShortLivedAccessToken(appId) {
    console.log('Connexion à Facebook, appId : '+appId);
    FB.init({appId: appId, status: false, xfbml: false, version: 'v10.0'});
    let accessToken = FB.login(function (response) {
        if (response.authResponse) {
            console.log('Connecté à FB !');
            return response.authResponse.accessToken;
        } else {
            console.log('Connection échouée');
            return false;
        }
    });
    return accessToken;
}

$(function () {
    $('#fb-log-btn').on('click', async function () {
        let appId = $('#fb-app-id').val();
        let shlt = await Fb.getShortLivedAccessToken(appId);
        console.log('Short lived acces token : '+ shlt);
    });
});