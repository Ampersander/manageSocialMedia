const $ = require('jquery');

async function getImageInfos(file) {
    let image = new Image();
    let reader = new FileReader();
    const promise = new Promise((resolve, reject) => {
        reader.onload = function () {
            image.onload = function () {
                resolve({
                    width: this.width,
                    height: this.height,
                    ratio: this.width / this.height,
                    size: file.size,
                    name: file.name,
                    ext: file.name.split('.')[1],
                    image: this
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

function getImageWarns(imageInfos) {
    console.log(imageInfos);
    let ratio = imageInfos.ratio;
    let ext = imageInfos.ext;
    let size = imageInfos.size;
    let warnMessages = [];
    // Extension
    if (!['png', 'jpg', 'jpeg', 'webp'].includes(ext)) {
        warnMessages.push('Format de l\'image incompatible avec les réseaux sociaux');
    }
    // Ratio
    if (ratio < 4 / 5) {
        warnMessages.push('Image trop grande verticalement, ratio minimum 4:5');
    }
    if (ratio > 1.91 / 1) {
        warnMessages.push('Image trop grande horizontalement, ratio maximum 1.91:1');
    }
    // Facebook
    if (size > 10 * (10 ** 6)) {
        warnMessages.push('Image trop lourde pour Facebook, taille maximum 10Mo');
    }
    // Insta
    if (size > 8 * (2 ** 20)) {
        warnMessages.push('Image trop lourde pour Instagram, taille maximum 8Mio');
    }
    // Twitter
    if (size > 5 * (10 ** 6)) {
        warnMessages.push('Image trop lourde pour Twitter, taille maximum 5Mo');
    }
    return warnMessages;
}

function getDescriptionWarns(description) {
    let warnMessages = [];
    if (description.length > 63206) {
        warnMessages.push('Description trop longue pour Facebook, maximum de caractères à 63206');
    }
    if (description.length > 2200) {
        warnMessages.push('Description trop longue pour Instagram, maximum de caractères à 2200');
    }
    if (description.length > 280) {
        console.log(description.length);
        warnMessages.push('Description trop longue pour Twitter, maximum de caractères à 280');
    }
    return warnMessages;
}

function resetImageList() {
    $('#image-list').empty();
    $('#form_image').val('');
}

function addImageToList(imageInfos, warns) {
    // Preview de l'image avec nom du fichier
    let image = imageInfos.image;
    $(image).width('5em');
    let name = $('<p>').text(imageInfos.name);
    let imagePrev = $('<div>').append(image, name);
    // Bloc contenant les warns
    let warnBlock = $('<ul>');
    if (warns.length == 0) {
        $('<li></li>').text('Cette image peut être postée sur tout les réseaux').appendTo(warnBlock);
    }
    for (const warn of warns) {
        $('<li></li>').text(warn).appendTo(warnBlock);
    }
    // Ajout du bloc a la liste preview des images à upload
    let imageBox = $('<div>').append(imagePrev, warnBlock).attr('name', imageInfos.name);
    $('#image-list').append(imageBox);
}

$(function () {
    resetImageList();

    $('#form_image').on('change', async function () {
        let files = $(this)[0].files;
        $('#image-list').empty();
        for (const file of files) {
            let imageInfos = await getImageInfos(file);
            let warns = getImageWarns(imageInfos);
            addImageToList(imageInfos, warns);
        }
    });

    $('#form_description').on('keyup', function () {
        if (document.activeElement == $(this)[0]){
            $('#desc-warns').empty();
            let warns = getDescriptionWarns($(this).val());
            for(warn of warns){
                $('#desc-warns').append('<p>').text(warn);
            }
        }
    });

    $('#trash').on('click', () => resetImageList());

});