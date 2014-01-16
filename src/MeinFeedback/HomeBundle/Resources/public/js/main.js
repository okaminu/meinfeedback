$(document).ready(function() {

$('.redirectUrl').val(window.parent.document.URL);

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

$('#pickerText').colpick({
    onChange:function(hsb,hex,rgb,fromSetColor) {
        $('.textColor').val(hex);
    },
    color: $('.textColor').val()
});

$('#pickerBackground').colpick({
    onChange:function(hsb,hex,rgb,fromSetColor) {
        $('.backgroundColor').val(hex);
    },
    color: $('.backgroundColor').val()
});

$('.colpick_submit').click(function(){
    $('.colpick_full').addClass('hide');
    $('.widgetColorForm').submit();
});
$('#pickerText, #pickerBackground').click(function(){
    $('.colpick_full').removeClass('hide');
});


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

$(function() {
    $( "#sortable" ).sortable({
        placeholder: "ui-sortable-placeholder",
        axis: 'y',
        stop: function (event, ui) {
            $.post(initial_vars.sort_feedback_uri, { item_order_str: $(this).sortable("serialize") });
            //var data = $(this).sortable('toArray').toString();
//            var data = $(this).sortable('serialize', { key: "sort", attribute: "class" });
//            $.ajax({
//                data: data,
//                type: 'POST',
//                url: initial_vars.sort_feedback_uri
//            });
        }
    });
});



