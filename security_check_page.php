<?php
function security_check_page() {
    $xml_rpc_disabled = is_xmlrpc_disabled();
    $file_edit_disabled = is_file_edit_disabled();
    $wp_version_removed = is_wp_version_removed();
    $strong_passwords_enforced = is_strong_passwords_enforced();
    $rest_api_disabled = is_rest_api_disabled();

    echo '<div class="wrap">';
    echo '<h1>Security Check</h1>';
    echo '<p>XML-RPC Disabled: ' . ($xml_rpc_disabled ? 'Yes' : 'No') . '</p>';
    echo '<p>File Edit Disabled: ' . ($file_edit_disabled ? 'Yes' : 'No') . '</p>';
    echo '<p>WordPress Version Removed: ' . ($wp_version_removed ? 'Yes' : 'No') . '</p>';
    echo '<p>Strong Passwords Enforced: ' . ($strong_passwords_enforced ? 'Yes' : 'No') . '</p>';
    echo '<p>REST API Disabled: ' . ($rest_api_disabled ? 'Yes' : 'No') . '</p>';
    echo '</div>';
}

function is_xmlrpc_disabled() {
    // Check if XML-RPC is disabled
    return !apply_filters('xmlrpc_enabled', true);
}

function is_file_edit_disabled() {
    // Check if file editing is disabled
    return defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT;
}

function is_wp_version_removed() {
    // Check if WordPress version is removed
    $version = apply_filters('the_generator', get_bloginfo('version'));
    return empty($version);
}

function is_strong_passwords_enforced() {
    // Check if strong passwords are enforced
    // Here we are assuming that the enforcement applies to all users, which might not be the case
    // as the Secure Patch Plugin only enforces strong passwords for users who can publish posts
    $user = wp_get_current_user();
    return has_filter('password_enforce_strong_password', 'enforce_strong_password') && apply_filters('password_enforce_strong_password', $user);
}

function is_rest_api_disabled() {
    // Check if the REST API is disabled for not logged in users
    // Here we are assuming that the restriction applies to all not logged in users
    return has_filter('rest_authentication_errors', 'disable_rest_api') && apply_filters('rest_authentication_errors', !is_user_logged_in());
}