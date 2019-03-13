var serviceAddress ="index.php";
var topImageContainer = "#topImage";

var allPictures =[];
var dayPictures = [];
var days = [];
var currentDay = 0;
var currentPicture = 0;

function displayFirstPicture(){
  $.getJSON( serviceAddress, { action: "getFirstPicture" } )
    .done(function( json ) {
      console.log( "JSON FirstPicture: " + json.name );
      $("<img />").attr("src", json.name);
      $(topImageContainer).attr("src",json.name);
      $(topImageContainer).fadeIn("slow");
    })
    .fail(function( jqxhr, textStatus, error ) {
      var err = textStatus + ", " + error;
      console.log( "Request Failed: " + err );
  });
}

function getAvailableDays(){
    $.getJSON( serviceAddress, { action: "getAvailableDays" } )
       .done(function( json ) {
      console.log( "JSON getAvailableDays: ");           
        $.each(json, function (key, val) {
            days.push(key);
            console.log("day: "+key+" pictures: "+val);
        });     
        console.log("Days count: "+days.length);
       })
       .fail(function( jqxhr, textStatus, error ) {
         var err = textStatus + ", " + error;
         console.log( "Request Failed: " + err );
     });    
 }
 

function initTilacam(){
    displayFirstPicture();
    getAvailableDays();
}