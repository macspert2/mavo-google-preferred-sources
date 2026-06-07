( function () {
	'use strict';

	function init() {
		document.querySelectorAll( '.gps-info' ).forEach( function ( btn ) {
			var tip = document.getElementById( btn.getAttribute( 'aria-controls' ) );
			if ( ! tip ) return;

			btn.addEventListener( 'click', function ( e ) {
				e.stopPropagation();
				var open = ! tip.hidden;
				closeAll();
				if ( ! open ) {
					tip.hidden = false;
					btn.setAttribute( 'aria-expanded', 'true' );
				}
			} );
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				closeAll();
			}
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! e.target.closest( '.gps-wrap' ) ) {
				closeAll();
			}
		} );
	}

	function closeAll() {
		document.querySelectorAll( '.gps-tip' ).forEach( function ( tip ) {
			tip.hidden = true;
		} );
		document.querySelectorAll( '.gps-info' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
