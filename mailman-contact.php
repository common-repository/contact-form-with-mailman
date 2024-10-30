<?php
/*
Plugin Name: Contact Form with Mailman
Description: Simple contact for with a check box to subscribe to a mailman mailing list
Version: 2.1.2
Author: Jeff Craft
Author URI: http://jeffcraft.ca
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Create Settings Page
add_action('admin_menu', 'cfwm_plugin_settings');
add_action('admin_init', 'cfwm_admin_init');

function cfwm_plugin_settings() {
	add_options_page('Contact Form with Mailman Settings', 'Mailman Settings', 'manage_options', 'contact-form-with-mailman', 'cfwm_display_settings');
}

function cfwm_admin_init() {
	register_setting('mailman-settings-group','cfwm_mailman_settings');
	add_settings_section('mailman-section','Mailman Settings','cfwm_mailman_section','contact-form-with-mailman');
	add_settings_field('list-name','Mailman List Name','cfwm_list_name','contact-form-with-mailman','mailman-section');
	add_settings_field('mailman-url','Mailman List URL','cfwm_mailman_url','contact-form-with-mailman','mailman-section');
	add_settings_field('list-password','Mailman List Password','cfwm_list_password','contact-form-with-mailman','mailman-section');
}

function cfwm_mailman_section() {
	echo '<p>Please fill in the following settings. All are required for this plugin to work correctly. If you need assistance please contact your mailing list provider.</p>';
	echo '<p>Add the short code [mailman_contact_form] to any page or post you would like the form to display on.</p>';
}

function cfwm_list_name() {
	$setting = get_option('cfwm_mailman_settings');
	echo '<input type="text" name="cfwm_mailman_settings[cfwm_list_name]" value="'.$setting['cfwm_list_name'].'" />';
}

function cfwm_mailman_url() {
	$setting = get_option('cfwm_mailman_settings');
	echo '<input type="text" name="cfwm_mailman_settings[cfwm_mailman_url]" value="'.$setting['cfwm_mailman_url'].'" />';
}

function cfwm_list_password() {
	$setting = get_option('cfwm_mailman_settings');
	echo '<input type="text" name="cfwm_mailman_settings[cfwm_list_password]" value="'.$setting['cfwm_list_password'].'" />';
}

function cfwm_display_settings() {
	?>
	<div class="wrap">
		<h2>Contact Form with Mailman Settings</h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'mailman-settings-group' ); ?>
			<?php do_settings_sections( 'contact-form-with-mailman' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// Prevent direct script access
if(!defined('WPINC')){ die(); }

function cfwm_html_form_code() {
	$tmp = cfwm_get_config();
	//print_r($tmp);
    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
    echo '<p>';
    echo 'Your Name (required) <br />';
    echo '<input type="text" name="cf-name" pattern="[a-zA-Z- ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Your Email (required) <br />';
    echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Subject (required) <br />';
    echo '<input type="text" name="cf-subject" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["cf-subject"] ) ? esc_attr( $_POST["cf-subject"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Subscribe to mailing list?<br />';
    echo '<input type="checkbox" name="cf-subscribe" />';
    echo '</p>';
    echo '<p>';
    echo 'Your Message (required) <br />';
    echo '<textarea rows="10" cols="35" name="cf-message">' . ( isset( $_POST["cf-message"] ) ? esc_attr( $_POST["cf-message"] ) : '' ) . '</textarea>';
    echo '</p>';
    echo '<p><input type="submit" name="cf-submitted" value="Send"/></p>';
    echo '</form>';
}

function cfwm_get_config() {
	$setting = get_option('cfwm_mailman_settings');
    $config['list_name'] = $setting['cfwm_list_name']; // eg. mylist
    $config['mailman_url'] = $setting['cfwm_mailman_url']; // eg. http://lists.domain.com/mailman/admin/
    $config['list_password'] = $setting['cfwm_list_password']; // Password you use to login to mailman admin
    
    return $config;
}

function cfwm_deliver_mail() {
    // if the submit button is clicked, send the email
    if ( isset( $_POST['cf-submitted'] ) ) {

        // sanitize form values
        $name    = sanitize_text_field( $_POST["cf-name"] );
        $email   = sanitize_email( $_POST["cf-email"] );
        $subject = sanitize_text_field( $_POST["cf-subject"] );
        $message = esc_textarea( $_POST["cf-message"] );
        
        if($_POST['cf-subscribe'] == 'on') {
            cfwm_subscribe_to_list($email);
        }

        // get the blog administrator's email address
        $to = get_option( 'admin_email' );

        $headers = "From: $name <$email>" . "\r\n";

        // If email has been process for sending, display a success message
        if ( wp_mail( $to, $subject, $message, $headers ) ) {
            echo '<div>';
            echo '<p>Thanks for contacting me, expect a response soon.</p>';
            echo '</div>';
        } else {
            echo 'wp_mail() was unable to send your email. Please check your Wordpress installation to verify it is able to send email.';
        }
    }
}

function cfwm_subscribe_to_list($email) {
    $config = cfwm_get_config();

    $path = '/members/add?subscribe_or_invite=0&send_welcome_msg_to_this_batch=0&notification_to_list_owner=0&subscribees_upload='.$email.'&adminpw='.$config['list_password'];
    $url = $config['mailman_url'] . $config['list_name'] . $path;
    $html = cfwm_get_data($url);
    if(preg_match('#<h5>Successfully subscribed:</h5>#i', $html)) {
        return true;
    } else {
        return false;
    }
}

function cfwm_cf_shortcode() {
    ob_start();
    cfwm_deliver_mail();
    cfwm_html_form_code();

    return ob_get_clean();
}

function cfwm_get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

add_shortcode( 'mailman_contact_form', 'cfwm_cf_shortcode' );

?>