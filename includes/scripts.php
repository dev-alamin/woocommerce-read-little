<?php
defined( 'ABSPATH' ) || exit;

$pdvwc_version = WP_DEBUG ? time() : PDVWC_VERSION;

// Enqueue Fancybox scripts and styles.
wp_enqueue_style( 'pdvwc-fancybox', PDVWC_ASSETS_URL . 'css/jquery.fancybox.min.css' );
wp_enqueue_script( 'pdvwc-fancybox', PDVWC_ASSETS_URL . 'js/jquery.fancybox.min.js', array( 'jquery' ), null, true );

// Enqueue custom scripts and styles.
wp_enqueue_script( 'pdvwc-frontend', PDVWC_ASSETS_URL . 'js/pdvwc-frontend.js', array( 'jquery', 'pdvwc-fancybox' ), $pdvwc_version, true );
wp_enqueue_style( 'pdvwc-frontend', PDVWC_ASSETS_URL . 'css/pdvwc-frontend.css', array(), $pdvwc_version, 'all' );

// Add inline styles for the button classes.
$button_bg_color      = get_option( 'pdvwc_button_color', '#0073aa' );
$button_border_color  = get_option( 'pdvwc_button_border_color', '#000' );
$button_hover_color   = get_option( 'pdvwc_button_hover_bg_color', '#005177' );
$button_border_radius = get_option( 'pdvwc_button_round_size', '5px' );
$button_width         = get_option( 'pdvwc_button_width', 'auto' );
$button_height        = get_option( 'pdvwc_button_height', 'auto' );
$button_margin        = get_option( 'pdvwc_button_margin', '10px' );
$button_padding       = get_option( 'pdvwc_button_padding', '10px 20px' );
$button_transparent   = get_option( 'pdvwc_button_transparent_bg', false );
$is_rounded_btn       = get_option( 'pdvwc_button_rounded', false );
$button_font_size     = get_option( 'pdvwc_button_font_size', '' );
$button_font_color    = get_option( 'pdvwc_button_font_color', '' );
$button_border_width  = get_option( 'pdvwc_button_border_width', '1px' );

$inline_styles = '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        transition: background-color 0.3s, border-color 0.3s; /* Smooth transition */
    }
';

if ( $button_hover_color ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn:hover,
    .pdvwc-button:hover {
        background-color: ' . esc_attr( $button_hover_color ) . ';
    }';
}

if ( $button_border_color ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn:hover,
    .pdvwc-button:hover {
        border-color: ' . esc_attr( $button_border_color ) . ';
    }';
}

if ( $button_border_color && $button_border_width ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        border: ' . esc_attr( $button_border_width ) . ' solid ' . esc_attr( $button_border_color ) . ';
    }';
}

if ( $button_width ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        width: ' . esc_attr( $button_width ) . ';
    }';
}

if ( $button_height ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        height: ' . esc_attr( $button_height ) . ';
    }';
}

if ( $button_margin ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        margin: ' . esc_attr( $button_margin ) . ';
    }';
}

if ( $button_padding ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        padding: ' . esc_attr( $button_padding ) . ';
    }';
}

if ( $button_bg_color ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        background-color: ' . esc_attr( $button_bg_color ) . ';
    }';
}

if ( $button_transparent ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        background-color: transparent;
    }';
}

if ( $is_rounded_btn ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        border-radius: ' . esc_attr( $button_border_radius ) . ';
    }';
}

if ( ! empty( $button_font_size ) ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        font-size: ' . esc_attr( $button_font_size ) . ';
    }';
}

if ( ! empty( $button_font_color ) ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        color: ' . esc_attr( $button_font_color ) . ';
    }';
}

// Add inline styles after the 'pdvwc-frontend' stylesheet.
wp_add_inline_style( 'pdvwc-frontend', wp_strip_all_tags( $inline_styles ) );
