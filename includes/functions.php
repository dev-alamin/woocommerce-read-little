<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Sanitize a single CSS length, or a space-separated shorthand list of up to
 * four CSS lengths (for margin/padding/border-radius-style shorthand).
 *
 * These values are concatenated directly into a <style> block via
 * wp_add_inline_style(), so this validates against an allow-list pattern
 * instead of trying to escape or strip dangerous characters. Anything that
 * doesn't look like "10px", "10px 5px", "auto", or "0" is rejected outright
 * — a single stray `;`, `{`, `}`, or `:` would otherwise let a saved value
 * break out of its declaration and inject arbitrary CSS.
 *
 * @param string $value Raw value.
 * @return string Sanitized value, or '' if it doesn't match a safe pattern.
 */
function pdvwc_sanitize_css_dimension( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	$unit    = '(?:px|em|rem|%|vh|vw|vmin|vmax|pt|pc|ex|ch|cm|mm|in)';
	$number  = '-?\d*\.?\d+' . $unit;
	$token   = '(?:0|auto|' . $number . ')';
	$pattern = '/^' . $token . '(?:\s+' . $token . '){0,3}$/';

	return preg_match( $pattern, $value ) ? $value : '';
}

/**
 * Re-validate a stored hex color at read time.
 *
 * sanitize_hex_color() already runs via the registered setting's
 * sanitize_callback on save, but this is defense-in-depth for values that
 * may have been written by an older version of the plugin, imported, or
 * edited directly in the database — get_option() never re-runs a
 * sanitize_callback.
 *
 * @param string $value   Raw value from get_option().
 * @param string $default Fallback to use if $value isn't a valid hex color.
 * @return string
 */
function pdvwc_sanitize_stored_hex_color( $value, $default = '' ) {
	$color = sanitize_hex_color( $value );
	return $color ? $color : $default;
}
