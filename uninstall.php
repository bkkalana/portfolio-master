<?php
/**
 * Uninstall file for Portfolio Master plugin
 * 
 * This file is executed when the plugin is uninstalled (deleted) from WordPress.
 * It cleans up any data that the plugin has created.
 * 
 * @package Portfolio_Master
 * @version 1.0.0
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has permission to uninstall plugins
if (!current_user_can('delete_plugins')) {
    exit;
}

// Clean up plugin data
pm_cleanup_plugin_data();

/**
 * Clean up all plugin data
 */
function pm_cleanup_plugin_data() {
    global $wpdb;
    
    // Delete all projects
    $projects = get_posts(array(
        'post_type'      => 'pm_project',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids'
    ));
    
    foreach ($projects as $project_id) {
        wp_delete_post($project_id, true);
    }
    
    // Delete all project types
    $project_types = get_terms(array(
        'taxonomy'   => 'project_type',
        'hide_empty' => false,
        'fields'     => 'ids'
    ));
    
    if (!is_wp_error($project_types)) {
        foreach ($project_types as $term_id) {
            wp_delete_term($term_id, 'project_type');
        }
    }
    
    // Delete plugin options (if any were created)
    delete_option('pm_plugin_version');
    delete_option('pm_plugin_settings');
    
    // Clean up any transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_pm_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_pm_%'");
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
