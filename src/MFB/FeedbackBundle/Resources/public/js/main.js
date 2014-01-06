
function setRating(rating, criteria){
    width = rating * 100 / 5;
    $('#' + criteria + ' .stars_gold').attr('style', 'width:'+ width +'%');
}

function clickRating(rating, criteria){
    $('#' + criteria + ' .stars_coords').unbind('mouseout');
    $('#' + criteria + ' .stars_coords').removeAttr('onmouseout');
    $('#' + criteria + ' .stars_gray').unbind('click');
    $('#' + criteria + ' .stars_coords').css('display', 'none');
    setRating(rating, criteria);
    $('#' + criteria + ' .rating').attr('value', rating);
    setTimeout(function () {
        $('#' + criteria + ' .stars_gray').bind('click', function () {
            resetRating(criteria);
        }),
            500
    });
}

function resetRating(criteria){
    currentRating = jQuery('#' + criteria + ' .rating').attr('value', 0);
    jQuery('#' + criteria + ' .stars_gray').bind('click');
    jQuery('#' + criteria + ' .stars_blue').attr('style', 'width: 0%');
    jQuery('#' + criteria + ' .type').html('');
    jQuery('#' + criteria + ' .button_undo').css('display', 'none');
    jQuery('#' + criteria + ' .stars_coords').css('display', 'block');
    jQuery('#' + criteria + ' .stars_coords').bind('mouseout', function () {
        setRating(currentRating);
    });
}
