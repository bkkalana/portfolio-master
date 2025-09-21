<?php
/**
 * Plugin Name: Portfolio Master
 * Plugin URI: https://example.com/portfolio-master
 * Description: A comprehensive portfolio management plugin for WordPress with custom post types, taxonomies, and responsive grid display.
 * Version: 1.0.0
 * Author: Portfolio Master Team
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: portfolio-master
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PM_PLUGIN_FILE', __FILE__);
define('PM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PM_VERSION', '1.0.0');

/**
 * Main Portfolio Master Plugin Class
 */
class Portfolio_Master {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        $this->register_post_type();
        $this->register_taxonomy();
        $this->register_shortcode();
        $this->add_single_project_display();
        
        // Load text domain
        load_plugin_textdomain('portfolio-master', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Initialize admin-specific functionality
     */
    public function admin_init() {
        $this->add_admin_columns();
        $this->add_meta_boxes();
    }
    
    /**
     * Register Projects custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Projects', 'Post type general name', 'portfolio-master'),
            'singular_name'         => _x('Project', 'Post type singular name', 'portfolio-master'),
            'menu_name'             => _x('Projects', 'Admin Menu text', 'portfolio-master'),
            'name_admin_bar'        => _x('Project', 'Add New on Toolbar', 'portfolio-master'),
            'add_new'               => __('Add New', 'portfolio-master'),
            'add_new_item'          => __('Add New Project', 'portfolio-master'),
            'new_item'              => __('New Project', 'portfolio-master'),
            'edit_item'             => __('Edit Project', 'portfolio-master'),
            'view_item'             => __('View Project', 'portfolio-master'),
            'all_items'             => __('All Projects', 'portfolio-master'),
            'search_items'          => __('Search Projects', 'portfolio-master'),
            'parent_item_colon'     => __('Parent Projects:', 'portfolio-master'),
            'not_found'             => __('No projects found.', 'portfolio-master'),
            'not_found_in_trash'    => __('No projects found in Trash.', 'portfolio-master'),
            'featured_image'        => _x('Project Featured Image', 'Overrides the "Featured Image" phrase', 'portfolio-master'),
            'set_featured_image'    => _x('Set featured image', 'Overrides the "Set featured image" phrase', 'portfolio-master'),
            'remove_featured_image' => _x('Remove featured image', 'Overrides the "Remove featured image" phrase', 'portfolio-master'),
            'use_featured_image'    => _x('Use as featured image', 'Overrides the "Use as featured image" phrase', 'portfolio-master'),
            'archives'              => _x('Project archives', 'The post type archive label', 'portfolio-master'),
            'insert_into_item'      => _x('Insert into project', 'Overrides the "Insert into post" phrase', 'portfolio-master'),
            'uploaded_to_this_item' => _x('Uploaded to this project', 'Overrides the "Uploaded to this post" phrase', 'portfolio-master'),
            'filter_items_list'     => _x('Filter projects list', 'Screen reader text for the filter links', 'portfolio-master'),
            'items_list_navigation' => _x('Projects list navigation', 'Screen reader text for the pagination', 'portfolio-master'),
            'items_list'            => _x('Projects list', 'Screen reader text for the items list', 'portfolio-master'),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'show_in_admin_bar'  => true,
            'show_in_rest'       => true, // Gutenberg compatible
            'query_var'          => true,
            'rewrite'            => array('slug' => 'projects'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields', 'revisions'),
            'taxonomies'         => array('project_type'),
        );
        
        register_post_type('pm_project', $args);
    }
    
    /**
     * Register Project Type taxonomy
     */
    public function register_taxonomy() {
        $labels = array(
            'name'              => _x('Project Types', 'Taxonomy general name', 'portfolio-master'),
            'singular_name'     => _x('Project Type', 'Taxonomy singular name', 'portfolio-master'),
            'search_items'      => __('Search Project Types', 'portfolio-master'),
            'all_items'         => __('All Project Types', 'portfolio-master'),
            'parent_item'       => __('Parent Project Type', 'portfolio-master'),
            'parent_item_colon' => __('Parent Project Type:', 'portfolio-master'),
            'edit_item'         => __('Edit Project Type', 'portfolio-master'),
            'update_item'       => __('Update Project Type', 'portfolio-master'),
            'add_new_item'      => __('Add New Project Type', 'portfolio-master'),
            'new_item_name'     => __('New Project Type Name', 'portfolio-master'),
            'menu_name'         => __('Project Types', 'portfolio-master'),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true, // Gutenberg compatible
            'query_var'         => true,
            'rewrite'           => array('slug' => 'project-type'),
        );
        
        register_taxonomy('project_type', array('pm_project'), $args);
    }
    
    /**
     * Add custom columns to Projects admin list
     */
    public function add_admin_columns() {
        add_filter('manage_pm_project_posts_columns', array($this, 'add_project_columns'));
        add_action('manage_pm_project_posts_custom_column', array($this, 'display_project_columns'), 10, 2);
        add_filter('manage_edit-pm_project_sortable_columns', array($this, 'make_project_columns_sortable'));
        add_action('pre_get_posts', array($this, 'sort_project_columns'));
    }
    
    /**
     * Add custom columns to the Projects admin list
     */
    public function add_project_columns($columns) {
        $new_columns = array();
        
        // Add featured image column after checkbox
        $new_columns['cb'] = $columns['cb'];
        $new_columns['featured_image'] = __('Featured Image', 'portfolio-master');
        $new_columns['title'] = $columns['title'];
        $new_columns['project_type'] = __('Project Type', 'portfolio-master');
        $new_columns['client_name'] = __('Client', 'portfolio-master');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Display content for custom columns
     */
    public function display_project_columns($column, $post_id) {
        switch ($column) {
            case 'featured_image':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '<span class="dashicons dashicons-format-image" style="font-size: 20px; color: #ccc;"></span>';
                }
                break;
                
            case 'project_type':
                $terms = get_the_terms($post_id, 'project_type');
                if ($terms && !is_wp_error($terms)) {
                    $term_names = array();
                    foreach ($terms as $term) {
                        $term_names[] = $term->name;
                    }
                    echo esc_html(implode(', ', $term_names));
                } else {
                    echo '—';
                }
                break;
                
            case 'client_name':
                $client_name = get_post_meta($post_id, '_pm_client_name', true);
                echo $client_name ? esc_html($client_name) : '—';
                break;
        }
    }
    
    /**
     * Make custom columns sortable
     */
    public function make_project_columns_sortable($columns) {
        $columns['project_type'] = 'project_type';
        $columns['client_name'] = 'client_name';
        return $columns;
    }
    
    /**
     * Handle sorting for custom columns
     */
    public function sort_project_columns($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        if ('project_type' === $orderby) {
            $query->set('meta_key', '_pm_project_type');
            $query->set('orderby', 'meta_value');
        } elseif ('client_name' === $orderby) {
            $query->set('meta_key', '_pm_client_name');
            $query->set('orderby', 'meta_value');
        }
    }
    
    /**
     * Add meta boxes for Project Details
     */
    public function add_meta_boxes() {
        add_meta_box(
            'pm_project_details',
            __('Project Details', 'portfolio-master'),
            array($this, 'project_details_meta_box'),
            'pm_project',
            'normal',
            'high'
        );
        
        add_action('save_post', array($this, 'save_project_details'));
    }
    
    /**
     * Display Project Details meta box
     */
    public function project_details_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('pm_project_details_nonce', 'pm_project_details_nonce');
        
        // Get existing values
        $client_name = get_post_meta($post->ID, '_pm_client_name', true);
        $project_url = get_post_meta($post->ID, '_pm_project_url', true);
        $completion_date = get_post_meta($post->ID, '_pm_completion_date', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pm_client_name"><?php _e('Client Name', 'portfolio-master'); ?></label>
                </th>
                <td>
                    <input type="text" id="pm_client_name" name="pm_client_name" value="<?php echo esc_attr($client_name); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the client name for this project.', 'portfolio-master'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pm_project_url"><?php _e('Project URL', 'portfolio-master'); ?></label>
                </th>
                <td>
                    <input type="url" id="pm_project_url" name="pm_project_url" value="<?php echo esc_attr($project_url); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the project URL (if applicable).', 'portfolio-master'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pm_completion_date"><?php _e('Completion Date', 'portfolio-master'); ?></label>
                </th>
                <td>
                    <input type="date" id="pm_completion_date" name="pm_completion_date" value="<?php echo esc_attr($completion_date); ?>" class="regular-text" />
                    <p class="description"><?php _e('Select the project completion date.', 'portfolio-master'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save Project Details meta box data
     */
    public function save_project_details($post_id) {
        // Check if nonce is valid
        if (!isset($_POST['pm_project_details_nonce']) || !wp_verify_nonce($_POST['pm_project_details_nonce'], 'pm_project_details_nonce')) {
            return;
        }
        
        // Check if user has permission to edit the post
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check post type
        if (get_post_type($post_id) !== 'pm_project') {
            return;
        }
        
        // Save meta fields
        $fields = array('pm_client_name', 'pm_project_url', 'pm_completion_date');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Register shortcode
     */
    public function register_shortcode() {
        add_shortcode('portfolio_master', array($this, 'portfolio_master_shortcode'));
    }
    
    /**
     * Portfolio Master shortcode callback
     */
    public function portfolio_master_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 12,
            'project_type'   => '',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ), $atts);
        
        $args = array(
            'post_type'      => 'pm_project',
            'posts_per_page' => intval($atts['posts_per_page']),
            'post_status'    => 'publish',
            'orderby'        => sanitize_text_field($atts['orderby']),
            'order'          => sanitize_text_field($atts['order']),
        );
        
        if (!empty($atts['project_type'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'project_type',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($atts['project_type']),
                ),
            );
        }
        
        $projects = new WP_Query($args);
        
        if (!$projects->have_posts()) {
            return '<p>' . __('No projects found.', 'portfolio-master') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="pm-portfolio-grid">
            <?php while ($projects->have_posts()) : $projects->the_post(); ?>
                <div class="pm-portfolio-item">
                    <div class="pm-portfolio-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', array('class' => 'pm-featured-image')); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>" class="pm-no-image">
                                <span class="dashicons dashicons-format-image"></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="pm-portfolio-content">
                        <h3 class="pm-portfolio-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="pm-portfolio-meta">
                            <?php
                            $project_types = get_the_terms(get_the_ID(), 'project_type');
                            if ($project_types && !is_wp_error($project_types)) :
                            ?>
                                <span class="pm-project-type">
                                    <?php echo esc_html($project_types[0]->name); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php
                            $client_name = get_post_meta(get_the_ID(), '_pm_client_name', true);
                            if ($client_name) :
                            ?>
                                <span class="pm-client-name">
                                    <?php echo esc_html($client_name); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Add project details display to single project pages
     */
    public function add_single_project_display() {
        add_action('the_content', array($this, 'display_project_details'));
    }
    
    /**
     * Display project details on single project pages
     */
    public function display_project_details($content) {
        // Only display on single project pages
        if (!is_singular('pm_project') || !is_main_query()) {
            return $content;
        }
        
        global $post;
        
        // Get meta values
        $client_name = get_post_meta($post->ID, '_pm_client_name', true);
        $project_url = get_post_meta($post->ID, '_pm_project_url', true);
        $completion_date = get_post_meta($post->ID, '_pm_completion_date', true);
        
        // Get project types
        $project_types = get_the_terms($post->ID, 'project_type');
        
        // Only add details if we have some data to show
        if (empty($client_name) && empty($project_url) && empty($completion_date) && (empty($project_types) || is_wp_error($project_types))) {
            return $content;
        }
        
        $details_html = '<div class="pm-project-details">';
        $details_html .= '<h3>' . __('Project Details', 'portfolio-master') . '</h3>';
        $details_html .= '<div class="pm-details-grid">';
        
        // Client Name
        if (!empty($client_name)) {
            $details_html .= '<div class="pm-detail-item">';
            $details_html .= '<strong>' . __('Client:', 'portfolio-master') . '</strong> ';
            $details_html .= '<span class="pm-client-name">' . esc_html($client_name) . '</span>';
            $details_html .= '</div>';
        }
        
        // Project URL
        if (!empty($project_url)) {
            $details_html .= '<div class="pm-detail-item">';
            $details_html .= '<strong>' . __('Project URL:', 'portfolio-master') . '</strong> ';
            $details_html .= '<a href="' . esc_url($project_url) . '" target="_blank" rel="noopener noreferrer" class="pm-project-url">';
            $details_html .= esc_html($project_url) . ' <span class="dashicons dashicons-external"></span>';
            $details_html .= '</a>';
            $details_html .= '</div>';
        }
        
        // Completion Date
        if (!empty($completion_date)) {
            $formatted_date = date_i18n(get_option('date_format'), strtotime($completion_date));
            $details_html .= '<div class="pm-detail-item">';
            $details_html .= '<strong>' . __('Completion Date:', 'portfolio-master') . '</strong> ';
            $details_html .= '<span class="pm-completion-date">' . esc_html($formatted_date) . '</span>';
            $details_html .= '</div>';
        }
        
        // Project Types
        if (!empty($project_types) && !is_wp_error($project_types)) {
            $details_html .= '<div class="pm-detail-item">';
            $details_html .= '<strong>' . __('Project Type:', 'portfolio-master') . '</strong> ';
            $type_names = array();
            foreach ($project_types as $type) {
                $type_names[] = '<span class="pm-project-type">' . esc_html($type->name) . '</span>';
            }
            $details_html .= implode(', ', $type_names);
            $details_html .= '</div>';
        }
        
        $details_html .= '</div>'; // Close pm-details-grid
        $details_html .= '</div>'; // Close pm-project-details
        
        // Add CSS for the project details
        $details_html .= '<style>
            .pm-project-details {
                background: #f9f9f9;
                border: 1px solid #e1e1e1;
                border-radius: 8px;
                padding: 20px;
                margin: 30px 0;
                clear: both;
            }
            .pm-project-details h3 {
                margin: 0 0 15px 0;
                color: #333;
                font-size: 18px;
                border-bottom: 2px solid #0073aa;
                padding-bottom: 10px;
            }
            .pm-details-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
            }
            .pm-detail-item {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            .pm-detail-item strong {
                color: #555;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .pm-client-name {
                color: #0073aa;
                font-weight: 500;
            }
            .pm-project-url {
                color: #0073aa;
                text-decoration: none;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }
            .pm-project-url:hover {
                text-decoration: underline;
            }
            .pm-project-url .dashicons {
                font-size: 14px;
            }
            .pm-completion-date {
                color: #666;
                font-weight: 500;
            }
            .pm-project-type {
                background: #e3f2fd;
                color: #1976d2;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                display: inline-block;
                margin: 2px;
            }
            @media (max-width: 768px) {
                .pm-details-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>';
        
        return $content . $details_html;
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'pm-frontend-style',
            PM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            PM_VERSION
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        global $post_type;
        
        if ('pm_project' === $post_type) {
            wp_enqueue_style(
                'pm-admin-style',
                PM_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                PM_VERSION
            );
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $this->register_post_type();
        $this->register_taxonomy();
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
Portfolio_Master::get_instance();

// Add CSS styles inline for better performance
add_action('wp_head', 'pm_add_inline_styles');

/**
 * Add inline CSS styles for the portfolio grid
 */
function pm_add_inline_styles() {
    ?>
    <style type="text/css">
        .pm-portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 20px 0;
        }
        
        .pm-portfolio-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .pm-portfolio-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .pm-portfolio-image {
            position: relative;
            overflow: hidden;
        }
        
        .pm-portfolio-image img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .pm-portfolio-item:hover .pm-portfolio-image img {
            transform: scale(1.05);
        }
        
        .pm-no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 250px;
            background: #f5f5f5;
            color: #999;
            text-decoration: none;
        }
        
        .pm-no-image .dashicons {
            font-size: 48px;
        }
        
        .pm-portfolio-content {
            padding: 20px;
        }
        
        .pm-portfolio-title {
            margin: 0 0 15px 0;
            font-size: 18px;
            line-height: 1.4;
        }
        
        .pm-portfolio-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .pm-portfolio-title a:hover {
            color: #0073aa;
        }
        
        .pm-portfolio-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .pm-project-type,
        .pm-client-name {
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .pm-project-type {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .pm-client-name {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .pm-portfolio-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .pm-portfolio-content {
                padding: 15px;
            }
        }
    </style>
    <?php
}

// Add admin CSS styles
add_action('admin_head', 'pm_add_admin_styles');

/**
 * Add admin CSS styles
 */
function pm_add_admin_styles() {
    global $post_type;
    
    if ('pm_project' === $post_type) {
        ?>
        <style type="text/css">
            .column-featured_image {
                width: 60px;
            }
            
            .column-featured_image img {
                border-radius: 4px;
            }
            
            .column-project_type,
            .column-client_name {
                width: 150px;
            }
        </style>
        <?php
    }
}
