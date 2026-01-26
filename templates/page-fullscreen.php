<?php
/**
 * Template Name: Retail Locations Fullscreen
 * Template Post Type: page
 *
 * Full-screen retail locations layout with map on left and location list on right
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove theme template hooks to create fullscreen layout
remove_all_actions( 'wp_head' );
remove_all_actions( 'wp_footer' );
remove_all_actions( 'wp_body_open' );

// Add back essential WordPress functions
add_action( 'wp_head', 'wp_head' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
    <style>
        html, body, #page, #content {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        body {
            display: flex;
            background: #f5f5f5;
        }
        #page, #content {
            width: 100%;
        }
    </style>
</head>
<body <?php body_class( 'retail-locations-fullscreen' ); ?>>
    <div class="retail-locations-fullscreen-container">
        <!-- Map Section (Left) -->
        <div class="retail-locations-fullscreen-map">
            <?php
            // Output the map shortcode
            echo do_shortcode( '[retail_locations_map category="" width="100%" height="100%" map_controls="yes" scrollwheel="yes" sticky="no"]' );
            ?>
        </div>

        <!-- List Section (Right) -->
        <div class="retail-locations-fullscreen-sidebar">
            <!-- Top Controls -->
            <div class="retail-locations-fullscreen-controls">
                <button class="retail-locations-sidebar-toggle" aria-label="<?php _e( 'Toggle menu', 'retail-locations' ); ?>">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <a href="<?php echo esc_url( wp_get_referer() ?: home_url() ); ?>" class="retail-locations-back-button" aria-label="<?php _e( 'Go back', 'retail-locations' ); ?>">
                    ← <?php _e( 'Back', 'retail-locations' ); ?>
                </a>
            </div>

            <!-- List Container -->
            <div class="retail-locations-fullscreen-list">
                <?php
                // Output the locations list with grouping
                echo do_shortcode( '[retail_locations layout="compact" group_by_area="yes" collapsible="yes" exclusive_accordion="yes" posts_per_page="-1"]' );
                ?>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
