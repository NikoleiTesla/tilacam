var serviceAddress ="index.php";
var topImageContainer = "#topImage";

var allPictures = [];
var dayPictures = [];
var days = [];
var currentDay = 0;
var currentPicture = 0;
var pictureDelay=1000;

var started=false;
var globalSlider = 0;
var timer = null;
var firstImage;
var picturesWidth;
var picturesHeight;

function displayFirstPicture(){
  $.getJSON( serviceAddress, { action: "getFirstPicture" } )
    .done(function( json ) {
      console.log( "JSON FirstPicture: " + json.name );
      firstImage = new Image();
      firstImage.src = json.name;
      picturesHeight = firstImage.naturalHeight;
      picturesWidth = firstImage.naturalWidth;
      fitPicture();
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
        currentDay = days.length -1;
        getPicturesForCurrentDay();
       })
       .fail(function( jqxhr, textStatus, error ) {
         var err = textStatus + ", " + error;
         console.log( "Request Failed: " + err );
     });    
 }

function getPicturesForCurrentDay(){
    dayPictures = [];
    var day = days[currentDay];
    $.getJSON( serviceAddress, { action: "getPicturesForDay",day: day } )
       .done(function( json ) {
      console.log( "JSON getPicturesForDay: "+day);
      console.log( " data: "+json);
        $.each(json, function (key, val) {
            dayPictures.push(val);
            console.log("day picture: "+val.name);
        });     
        console.log("Day Picture count: "+dayPictures.length);
       })
       .fail(function( jqxhr, textStatus, error ) {
         var err = textStatus + ", " + error;
         console.log( "Request Failed: " + err );
     });      
}

function tick() {
    playSlider();
    start();        // restart the timer
};

function start() {  // use a one-off timer
    timer = setTimeout(tick, pictureDelay);
    started = true;
};

function stop() {
    clearTimeout(timer);
    started = false;
};

function playSlider(){
    if(currentPicture > dayPictures.length-1){
        changeDay();
    }
    if(dayPictures[currentPicture] === undefined)
        return;
    
    console.log("Play Picture "+currentPicture+" "+dayPictures[currentPicture].name);
    $(topImageContainer).attr('src',dayPictures[currentPicture].name);
    currentPicture++;
}

function changeDay(){
    currentDay++;
    if(currentDay > days.length-1)
        currentDay = 0;  
    currentPicture = 0;
    getPicturesForCurrentDay();   
}

function tilacamPlay(){
    start();
}

function tilacamStop(){
    stop();
}

function initTilacam(){
    displayFirstPicture();
    getAvailableDays();
}

//Resize container to picture

window.addEventListener("resize", fitPicture);

function fitPicture() {
  var originalRatio = picturesWidth / picturesHeight;  
  console.log("Orig Picture x"+picturesWidth+" y "+picturesHeight+" ratio "+originalRatio);    
  var windowWidth = window.innerWidth;
  var windowHeight = window.innerHeight;
   console.log("Viewport width Picture x"+windowWidth+" y "+windowHeight);  
  var newWidth;
  var newHeight;
  if(windowWidth >= windowHeight*originalRatio){
      newHeight = windowHeight;
      newWidth = Math.floor(windowHeight*originalRatio);      
  } else {
      newWidth = windowWidth;
      newHeight = Math.floor(windowWidth/originalRatio);          
  }
    
  $("#topImage").css("height",newHeight);
  $("#topImage").css("width",newWidth);
  $("#sizeDiv").css("height",newHeight);
  $("#sizeDiv").css("width",newWidth);
  
  
  console.log("picture resized to: x"+newWidth+" y "+newHeight);
 }