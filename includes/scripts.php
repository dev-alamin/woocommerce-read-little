<?php
defined( 'ABSPATH' ) || exit;

$version = WP_DEBUG ? time() : WOO_READ_LITTLE_VERSION;

// Enqueue Fancybox scripts and styles
wp_enqueue_script( 'wcrl_fancybox', WOO_READ_LITTLE_ASSETS_URL . 'js/jquery.fancybox.js', array( 'jquery' ), '3.5.7', true );
wp_enqueue_style( 'wcrl_fancybox', WOO_READ_LITTLE_ASSETS_URL . 'css/jquery.fancybox.css', array(), '3.5.7' );

// Enqueue custom scripts and styles
wp_enqueue_script( 'read-little', WOO_READ_LITTLE_ASSETS_URL . 'js/read-little.js', array( 'jquery', 'wcrl_fancybox' ), $version, true );
wp_enqueue_style( 'read-little', WOO_READ_LITTLE_ASSETS_URL . 'css/read-little.css', [], $version, 'all' );

// Add inline styles for the open-pdf-popup-btn class
$button_bg_color      = get_option('wcrl_button_color', '#0073aa');           // Default button color
$button_border_color  = get_option('wcrl_button_border_color', '#000');       // Default border color
$button_hover_color   = get_option('wcrl_button_hover_bg_color', '#005177');  // Default hover color
$button_border_radius = get_option('wcrl_button_round_size', '5px');          // Default border radius
$button_width         = get_option('wcrl_button_width', 'auto');              // Default width
$button_height        = get_option('wcrl_button_height', 'auto');             // Default height
$button_margin        = get_option('wcrl_button_margin', '10px');             // Default margin
$button_padding       = get_option('wcrl_button_padding', '10px 20px');       // Default padding
$button_transparent   = get_option( 'wcrl_button_transparent_bg', false );
$is_rounded_btn       = get_option( 'wcrl_button_rounded', false );
$button_font_size     = get_option( 'wcrl_button_font_size', '' );
$button_font_color    = get_option( 'wcrl_button_font_color', '' );
$button_border_width  = get_option( 'wcrl_button_border_width', '1px' );

$inline_styles = "
    .open-pdf-popup-btn,
    .wrl-button {
        transition: background-color 0.3s, border-color 0.3s; /* Smooth transition */
    }
";

if ( $button_hover_color ) {
    $inline_styles .= "
    .open-pdf-popup-btn:hover,
    .wrl-button:hover {
        background-color: " . esc_attr( $button_hover_color ) . ";
    }";
}

if ( $button_border_color ) {
    $inline_styles .= "
    .open-pdf-popup-btn:hover,
    .wrl-button:hover {
        border-color: " . esc_attr( $button_border_color ) . ";
    }";
}

if ( $button_border_color && $button_border_width ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        border: " . esc_attr( $button_border_width ) . " solid " . esc_attr( $button_border_color ) . ";
    }";
}

if ( $button_width ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        width: " . esc_attr( $button_width ) . ";
    }";
}

if ( $button_height ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        height: " . esc_attr( $button_height ) . ";
    }";
}

if ( $button_margin ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        margin: " . esc_attr( $button_margin ) . ";
    }";
}

if ( $button_padding ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        padding: " . esc_attr( $button_padding ) . ";
    }";
}

if ( $button_bg_color ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        background-color: " . esc_attr( $button_bg_color ) . ";
    }";
}

if ( $button_transparent ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        background-color: transparent;
    }";
}

if ( $is_rounded_btn ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        border-radius: " . esc_attr( $button_border_radius ) . ";
    }";
}

if ( ! empty( $button_font_size ) ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        font-size: " . esc_attr( $button_font_size ) . ";
    }";
}

if ( ! empty( $button_font_color ) ) {
    $inline_styles .= "
    .open-pdf-popup-btn,
    .wrl-button {
        color: " . esc_attr( $button_font_color ) . ";
    }";
}

// Add inline styles after the 'read-little' stylesheet
wp_add_inline_style('read-little', wp_strip_all_tags( $inline_styles ) );