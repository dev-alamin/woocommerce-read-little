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
//
// Every value below is re-validated here with pdvwc_sanitize_css_dimension()
// / pdvwc_sanitize_stored_hex_color() (see includes/functions.php) even
// though the settings already run a sanitize_callback on save. That save-time
// check can't be trusted alone: get_option() never re-runs it, so a value
// written by an older plugin version, an import, or a direct DB edit could
// still reach this file unvalidated. esc_attr() — which was used here
// before — escapes for an HTML attribute, not a CSS declaration, so it does
// not stop a value from breaking out of the rule with `;`, `{`, `}`, or `:`
// and injecting arbitrary CSS. Validating against an allow-list instead of
// escaping means every value below is guaranteed to be a safe CSS length or
// hex color before it's concatenated in.
$button_bg_color      = pdvwc_sanitize_stored_hex_color( get_option( 'pdvwc_button_color' ), '#0073aa' );
$button_border_color  = pdvwc_sanitize_stored_hex_color( get_option( 'pdvwc_button_border_color' ), '#000' );
$button_hover_color   = pdvwc_sanitize_stored_hex_color( get_option( 'pdvwc_button_hover_bg_color' ), '#005177' );
$button_border_radius = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_round_size', '5px' ) );
$button_width         = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_width', 'auto' ) );
$button_height        = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_height', 'auto' ) );
$button_margin        = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_margin', '10px' ) );
$button_padding       = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_padding', '10px 20px' ) );
$button_transparent   = get_option( 'pdvwc_button_transparent_bg', false );
$is_rounded_btn       = get_option( 'pdvwc_button_rounded', false );
$button_font_size     = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_font_size', '' ) );
$button_font_color    = pdvwc_sanitize_stored_hex_color( get_option( 'pdvwc_button_font_color' ), '' );
$button_border_width  = pdvwc_sanitize_css_dimension( get_option( 'pdvwc_button_border_width', '1px' ) );

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
        background-color: ' . $button_hover_color . ';
    }';
}

if ( $button_border_color ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn:hover,
    .pdvwc-button:hover {
        border-color: ' . $button_border_color . ';
    }';
}

if ( $button_border_color && $button_border_width ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        border: ' . $button_border_width . ' solid ' . $button_border_color . ';
    }';
}

if ( $button_width ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        width: ' . $button_width . ';
    }';
}

if ( $button_height ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        height: ' . $button_height . ';
    }';
}

if ( $button_margin ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        margin: ' . $button_margin . ';
    }';
}

if ( $button_padding ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        padding: ' . $button_padding . ';
    }';
}

if ( $button_bg_color ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        background-color: ' . $button_bg_color . ';
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
        border-radius: ' . $button_border_radius . ';
    }';
}

if ( ! empty( $button_font_size ) ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        font-size: ' . $button_font_size . ';
    }';
}

if ( ! empty( $button_font_color ) ) {
	$inline_styles .= '
    .pdvwc-open-popup-btn,
    .pdvwc-button {
        color: ' . $button_font_color . ';
    }';
}

// wp_strip_all_tags() is removed: it only strips HTML tags and does nothing
// to stop CSS-syntax injection (`;`, `{`, `}`, `:`), so it never actually
// addressed this issue. Every value concatenated into $inline_styles above
// has already been validated against a safe allow-list, so the string is
// safe to pass through as-is.
wp_add_inline_style( 'pdvwc-frontend', $inline_styles );
