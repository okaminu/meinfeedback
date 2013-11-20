$('.tip').each(function() {
    bottomLeft = {
            my: 'top right',
            at: 'bottom left'
                };

    bottomRight = {
        my: 'top left',
        at: 'bottom right'
    };
    boxPosition = bottomRight;

    if($(this).hasClass('positionBottomLeft')){
        boxPosition = bottomLeft
    }


    $(this).qtip({ //
        content: {
            text: $(this).next(), // WILL work, because .each() sets "this" to refer to each element
            title: {
                text: $(this).attr('title'),
                button: 'Close' // Close button
            }
        },
        position: boxPosition,
        style: 'qtip-light qtip-shadow',
        show: 'click',
        hide: {
            event: 'click'
        }
    });
});