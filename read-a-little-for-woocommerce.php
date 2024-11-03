<?php
/**
 * Plugin Name: Read a Little for Woocommerce
 * Plugin URI: https://shaliktheme.com/plugins/read-a-little
 * Description: Adds a "Read a Little" button to WooCommerce Product Page for previewing content.
 * Version: 1.1.0
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
    define( 'WOO_READ_LITTLE_VERSION', '1.1.0' );
}

if( ! defined( 'WOO_READ_LITTLE_ASSETS_URL' ) ) {
    define( 'WOO_READ_LITTLE_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
}

class WooCommerceReadLittle {
    
    public function __construct() {
        // Initialize Carbon Fields
        add_action( 'after_setup_theme', [ $this, 'crb_load' ] );
        add_action( 'carbon_fields_register_fields', [ $this, 'crb_attach_product_fields' ] );

        add_shortcode( 'read_little_button', [ $this, 'read_little_button_shortcode' ] );

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
        Container::make( 'post_meta', __( 'Read Little Sample File', 'read-a-little-for-woocommerce' ) )
            ->where( 'post_type', '=', 'product' )
            ->add_fields( array(
                Field::make( 'media_gallery', 'read_little_pdf', __( 'Upload images, documents, PDFs, and Excel files to showcase the preview.', 'read-a-little-for-woocommerce' ) )
                ->set_type( array( 'image', 'file' ) ),
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

            $has_pdf_or_doc = false;
    
            if ( $this->is_local_server() ) {
                $this->show_localhost_notice();
            }

            foreach( $book_preview_ids as $media_id ) {
                $file_type = get_post_mime_type( $media_id );
                
                $temp_file = 'https://shaliktheme.com/wp-content/uploads/2024/11/freelancer-work-contract.pdf';
                $file_url = $this->is_local_server() ? $temp_file : wp_get_attachment_url( $media_id );

                // Check for PDF, Word, Excel, and PowerPoint MIME types
                if ( strpos( $file_type, 'pdf' ) !== false ||    // PDF
                     strpos( $file_type, 'msword' ) !== false ||  // Word
                     strpos( $file_type, 'vnd.openxmlformats-officedocument.wordprocessingml.document' ) !== false || // Word .docx
                     strpos( $file_type, 'vnd.ms-excel' ) !== false || // Excel
                     strpos( $file_type, 'vnd.openxmlformats-officedocument.spreadsheetml.sheet' ) !== false || // Excel .xlsx
                     strpos( $file_type, 'vnd.ms-powerpoint' ) !== false || // PowerPoint
                     strpos( $file_type, 'vnd.openxmlformats-officedocument.presentationml.presentation' ) !== false // PowerPoint .pptx
                ) {
                    $has_pdf_or_doc = true;
                    
                    // Using Google Docs Viewer to display these file types
                    echo '<a class="iframe fancybox-pdf" data-fancybox data-type="iframe" href="https://docs.google.com/gview?url=' . esc_url( $file_url ) . '&embedded=true">';
                        echo '<button class="button btn wrl-button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
                    echo '</a>';
                }
            }
    
            // If no PDF or document found, show the default button (no file found)
            if( ! $has_pdf_or_doc ) {
                echo '<button class="open-pdf-popup-btn wd-buy-now-btn button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
            }
    
            // Display the thumbnails for image files
            echo '<ul class="pdf-thumbnails hidden">';
            foreach ( $book_preview_ids as $id ) {
                $file_type = get_post_mime_type( $id );
                $image_url = wp_get_attachment_image_url( $id, 'full' );
    
                if( strpos( $file_type, 'image' ) !== false ) {
                    echo '<li class="ff-pdf-thumbnail-link">';
                    echo '<a data-fancybox="gallery" href="' . esc_url( $image_url ) . '">';
                    echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title( get_the_ID() ) ) . '">';
                    echo '</a>';
                    echo '</li>';
                }
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    public function read_little_button_shortcode( $atts ) {
        global $product;
    
        // Shortcode attributes (optional: media IDs)
        $atts = shortcode_atts( array(
            'media_ids' => '', // Optional media IDs passed to the shortcode
        ), $atts, 'read_little_button' );
    
        // Get media IDs from the shortcode, or use the product's media if no media IDs are provided
        $media_ids = !empty( $atts['media_ids'] ) ? explode( ',', $atts['media_ids'] ) : carbon_get_post_meta( $product->get_id(), 'read_little_pdf' );
    
        // Ensure media_ids is an array
        if ( !is_array( $media_ids ) || empty( $media_ids ) ) {
            return '<p>' . __( 'No book preview available.', 'read-a-little-for-woocommerce' ) . '</p>';
        }
    
        ob_start();
    
        // Retrieve button settings from options
        $button_text        = get_option( 'wcrl_button_text', 'Read a Little' );
        $extra_button_class = get_option( 'wcrl_button_class', '' );
    
        echo '<div class="wrl-pdf-thumbnails-container">';
    
        $has_pdf_or_doc = false;
        
        if ( $this->is_local_server() ) {
            $this->show_localhost_notice();
        }

        foreach( $media_ids as $media_id ) {
            $file_type = get_post_mime_type( $media_id );
    
            // If local server, show a temp file
            $temp_file = 'https://shaliktheme.com/wp-content/uploads/2024/11/freelancer-work-contract.pdf';
            $file_url = ( $this->is_local_server() ) ? $temp_file : wp_get_attachment_url( $media_id );
    
            // Check for file types: PDF, Word, Excel, PowerPoint
            if ( strpos( $file_type, 'pdf' ) !== false ||    // PDF
                 strpos( $file_type, 'msword' ) !== false ||  // Word
                 strpos( $file_type, 'vnd.openxmlformats-officedocument.wordprocessingml.document' ) !== false || // Word .docx
                 strpos( $file_type, 'vnd.ms-excel' ) !== false || // Excel
                 strpos( $file_type, 'vnd.openxmlformats-officedocument.spreadsheetml.sheet' ) !== false || // Excel .xlsx
                 strpos( $file_type, 'vnd.ms-powerpoint' ) !== false || // PowerPoint
                 strpos( $file_type, 'vnd.openxmlformats-officedocument.presentationml.presentation' ) !== false // PowerPoint .pptx
            ) {
                $has_pdf_or_doc = true;
    
                // Use Google Docs Viewer to display these file types
                echo '<a class="iframe fancybox-pdf" data-fancybox data-type="iframe" href="https://docs.google.com/gview?url=' . esc_url( $file_url ) . '&embedded=true">';
                    echo '<button class="button btn wrl-button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
                echo '</a>';
            }
        }
    
        // If no PDF or document found, show the default button (no file found)
        if( ! $has_pdf_or_doc ) {
            echo '<button class="open-pdf-popup-btn wd-buy-now-btn button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
        }
    
        // Display the thumbnails for image files
        echo '<ul class="pdf-thumbnails hidden">';
        foreach ( $media_ids as $id ) {
            $file_type = get_post_mime_type( $id );
            $image_url = wp_get_attachment_image_url( $id, 'full' );
    
            if( strpos( $file_type, 'image' ) !== false ) {
                echo '<li class="ff-pdf-thumbnail-link">';
                echo '<a data-fancybox="gallery" href="' . esc_url( $image_url ) . '">';
                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title( get_the_ID() ) ) . '">';
                echo '</a>';
                echo '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    
        return ob_get_clean();
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
        if( ! get_option( 'wcrl_hide_button_position' ) ){
            add_action( $position, [ $this, 'display_read_little_button' ], intval( $priority ) );
        }
    }

    // Add settings link to the plugin action links
    public function add_plugin_action_links( $links ) {
        $settings_link = '<a href="admin.php?page=wcrl-settings">' . __( 'Settings', 'read-a-little-for-woocommerce' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    public function is_local_server() {
        // Check if the server IP is localhost (127.0.0.1 or ::1)
        if ( $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1' ) {
            return true;  // Local server
        }
    
        return false; // Live server
    }

    public function show_localhost_notice(){
        $notice = 'This is a local server & the PDF, Doc, Spread Sheet etc does not support in localhost. 
                    Once you move it to live. This text won\'t show and you original file will display.';
                    echo '<span class="wrl-local-info">';
                    echo '<i>' . esc_html( '𝐢' ) . '</i>';
                    echo '<span class="notice">';
                    esc_html_e( $notice, 'read-a-little-for-woocommerce' );
                    echo '</span>';
                    echo '</span>';

        return $notice;
    }
}

// Initialize the plugin
new WooCommerceReadLittle();