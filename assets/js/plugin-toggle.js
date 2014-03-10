(function( window, undefined ) {
	'use strict';

	var document = window.document,
		container, menuItem, setMaxHeight, toolbar, toolbarHeight;

	setMaxHeight = function() {
		container.style.maxHeight = window.innerHeight - toolbarHeight + 'px';
	};

	window.addEventListener( 'load', function() {
		toolbar = document.getElementById( 'wpadminbar' );
		if ( ! toolbar ) {
			return;
		}

		toolbarHeight = toolbar.clientHeight;
		menuItem = document.getElementById( 'wp-admin-bar-plugin-toggle' );
		container = menuItem.querySelector( '.ab-sub-wrapper' );

		setMaxHeight();
		window.addEventListener( 'resize', setMaxHeight );
	});

})( this );
