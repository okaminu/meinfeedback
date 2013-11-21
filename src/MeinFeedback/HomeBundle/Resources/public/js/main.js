$(document).ready(function() {

// Lazy loading.
$("img.lazy").lazyload({ 
  // The image starts loading 200 px before it is in viewport
   threshold : 200,
   // Remove the line if you don`t need fade effect. 
   effect : "fadeIn", 
   // Change this for fade in speed
    effectspeed: 600,
   //  Hide spinner when loaded
   load : function(elements_left, settings) {
 $(".lazy-container").has(this).addClass('loaded');
 $(".loaded .spinner").remove();
// refresh bootstrap scrollspy, when image is loaded
 $('[data-spy="scroll"]').each(function () {
  var $spy = $(this).scrollspy('refresh')
});
      }
}); 

// Lightbox

$('.lightbox').magnificPopup({
  type:'image',
   disableOn: function() {
    // Detect here whether you want to show the popup
    // return true if you want
    if($(window).width() < 500) {
      return false;
    }
    return true; },
     preloader: true,
tLoading: 'Loading',

// Delay in milliseconds before popup is removed
	removalDelay: 300,
  mainClass: 'mfp-fade',
  callbacks: {
    open: function() {
      $('.navbar').fadeOut('slow');
    },
    close: function() {
     $('.navbar').fadeIn('slow');
    }
  }
});

// Lightbox video/maps

$(' .iframe-small').magnificPopup(popupSmallParams);
$(' .iframe').magnificPopup(popupFullParams);


// .scroll class for link scrolling.

 $('.scroll[href^="#"]').bind('click.smoothscroll',function (e) {
    e.preventDefault();
    var target = this.hash;
        $target = $(target);
    $('html, body').stop().animate({
        'scrollTop': $target.offset().top
    }, 900, 'swing', function () {
        window.location.hash = target;
      });

});

// .scroll class for link scrolling.
 
$('.collapse').on('show', function(){
    $(this).parent().find(".icon-plus").removeClass("icon-plus").addClass("icon-minus");
    $(this).parent().find(".accordion-heading").addClass("active");
}).on('hide', function(){
    $(this).parent().find(".icon-minus").removeClass("icon-minus").addClass("icon-plus");
    $(this).parent().find(".accordion-heading").removeClass("active");
});

// Close menu when clicked. Mobile view

$('#menu a').click(function (e) {
        
        $(this).tab('show');
        if ($('.btn-nav').is(":visible"))
          $('.btn-nav').click();
      });
});
