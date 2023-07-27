<?php
/**
* Template Name: Custom Forgot Password Page
*/
?>
<div class="login-page">
    <div class="uk-column-1-2 uk-column-1-1@s page-row">
       <div class="uk-slider-container-offset page-slider" uk-slider style="background-image : url(<?php echo (get_option('ss_background_image') != null)?get_option('ss_background_image'): plugin_dir_url( dirname( __FILE__ ) ). 'admin/images/login-cover-banner.png'?>);">
            <div class="uk-position-relative uk-visible-toggle uk-light inner-page-slider" tabindex="-1">
                <ul class="uk-slider-items uk-child-width-1@s uk-grid">
                    <?php load_template(dirname(__FILE__) . '/custom-rss-feed.php');?>
                </ul>
                <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>
            </div>
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin page-navigation"></ul>
        </div>
        <div class="page-form">
            <div class="form-logo">
                <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ). 'admin/images/logo.svg'?>" alt="">
            </div>
            <div class="login-error">
            <?php 
             if (isset($_GET['login_error'])) {
                switch ($_GET['login_error']) {
                    case 'empty_username':
                        $error = 'Please enter your username.';
                        break;
                    case 'empty_password':
                        $error = 'Please enter your password.';
                        break;
                    case 'invalid_username':
                    case 'invalid_email':
                        $error = 'Invalid username or email.';
                        break;
                    case 'incorrect_password':
                        $error = 'Incorrect password.';
                        break;
                }
            }
            
            if (isset($_GET['login_error'])) { $error_message = $_GET['login_error']; echo '<p class="login-error">' . esc_html($error_message) . '</p>';} ?>
            </div>
            <form method="post" action="<?php echo esc_url(network_site_url('wp-login.php?action=lostpassword', 'login_post')); ?>" id="custom-login-form">
                <!-- Your custom form fields go here -->
                <p>
                    <label for="user_login"><?php _e('Username or Email'); ?></label>
                    <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" />
                </p>
                <?php do_action('lostpassword_form'); ?>
                <p class="submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php esc_attr_e('Reset Password'); ?>" />
                </p>
            </form>
            <a href="<?php echo site_url( 'wp-login.php' ) ;?>" class="link-btn">Back to Login</a>
        </div>
    </div>
</div>