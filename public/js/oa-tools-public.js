(function( $ ) {
	'use strict';

	var $positions = $('.oaldr-positions');
	if ( $positions.length ) {
		$('.oaldr-positions').mixItUp({
			selectors: {
				target: '.oaldr-position'
			},
			load: {
				sort: 'default:desc'
			}
		});
	}

})( jQuery );
