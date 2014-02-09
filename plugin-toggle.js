(function( window, undefined ) {
	'use strict';

	var document = window.document,
		container, menuItem, setMaxHeight, toolbarHeight;

	setMaxHeight = function() {
		container.style.maxHeight = window.innerHeight - toolbarHeight + 'px';
	};

	window.addEventListener( 'load', function() {
		toolbarHeight = document.getElementById( 'wpadminbar' ).clientHeight;
		menuItem = document.getElementById( 'wp-admin-bar-plugin-toggle' );
		container = menuItem.querySelector( '.ab-sub-wrapper' );

		setMaxHeight();
		window.addEventListener( 'resize', setMaxHeight );
	});

})( this );
