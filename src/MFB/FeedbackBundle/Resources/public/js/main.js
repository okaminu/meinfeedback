
function setRating(rating){
    width = rating * 100 / 5;
    $('.stars_gold').attr('style', 'width:'+ width +'%');
}

function clickRating(rating){
    $('.stars_coords').unbind('mouseout');
    $('.stars_coords').removeAttr('onmouseout');
    $('.stars_gray').unbind('click');
    $('.stars_coords').css('display', 'none');
    setRating(rating);
    $('.rating').attr('value', rating);
    setTimeout(function () {
        $('.stars_gray').bind('click', function () {
            resetRating();
        }),
            500
    });
}

function resetRating(){
    currentRating = jQuery('.rating').attr('value', 0);
    jQuery('.stars_gray').bind('click');
    jQuery('.stars_blue').attr('style', 'width: 0%');
    jQuery('.type').html('');
    jQuery('.button_undo').css('display', 'none');
    jQuery('.stars_coords').css('display', 'block');
    jQuery('.stars_coords').bind('mouseout', function () {
        setRating(currentRating);
    });
}