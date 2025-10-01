<?php
/**
 * Uninstall file for DinoPack for Elementor
 *
 * This file is executed when the plugin is uninstalled.
 * It cleans up any data that the plugin may have created.
 *
 * @package DinoPack
 * @since 1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clean up any plugin-specific data
// Note: Since this is a free plugin, we don't store user data
// But we can clean up any cached data or temporary files

// Clear any cached data
wp_cache_flush();

// Remove plugin-specific options that are actually used
delete_option( 'dinopack_settings' ); // Used in settings page

// Note: We don't remove user-created content as it belongs to the user
// Any widgets or templates created with DinoPack will remain in the database
// This is standard practice for WordPress plugins
