( function () {
	'use strict';

	function getSettings() {
		return window.llrLoginRegister || {};
	}

	function handleSubmit( event ) {
		const form = event.target;

		if ( ! form.classList || ! form.classList.contains( 'llr-login-form__form' ) ) {
			return;
		}

		event.preventDefault();

		const wrapper = form.closest( '.llr-login-form' );
		const messageEl = wrapper ? wrapper.querySelector( '.llr-form-message' ) : null;
		const submitButton = form.querySelector( '.llr-login-form__submit' );
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
						messageEl.textContent = data.message || 'Login successful. Redirecting…';
						messageEl.classList.add( 'llr-success' );
					}

					window.setTimeout( function () {
						window.location.href = data.redirect_url || settings.homeUrl || '/';
					}, 800 );

					return;
				}

				if ( messageEl ) {
					messageEl.textContent = data.message || 'Login failed. Please try again.';
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

	document.addEventListener( 'submit', handleSubmit );
} )();
