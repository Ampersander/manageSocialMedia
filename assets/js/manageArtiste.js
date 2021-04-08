  /*const $ = require('jquery');
  const autocomplete = require('autocompleter');


  $(function() {


      $(document).on("click", ".btn-close", function(e) {
          $('#artisteModal').hide();
      });

      $(document).on("click", ".save", function() {
          var value = document.getElementById('testinput').value;
          console.log(value);
          var desc = document.getElementById('form_description').value;
          console.log(desc);

          var string = desc + value;
          console.log(string);
          document.getElementById('form_description').value = string;
          $('#artisteModal').hide();
      });

  });

  $.ajax({
      url: 'http://localhost:8000/api/artistes',
      type: 'GET',
      dataType: 'json',
      success: function(resultat, statut) {
          console.log(resultat);
          var countries = [];
          for (var i = 0; i < resultat.length; i++) {
              var elem = resultat[i];
              countries.push(elem.nom);
          }
      },

      error: function(resultat, statut, erreur) {
          console.log(resultat);
      }

  });

  document
      .getElementById("form_description")
      .addEventListener("keydown", function(event) {
          console.log(event.key);
          if (event.key === "@") {
              $('#artisteModal').show();
              setTimeout(function() { document.getElementById('testinput').value = ''; }, 20);
              $.ajax({
                  url: 'http://localhost:8000/api/artistes',
                  type: 'GET',
                  dataType: 'json',
                  success: function(resultat, statut) {
                      console.log(resultat);
                      var countries = [];
                      for (var i = 0; i < resultat.length; i++) {
                          var elem = resultat[i];
                          countries.push(elem.nom);
                      }

                      //var countries = ['Afghanistan', 'Albania', 'Algeria', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bangladesh', 'Belarus', 'Belgium', 'Bolivia', 'Brazil', 'Bulgaria', 'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Denmark', 'Dominica', 'Dominican Republic', 'Ecuador', 'Finland', 'France', 'Georgia', 'Germany', 'Gibraltar', 'Greece', 'Greenland', 'Guadeloupe', 'Guatemala', 'Guyana', 'Haiti', 'Iceland', 'India', 'Indonesia', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jersey', 'Jordan', 'Kazakhstan', 'Kenya', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Mauritius', 'Mayotte', 'Mexico', 'Moldova', 'Norway', 'Palau', 'Paraguay', 'Poland', 'Portugal', 'Romania', 'Saint Helena', 'Saint Pierre and Miquelon', 'South Africa', 'South Georgia', 'South Korea', 'Spain', 'Sweden', 'Swaziland', 'Switzerland', 'United Kingdom', 'United States'];

                      var items = countries.map(function(n) { return { label: n, group: "Artiste" } });
                      var allowedChars = new RegExp(/^[a-zA-Z\s]+$/)

                      function charsAllowed(value) {
                          return allowedChars.test(value);
                      }

                      autocomplete({
                          input: document.getElementById('testinput'),
                          minLength: 1,
                          onSelect: function(item, inputfield) {
                              inputfield.value = item.label
                          },
                          fetch: function(text, callback) {
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
                          emptyMsg: "No artiste found",
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

                  error: function(resultat, statut, erreur) {
                      console.log(resultat);
                  }

              });
          }
      });*/
