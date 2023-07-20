<?php
/*
Plugin Name: Secure Patch Plugin
Description: A plugin to increase the security of your WordPress site.
Author: Maksym "Qwazar" Mezhyrytskyi
Version: 1.0.8
Author URI: https://github.com/qwazar14/
Plugin URI: https://github.com/qwazar14/secure-patch
*/

require_once plugin_dir_path(__FILE__) . 'settings_page.php';
require_once plugin_dir_path(__FILE__) . 'security_check_page.php';

class SecurePatchPlugin
{
    private $options;

    public function __construct()
    {
        $this->options = get_option('secure_patch_plugin', [
            'disable_xml_rpc' => 0,
            'disable_file_edit' => 0,
            'remove_wp_version' => 0,
            'enforce_strong_passwords' => 0,
            'disable_rest_api' => 0,
            'max_login_attempts' => 5,
            'lock_duration' => 60
        ]);

        if ($this->options['disable_xml_rpc']) {
            add_filter('xmlrpc_enabled', '__return_false');
        }

        if ($this->options['disable_file_edit']) {
            define('DISALLOW_FILE_EDIT', true);
        }

        if ($this->options['remove_wp_version']) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', '__return_false');
            add_filter('style_loader_src', [$this, 'remove_version_from_style_js'], 9999);
            add_filter('script_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        }

        if ($this->options['enforce_strong_passwords']) {
            add_filter('password_enforce_strong_password', [$this, 'enforce_strong_password']);
        }

        if ($this->options['disable_rest_api']) {
            add_filter('rest_authentication_errors', [$this, 'disable_rest_api']);
        }

        register_setting('secure_patch_plugin', 'secure_patch_plugin', [$this, 'validate_options']);
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('wp_login_failed', [$this, 'handle_failed_login']);
        add_filter('authenticate', [$this, 'authenticate_user'], 30, 3);
        add_filter('login_errors', [$this, 'modify_login_errors']);
        add_action('plugins_loaded', [$this, 'load_textdomain']);
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

    public function admin_menu()
    {
        add_options_page(
            'Secure Patch Plugin Settings',
            'Secure Patch Plugin',
            'manage_options',
            'secure-patch-plugin',
            'secure_patch_settings_page'
        );
    }

    public function validate_options($input)
    {
        $newinput['disable_xml_rpc'] = isset($input['disable_xml_rpc']) ? 1 : 0;
        $newinput['disable_file_edit'] = isset($input['disable_file_edit']) ? 1 : 0;
        $newinput['remove_wp_version'] = isset($input['remove_wp_version']) ? 1 : 0;
        $newinput['enforce_strong_passwords'] = isset($input['enforce_strong_passwords']) ? 1 : 0;
        $newinput['disable_rest_api'] = isset($input['disable_rest_api']) ? 1 : 0;
        $newinput['max_login_attempts'] = absint($input['max_login_attempts']);
        $newinput['lock_duration'] = absint($input['lock_duration']);
        return $newinput;
    }

    public function handle_failed_login($username)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $transient_name = 'secure_patch_failed_login_' . str_replace('.', '_', $ip);
        $failed_attempts = (int)get_transient($transient_name);

        if ($failed_attempts >= $this->options['max_login_attempts']) {
            return;
        }

        $failed_attempts++;

        if ($failed_attempts >= $this->options['max_login_attempts']) {
            $this->lock_ip($ip);
            return;
        }

        set_transient($transient_name, $failed_attempts, $this->options['lock_duration']);
    }

    public function lock_ip($ip)
    {
        set_transient('secure_patch_locked_ip_' . str_replace('.', '_', $ip), true, $this->options['lock_duration']);
    }

    public function is_ip_locked($ip)
    {
        return (bool)get_transient('secure_patch_locked_ip_' . str_replace('.', '_', $ip));
    }

    public function authenticate_user($user, $username, $password)
    {
        if ($this->is_ip_locked($_SERVER['REMOTE_ADDR'])) {
            return new WP_Error('authentication_failed', 'You have exceeded the maximum number of login attempts. Please try again later.');
        }
        return $user;
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('secure-patch-plugin', false, basename(dirname(__FILE__)) . '/languages/');
    }

    public function modify_login_errors()
    {
        return 'The entered login information is incorrect. Please try again.';
    }

}

new SecurePatchPlugin();