function displayFirstPicture(){
  $.getJSON( "index.php", { action: "getFirstPicture" } )
    .done(function( json ) {
      console.log( "JSON Data: " + json.name );
      $("<img />").attr("src", json.name);
      $("#topImage").attr("src",json.name);
      $("#topImage").fadeIn("slow");
    })
    .fail(function( jqxhr, textStatus, error ) {
      var err = textStatus + ", " + error;
      console.log( "Request Failed: " + err );
  });
}
