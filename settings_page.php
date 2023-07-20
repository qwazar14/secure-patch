<?php
function secure_patch_settings_page() {
    $options = get_option('secure_patch_plugin', [
        'disable_xml_rpc' => 0,
        'disable_file_edit' => 0,
        'remove_wp_version' => 0,
        'enforce_strong_passwords' => 0,
        'disable_rest_api' => 0,
        'max_login_attempts' => 5,
        'lock_duration' => 60
    ]);

    ?>
    <div class="wrap">
        <h2><?php echo __('Secure Patch Plugin Settings', 'secure-patch-plugin'); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('secure_patch_plugin'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __('Disable XML-RPC', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="checkbox" id="disable_xml_rpc" name="secure_patch_plugin[disable_xml_rpc]" value="1" <?php checked(1, $options['disable_xml_rpc'], true); ?> />
                        <label for="disable_xml_rpc"><?php echo __('Disable XML-RPC', 'secure-patch-plugin'); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Disable File Edit', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="checkbox" id="disable_file_edit" name="secure_patch_plugin[disable_file_edit]" value="1" <?php checked(1, $options['disable_file_edit'], true); ?> />
                        <label for="disable_file_edit"><?php echo __('Disable File Edit', 'secure-patch-plugin'); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Remove WordPress Version', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="checkbox" id="remove_wp_version" name="secure_patch_plugin[remove_wp_version]" value="1" <?php checked(1, $options['remove_wp_version'], true); ?> />
                        <label for="remove_wp_version"><?php echo __('Remove WordPress Version', 'secure-patch-plugin'); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Enforce Strong Passwords', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="checkbox" id="enforce_strong_passwords" name="secure_patch_plugin[enforce_strong_passwords]" value="1" <?php checked(1, $options['enforce_strong_passwords'], true); ?> />
                        <label for="enforce_strong_passwords"><?php echo __('Enforce Strong Passwords', 'secure-patch-plugin'); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Disable REST API', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="checkbox" id="disable_rest_api" name="secure_patch_plugin[disable_rest_api]" value="1" <?php checked(1, $options['disable_rest_api'], true); ?> />
                        <label for="disable_rest_api"><?php echo __('Disable REST API', 'secure-patch-plugin'); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Max Login Attempts', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="number" id="max_login_attempts" name="secure_patch_plugin[max_login_attempts]" value="<?php echo $options['max_login_attempts']; ?>" min="1" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __('Lock Duration (seconds)', 'secure-patch-plugin'); ?></th>
                    <td>
                        <input type="number" id="lock_duration" name="secure_patch_plugin[lock_duration]" value="<?php echo $options['lock_duration']; ?>" min="1" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
