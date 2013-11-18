$('.tip').each(function() { // Grab all elements with a title attribute,and set "this"
    $(this).qtip({ //
        content: {
            text: $(this).next(), // WILL work, because .each() sets "this" to refer to each element
            title: {
                text: $(this).attr('title'),
                button: 'Close' // Close button
            }
        },
        style: 'qtip-light qtip-shadow',
        show: 'click',
        hide: {
            event: 'click',
            inactive: 1500
        }
    });
});