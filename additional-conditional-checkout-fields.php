
/**
 * Check if a specific coupon was used
 */
function sleepy_coupon_was_used() {
	// Add a list of coupons to check for here.
	$coupons = array( 'percent_coupon', 'free_shipping' );

	// Coupons Used
	$coupons_used = WC()->cart->applied_coupons;

	// If ones of our special coupons were used, return true.
	if ( in_array( $coupons, $coupons_used ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Conditionally add a checkout field based on Coupons used in the cart
 */
function sleepy_add_checkout_field( $fields ) {


	if ( ! sleepy_coupon_was_used() ) {
		unset( $fields['billing']['billing_company'] );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'sleepy_add_checkout_field' );


/**
 * Add the field to the checkout for free gift card address / name and email
 */
add_action( 'woocommerce_after_order_notes', 'sleepy_free_gift_checkout_fields' );

function sleepy_free_gift_checkout_fields( $checkout ) {

	echo '<div id="sleepy_free_gift_checkout_fields"><h2>' . __('Free Gift Shipping Information') . '</h2>';
	echo '<p>Fill Out The Following Information to receive your Free Gift!</p>';

	woocommerce_form_field( 'free_gift_name', array(
		'type'          => 'text',
		'class'         => array('sleepy-free-gift-form form-row-wide'),
		'label'         => __('Name of Free Gift Recipient'),
		'placeholder'   => __('Your Name'),
		'priority'     => 10,
	), $checkout->get_value( 'free_gift_name' ));


	woocommerce_form_field( 'free_gift_email', array(
		'type'          => 'email',
		'class'         => array('sleepy-free-gift-form form-row-wide'),
		'label'         => __('Email of Free Gift Recipient'),
		'placeholder'   => __('Your Email'),
		'priority'     => 20,
	), $checkout->get_value( 'free_gift_email' ));

	woocommerce_form_field( 'free_gift_address_1', array(
		'label'        => __( 'Street address', 'woocommerce' ),
		/* translators: use local order of street name and house number. */
		'placeholder'  => esc_attr__( 'House number and street name', 'woocommerce' ),
		'required'     => true,
		'class'        => array( 'form-row-wide', 'address-field' ),
		'autocomplete' => 'address-line1',
		'priority'     => 50,
	), $checkout->get_value( 'free_gift_address_1' ));

	woocommerce_form_field('free_gift_address_2', array(
		'placeholder'  => esc_attr__( 'Apartment, suite, unit etc. (optional)', 'woocommerce' ),
		'class'        => array( 'form-row-wide', 'address-field' ),
		'required'     => false,
		'autocomplete' => 'address-line2',
		'priority'     => 60,
	), $checkout->get_value( 'free_gift_address_2' ));

		woocommerce_form_field(	'free_gift_city', array(
		'label'        => __( 'Town / City', 'woocommerce' ),
		'required'     => true,
		'class'        => array( 'form-row-wide', 'address-field' ),
		'autocomplete' => 'address-level2',
		'priority'     => 70,
	),$checkout->get_value( 'free_gift_city' ));
		woocommerce_form_field(	'free_gift_state', array(
		'type'         => 'state',
		'label'        => __( 'State / County', 'woocommerce' ),
		'required'     => true,
		'class'        => array( 'form-row-wide', 'address-field' ),
		'validate'     => array( 'state' ),
		'autocomplete' => 'address-level1',
		'priority'     => 80,
	),$checkout->get_value( 'free_gift_state' ));
			woocommerce_form_field('free_gift_postcode', array(
		'label'        => __( 'Postcode / ZIP', 'woocommerce' ),
		'required'     => true,
		'class'        => array( 'form-row-wide', 'address-field' ),
		'validate'     => array( 'postcode' ),
		'autocomplete' => 'postal-code',
		'priority'     => 90,
	),$checkout->get_value( 'free_gift_postcode' ));
	echo '</div>';

}


/**
 * Process the checkout errors
 */
add_action('woocommerce_checkout_process', 'sleepy_custom_checkout_field_process');

function sleepy_custom_checkout_field_process() {
	// Check if set, if its not set add an error.
	if ( ! $_POST['free_gift_name'] )
		wc_add_notice( __( 'Please enter your name to receive your free gift' ), 'error' );
	if ( ! $_POST['free_gift_email'] )
		wc_add_notice( __( 'Please enter your email to receive your free gift' ), 'error' );
	if ( ! $_POST['free_gift_address_1'] )
		wc_add_notice( __( 'Please enter your address to receive your free gift' ), 'error' );
	if ( ! $_POST['free_gift_city'] )
		wc_add_notice( __( 'Please enter your city to receive your free gift' ), 'error' );
	if ( ! $_POST['free_gift_state'] )
		wc_add_notice( __( 'Please enter your state to receive your free gift' ), 'error' );
	if ( ! $_POST['free_gift_postcode'] )
		wc_add_notice( __( 'Please enter your zipcode to receive your free gift' ), 'error' );
}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

function sleepy_checkout_field_update_order_meta( $order_id ) {
	if ( ! empty( $_POST['free_gift_name'] ) ) {
		update_post_meta( $order_id, 'Free Gift Name', sanitize_text_field( $_POST['free_gift_name'] ) );
	}
	if ( ! empty( $_POST['free_gift_email'] ) ) {
		update_post_meta( $order_id, 'Free Gift Email', sanitize_text_field( $_POST['free_gift_email'] ) );
	}
	if ( ! empty( $_POST['free_gift_address_1'] ) ) {
		update_post_meta( $order_id, 'Free Gift Address 1', sanitize_text_field( $_POST['free_gift_address_1'] ) );
	}
	if ( ! empty( $_POST['free_gift_address_2'] ) ) {
		update_post_meta( $order_id, 'Free Gift Address 2', sanitize_text_field( $_POST['free_gift_address_2'] ) );
	}
	if ( ! empty( $_POST['free_gift_city'] ) ) {
		update_post_meta( $order_id, 'Free Gift City', sanitize_text_field( $_POST['free_gift_city'] ) );
	}
	if ( ! empty( $_POST['free_gift_state'] ) ) {
		update_post_meta( $order_id, 'Free Gift State', sanitize_text_field( $_POST['free_gift_state'] ) );
	}
	if ( ! empty( $_POST['free_gift_postcode'] ) ) {
		update_post_meta( $order_id, 'Free Gift Postcode', sanitize_text_field( $_POST['free_gift_postcode'] ) );
	}
}



/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'sleepy_checkout_field_display_admin_order_meta', 10, 1 );

function sleepy_checkout_field_display_admin_order_meta($order){
	echo '<p><strong>'.__('Free Gift Name').':</strong> ' . get_post_meta( $order->id, 'Free Gift Name', true ) . '</p>';
	echo '<p><strong>'.__('Free Gift Email').':</strong> ' . get_post_meta( $order->id, 'Free Gift Email', true ) . '</p>';
	echo '<p><strong>'.__('Free Gift Address 1').':</strong> ' . get_post_meta( $order->id, 'Free Gift Address 1', true ) . '</p>';
	echo '<p><strong>'.__('Free Gift Address 2').':</strong> ' . get_post_meta( $order->id, 'Free Gift Address 2', true ) . '</p>';
	echo '<p><strong>'.__('Free Gift City').':</strong> ' . get_post_meta( $order->id, 'Free Gift City', true ) . '</p>';
	echo '<p><strong>'.__('Free Gift State').':</strong> ' . get_post_meta( $order->id, 'Free Gift State', true ) . '</p>';
	echo '<p><strong>'.__('Free Gift Postcode').':</strong> ' . get_post_meta( $order->id, 'Free Gift Postcode', true ) . '</p>';
}


/* To use:
1. Add this snippet to your theme's functions.php file
2. Change the meta key names in the snippet
3. Create a custom field in the order post - e.g. key = "Tracking Code" value = abcdefg
4. When next updating the status, or during any other event which emails the user, they will see this field in their email
*/
add_filter('woocommerce_email_order_meta_keys', 'sleepy_order_meta_keys');

function sleepy_order_meta_keys( $keys ) {
	$keys[] = 'Free Gift Name'; // This will look for a custom field called 'Tracking Code' and add it to emails
	$keys[] = 'Free Gift Email'; // This will look for a custom field called 'Tracking Code' and add it to emails
	$keys[] = 'Free Gift Address 1'; // This will look for a custom field called 'Tracking Code' and add it to emails
	$keys[] = 'Free Gift Address 2'; // This will look for a custom field called 'Tracking Code' and add it to emails
	$keys[] = 'Free Gift City'; // This will look for a custom field called 'Tracking Code' and add it to emails
	$keys[] = 'Free Gift State'; // This will look for a custom field called 'Tracking Code' and add it to emails
    $keys[] = 'Free Gift Postcode'; // This will look for a custom field called 'Tracking Code' and add it to emails
	return $keys;
}
