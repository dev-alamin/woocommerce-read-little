<?php
/**
 * Plugin Name: Read a Little for Woocommerce
 * Plugin URI: https://shaliktheme.com/plugins/read-a-little
 * Description: Adds a "Read a Little" button to WooCommerce book products for previewing content.
 * Version: 1.0.0
 * Author: Al Amin
 * Author URI: https://almn.me/read-a-little-for-woocommerce/
 * Requires Plugins: woocommerce
 * Text Domain: read-a-little-for-woocommerce
 * Domain Path: /languages
 * License: GPLv2 or later
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', 'wrl_woocommerce_inactive_notice' );
    return;
}

function wrl_woocommerce_inactive_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e( 'WooCommerce Read Little requires WooCommerce to be installed and activated.', 'read-a-little-for-woocommerce' ); ?></p>
    </div>
    <?php
}

// Include required files
include_once( plugin_dir_path( __FILE__ ) . '/includes/admin-settings.php' );

// Include required files for Carbon Fields
use Carbon_Fields\Container;
use Carbon_Fields\Field;

if( ! defined( 'WOO_READ_LITTLE_VERSION' ) ) {
    define( 'WOO_READ_LITTLE_VERSION', '1.0.0' );
}

if( ! defined( 'WOO_READ_LITTLE_ASSETS_URL' ) ) {
    define( 'WOO_READ_LITTLE_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
}

class WooCommerceReadLittle {
    
    public function __construct() {
        // Initialize Carbon Fields
        add_action( 'after_setup_theme', [ $this, 'crb_load' ] );
        add_action( 'carbon_fields_register_fields', [ $this, 'crb_attach_product_fields' ] );

        // Enqueue Fancybox scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        // Add WooCommerce hook for button
        add_action( 'wp', [ $this, 'add_woocommerce_button_hook' ] );

        // Add settings link to plugin action links
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'add_plugin_action_links' ] );
    }

    // Load Carbon Fields
    public function crb_load() {
        require_once( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot();
    }

    // Enqueue Fancybox and custom styles/scripts
    public function enqueue_scripts() {
        $file = __DIR__ . '/includes/scripts.php';
        
        // Check if the file exists before including it
        if ( file_exists( $file ) ) {
            include_once $file;
        } else {
            error_log('Missing scripts.php in includes folder.');
        }
    }    

    // Add a custom field for the PDF preview using Carbon Fields
    public function crb_attach_product_fields() {
        Container::make( 'post_meta', __( 'Book Preview', 'read-a-little-for-woocommerce' ) )
            ->where( 'post_type', '=', 'product' )
            ->add_fields( array(
                Field::make( 'media_gallery', 'read_little_pdf', __( 'Upload Images for Book Preview', 'read-a-little-for-woocommerce' ) )
                ->set_type( array( 'image' ) ),
            ) );
    }

    // Display the "Read a Little" button
    public function display_read_little_button() {
        global $product;
        $book_preview_ids = carbon_get_post_meta( $product->get_id(), 'read_little_pdf' );
    
        // Retrieve button settings from options
        $button_text        = get_option( 'wcrl_button_text', 'Read a Little' );
        $extra_button_class = get_option( 'wcrl_button_class', '' );

        if ( is_array( $book_preview_ids ) && ! empty( $book_preview_ids ) ) {
            echo '<div class="wrl-pdf-thumbnails-container">';
            echo '<button class="open-pdf-popup-btn wd-buy-now-btn button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
            
            echo '<ul class="pdf-thumbnails hidden">';
            foreach ( $book_preview_ids as $id ) {
                $image_url = wp_get_attachment_image_url( $id, 'full' );
                echo '<li class="ff-pdf-thumbnail-link" href="' . esc_url( wp_get_attachment_url( $id ) ) . '">';
                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title( get_the_ID() ) ) . '">';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    // Add WooCommerce hook based on the admin-selected position
    public function add_woocommerce_button_hook() {
        $position = get_option( 'wcrl_button_position', 'woocommerce_single_product_summary' );

        if ( ! in_array( $position, [
            'woocommerce_before_single_product_summary',
            'woocommerce_single_product_summary',
            'woocommerce_before_add_to_cart_form',
            'woocommerce_before_variations_form',
            'woocommerce_before_add_to_cart_button',
            'woocommerce_before_single_variation',
            'woocommerce_single_variation',
            'woocommerce_before_add_to_cart_quantity',
            'woocommerce_after_add_to_cart_quantity',
            'woocommerce_after_single_variation',
            'woocommerce_after_add_to_cart_button',
            'woocommerce_after_variations_form',
            'woocommerce_after_add_to_cart_form',
            'woocommerce_product_meta_start',
            'woocommerce_product_meta_end',
            'woocommerce_share',
            'woocommerce_after_single_product_summary',
        ] ) ) {
            $position = 'woocommerce_single_product_summary';
        }

        $priority = get_option( 'wcrl_hook_priority', 30 );
        add_action( $position, [ $this, 'display_read_little_button' ], intval( $priority ) );
    }

    // Add settings link to the plugin action links
    public function add_plugin_action_links( $links ) {
        $settings_link = '<a href="admin.php?page=wcrl-settings">' . __( 'Settings', 'read-a-little-for-woocommerce' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }
}

// Initialize the plugin
new WooCommerceReadLittle();
