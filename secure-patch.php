<?php
/*
Plugin Name: Secure Patch Plugin
Description: A plugin to increase the security of your WordPress site.
Author: Maksym "Qwazar" Mezhyrytskyi
Version: 1.0.1
Author URI: https://github.com/qwazar14/
*/

class SecurePatchPlugin
{
    public $is_404 = false;  // Initialized here

    public function __construct()
    {
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', [$this, 'remove_version_info']);
        add_filter('style_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        add_filter('script_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        add_action('login_enqueue_scripts', [$this, 'login_protect']);
        add_action('plugins_loaded', [$this, 'redirect_login_page']);
        add_filter('site_url', [$this, 'wplogin_filter'], 10, 3);
        add_action('template_redirect', [$this, 'do_404']);
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

    // Change the login URL
    public function login_protect()
    {
        error_log("Checking login_protect...");
        error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("GET: " . print_r($_GET, true));

        if (strpos($_SERVER['SCRIPT_NAME'], 'wp-login.php') !== false && $_SERVER['REQUEST_METHOD'] == 'GET' && (empty($_GET) || (!empty($_GET) && empty($_GET['action'])))) {
            if (isset($_GET['key']) && $_GET['key'] === SECURE_LOGIN_KEY) {
                return;
            } else {
                $this->is_404 = true;
            }
        }
    }

    public function redirect_login_page()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $path = ltrim($_SERVER['REQUEST_URI'], '/');
            if ($path == 'wp-login.php' || $path == 'wp-admin/' || $path == 'wp-admin') {
                $this->is_404 = true;
            }
        }
    }

    public function wplogin_filter($url, $path, $orig_scheme)
    {
        if ($path == 'wp-login.php') {
            $url = str_replace('wp-login.php', SECURE_LOGIN_SLUG . '?key=' . SECURE_LOGIN_KEY, $url);
        }
        return $url;
    }

    public function do_404()
    {
        if ($this->is_404) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
            include(get_query_template('404'));
            die();
        }
    }
}

new SecurePatchPlugin();

?>