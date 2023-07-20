<?php
/*
Plugin Name: Secure Patch Plugin
Description: A plugin to increase the security of your WordPress site.
Author: Maksym "Qwazar" Mezhyrytskyi
Version: 1.0.4
Author URI: https://github.com/qwazar14/
Plugin URI: https://github.com/qwazar14/secure-patch
*/

class SecurePatchPlugin
{
    private $options;

    public function __construct()
    {
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', [$this, 'remove_version_info']);
        add_filter('style_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        add_filter('script_loader_src', [$this, 'remove_version_from_style_js'], 9999);
        add_filter('password_enforce_strong_password', [$this, 'enforce_strong_password']);
        add_filter('rest_authentication_errors', [$this, 'disable_rest_api']);

        $this->options = get_option('secure_patch_plugin', [
            'disable_xml_rpc' => 0,
            'disable_file_edit' => 0
        ]);

        if ($this->options['disable_xml_rpc']) {
            add_filter('xmlrpc_enabled', '__return_false');
        }

        if ($this->options['disable_file_edit']) {
            define('DISALLOW_FILE_EDIT', true);
        }

        register_setting('secure_patch_plugin', 'secure_patch_plugin', [$this, 'validate_options']);
        add_action('admin_menu', [$this, 'admin_menu']);
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

    public function admin_menu()
    {
        add_options_page(
            'Secure Patch Plugin Settings',
            'Secure Patch Plugin',
            'manage_options',
            'secure-patch-plugin',
            [$this, 'settings_page']
        );
    }

    public function settings_page()
    {
        ?>
        <div class="wrap">
            <h2>Secure Patch Plugin Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('secure_patch_plugin'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Disable XML-RPC</th>
                        <td>
                            <input type="checkbox" id="disable_xml_rpc" name="secure_patch_plugin[disable_xml_rpc]"
                                   value="1" <?php checked(1, $this->options['disable_xml_rpc'], true); ?> />
                            <label for="disable_xml_rpc">Disable XML-RPC</label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Disable File Edit</th>
                        <td>
                            <input type="checkbox" id="disable_file_edit" name="secure_patch_plugin[disable_file_edit]"
                                   value="1" <?php checked(1, $this->options['disable_file_edit'], true); ?> />
                            <label for="disable_file_edit">Disable File Edit</label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function validate_options($input)
    {
        $newinput['disable_xml_rpc'] = isset($input['disable_xml_rpc']) ? 1 : 0;
        $newinput['disable_file_edit'] = isset($input['disable_file_edit']) ? 1 : 0;
        return $newinput;
    }
}

new SecurePatchPlugin();