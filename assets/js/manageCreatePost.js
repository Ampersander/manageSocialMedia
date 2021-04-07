const $ = require('jquery');
const autocomplete = require('autocompleter');


$(function () {


    $(document).on("click", ".btn-close", function (e) {
        $('#artisteModal').hide();
    });

    $(document).on("click", ".save", function () {
        var value = document.getElementById('testinput').value;
        console.log(value);
        var desc = document.getElementById('form_description').value;
        console.log(desc);
        
        var string = desc+ value;
        console.log(string);
        document.getElementById('form_description').value = string;
        $('#artisteModal').hide();   
    });

});

document
    .getElementById("form_description")
    .addEventListener("keydown", function(event) {
        console.log(event.key);
        if (event.key === "@") {
            $('#artisteModal').show();
            setTimeout(function(){ document.getElementById('testinput').value = ''; }, 20);
            $.ajax({
                url : 'http://localhost:8000/api/artistes',
                type : 'GET',
                dataType : 'json',
                success : function(resultat, statut){ 
                    console.log(resultat);
                    var artistes =[];
                    for(var i = 0 ; i< resultat.length ;i++){
                       var elem =  resultat[i];
                       artistes.push(elem.nom); 
                    }
               
        
            
            var items = artistes.map(function (n) { return { label: n, group: "Artistes" }});
            var allowedChars = new RegExp(/^[a-zA-Z\s]+$/)
        
            function charsAllowed(value) {
                return allowedChars.test(value);
            }
        
            autocomplete({
                input: document.getElementById('testinput'),
                minLength: 1,
                onSelect: function (item, inputfield) {
                    inputfield.value = item.label
                },
                fetch: function (text, callback) {
                    var match = text.toLowerCase();
                    callback(items.filter(function(n) { return n.label.toLowerCase().indexOf(match) !== -1; }));
                },
                render: function(item, value) {
                    var itemElement = document.createElement("div");
                    if (charsAllowed(value)) {
                        var regex = new RegExp(value, 'gi');
                        var inner = item.label.replace(regex, function(match) { return "<strong>" + match + "</strong>" });
                        itemElement.innerHTML = inner;
                    } else {
                        itemElement.textContent = item.label;
                    }
                    return itemElement;
                },
                emptyMsg: "No artistes found",
                customize: function(input, inputRect, container, maxHeight) {
                    if (maxHeight < 100) {
                        container.style.top = "";
                        container.style.bottom = (window.innerHeight - inputRect.bottom + input.offsetHeight) + "px";
                        container.style.maxHeight = "140px";
                    }
                }
            })
        
            document.getElementById('testinput').focus();
        },
            
        error : function(resultat, statut, erreur){
            console.log(resultat);
        }
    
     });
        }
    });


   
    document
    .getElementById("form_templatePost")
    .addEventListener("change", function(event) {

            $.ajax({
                url : 'http://localhost:8000/api/templateposts',
                type : 'GET',
                dataType : 'json',
                success : function(resultat, statut){ 
                    console.log(resultat);
                    console.log(document.getElementById("form_templatePost").value);
                    if(document.getElementById("form_templatePost").value == 0){
                        var desc = document.getElementById('form_description');
                        var descArray = desc.value.split('\n '); 
                        console.log(descArray);                 
                        desc.value = descArray[0]; 
                    }else{
                        for(var i = 0 ; i< resultat.length ;i++){
                        if(document.getElementById("form_templatePost").value == resultat[i].id) {
                            var desc = document.getElementById('form_description');
                            var descArray = desc.value.split('\n ');                    
                            desc.value = descArray[0]+'\n '+resultat[i].description;                         
                            }                        
                        } 
                }  
        },
            
        error : function(resultat, statut, erreur){
            console.log(resultat);
        }
    
    });
       });


