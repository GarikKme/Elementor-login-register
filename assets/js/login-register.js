( function () {
	'use strict';

	const TRACKED_FORM_SELECTOR = '.llr-login-form__form, .llr-register-form__form';
	const FORM_WRAPPER_SELECTOR = '.llr-login-form, .llr-register-form';

	function getSettings() {
		return window.llrLoginRegister || {};
	}

	function handleSubmit( event ) {
		const form = event.target;

		if ( ! form.matches || ! form.matches( TRACKED_FORM_SELECTOR ) ) {
			return;
		}

		event.preventDefault();

		const wrapper = form.closest( FORM_WRAPPER_SELECTOR );
		const messageEl = wrapper ? wrapper.querySelector( '.llr-form-message' ) : null;
		const submitButton = form.querySelector( 'button[type="submit"]' );
		const settings = getSettings();
		const ajaxUrl = settings.ajaxUrl || '/wp-admin/admin-ajax.php';

		if ( messageEl ) {
			messageEl.textContent = '';
			messageEl.classList.remove( 'llr-error', 'llr-success' );
		}

		if ( submitButton ) {
			submitButton.disabled = true;
		}

		fetch( ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: new FormData( form ),
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( response ) {
				const data = response && response.data ? response.data : {};

				if ( response && response.success ) {
					if ( messageEl ) {
						messageEl.textContent = data.message || 'Success. Redirecting…';
						messageEl.classList.add( 'llr-success' );
					}

					window.setTimeout( function () {
						window.location.href = data.redirect_url || settings.homeUrl || '/';
					}, 800 );

					return;
				}

				if ( messageEl ) {
					messageEl.textContent = data.message || 'Request failed. Please try again.';
					messageEl.classList.add( 'llr-error' );
				}

				if ( submitButton ) {
					submitButton.disabled = false;
				}
			} )
			.catch( function () {
				if ( messageEl ) {
					messageEl.textContent = 'Something went wrong. Please try again.';
					messageEl.classList.add( 'llr-error' );
				}

				if ( submitButton ) {
					submitButton.disabled = false;
				}
			} );
	}

	function handleTabClick( event ) {
		const tabButton = event.target.closest( '.llr-tabs [data-tab]' );

		if ( ! tabButton ) {
			return;
		}

		const tabsEl = tabButton.closest( '.llr-tabs' );

		if ( ! tabsEl ) {
			return;
		}

		const targetTab = tabButton.getAttribute( 'data-tab' );

		tabsEl.querySelectorAll( '.llr-tabs__tab' ).forEach( function ( button ) {
			const isActive = button.getAttribute( 'data-tab' ) === targetTab;
			button.classList.toggle( 'llr-tabs__tab--active', isActive );
			button.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
		} );

		tabsEl.querySelectorAll( '.llr-tabs__panel' ).forEach( function ( panel ) {
			const isActive = panel.getAttribute( 'data-tab-panel' ) === targetTab;
			panel.classList.toggle( 'llr-tabs__panel--active', isActive );
			panel.toggleAttribute( 'hidden', ! isActive );
		} );

		tabsEl.setAttribute( 'data-active', targetTab );
	}

	function handlePasswordToggleClick( event ) {
		const toggleButton = event.target.closest( '.llr-password-toggle' );

		if ( ! toggleButton ) {
			return;
		}

		const wrapper = toggleButton.closest( '.llr-password-field' );
		const input = wrapper ? wrapper.querySelector( 'input' ) : null;

		if ( ! input ) {
			return;
		}

		const willShow = 'password' === input.type;

		input.type = willShow ? 'text' : 'password';
		toggleButton.setAttribute( 'aria-pressed', willShow ? 'true' : 'false' );
		toggleButton.setAttribute( 'aria-label', willShow ? 'Hide password' : 'Show password' );

		const showIcon = toggleButton.querySelector( '.llr-password-toggle__icon--show' );
		const hideIcon = toggleButton.querySelector( '.llr-password-toggle__icon--hide' );

		if ( showIcon ) {
			showIcon.toggleAttribute( 'hidden', willShow );
		}

		if ( hideIcon ) {
			hideIcon.toggleAttribute( 'hidden', ! willShow );
		}
	}

	document.addEventListener( 'submit', handleSubmit );
	document.addEventListener( 'click', handleTabClick );
	document.addEventListener( 'click', handlePasswordToggleClick );
} )();
