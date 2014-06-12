var RESTtester = Class.$extend({

  __init__: function(){
    this.createGUI();
  },

  createGUI: function(){
    // Bygg upp ett formulär och en tom div för resultat
    main = $('<div class="main">').appendTo('body');
    this.form = $('<form/>').appendTo(main);
    this.resultDiv = $('<div/>').appendTo(main);
    $(
      '<label for="method">Metod:</label>' +
      '<select id="method">' +
      '<option value="GET">GET</option>' +
      '<option value="POST">POST</option>' +
      '<option value="PUT">PUT</option>' +
      '<option value="DELETE">DELETE</option>' +
      '</select>' +
      '<label for="url">URL (t.ex. product, product/1, user, user/1):</label>' +
      '<input id="url" type="text">' +
      '<label for="data">Data att skicka (JSON):</label>' +
      '<textarea id="data"></textarea>' +
      '<input class="submit" type="submit" value="Skicka">'
    ).appendTo(this.form);

    // När formuläret "skickas"
    // hindra sidomladdning, samla ihop data från det
    // och gör en request mot vårt restAPI...
    $(this.form).submit(function(e){
      // Hindra sidomladdning
      e.preventDefault();
      // Försök att tolka om "halvbra" JSON till giltig
      var data;
      try {
        eval("data="+me.form.find('#data').val());
      }
      catch(ee){}
      // Gör en request
      me.restRequest(
        me.form.find('#url').val(),
        me.form.find('#method').val(),
        // Skicka ingen data om vi gör en GET
        me.form.find('#method').val()  == "GET" ?
          undefined : data
      );
    });
    me = this;
  },

  restRequest: function(url,method,data){
    // Set method to GET if not set
    method = method || "GET";

    $.ajax({

      // Vår URL
      url: "restapi/" + url,
      
      // Hur vi vill anropa URL:en
      // (GET, POST, PUT, DELETE)
      type:method,
      
      // Stäng av jQuery:s inbyggda
      // automatiska omvandling av data
      // till formulär-encoding...
      processData: false,
      
      // Data vi vill skicka
      // normalt sett som ett objekt
      // men nu när vi har stängt av processData
      // använder vi "rå" text
      
      // JSON.stringifty gör om javascript objekt
      // till en sträng json
      data: JSON.stringify(data),
      
      // Hur vi vill ta emot data
      // (om vi ska försöka omvandla text till json eller inte)
      // så antingen text eller json
      dataType: "json",
      
      // Vad vi vill göra (callback function)
      // när vi tar emot datan
      success:function(data){
        // i det här fallet skriva ut den i vårt API
        me.resultDiv.html("URL: restapi/" + url+ ", method: " + method +
          '<pre>'+JSON.stringify(data,"","\t")+'</pre>');
      },

      // Vid ett fel
      error:function(data){
         me.resultDiv.html("URL: restapi/" + url+ ", method: " + method +
          '<pre class="error">'+JSON.stringify(data,"","\t")+'</pre>');
      }

    });

    me = this;
    return true;

  }

});

// Vänta på DOM:en och skapa sedan en instans av RESTtester
$(function(){
  var test = new RESTtester();
});