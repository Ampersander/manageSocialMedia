const $ = require('jquery');
$.ajax({
    url: 'http://localhost:8000/api/post',
    type: 'GET',
    dataType: 'json',
    success: function(resultat, statut) {
        console.log(resultat);
        console.log(resultat[0].date);
        var attente = 0;
        var retard = 0;
        //var todayDate = new Date().toISOString().slice(0, 10);
        var todayDate = new Date().toISOString();
        console.log(todayDate);
        if (resultat.length != 0) {
            for (var i = 0; i < resultat.length; i++) {
                if (resultat[i].date < todayDate) {
                    retard = retard + 1;
                } else if (resultat[i].date >= todayDate) {
                    attente = attente + 1;
                }
            }
        }
        console.log(attente);
        $('#post_attente').append(attente);
        $('#post_retard').append(retard);
    },

    error: function(resultat, statut, erreur) {
        console.log(resultat);
    }

});