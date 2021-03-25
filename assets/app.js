/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
 
// any CSS you import will output into a single css file (app.css in this case)
import './styles/custom.scss';

// You can specify which plugins you need
//import { Dropdown, Navbar } from 'bootstrap';
//const bootstrap = require('bootstrap');
//import bootstrap from 'bootstrap';

// start the Stimulus application
import './bootstrap';

$(function () {
    $('#image-dim').on('click', function () {
        let file = $('#file-input').files[0];
        console.log(getImageDimentions(file));
    })

    function getImageDimentions(file) {
        let reader = new FileReader();
        let binary = reader.readAsText(file);
        var image = new Image();
        image.src = 'data:image/jpeg;base64,' + binary;
        return (image.width, image.height);
    }
    
    function hexToBase64(str) {
        return btoa(String.fromCharCode.apply(null, str.replace(/\r|\n/g, "").replace(/([\da-fA-F]{2}) ?/g, "0x$1 ").replace(/ +$/, "").split(" ")));
    }
});
