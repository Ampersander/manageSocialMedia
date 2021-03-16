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

$(async function () {
    // Short-lived User Access Token, obtenu avec le graph explorer tool
    let shortLivedToken = 'EAABZCHn2BZAjMBAFpwt6b20mCfuA3avSl3kAUST8WLGqOFGp93LLZBeHO2I9XKLhTayaV1NiiVrjBpDiO56UiJ32lENKB8NVHDU3yTyJ5GeZCxO6npIZAZALobEUQhtBZB4XgwLTA8tiN3RoFErO9ZCY6mxYWvZAsPbKr06pcOH29rdo2aX0TRbzCy015SLe8cjBCSSoIZCqYpRitSVeznU6Mv';
    let pageId = '102213561957064';

    try {
        const longLivedToken = await getLongLivedUserToken(shortLivedToken);
        console.log('Long lived access token : ' + longLivedToken);

        const pageAccessToken = await getPageAccessToken(longLivedToken, pageId);
        console.log('Page access token : ' + pageAccessToken);

        const messagePostResult = await postMessageOnPage(pageAccessToken, pageId, 'Mission complete');
        console.log(messagePostResult);
    } catch (error) {
        console.log(error);
    }

});

// Publie un statut avec un lien éventuel sur la page Facebook
async function postMessageOnPage(pageAccessToken, pageId, message, link) {

    let url = new URL('https://graph.facebook.com/' + pageId + '/feed/');
    let requestOptions = {
        method: 'POST'
    };
    let params = {
        message: message,
        access_token: pageAccessToken
    };
    if (link !== undefined) params.link = link;

    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

    let response = await fetch(url, requestOptions)
        .then(response => response.json())
        .catch(error => console.log('error', error));

    if (response.id !== undefined) {
        return response.id;
    } else {
        throw response.error.message;
    }
}


// Renvoie le Page Access Token
async function getPageAccessToken(longLivedToken, pageId) {

    let url = new URL('https://graph.facebook.com/' + pageId);
    let requestOptions = {
        method: 'GET'
    };
    let params = {
        fields: 'access_token',
        access_token: longLivedToken
    };
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

    let response = await fetch(url, requestOptions)
        .then(response => response.json())
        .catch(error => console.log('error', error));

    if (response.access_token !== undefined) {
        return response.access_token;
    } else {
        throw response.error.message;
    }
}

// renvoie le Long-lived User Access Token
async function getLongLivedUserToken(shortLivedToken) {
    let requestOptions = {
        method: 'GET'
    };
    let url = new URL('https://graph.facebook.com/oauth/access_token');
    let params = {
        grant_type: 'fb_exchange_token',
        client_id: '139768931378739',
        client_secret: 'ac660241b09b4640889456be63f3f7da',
        fb_exchange_token: shortLivedToken
    };
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

    let response = await fetch(url, requestOptions)
        .then(response => response.json())
        .catch(error => console.log('error', error));

    if (response.access_token !== undefined) {
        return response.access_token;
    } else {
        throw response.error.message;
    }
}