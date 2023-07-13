<?php
/**
* Template Name: Custom Login Page
*/
?>
<div class="login-page">
    <div class="uk-column-1-2 uk-column-1-1@s page-row">
       <div class="uk-slider-container-offset page-slider" uk-slider>
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
                <!-- <img src="<?php //echo plugin_dir_url( dirname( __FILE__ ) ). 'admin/images/logo.svg'?>" alt=""> -->
                <img src="admin/images/logo.svg" alt="">
            </div>
            <form id="custom-login-form" action="<?php echo esc_url(wp_login_url()); ?>" method="post">
                <p>
                    <label for="user_login"></label>
                    <input type="text" name="log" id="user_login" class="input" value="" size="20" placeholder="<?php esc_html_e('Username'); ?>"/>
                </p>
                <p>
                    <label for="user_pass"></label>
                    <input type="password" name="pwd" id="user_pass" class="input" value="" size="20" placeholder="<?php esc_html_e('Password'); ?>"/>
                </p>
                <p class="submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php esc_attr_e('Log In'); ?>" />
                </p>
            </form>
        </div>
    </div>
</div>