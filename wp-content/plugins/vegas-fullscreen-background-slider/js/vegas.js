jQuery(function() {
    jQuery('span.vegasRemoteL').click(function() { jQuery.vegas('previous'); });
		jQuery('span.vegasRemoteR').click(function() { jQuery.vegas('next'); });
		jQuery('span.vegasRemoteP').click(function() { 
	    jQuery(this).toggleClass('pause');
	    if (jQuery(this).hasClass('pause')) { jQuery.vegas('pause'); } else { jQuery.vegas('slideshow'); }
    });
	jQuery('span.vegasRemoteF').click(function() {
	    jQuery('div#page').toggleClass('vegasHidden');
	    if (jQuery('div#page').hasClass('vegasHidden')) {
				jQuery('div#page').fadeTo(500, .01);
				jQuery(this).attr('title', 'Вернуть сайт'); 
			} else {
            jQuery('div#page').fadeTo(500, 1);
				jQuery(this).attr('title', 'Насладиться слайдшоу');
			}
	});

	jQuery('span.vegasRemoteSound').click(function() {
		jQuery(this).toggleClass('pause');
		if (jQuery(this).hasClass('pause')) {
			jQuery('audio#vegassound')[0].pause();
			wpCookies.set( 'areyouready', 0, 30 * 24 * 60 * 60 );
		} else {
			jQuery('audio#vegassound')[0].play();
			wpCookies.set( 'areyouready', 1, 30 * 24 * 60 * 60 );
		}
	});

	var areyouready = wpCookies.get('areyouready');
	if ( (typeof(areyouready)=="undefined") || (areyouready==1) ){
		jQuery('span.vegasRemoteSound').trigger('click');
	}



});