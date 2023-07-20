<?php
/*
Plugin Name: Secure Patch Plugin
Description: A plugin to increase the security of your WordPress site.
Author: Maksym "Qwazar" Mezhyrytskyi
Version: 1.0.3
Author URI: https://github.com/qwazar14/
Plugin URI: https://github.com/qwazar14/secure-patch
*/

class SecurePatchPlugin
{
    public function __construct()
    {
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', [$this, 'remove_version_info']);
        add_filter('style_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        add_filter('script_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        add_filter('xmlrpc_enabled', '__return_false');
        define('DISALLOW_FILE_EDIT', true);
        add_filter('password_enforce_strong_password', [$this, 'enforce_strong_password']);
        add_filter('rest_authentication_errors', [$this, 'disable_rest_api']);
    }

    public function remove_version_info()
    {
        return '';
    }

    public function remove_version_from_style_js($src)
    {
        if (strpos($src, 'ver=' . get_bloginfo('version'))) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }

    public function enforce_strong_password($user)
    {
        $enforce = false;
        if (user_can($user, 'publish_posts')) {
            $enforce = true;
        }
        return $enforce;
    }

    public function disable_rest_api($result)
    {
        if (!empty($result)) {
            return $result;
        }
        if (!is_user_logged_in()) {
            return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
        }
        return $result;
    }
}

new SecurePatchPlugin();
