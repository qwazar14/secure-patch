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
            <!-- Add other options here -->
        </table>
        <?php submit_button(); ?>
    </form>
</div>
