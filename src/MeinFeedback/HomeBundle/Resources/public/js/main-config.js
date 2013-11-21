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