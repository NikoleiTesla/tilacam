var serviceAddress ="main.php";
var topImageContainer = "#topImage";
var bottomImageContainer = "#bottomImage";

var allPictures = [];
var dayPictures = [];
var days = [];
var currentDay = 0;
var currentPicture = 0;
var pictureDelay=1500;

var started=false;
var globalSlider = 0;
var timer = null;
var firstImage;
var picturesWidth=1920;
var picturesHeight=1080;

var imageBuffer = [];

var opc=0;

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
    var isStarted = started;
    if(isStarted)
        stop();
    dayPictures = [];
    imageBuffer = [];   
    var day = days[currentDay];
    $.getJSON( serviceAddress, { action: "getPicturesForDay",day: day } )
       .done(function( json ) {
      console.log( "JSON getPicturesForDay: "+day);
        $.each(json, function (key, val) {
            dayPictures.push(val);
            console.log("day picture: "+val.name);
        });     
        console.log("Day Picture count: "+dayPictures.length);
        updateSlider();
        if(isStarted)
            start();        
       })
       .fail(function( jqxhr, textStatus, error ) {
         var err = textStatus + ", " + error;
         console.log( "Request Failed: " + err );
     });      
}

function tick() {
    start();        // restart the timer
};

function start() {  // use a one-off timer
    playSlider();    
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

    $('#timeSlider').val(currentPicture+1).change();    
     displayImage();
    currentPicture++;
}

function displayImage(){
    if(opc > 0){
        if(opc %2 === 0){
            $(bottomImageContainer).attr('src',dayPictures[currentPicture].name);
            $(bottomImageContainer).addClass('fadein');
        } else  {
            $(topImageContainer).attr('src',dayPictures[currentPicture].name);
            $(bottomImageContainer).removeClass('fadein');     
        }
    }
    
    console.log("Play Picture "+currentPicture+" "+dayPictures[currentPicture].name);
 
    opc++;
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

function togglePlay(){
    if(started){
        $("#playPause").text("play_arrow");
        stop();
    } else {
        $("#playPause").text("pause");        
        start();
    }
}


function initTilacam(){
    displayFirstPicture();
    getAvailableDays();
    initSlider();
}

function updateSlider(){
  console.log('update Slider');
  console.log(' max value: '+dayPictures.length);  
  var inputRange = $('#timeSlider');
  inputRange.attr("max",dayPictures.length);
  $('#timeSlider').val(1).change();
  currentImage = 0;
  displayImage();
  $('#timeSlider').rangeslider('update',true);          
}     
     
function initSlider(){     

    $('#timeSlider').rangeslider({
      // Feature detection the default is `true`.
        // Set this to `false` if you want to use
        // the polyfill also in Browsers which support
        // the native <input type="range"> element.
        polyfill: false,

        // Default CSS classes
        rangeClass: 'rangeslider',
        disabledClass: 'rangeslider--disabled',
        horizontalClass: 'rangeslider--horizontal',
        fillClass: 'rangeslider__fill',
        handleClass: 'rangeslider__handle',

        // Callback function
        onInit: function() {
          console.log("init slider");
          $rangeEl = this.$range;
           // add value label to handle
          var $handle = $rangeEl.find('.rangeslider__handle');
          var handleValue = '<div class="rangeslider__handle__value"></div>';
          $handle.append(handleValue);
        },

        // Callback function
        onSlide: function(position, value) {
          if(started){
            stop();
            started = true;
          }
          var $handle = this.$range.find('.rangeslider__handle__value');
          var picture = dayPictures[value-1];
          var timeText = picture.hour+':'+picture.minute;
          $handle.text(timeText);
          var lastPicture = dayPictures[dayPictures.length-1];
          var totalText = lastPicture.hour+':'+lastPicture.minute;
          var pictureDate = dayPictures[currentPicture].formatedDateTime;
          var pd = new Date(pictureDate);
          var dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
          var dateText = pd.toLocaleDateString("de-AT",dateOptions);
          $("#pictureInfo").text(dateText+' '+timeText +' / '+totalText);
        },

        // Callback function
        onSlideEnd: function(position, value) {
            currentPicture = value-1;
            displayImage();
            if(started)
               start();        
        }
    });
}

function toggleFullScreen() {
  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      document.documentElement.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}

//Resize container to picture

window.addEventListener("resize", fitPicture);

function fitPicture() {
  var originalRatio = picturesWidth / picturesHeight;  
 // console.log("Orig Picture x"+picturesWidth+" y "+picturesHeight+" ratio "+originalRatio);    
  var windowWidth = window.innerWidth;
  var windowHeight = window.innerHeight;
 //  console.log("Viewport width Picture x"+windowWidth+" y "+windowHeight);  
  var newWidth;
  var newHeight;
  if(windowWidth >= windowHeight*originalRatio){
      newHeight = windowHeight;
      newWidth = Math.floor(windowHeight*originalRatio);      
  } else {
      newWidth = windowWidth;
      newHeight = Math.floor(windowWidth/originalRatio);          
  }
    
  $(topImageContainer).css("height",newHeight);
  $(topImageContainer).css("width",newWidth);
  $(bottomImageContainer).css("height",newHeight);
  $(bottomImageContainer).css("width",newWidth);
    
  $("#sizeDiv").css("height",newHeight);
  $("#sizeDiv").css("width",newWidth);
  
  
 // console.log("picture resized to: x"+newWidth+" y "+newHeight);
 }