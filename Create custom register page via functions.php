//Create custom register page via functions.php
//https://www.businessbloomer.com/woocommerce-separate-login-registration/
//[custom_wc_reg_form]



/************** Redirect to acccount page after reset password **************/
function woocommerce_new_pass_redirect( $user ) {
        wp_redirect( get_permalink( get_page_by_path( 'login' ) ) );
        exit;
}

add_action( 'woocommerce_customer_reset_password', 'woocommerce_new_pass_redirect' );




/************** Custom user register form **************/
//https://www.businessbloomer.com/woocommerce-separate-login-registration/
// 1. Add Shortcode [custom_wc_reg_form] for Woo Registration form on the create account page
add_shortcode( 'custom_wc_reg_form', 'custom_separate_registration_form' );

function custom_separate_registration_form() {
   if ( is_admin() ) return;
   if ( is_user_logged_in() ) return 'You are already logged in.';
   ob_start();

   // NOTE: THE FOLLOWING <FORM></FORM> IS COPIED FROM woocommerce\templates\myaccount\form-login.php
   // IF WOOCOMMERCE RELEASES AN UPDATE TO THAT TEMPLATE, YOU MUST CHANGE THIS ACCORDINGLY

   do_action( 'woocommerce_before_customer_login_form' );

   ?>
      <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

         <?php do_action( 'woocommerce_register_form_start' ); ?>

         <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
               <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
               <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

         <?php endif; ?>

                 <p class="form-row form-row-first">
                        <label for="reg_billing_first_name"><?php _e( 'First Name', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
                </p>

                <p class="form-row form-row-last">
                        <label for="reg_billing_last_name"><?php _e( 'Last Name', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
                </p>

                <div class="clear"></div>


         <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_email"><?php esc_html_e( 'Email', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
         </p>

         <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
               <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
               <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
            </p>

         <?php else : ?>

            <p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

         <?php endif; ?>

                 <div class="g-recaptcha" data-theme="light"></div>

         <?php do_action( 'woocommerce_register_form' ); ?>

         <p class="woocommerce-FormRow form-row">
            <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Create an Account', 'woocommerce' ); ?></button>
         </p>

         <?php do_action( 'woocommerce_register_form_end' ); ?>

      </form>

   <?php

   return ob_get_clean();
}


// 2. Validate firstname and lastname fields
add_filter( 'woocommerce_registration_errors', 'bbloomer_validate_name_fields', 10, 3 );
function bbloomer_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
    return $errors;
}

// 3. Save firstname and lastname fields
add_action( 'woocommerce_created_customer', 'bbloomer_save_name_fields' );
function bbloomer_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
    }

}

// 4. Redirect to home url after registration
add_filter('woocommerce_registration_redirect', 'custom_registration_redirect');
function custom_registration_redirect( $redirection_url ) {
    $redirection_url = get_home_url() . '/my-account/';
    return $redirection_url;
}


// 5. Separate WooCommerce Login (Shortcode [custom_wc_login_form])
add_shortcode( 'custom_wc_login_form', 'custom_separate_login_form' );
function custom_separate_login_form() {
   if ( is_admin() ) return;
   if ( is_user_logged_in() ) return 'You are already logged in.';
   ob_start();
   woocommerce_login_form( array( 'redirect' => get_home_url() . '/my-account/' ) );
   return ob_get_clean();
}


