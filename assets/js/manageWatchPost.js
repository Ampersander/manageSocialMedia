'use strict';
const $ = require('jquery');
import '../bootstrap';
$(function () {


/*var contentLink = $('.linkJS');
for(var i=0;i< contentLink.length();i++){
   regexlink = contentLink[i].split(new RegExp(/#([a-zA-Z]*)/gm));

    const regex = /Dog/i;
    console.log(p.replace(regex, 'ferret'));

}*/


    $(document).on("click", "#cb1", function () {
        //alert('yes');
        if ($("#cb1").prop('checked'))
            $('#facebookModal').show();
    });

    $(document).on("click", "#cb2", function () {
        if ($("#cb2").prop('checked'))
            $('#instagramModal').show();
    });

    $(document).on("click", "#cb3", function () {
        if ($("#cb3").prop('checked'))
            $('#twitterModal').show();
    });

    $(document).on("click", ".saveTw", function (e) {
       // e.preventDefault();
        $('#twitterModal').hide();
        var checkboxEmpty = true;

        for (var i = 0; i < $('.twitter').length; i++) {
            var checkbox = $('.twitter')[i];
            if (checkbox.checked) {
                checkboxEmpty = false;
                $("#cb3").prop('checked', true);
            } 
           
        }
        if(checkboxEmpty == true)
        $("#cb3").prop('checked', false);
    });

    $(document).on("click", ".saveInst", function (e) {
       // e.preventDefault();
        $('#instagramModal').hide();
        var checkboxEmpty = true;
        for (var i = 0; i < $('.instagram').length; i++) {
            var checkbox = $('.instagram')[i];
            if (checkbox.checked) {
                checkboxEmpty = false;
                $("#cb2").prop('checked', true);
            } 
        }
        if(checkboxEmpty == true)
        $("#cb2").prop('checked', false);

    });

    $(document).on("click", ".saveFb", function (e) {
       // e.preventDefault();
        $('#facebookModal').hide();

        var checkboxEmpty = true;
        for (var i = 0; i < $('.facebook').length; i++) {
            var checkbox = $('.facebook')[i];
            if (checkbox.checked) {
                checkboxEmpty = false;
                $("#cb1").prop('checked', true);
            }
        }
        if(checkboxEmpty == true)
            $("#cb1").prop('checked', false);


    });




    $(document).on("click", ".btn-close", function () {
        
        for (var i = 0; i < $('input[type=checkbox]:visible').length; i++) {
            var checkbox = $('input[type=checkbox]:visible')[i];
            checkbox.checked = false;
            if(checkbox.classList.contains("instagram"))
            $("#cb2").prop('checked', false);
            if(checkbox.classList.contains("twitter"))
            $("#cb3").prop('checked', false);
            if(checkbox.classList.contains("facebook"))
            $("#cb1").prop('checked', false);
        }
       


        $('#instagramModal').hide();
        $('#facebookModal').hide();
        $('#twitterModal').hide();
    });





});

