/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/custom.scss';

// You can specify which plugins you need
//import { Tooltip, Toast, Popover } from 'bootstrap';

// start the Stimulus application
import './bootstrap';
import { inArray } from 'jquery';

async function getImageInfos(file) {
    let image = new Image();
    let reader = new FileReader();
    const promise = new Promise((resolve, reject) => {
        reader.onload = function (event) {
            image.onload = function () {
                let height = this.height;
                let width = this.width;
                let ratio = width / height;
                let size = file.size;
                resolve({
                    height: height,
                    width: width,
                    ratio: ratio,
                    size: size
                });
            };
            image.onerror = function () {
                reject(false);
            };
            image.src = reader.result;
        };
        reader.onerror = function () {
            reject(false);
        }
        reader.readAsDataURL(file);
    });
    return promise;
}

async function facebookImgVerif(imageInfos) {
    let ratio = imageInfos.width / imageInfos.height;
    if(ratio < 4/5) {
        return 'Ratio trop vertical, ratio minimum 4:5';
    }
    if(ratio > 1.91/1) {
        return 'Ratio trop horizontal, ratio maximum 1.91:1'
    }
    if(imageInfos.size > 10*(10^2) ){
        return 'Image trop lourde, taille maximum 10Mo'
    }
}

$(function () {
    $('#image-dim').on('click', async function () {
        let file = $('#file-input')[0].files[0];
        let infos = await getImageInfos(file);
        console.log(infos);
    })
});
