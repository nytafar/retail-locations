<?php
/**
 * Main Retail Locations class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Retail_Locations {

    private static $instance = null;
    
    public $post_type = 'retail_location';
    public $post_slug = 'location';

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'init', array( $this, 'register_blocks' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        add_action( 'init', array( $this, 'maybe_flush_rewrite' ) );
        
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor' ) );
        
        add_action( 'wp_ajax_retail_locations_get_stores', array( $this, 'ajax_get_stores' ) );
        add_action( 'wp_ajax_nopriv_retail_locations_get_stores', array( $this, 'ajax_get_stores' ) );
        
        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'admin_init', array( $this, 'handle_export_request' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post_' . $this->post_type, array( $this, 'save_meta' ) );
            
            add_action( 'location_area_add_form_fields', array( $this, 'area_add_fields' ) );
            add_action( 'location_area_edit_form_fields', array( $this, 'area_edit_fields' ) );
            add_action( 'created_location_area', array( $this, 'save_area_meta' ) );
            add_action( 'edited_location_area', array( $this, 'save_area_meta' ) );
        }
    }

    public function maybe_flush_rewrite() {
        if ( get_option( 'retail_locations_flush_rewrite' ) ) {
            flush_rewrite_rules();
            delete_option( 'retail_locations_flush_rewrite' );
        }
    }

    public function register_post_type() {
        $slug = get_option( 'retail_locations_slug', 'location' );
        $this->post_slug = $slug ?: 'location';

        $labels = array(
            'name'               => __( 'Retail Locations', 'retail-locations' ),
            'singular_name'      => __( 'Location', 'retail-locations' ),
            'menu_name'          => __( 'Retail Locations', 'retail-locations' ),
            'add_new'            => __( 'Add New', 'retail-locations' ),
            'add_new_item'       => __( 'Add New Location', 'retail-locations' ),
            'edit_item'          => __( 'Edit Location', 'retail-locations' ),
            'new_item'           => __( 'New Location', 'retail-locations' ),
            'view_item'          => __( 'View Location', 'retail-locations' ),
            'all_items'          => __( 'All Locations', 'retail-locations' ),
            'search_items'       => __( 'Search Locations', 'retail-locations' ),
            'not_found'          => __( 'No locations found.', 'retail-locations' ),
            'not_found_in_trash' => __( 'No locations found in Trash.', 'retail-locations' ),
        );

        register_post_type( $this->post_type, array(
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-location',
            'menu_position'       => 20,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'rewrite'             => array( 'slug' => $this->post_slug, 'with_front' => false ),
        ));
    }

    public function register_taxonomies() {
        register_taxonomy( 'location_category', $this->post_type, array(
            'labels' => array(
                'name'          => __( 'Categories', 'retail-locations' ),
                'singular_name' => __( 'Category', 'retail-locations' ),
                'search_items'  => __( 'Search Categories', 'retail-locations' ),
                'all_items'     => __( 'All Categories', 'retail-locations' ),
                'edit_item'     => __( 'Edit Category', 'retail-locations' ),
                'add_new_item'  => __( 'Add New Category', 'retail-locations' ),
            ),
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'location-category' ),
        ));

        register_taxonomy( 'location_area', $this->post_type, array(
            'labels' => array(
                'name'          => __( 'Areas', 'retail-locations' ),
                'singular_name' => __( 'Area', 'retail-locations' ),
                'search_items'  => __( 'Search Areas', 'retail-locations' ),
                'all_items'     => __( 'All Areas', 'retail-locations' ),
                'edit_item'     => __( 'Edit Area', 'retail-locations' ),
                'add_new_item'  => __( 'Add New Area', 'retail-locations' ),
            ),
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'location-area' ),
        ));
    }

    public function register_blocks() {
        register_block_type( 'retail-locations/map', array(
            'attributes' => array(
                'category'       => array( 'type' => 'string', 'default' => '' ),
                'width'          => array( 'type' => 'string', 'default' => '100%' ),
                'height'         => array( 'type' => 'string', 'default' => '500px' ),
                'map_controls'   => array( 'type' => 'string', 'default' => 'yes' ),
                'scrollwheel'    => array( 'type' => 'string', 'default' => 'no' ),
                'mobile_draggable' => array( 'type' => 'string', 'default' => 'no' ),
                'sticky'         => array( 'type' => 'string', 'default' => 'no' ),
            ),
            'render_callback' => array( $this, 'render_map' ),
        ));

        register_block_type( 'retail-locations/stores', array(
            'attributes' => array(
                'category'            => array( 'type' => 'string', 'default' => '' ),
                'posts_per_page'      => array( 'type' => 'string', 'default' => '-1' ),
                'layout'              => array( 'type' => 'string', 'default' => 'fullwidth' ),
                'show_hours'          => array( 'type' => 'string', 'default' => 'yes' ),
                'show_contact'        => array( 'type' => 'string', 'default' => 'yes' ),
                'show_description'    => array( 'type' => 'string', 'default' => 'yes' ),
                'show_image'          => array( 'type' => 'string', 'default' => 'yes' ),
                'group_by_category'   => array( 'type' => 'string', 'default' => 'no' ),
                'group_by_area'       => array( 'type' => 'string', 'default' => 'no' ),
                'collapsible'         => array( 'type' => 'string', 'default' => 'no' ),
                'exclusive_accordion' => array( 'type' => 'string', 'default' => 'no' ),
            ),
            'render_callback' => array( $this, 'render_stores' ),
        ));
    }

    public function register_shortcodes() {
        add_shortcode( 'retail_locations_map', array( $this, 'render_map' ) );
        add_shortcode( 'retail_locations', array( $this, 'render_stores' ) );
    }

    public function enqueue_frontend() {
        $api_key = get_option( 'retail_locations_api_key', '' );
        if ( empty( $api_key ) ) return;

        $js_file = RETAIL_LOCATIONS_DIR . 'assets/js/frontend.js';
        $css_file = RETAIL_LOCATIONS_DIR . 'assets/css/frontend.css';

        wp_enqueue_script(
            'google-maps',
            'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places',
            array(),
            null,
            true
        );

        wp_enqueue_script(
            'retail-locations',
            RETAIL_LOCATIONS_URI . 'assets/js/frontend.js',
            array( 'jquery', 'google-maps' ),
            file_exists( $js_file ) ? filemtime( $js_file ) : RETAIL_LOCATIONS_VERSION,
            true
        );

        wp_localize_script( 'retail-locations', 'retailLocations', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'apiKey'  => $api_key,
        ));

        wp_enqueue_style(
            'retail-locations',
            RETAIL_LOCATIONS_URI . 'assets/css/frontend.css',
            array(),
            file_exists( $css_file ) ? filemtime( $css_file ) : RETAIL_LOCATIONS_VERSION
        );
    }

    public function enqueue_block_editor() {
        $js_file = RETAIL_LOCATIONS_DIR . 'assets/js/blocks.js';
        $css_file = RETAIL_LOCATIONS_DIR . 'assets/css/blocks.css';
        
        wp_enqueue_script(
            'retail-locations-blocks',
            RETAIL_LOCATIONS_URI . 'assets/js/blocks.js',
            array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ),
            file_exists( $js_file ) ? filemtime( $js_file ) : RETAIL_LOCATIONS_VERSION,
            true
        );

        wp_enqueue_style(
            'retail-locations-blocks',
            RETAIL_LOCATIONS_URI . 'assets/css/blocks.css',
            array(),
            file_exists( $css_file ) ? filemtime( $css_file ) : RETAIL_LOCATIONS_VERSION
        );
    }

    public function enqueue_admin( $hook ) {
        global $post_type;
        if ( $post_type !== $this->post_type ) return;

        $api_key = get_option( 'retail_locations_api_key', '' );
        if ( ! empty( $api_key ) ) {
            wp_enqueue_script(
                'google-maps',
                'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places',
                array(),
                null,
                true
            );
        }

        $js_file = RETAIL_LOCATIONS_DIR . 'assets/js/admin.js';
        $css_file = RETAIL_LOCATIONS_DIR . 'assets/css/admin.css';

        wp_enqueue_script(
            'retail-locations-admin',
            RETAIL_LOCATIONS_URI . 'assets/js/admin.js',
            array( 'jquery' ),
            file_exists( $js_file ) ? filemtime( $js_file ) : RETAIL_LOCATIONS_VERSION,
            true
        );

        wp_enqueue_style(
            'retail-locations-admin',
            RETAIL_LOCATIONS_URI . 'assets/css/admin.css',
            array(),
            file_exists( $css_file ) ? filemtime( $css_file ) : RETAIL_LOCATIONS_VERSION
        );
    }

    public function admin_menu() {
        add_submenu_page(
            'edit.php?post_type=' . $this->post_type,
            __( 'Settings', 'retail-locations' ),
            __( 'Settings', 'retail-locations' ),
            'manage_options',
            'retail-locations-settings',
            array( $this, 'settings_page' )
        );
        
        add_submenu_page(
            'edit.php?post_type=' . $this->post_type,
            __( 'Export Taxonomies', 'retail-locations' ),
            __( 'Export', 'retail-locations' ),
            'manage_options',
            'retail-locations-export',
            array( $this, 'export_page' )
        );
    }

    public function register_settings() {
        register_setting( 'retail_locations', 'retail_locations_api_key' );
        register_setting( 'retail_locations', 'retail_locations_slug' );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Retail Locations Settings', 'retail-locations' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'retail_locations' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="retail_locations_api_key"><?php _e( 'Google Maps API Key', 'retail-locations' ); ?></label></th>
                        <td>
                            <input type="text" id="retail_locations_api_key" name="retail_locations_api_key" value="<?php echo esc_attr( get_option( 'retail_locations_api_key', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Enter your Google Maps API key.', 'retail-locations' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="retail_locations_slug"><?php _e( 'URL Slug', 'retail-locations' ); ?></label></th>
                        <td>
                            <input type="text" id="retail_locations_slug" name="retail_locations_slug" value="<?php echo esc_attr( get_option( 'retail_locations_slug', 'location' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'The URL slug for locations (default: location).', 'retail-locations' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function handle_export_request() {
        // Handle export action early, before any HTML output
        if ( isset( $_POST['retail_locations_export'] ) && check_admin_referer( 'retail_locations_export_taxonomies' ) ) {
            $this->export_taxonomies();
            exit;
        }
    }

    public function export_page() {
        // Handle import action
        if ( isset( $_POST['retail_locations_import'] ) && check_admin_referer( 'retail_locations_import_taxonomies' ) ) {
            $import_result = $this->import_taxonomies();
        }
        
        ?>
        <div class="wrap">
            <h1><?php _e( 'Export Taxonomies', 'retail-locations' ); ?></h1>
            <p><?php _e( 'Export your location categories and areas to a JSON file for backup or migration to another site.', 'retail-locations' ); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'retail_locations_export_taxonomies' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Export Data', 'retail-locations' ); ?></th>
                        <td>
                            <p><?php _e( 'This will export:', 'retail-locations' ); ?></p>
                            <ul style="list-style: disc; margin-left: 20px;">
                                <li><?php _e( 'All location categories with hierarchy', 'retail-locations' ); ?></li>
                                <li><?php _e( 'All location areas with coordinates and zoom levels', 'retail-locations' ); ?></li>
                            </ul>
                            <p class="description"><?php _e( 'Note: This exports taxonomy terms only. Use WordPress\'s built-in export for location posts.', 'retail-locations' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button( __( 'Download Export File', 'retail-locations' ), 'primary', 'retail_locations_export' ); ?>
            </form>
            
            <hr>
            
            <h2><?php _e( 'Import Taxonomies', 'retail-locations' ); ?></h2>
            
            <?php if ( isset( $import_result ) ) : ?>
                <?php if ( $import_result['success'] ) : ?>
                    <div class="notice notice-success">
                        <p><strong><?php _e( 'Import successful!', 'retail-locations' ); ?></strong></p>
                        <ul style="list-style: disc; margin-left: 20px;">
                            <li><?php printf( __( 'Categories imported: %d', 'retail-locations' ), $import_result['categories'] ); ?></li>
                            <li><?php printf( __( 'Areas imported: %d', 'retail-locations' ), $import_result['areas'] ); ?></li>
                        </ul>
                    </div>
                <?php else : ?>
                    <div class="notice notice-error">
                        <p><strong><?php _e( 'Import failed:', 'retail-locations' ); ?></strong> <?php echo esc_html( $import_result['error'] ); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <p><?php _e( 'Upload a JSON file exported from another Retail Locations installation.', 'retail-locations' ); ?></p>
            
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field( 'retail_locations_import_taxonomies' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="import_file"><?php _e( 'Import File', 'retail-locations' ); ?></label></th>
                        <td>
                            <input type="file" name="import_file" id="import_file" accept=".json" required />
                            <p class="description"><?php _e( 'Select a JSON file exported from Retail Locations.', 'retail-locations' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button( __( 'Import Taxonomies', 'retail-locations' ), 'secondary', 'retail_locations_import' ); ?>
            </form>
        </div>
        <?php
    }

    private function import_taxonomies() {
        $result = array( 'success' => false, 'error' => '', 'categories' => 0, 'areas' => 0 );
        
        // Check file upload
        if ( ! isset( $_FILES['import_file'] ) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK ) {
            $result['error'] = __( 'No file uploaded or upload error occurred.', 'retail-locations' );
            return $result;
        }
        
        // Read file contents
        $json_content = file_get_contents( $_FILES['import_file']['tmp_name'] );
        $import_data = json_decode( $json_content, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $result['error'] = __( 'Invalid JSON file.', 'retail-locations' );
            return $result;
        }
        
        // Validate data structure
        if ( ! isset( $import_data['categories'] ) || ! isset( $import_data['areas'] ) ) {
            $result['error'] = __( 'Invalid export file format.', 'retail-locations' );
            return $result;
        }
        
        // Import categories
        $parent_map = array(); // Map old parent IDs to new ones
        
        foreach ( $import_data['categories'] as $category ) {
            $args = array(
                'slug' => $category['slug'],
                'description' => $category['description'],
            );
            
            // Handle parent relationship
            if ( ! empty( $category['parent'] ) && isset( $parent_map[ $category['parent'] ] ) ) {
                $args['parent'] = $parent_map[ $category['parent'] ];
            }
            
            $term = wp_insert_term( $category['name'], 'location_category', $args );
            
            if ( ! is_wp_error( $term ) ) {
                $parent_map[ $category['term_id'] ] = $term['term_id'];
                $result['categories']++;
            }
        }
        
        // Import areas with meta
        $area_parent_map = array();
        
        foreach ( $import_data['areas'] as $area ) {
            $args = array(
                'slug' => $area['slug'],
                'description' => $area['description'],
            );
            
            // Handle parent relationship
            if ( ! empty( $area['parent'] ) && isset( $area_parent_map[ $area['parent'] ] ) ) {
                $args['parent'] = $area_parent_map[ $area['parent'] ];
            }
            
            $term = wp_insert_term( $area['name'], 'location_area', $args );
            
            if ( ! is_wp_error( $term ) ) {
                $area_parent_map[ $area['term_id'] ] = $term['term_id'];
                
                // Import meta data
                if ( isset( $area['meta'] ) ) {
                    if ( ! empty( $area['meta']['area_lat'] ) ) {
                        update_term_meta( $term['term_id'], 'area_lat', $area['meta']['area_lat'] );
                    }
                    if ( ! empty( $area['meta']['area_lng'] ) ) {
                        update_term_meta( $term['term_id'], 'area_lng', $area['meta']['area_lng'] );
                    }
                    if ( ! empty( $area['meta']['area_zoom'] ) ) {
                        update_term_meta( $term['term_id'], 'area_zoom', $area['meta']['area_zoom'] );
                    }
                }
                
                $result['areas']++;
            }
        }
        
        $result['success'] = true;
        return $result;
    }

    private function export_taxonomies() {
        $export_data = array(
            'version' => RETAIL_LOCATIONS_VERSION,
            'export_date' => current_time( 'mysql' ),
            'site_url' => get_site_url(),
            'categories' => array(),
            'areas' => array(),
        );
        
        // Export categories
        $categories = get_terms( array(
            'taxonomy' => 'location_category',
            'hide_empty' => false,
        ) );
        
        if ( ! is_wp_error( $categories ) ) {
            foreach ( $categories as $term ) {
                $export_data['categories'][] = array(
                    'term_id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'description' => $term->description,
                    'parent' => $term->parent,
                    'count' => $term->count,
                );
            }
        }
        
        // Export areas with meta
        $areas = get_terms( array(
            'taxonomy' => 'location_area',
            'hide_empty' => false,
        ) );
        
        if ( ! is_wp_error( $areas ) ) {
            foreach ( $areas as $term ) {
                $export_data['areas'][] = array(
                    'term_id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'description' => $term->description,
                    'parent' => $term->parent,
                    'count' => $term->count,
                    'meta' => array(
                        'area_lat' => get_term_meta( $term->term_id, 'area_lat', true ),
                        'area_lng' => get_term_meta( $term->term_id, 'area_lng', true ),
                        'area_zoom' => get_term_meta( $term->term_id, 'area_zoom', true ),
                    ),
                );
            }
        }
        
        // Generate filename
        $filename = 'retail-locations-taxonomies-' . date( 'Y-m-d-His' ) . '.json';
        
        // Set headers for download
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        
        // Output JSON
        echo json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        exit;
    }

    public function add_meta_boxes() {
        add_meta_box(
            'retail_location_details',
            __( 'Location Details', 'retail-locations' ),
            array( $this, 'meta_box_details' ),
            $this->post_type,
            'normal',
            'high'
        );
    }

    public function meta_box_details( $post ) {
        wp_nonce_field( 'retail_location_meta', 'retail_location_nonce' );
        
        $address   = get_post_meta( $post->ID, '_location_address', true );
        $lat       = get_post_meta( $post->ID, '_location_lat', true );
        $lng       = get_post_meta( $post->ID, '_location_lng', true );
        $website   = get_post_meta( $post->ID, '_location_website', true );
        $instagram = get_post_meta( $post->ID, '_location_instagram', true );
        $contacts  = get_post_meta( $post->ID, '_location_contacts', true ) ?: array();
        $hours     = get_post_meta( $post->ID, '_location_hours', true ) ?: array();
        ?>
        <div class="retail-location-meta">
            <p>
                <label for="location_address"><strong><?php _e( 'Address', 'retail-locations' ); ?></strong></label><br>
                <textarea id="location_address" name="location_address" rows="3" style="width:100%;"><?php echo esc_textarea( $address ); ?></textarea>
            </p>
            <p>
                <label for="location_lat"><strong><?php _e( 'Latitude', 'retail-locations' ); ?></strong></label><br>
                <input type="text" id="location_lat" name="location_lat" value="<?php echo esc_attr( $lat ); ?>" class="regular-text" />
            </p>
            <p>
                <label for="location_lng"><strong><?php _e( 'Longitude', 'retail-locations' ); ?></strong></label><br>
                <input type="text" id="location_lng" name="location_lng" value="<?php echo esc_attr( $lng ); ?>" class="regular-text" />
            </p>
            <div id="location-map-preview" style="height:300px;margin:10px 0;background:#f0f0f0;"></div>
            
            <h4><?php _e( 'Links', 'retail-locations' ); ?></h4>
            <p>
                <label for="location_website"><strong><?php _e( 'Website URL', 'retail-locations' ); ?></strong></label><br>
                <input type="url" id="location_website" name="location_website" value="<?php echo esc_url( $website ); ?>" class="regular-text" placeholder="https://" />
            </p>
            <p>
                <label for="location_instagram"><strong><?php _e( 'Instagram Handle', 'retail-locations' ); ?></strong></label><br>
                <input type="text" id="location_instagram" name="location_instagram" value="<?php echo esc_attr( $instagram ); ?>" class="regular-text" placeholder="@username" />
                <p class="description"><?php _e( 'Enter with or without @', 'retail-locations' ); ?></p>
            </p>
            
            <h4><?php _e( 'Contact Info', 'retail-locations' ); ?></h4>
            <div id="location-contacts">
                <?php foreach ( $contacts as $i => $contact ) : ?>
                <div class="contact-row">
                    <input type="text" name="location_contacts[<?php echo $i; ?>][label]" value="<?php echo esc_attr( $contact['label'] ?? '' ); ?>" placeholder="<?php _e( 'Label', 'retail-locations' ); ?>" />
                    <input type="text" name="location_contacts[<?php echo $i; ?>][value]" value="<?php echo esc_attr( $contact['value'] ?? '' ); ?>" placeholder="<?php _e( 'Value', 'retail-locations' ); ?>" />
                    <input type="url" name="location_contacts[<?php echo $i; ?>][link]" value="<?php echo esc_url( $contact['link'] ?? '' ); ?>" placeholder="<?php _e( 'Link (optional)', 'retail-locations' ); ?>" />
                    <button type="button" class="button remove-row">&times;</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="add-contact"><?php _e( '+ Add Contact', 'retail-locations' ); ?></button>
            
            <h4><?php _e( 'Business Hours', 'retail-locations' ); ?></h4>
            <div id="location-hours">
                <?php foreach ( $hours as $i => $hour ) : ?>
                <div class="hours-row">
                    <input type="text" name="location_hours[<?php echo $i; ?>][day]" value="<?php echo esc_attr( $hour['day'] ?? '' ); ?>" placeholder="<?php _e( 'Day', 'retail-locations' ); ?>" />
                    <input type="text" name="location_hours[<?php echo $i; ?>][open]" value="<?php echo esc_attr( $hour['open'] ?? '' ); ?>" placeholder="<?php _e( 'Open', 'retail-locations' ); ?>" />
                    <input type="text" name="location_hours[<?php echo $i; ?>][close]" value="<?php echo esc_attr( $hour['close'] ?? '' ); ?>" placeholder="<?php _e( 'Close', 'retail-locations' ); ?>" />
                    <button type="button" class="button remove-row">&times;</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="add-hours"><?php _e( '+ Add Hours', 'retail-locations' ); ?></button>
        </div>
        <?php
    }

    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['retail_location_nonce'] ) || ! wp_verify_nonce( $_POST['retail_location_nonce'], 'retail_location_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        if ( isset( $_POST['location_address'] ) ) {
            update_post_meta( $post_id, '_location_address', sanitize_textarea_field( $_POST['location_address'] ) );
        }
        if ( isset( $_POST['location_lat'] ) ) {
            update_post_meta( $post_id, '_location_lat', sanitize_text_field( $_POST['location_lat'] ) );
        }
        if ( isset( $_POST['location_lng'] ) ) {
            update_post_meta( $post_id, '_location_lng', sanitize_text_field( $_POST['location_lng'] ) );
        }
        if ( isset( $_POST['location_website'] ) ) {
            update_post_meta( $post_id, '_location_website', esc_url_raw( $_POST['location_website'] ) );
        }
        if ( isset( $_POST['location_instagram'] ) ) {
            $instagram = sanitize_text_field( $_POST['location_instagram'] );
            $instagram = ltrim( $instagram, '@' ); // Store without @
            update_post_meta( $post_id, '_location_instagram', $instagram );
        }
        
        $contacts = array();
        if ( isset( $_POST['location_contacts'] ) && is_array( $_POST['location_contacts'] ) ) {
            foreach ( $_POST['location_contacts'] as $contact ) {
                if ( ! empty( $contact['label'] ) || ! empty( $contact['value'] ) ) {
                    $contacts[] = array(
                        'label' => sanitize_text_field( $contact['label'] ?? '' ),
                        'value' => sanitize_text_field( $contact['value'] ?? '' ),
                        'link'  => esc_url_raw( $contact['link'] ?? '' ),
                    );
                }
            }
        }
        update_post_meta( $post_id, '_location_contacts', $contacts );

        $hours = array();
        if ( isset( $_POST['location_hours'] ) && is_array( $_POST['location_hours'] ) ) {
            foreach ( $_POST['location_hours'] as $hour ) {
                if ( ! empty( $hour['day'] ) ) {
                    $hours[] = array(
                        'day'   => sanitize_text_field( $hour['day'] ?? '' ),
                        'open'  => sanitize_text_field( $hour['open'] ?? '' ),
                        'close' => sanitize_text_field( $hour['close'] ?? '' ),
                    );
                }
            }
        }
        update_post_meta( $post_id, '_location_hours', $hours );
    }

    public function area_add_fields() {
        ?>
        <div class="form-field">
            <label for="area_lat"><?php _e( 'Latitude', 'retail-locations' ); ?></label>
            <input type="text" name="area_lat" id="area_lat" />
        </div>
        <div class="form-field">
            <label for="area_lng"><?php _e( 'Longitude', 'retail-locations' ); ?></label>
            <input type="text" name="area_lng" id="area_lng" />
        </div>
        <div class="form-field">
            <label for="area_zoom"><?php _e( 'Map Zoom', 'retail-locations' ); ?></label>
            <input type="number" name="area_zoom" id="area_zoom" value="12" min="1" max="20" />
        </div>
        <?php
    }

    public function area_edit_fields( $term ) {
        $lat  = get_term_meta( $term->term_id, 'area_lat', true );
        $lng  = get_term_meta( $term->term_id, 'area_lng', true );
        $zoom = get_term_meta( $term->term_id, 'area_zoom', true ) ?: 12;
        ?>
        <tr class="form-field">
            <th><label for="area_lat"><?php _e( 'Latitude', 'retail-locations' ); ?></label></th>
            <td><input type="text" name="area_lat" id="area_lat" value="<?php echo esc_attr( $lat ); ?>" /></td>
        </tr>
        <tr class="form-field">
            <th><label for="area_lng"><?php _e( 'Longitude', 'retail-locations' ); ?></label></th>
            <td><input type="text" name="area_lng" id="area_lng" value="<?php echo esc_attr( $lng ); ?>" /></td>
        </tr>
        <tr class="form-field">
            <th><label for="area_zoom"><?php _e( 'Map Zoom', 'retail-locations' ); ?></label></th>
            <td><input type="number" name="area_zoom" id="area_zoom" value="<?php echo esc_attr( $zoom ); ?>" min="1" max="20" /></td>
        </tr>
        <?php
    }

    public function save_area_meta( $term_id ) {
        if ( isset( $_POST['area_lat'] ) ) {
            update_term_meta( $term_id, 'area_lat', sanitize_text_field( $_POST['area_lat'] ) );
        }
        if ( isset( $_POST['area_lng'] ) ) {
            update_term_meta( $term_id, 'area_lng', sanitize_text_field( $_POST['area_lng'] ) );
        }
        if ( isset( $_POST['area_zoom'] ) ) {
            update_term_meta( $term_id, 'area_zoom', intval( $_POST['area_zoom'] ) );
        }
    }

    public function ajax_get_stores() {
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
        
        $args = array(
            'post_type'      => $this->post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        if ( $category ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'location_category',
                    'field'    => 'slug',
                    'terms'    => $category,
                ),
            );
        }

        $query = new WP_Query( $args );
        $markers = array();

        while ( $query->have_posts() ) {
            $query->the_post();
            $lat = get_post_meta( get_the_ID(), '_location_lat', true );
            $lng = get_post_meta( get_the_ID(), '_location_lng', true );
            
            if ( $lat && $lng ) {
                $categories = get_the_terms( get_the_ID(), 'location_category' );
                $cat_names = array();
                if ( $categories && ! is_wp_error( $categories ) ) {
                    $cat_names = wp_list_pluck( $categories, 'name' );
                }
                
                $hours = get_post_meta( get_the_ID(), '_location_hours', true ) ?: array();
                
                $markers[] = array(
                    'id'         => get_the_ID(),
                    'title'      => get_the_title(),
                    'lat'        => floatval( $lat ),
                    'lng'        => floatval( $lng ),
                    'address'    => get_post_meta( get_the_ID(), '_location_address', true ),
                    'link'       => get_permalink(),
                    'categories' => $cat_names,
                    'hours'      => $hours,
                );
            }
        }
        wp_reset_postdata();

        wp_send_json_success( array( 'markers' => $markers ) );
    }

    public function render_map( $atts ) {
        $atts = shortcode_atts( array(
            'category'         => '',
            'width'            => '100%',
            'height'           => '500px',
            'map_controls'     => 'yes',
            'scrollwheel'      => 'no',
            'mobile_draggable' => 'no',
            'sticky'           => 'no',
        ), $atts );

        $settings = array(
            'category'       => $atts['category'],
            'width'          => $atts['width'],
            'height'         => $atts['height'],
            'mapControls'    => $atts['map_controls'] === 'yes',
            'scrollwheel'    => $atts['scrollwheel'] === 'yes',
            'mobileDraggable' => $atts['mobile_draggable'] === 'yes',
            'sticky'         => $atts['sticky'] === 'yes',
        );

        $wrapper_class = 'retail-locations-map';
        if ( $atts['sticky'] === 'yes' ) {
            $wrapper_class .= ' retail-locations-map--sticky';
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $wrapper_class ); ?>" data-settings="<?php echo esc_attr( json_encode( $settings ) ); ?>">
            <div class="retail-locations-map-canvas" style="width:<?php echo esc_attr( $atts['width'] ); ?>;height:<?php echo esc_attr( $atts['height'] ); ?>;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_stores( $atts ) {
        $atts = shortcode_atts( array(
            'category'            => '',
            'posts_per_page'      => -1,
            'layout'              => 'fullwidth',
            'show_hours'          => 'yes',
            'show_contact'        => 'yes',
            'show_description'    => 'yes',
            'show_image'          => 'yes',
            'group_by_category'   => 'no',
            'group_by_area'       => 'no',
            'collapsible'         => 'no',
            'exclusive_accordion' => 'no',
        ), $atts );

        $args = array(
            'post_type'      => $this->post_type,
            'posts_per_page' => intval( $atts['posts_per_page'] ),
            'post_status'    => 'publish',
        );

        if ( $atts['category'] ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'location_category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            );
        }

        $query = new WP_Query( $args );
        
        if ( $atts['group_by_area'] === 'yes' ) {
            return $this->render_stores_grouped_by_area( $query, $atts );
        }
        
        if ( $atts['group_by_category'] === 'yes' ) {
            return $this->render_stores_grouped_by_category( $query, $atts );
        }

        ob_start();
        ?>
        <div class="retail-locations-list layout-<?php echo esc_attr( $atts['layout'] ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php $this->render_store_item( $atts ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_stores_grouped_by_area( $query, $atts ) {
        $grouped = array();
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $areas = get_the_terms( get_the_ID(), 'location_area' );
            $area_key = 'uncategorized';
            $area_name = __( 'Other', 'retail-locations' );
            $area_meta = array();
            
            if ( $areas && ! is_wp_error( $areas ) ) {
                $area = $areas[0];
                $area_key = $area->slug;
                $area_name = $area->name;
                $area_meta = array(
                    'lat'  => get_term_meta( $area->term_id, 'area_lat', true ),
                    'lng'  => get_term_meta( $area->term_id, 'area_lng', true ),
                    'zoom' => get_term_meta( $area->term_id, 'area_zoom', true ) ?: 12,
                );
            }
            
            if ( ! isset( $grouped[ $area_key ] ) ) {
                $grouped[ $area_key ] = array(
                    'name'  => $area_name,
                    'meta'  => $area_meta,
                    'posts' => array(),
                );
            }
            $grouped[ $area_key ]['posts'][] = get_post();
        }
        wp_reset_postdata();

        $is_collapsible = $atts['collapsible'] === 'yes';
        $is_exclusive = $atts['exclusive_accordion'] === 'yes';
        
        $list_classes = 'retail-locations-list layout-' . esc_attr( $atts['layout'] ) . ' grouped-by-area';
        $list_attrs = '';
        if ( $is_collapsible ) {
            $list_attrs .= ' data-collapsible="yes"';
            if ( $is_exclusive ) {
                $list_attrs .= ' data-exclusive="yes"';
            }
        }

        ob_start();
        ?>
        <div class="<?php echo $list_classes; ?>"<?php echo $list_attrs; ?>>
            <?php foreach ( $grouped as $slug => $group ) : 
                $group_classes = 'retail-locations-group';
                if ( $is_collapsible ) {
                    $group_classes .= ' is-collapsed';
                }
            ?>
                <div class="<?php echo esc_attr( $group_classes ); ?>" data-area="<?php echo esc_attr( $slug ); ?>">
                    <h2 class="retail-locations-group-title"
                        <?php if ( ! empty( $group['meta']['lat'] ) && ! empty( $group['meta']['lng'] ) ) : ?>
                        data-lat="<?php echo esc_attr( $group['meta']['lat'] ); ?>"
                        data-lng="<?php echo esc_attr( $group['meta']['lng'] ); ?>"
                        data-zoom="<?php echo esc_attr( $group['meta']['zoom'] ); ?>"
                        title="<?php _e( 'Click to focus map', 'retail-locations' ); ?>"
                        <?php endif; ?>
                        <?php if ( $is_collapsible ) : ?>
                        aria-expanded="false"
                        role="button"
                        tabindex="0"
                        <?php endif; ?>
                    ><?php echo esc_html( $group['name'] ); ?></h2>
                    <div class="retail-locations-group-content">
                        <?php foreach ( $group['posts'] as $location_post ) : ?>
                            <?php 
                            global $post;
                            $post = $location_post;
                            setup_postdata( $post );
                            $this->render_store_item( $atts );
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_stores_grouped_by_category( $query, $atts ) {
        $grouped = array();
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $cats = get_the_terms( get_the_ID(), 'location_category' );
            $cat_key = 'uncategorized';
            $cat_name = __( 'Other', 'retail-locations' );
            
            if ( $cats && ! is_wp_error( $cats ) ) {
                $cat = $cats[0];
                $cat_key = $cat->slug;
                $cat_name = $cat->name;
            }
            
            if ( ! isset( $grouped[ $cat_key ] ) ) {
                $grouped[ $cat_key ] = array(
                    'name'  => $cat_name,
                    'posts' => array(),
                );
            }
            $grouped[ $cat_key ]['posts'][] = get_post();
        }
        wp_reset_postdata();

        $is_collapsible = $atts['collapsible'] === 'yes';
        $is_exclusive = $atts['exclusive_accordion'] === 'yes';
        
        $list_classes = 'retail-locations-list layout-' . esc_attr( $atts['layout'] ) . ' grouped-by-category';
        $list_attrs = '';
        if ( $is_collapsible ) {
            $list_attrs .= ' data-collapsible="yes"';
            if ( $is_exclusive ) {
                $list_attrs .= ' data-exclusive="yes"';
            }
        }

        ob_start();
        ?>
        <div class="<?php echo $list_classes; ?>"<?php echo $list_attrs; ?>>
            <?php foreach ( $grouped as $slug => $group ) : 
                $group_classes = 'retail-locations-group';
                if ( $is_collapsible ) {
                    $group_classes .= ' is-collapsed';
                }
            ?>
                <div class="<?php echo esc_attr( $group_classes ); ?>" data-category="<?php echo esc_attr( $slug ); ?>">
                    <h2 class="retail-locations-group-title"
                        <?php if ( $is_collapsible ) : ?>
                        aria-expanded="false"
                        role="button"
                        tabindex="0"
                        <?php endif; ?>
                    ><?php echo esc_html( $group['name'] ); ?></h2>
                    <div class="retail-locations-group-content">
                        <?php foreach ( $group['posts'] as $location_post ) : ?>
                            <?php 
                            global $post;
                            $post = $location_post;
                            setup_postdata( $post );
                            $this->render_store_item( $atts );
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_google_maps_url( $address ) {
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode( $address );
    }

    private function render_store_item( $atts ) {
        $contacts   = get_post_meta( get_the_ID(), '_location_contacts', true ) ?: array();
        $hours      = get_post_meta( get_the_ID(), '_location_hours', true ) ?: array();
        $address    = get_post_meta( get_the_ID(), '_location_address', true );
        $website    = get_post_meta( get_the_ID(), '_location_website', true );
        $instagram  = get_post_meta( get_the_ID(), '_location_instagram', true );
        $categories = get_the_terms( get_the_ID(), 'location_category' );
        ?>
        <article class="retail-location-item">
            <?php if ( $atts['show_image'] === 'yes' && has_post_thumbnail() ) : ?>
                <figure class="retail-location-image">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                </figure>
            <?php endif; ?>
            
            <div class="retail-location-content">
                <h3 class="retail-location-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                        <span class="retail-location-tags">
                            <?php foreach ( $categories as $cat ) : ?>
                                <span class="retail-location-tag"><?php echo esc_html( $cat->name ); ?></span>
                            <?php endforeach; ?>
                        </span>
                    <?php endif; ?>
                </h3>
                
                <?php if ( $address ) : ?>
                    <div class="retail-location-address">
                        <?php echo retail_locations_icon_map(); ?>
                        <a href="<?php echo esc_url( $this->get_google_maps_url( $address ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $address ); ?></a>
                    </div>
                <?php endif; ?>
                
                <div class="retail-location-links">
                    <?php if ( $website ) : ?>
                        <a href="<?php echo esc_url( $website ); ?>" class="retail-location-link retail-location-link--website" target="_blank" rel="noopener noreferrer">
                            <?php echo retail_locations_icon_website(); ?>
                            <span><?php _e( 'Website', 'retail-locations' ); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ( $instagram ) : ?>
                        <a href="https://instagram.com/<?php echo esc_attr( $instagram ); ?>" class="retail-location-link retail-location-link--instagram" target="_blank" rel="noopener noreferrer">
                            <?php echo retail_locations_icon_instagram(); ?>
                            <span>@<?php echo esc_html( $instagram ); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if ( $atts['show_description'] === 'yes' ) : ?>
                    <div class="retail-location-description"><?php the_excerpt(); ?></div>
                <?php endif; ?>
                
                <?php if ( $atts['show_contact'] === 'yes' && ! empty( $contacts ) ) : ?>
                    <div class="retail-location-contacts">
                        <?php foreach ( $contacts as $contact ) : ?>
                            <div class="retail-location-contact">
                                <span class="contact-label"><?php echo esc_html( $contact['label'] ); ?>:</span>
                                <?php if ( ! empty( $contact['link'] ) ) : ?>
                                    <a href="<?php echo esc_url( $contact['link'] ); ?>"><?php echo esc_html( $contact['value'] ); ?></a>
                                <?php else : ?>
                                    <span class="contact-value"><?php echo esc_html( $contact['value'] ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php 
                // Ensure hours is an array
                if ( ! is_array( $hours ) ) {
                    $hours = array();
                }

                // Filter hours to ensure we don't show empty icon
                $hours = array_filter( $hours, function( $h ) {
                    return ! empty( $h['day'] ) || ! empty( $h['open'] );
                });
                
                if ( $atts['show_hours'] === 'yes' && ! empty( $hours ) ) : ?>
                    <div class="retail-location-hours">
                        <?php echo retail_locations_icon_clock(); ?>
                        <div class="retail-location-hours-list">
                            <?php foreach ( $hours as $hour ) : ?>
                                <div class="retail-location-hour">
                                    <span class="hour-day"><?php echo esc_html( $hour['day'] ); ?></span>
                                    <span class="hour-time"><?php echo esc_html( $hour['open'] ); ?> – <?php echo esc_html( $hour['close'] ); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }

    public static function get_api_key() {
        return get_option( 'retail_locations_api_key', '' );
    }
}
