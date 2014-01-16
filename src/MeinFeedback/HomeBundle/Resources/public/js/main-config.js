popupSmallParams = {
    type:'iframe',
    mainClass : 'mfp-fad-small',
    disableOn: function() {
        if($(window).width() < 500) {
            return false;
        }
        return true; },
    preloader: true,
    callbacks: {
        open: function() {
            $('.navbar').fadeOut('slow');
            resizeIframe();

        },
        close: function() {
            $('.navbar').fadeIn('slow');
        }
    }
};

popupFullParams = {
    type:'iframe',
    mainClass : 'mfp-fad',
    disableOn: function() {
        if($(window).width() < 500) {
            return false;
        }
        return true; },
    preloader: true,
    callbacks: {
        open: function() {
            $('.navbar').fadeOut('slow');
        },
        close: function() {
            $('.navbar').fadeIn('slow');
        }
    }
};


function resizeIframe(){
    $('.mfp-content iframe').on('load', function(){
        height = $(this).contents().find('body').height();
//        height+=100;
        $('.mfp-content').css('height', height + 'px');
        $(this).contents().find('body').css('margin-top', '-120px');
    });
}