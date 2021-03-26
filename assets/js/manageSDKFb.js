

// Renvoie le user short-lived token à partid du appId, puis déconnecte la session
async function sendFacebookToken(appId) {
    console.log('Connexion à Facebook, appId : ' + appId);
    FB.init({ appId: appId, status: true, xfbml: false, version: 'v10.0' });
    FB.login(function (response) {
        if (response.authResponse) {
            console.log('Connecté à Facebook ! Access token : ' + response.authResponse.accessToken);
            // Afficher l'access token
            $('#access-token').val(response.authResponse.accessToken);
            console.log($('#access-token').val());
            FB.api(
                '/me',
                'GET',
                {"fields":"id,name"},
                function(response) {
                    console.log(response);
                    $('#name-account').val(response.name);
                    $('.invisible').removeClass('invisible');
                    $('#fb-log-btn').addClass('invisible');
                }
              );

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

