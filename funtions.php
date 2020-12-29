<?php

// Add a second field for confirmation password in the registration form in my account page
function woocommerce_registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
	global $woocommerce;
	extract( $_POST );
	if ( strcmp( $password, $password2 ) !== 0 ) {
		return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
	}
	return $reg_errors;
}
add_filter('woocommerce_registration_errors', 'woocommerce_registration_errors_validation', 10, 3);

// Add the HTML for the field
function woocommerce_register_form_password_repeat() {
	?>
	<p class="form-row form-row-wide">
		
		<input type="password" class="input-text" name="password2" id="reg_password2" placeholder="Confirm Password" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
	</p>
	<?php
}
add_action( 'woocommerce_register_form', 'woocommerce_register_form_password_repeat' );


// Add a second field for confirmation password in the registration field in the checkout page form
add_filter( 'woocommerce_checkout_fields', 'wc_add_confirm_password_checkout', 10, 1 );
function wc_add_confirm_password_checkout( $checkout_fields ) {
    if ( get_option( 'woocommerce_registration_generate_password' ) == 'no' ) {
        $checkout_fields['account']['account_password2'] = array(
                'type'              => 'password',
                'label'             => __( 'Confirm password', 'woocommerce' ),
                'required'          => true,
                'placeholder'       => _x( 'Confirm Password', 'placeholder', 'woocommerce' )
        );
    }

    return $checkout_fields;
}

// Check if the password are the same
add_action( 'woocommerce_after_checkout_validation', 'wc_check_confirm_password_matches_checkout', 10, 2 );
function wc_check_confirm_password_matches_checkout( $posted ) {
    $checkout = WC()->checkout;
    if ( ! is_user_logged_in() && ( $checkout->must_create_account || ! empty( $posted['createaccount'] ) ) ) {
        if ( strcmp( $posted['account_password'], $posted['account_password2'] ) !== 0 ) {
            wc_add_notice( __( 'Passwords do not match.', 'woocommerce' ), 'error' );
        }
    }
}
