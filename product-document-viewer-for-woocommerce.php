<?php
/**
 * Plugin Name: Product Document Viewer for WooCommerce
 * Plugin URI: https://github.com/dev-alamin/woocommerce-read-little
 * Description: Preview PDFs, Word, Excel, and PowerPoint files directly on WooCommerce product pages.
 * Version: 1.3.1
 * Author: Al Amin
 * Author URI: https://github.com/dev-alamin/
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * Text Domain: product-document-viewer-for-woocommerce
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Prefix: pdvwc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if WooCommerce is active.
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	add_action( 'admin_notices', 'pdvwc_woocommerce_inactive_notice' );
	return;
}

/**
 * Admin notice shown when WooCommerce isn't active.
 */
function pdvwc_woocommerce_inactive_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Product Document Viewer for WooCommerce requires WooCommerce to be installed and activated.', 'product-document-viewer-for-woocommerce' ); ?></p>
	</div>
	<?php
}

require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

use Carbon_Fields\Container;
use Carbon_Fields\Field;

if ( ! defined( 'PDVWC_VERSION' ) ) {
	define( 'PDVWC_VERSION', '1.3.0' );
}

if ( ! defined( 'PDVWC_ASSETS_URL' ) ) {
	define( 'PDVWC_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
}

if ( ! defined( 'PDVWC_ASSETS_PATH' ) ) {
	define( 'PDVWC_ASSETS_PATH', plugin_dir_path( __FILE__ ) . 'assets/' );
}

// The product-meta key used to store preview file attachment IDs (Carbon Fields media_gallery).
if ( ! defined( 'PDVWC_META_KEY' ) ) {
	define( 'PDVWC_META_KEY', 'pdvwc_preview_files' );
}

// MIME types this plugin knows how to preview, mapped to a viewer "family".
// Centralised here so both render paths (hook + shortcode) share one source of truth.
const PDVWC_OFFICE_MIME_TYPES = array(
	'msword',
	'vnd.openxmlformats-officedocument.wordprocessingml.document',
	'vnd.ms-excel',
	'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'vnd.ms-powerpoint',
	'vnd.openxmlformats-officedocument.presentationml.presentation',
);

/**
 * One-time migration from the plugin's earlier "Read a Little" naming
 * (wcrl_* options, read_little_pdf product meta) to the pdvwc_* scheme.
 *
 * Runs on activation for fresh setups, and is also checked on 'plugins_loaded'
 * so sites that got this file replaced via an in-place update (no
 * deactivate/reactivate, so no activation hook fires) still get migrated.
 * Guarded by a version flag so it only ever runs once.
 */
function pdvwc_maybe_migrate_legacy_data() {
	if ( get_option( 'pdvwc_migrated_1_3_0' ) ) {
		return;
	}

	global $wpdb;

	// Map of old option name => new option name.
	$option_map = array(
		'wcrl_button_text'           => 'pdvwc_button_text',
		'wcrl_button_class'          => 'pdvwc_button_class',
		'wcrl_button_color'          => 'pdvwc_button_color',
		'wcrl_button_position'       => 'pdvwc_button_position',
		'wcrl_hook_priority'         => 'pdvwc_hook_priority',
		'wcrl_hide_button_position'  => 'pdvwc_hide_button_position',
		'wcrl_button_rounded'        => 'pdvwc_button_rounded',
		'wcrl_button_round_size'     => 'pdvwc_button_round_size',
		'wcrl_button_transparent_bg' => 'pdvwc_button_transparent_bg',
		'wcrl_button_font_color'     => 'pdvwc_button_font_color',
		'wcrl_button_border_color'   => 'pdvwc_button_border_color',
		'wcrl_button_hover_bg_color' => 'pdvwc_button_hover_bg_color',
		'wcrl_button_width'          => 'pdvwc_button_width',
		'wcrl_button_height'         => 'pdvwc_button_height',
		'wcrl_button_margin'         => 'pdvwc_button_margin',
		'wcrl_button_padding'        => 'pdvwc_button_padding',
		'wcrl_button_font_size'      => 'pdvwc_button_font_size',
		'wcrl_button_border_width'   => 'pdvwc_button_border_width',
	);

	foreach ( $option_map as $old_name => $new_name ) {
		// Only migrate if the old value exists and the new one hasn't already been set
		// (e.g. by the admin re-saving settings before this migration ran).
		$old_value = get_option( $old_name, null );
		if ( null !== $old_value && false === get_option( $new_name, false ) ) {
			update_option( $new_name, $old_value );
		}
	}

	// Migrate the product-meta key (Carbon Fields media_gallery: read_little_pdf -> pdvwc_preview_files)
	// across every product that has the old meta key set.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
			'read_little_pdf'
		)
	);

	foreach ( (array) $rows as $row ) {
		if ( ! metadata_exists( 'post', $row->post_id, PDVWC_META_KEY ) ) {
			update_post_meta( $row->post_id, PDVWC_META_KEY, maybe_unserialize( $row->meta_value ) );
		}
	}

	update_option( 'pdvwc_migrated_1_3_0', 1 );
}
register_activation_hook( __FILE__, 'pdvwc_maybe_migrate_legacy_data' );
add_action( 'plugins_loaded', 'pdvwc_maybe_migrate_legacy_data' );

class PDVWC_Plugin {

	public function __construct() {
		// Initialize Carbon Fields.
		add_action( 'after_setup_theme', array( $this, 'crb_load' ) );
		add_action( 'carbon_fields_register_fields', array( $this, 'crb_attach_product_fields' ) );

		// New canonical shortcode, plus the old names kept as aliases for
		// backward compatibility with content that already uses them.
		add_shortcode( 'pdvwc_preview_button', array( $this, 'preview_button_shortcode' ) );
		add_shortcode( 'read_little_button', array( $this, 'preview_button_shortcode' ) );
		add_shortcode( 'product_preview_button', array( $this, 'preview_button_shortcode' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp', array( $this, 'add_woocommerce_button_hook' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );
	}

	// Load Carbon Fields.
	public function crb_load() {
		$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

		if ( ! file_exists( $autoload ) ) {
			// Fail gracefully rather than fatal-erroring the whole site if
			// composer dependencies were never installed / were excluded from a deploy.
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error"><p>' .
					esc_html__( 'Product Document Viewer for WooCommerce: Carbon Fields library is missing (vendor/autoload.php not found). Custom fields will not appear.', 'product-document-viewer-for-woocommerce' ) .
					'</p></div>';
				}
			);
			return;
		}

		require_once $autoload;

		if ( class_exists( '\Carbon_Fields\Carbon_Fields' ) ) {
			\Carbon_Fields\Carbon_Fields::boot();
		}
	}

	// Enqueue Fancybox and custom styles/scripts.
	public function enqueue_scripts() {
		$file = __DIR__ . '/includes/scripts.php';

		if ( file_exists( $file ) ) {
			include_once $file;
		} else {
			error_log( 'Product Document Viewer for WooCommerce: missing includes/scripts.php.' );
		}
	}

	// Add a custom field for the document preview using Carbon Fields.
	public function crb_attach_product_fields() {
		if ( ! class_exists( '\Carbon_Fields\Container\Container' ) && ! class_exists( 'Carbon_Fields\Container' ) ) {
			return;
		}

		Container::make( 'post_meta', __( 'Document Preview Files', 'product-document-viewer-for-woocommerce' ) )
			->where( 'post_type', '=', 'product' )
			->add_fields(
				array(
					Field::make( 'media_gallery', PDVWC_META_KEY, __( 'Upload images, documents, PDFs, and Excel files to showcase the preview.', 'product-document-viewer-for-woocommerce' ) )
						->set_type( array( 'image', 'file' ) ),
				)
			);
	}

	/**
	 * Determine whether a MIME type is one of the previewable document types,
	 * and if so, which viewer family it needs ('office' or 'pdf').
	 *
	 * @param string|false $file_type Result of get_post_mime_type().
	 * @return string|false 'office', 'pdf', or false if not previewable.
	 */
	private function get_viewer_family( $file_type ) {
		if ( empty( $file_type ) ) {
			return false;
		}

		if ( strpos( $file_type, 'pdf' ) !== false ) {
			return 'pdf';
		}

		foreach ( PDVWC_OFFICE_MIME_TYPES as $office_type ) {
			if ( strpos( $file_type, $office_type ) !== false ) {
				return 'office';
			}
		}

		return false;
	}

	/**
	 * Build a safe, working viewer URL for a given file + family.
	 *
	 * Office formats go through Microsoft's officially supported Office
	 * Online viewer, which — unlike Google's undocumented gview endpoint —
	 * reliably renders .docx/.xlsx/.pptx.
	 *
	 * PDFs are linked directly; Fancybox's own delegated click handler picks
	 * up [data-fancybox] elements once its library has loaded, and
	 * data-type="iframe" tells it to render the file inline rather than
	 * navigate to it.
	 *
	 * @param string $file_url Public, unauthenticated URL to the file.
	 * @param string $family   'office' or 'pdf'.
	 * @return string Fully escaped, ready-to-output URL.
	 */
	private function build_viewer_url( $file_url, $family ) {
		if ( 'office' === $family ) {
			$viewer = 'https://view.officeapps.live.com/op/embed.aspx?src=' . rawurlencode( $file_url );
			return esc_url( $viewer );
		}

		return esc_url( $file_url );
	}

	/**
	 * Resolve the URL to preview for a given attachment ID.
	 *
	 * On a local environment, real attachment URLs usually aren't publicly
	 * reachable (Office/PDF viewers need a public HTTPS URL), so we fall back
	 * to a small sample file bundled with the plugin instead of depending on
	 * an unrelated third-party file on a remote domain.
	 *
	 * @param int $media_id Attachment ID.
	 * @return string|false File URL, or false if it can't be resolved.
	 */
	private function resolve_file_url( $media_id ) {
		if ( $this->is_local_server() ) {
			$sample_path = PDVWC_ASSETS_PATH . 'sample/demo-preview.pdf';
			if ( file_exists( $sample_path ) ) {
				return PDVWC_ASSETS_URL . 'sample/demo-preview.pdf';
			}
			return false; // No bundled sample present — skip rather than reach out to a third-party domain.
		}

		$url = wp_get_attachment_url( $media_id );
		return $url ? $url : false;
	}

	/**
	 * Shared renderer used by both the WooCommerce hook and the shortcode,
	 * so viewer/URL/escaping fixes only need to live in one place.
	 *
	 * @param array $media_ids Attachment IDs from Carbon Fields or shortcode attr.
	 */
	private function render_preview_markup( array $media_ids ) {
		$button_text        = get_option( 'pdvwc_button_text', 'Read a Little' );
		$extra_button_class = get_option( 'pdvwc_button_class', '' );

		echo '<div class="pdvwc-preview-container">';

		if ( $this->is_local_server() ) {
			$this->show_localhost_notice();
		}

		// Unique per render call so multiple products with previews on the same
		// page (e.g. a related-products loop) don't share one Fancybox group.
		$gallery_group = 'pdvwc-preview-' . wp_unique_id();

		$viewer_urls = array();

		foreach ( $media_ids as $media_id ) {
			$file_type = get_post_mime_type( $media_id );
			$family    = $this->get_viewer_family( $file_type );

			if ( ! $family ) {
				continue; // Not a previewable document type (or deleted/invalid attachment).
			}

			$file_url = $this->resolve_file_url( $media_id );
			if ( ! $file_url ) {
				continue;
			}

			$viewer_urls[] = $this->build_viewer_url( $file_url, $family );
		}

		if ( ! empty( $viewer_urls ) ) {
			// Fixed viewport size — without this, Fancybox's autoSize measures
			// the embedded PDF/Office viewer's full content height (which grows
			// with page count) instead of treating it as a scrollable frame.

			// One visible trigger button, using the first document as its href.
			$first_url = array_shift( $viewer_urls );
			echo '<a class="iframe pdvwc-fancybox-trigger" data-fancybox="' . esc_attr( $gallery_group ) . '" data-type="iframe" data-width="90%" data-height="90%" href="' . esc_url( $first_url ) . '">';
				echo '<button type="button" class="button btn pdvwc-button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
			echo '</a>';

			// Remaining documents: same gallery group, hidden from view — Fancybox's
			// own next/prev arrows let people page through them once the lightbox is open.
			foreach ( $viewer_urls as $extra_url ) {
				echo '<a class="iframe pdvwc-fancybox-trigger" data-fancybox="' . esc_attr( $gallery_group ) . '" data-type="iframe" data-width="90%" data-height="90%" href="' . esc_url( $extra_url ) . '" style="display:none;" aria-hidden="true"></a>';
			}
		} else {
			echo '<button type="button" class="pdvwc-open-popup-btn wd-buy-now-btn button ' . esc_attr( $extra_button_class ) . '">' . esc_html( $button_text ) . '</button>';
		}

		// Thumbnails for image entries in the same media gallery field.
		echo '<ul class="pdvwc-thumbnails hidden">';
		foreach ( $media_ids as $id ) {
			$file_type = get_post_mime_type( $id );

			if ( empty( $file_type ) || strpos( $file_type, 'image' ) === false ) {
				continue;
			}

			$image_url = wp_get_attachment_image_url( $id, 'full' );
			if ( ! $image_url ) {
				continue;
			}

			echo '<li class="pdvwc-thumbnail-item">';
				echo '<a data-fancybox="gallery" href="' . esc_url( $image_url ) . '">';
					echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '">';
				echo '</a>';
			echo '</li>';
		}
		echo '</ul>';

		echo '</div>';
	}

	// Display the preview button (hooked into WooCommerce templates).
	public function display_preview_button() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$preview_ids = carbon_get_post_meta( $product->get_id(), PDVWC_META_KEY );

		if ( is_array( $preview_ids ) && ! empty( $preview_ids ) ) {
			$this->render_preview_markup( $preview_ids );
		}
	}

	public function preview_button_shortcode( $atts ) {
		global $product;

		$atts = shortcode_atts(
			array(
				'media_ids' => '',
			),
			$atts,
			'pdvwc_preview_button'
		);

		if ( ! empty( $atts['media_ids'] ) ) {
			$media_ids = array_map( 'absint', explode( ',', $atts['media_ids'] ) );
		} elseif ( $product instanceof WC_Product ) {
			$media_ids = carbon_get_post_meta( $product->get_id(), PDVWC_META_KEY );
		} else {
			$media_ids = array();
		}

		if ( ! is_array( $media_ids ) || empty( $media_ids ) ) {
			return '<p>' . esc_html__( 'No document preview available.', 'product-document-viewer-for-woocommerce' ) . '</p>';
		}

		ob_start();
		$this->render_preview_markup( $media_ids );
		return ob_get_clean();
	}

	// Add WooCommerce hook based on the admin-selected position.
	public function add_woocommerce_button_hook() {
		$position = get_option( 'pdvwc_button_position', 'woocommerce_single_product_summary' );

		$allowed_positions = array(
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
		);

		if ( ! in_array( $position, $allowed_positions, true ) ) {
			$position = 'woocommerce_single_product_summary';
		}

		// Default kept consistent with the settings page default (30).
		$priority = get_option( 'pdvwc_hook_priority', 30 );

		if ( ! get_option( 'pdvwc_hide_button_position' ) ) {
			add_action( $position, array( $this, 'display_preview_button' ), intval( $priority ) );
		}
	}

	// Add settings link to the plugin action links.
	public function add_plugin_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=pdvwc-settings' ) ) . '">' . esc_html__( 'Settings', 'product-document-viewer-for-woocommerce' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Whether this is a local/dev environment.
	 *
	 * Uses WordPress's own WP_ENVIRONMENT_TYPE mechanism rather than inspecting
	 * $_SERVER['SERVER_ADDR'], which is unreliable behind reverse proxies /
	 * internal loopback requests and could misfire on a live server.
	 * Set in wp-config.php: define( 'WP_ENVIRONMENT_TYPE', 'local' );
	 */
	public function is_local_server() {
		return function_exists( 'wp_get_environment_type' ) && 'local' === wp_get_environment_type();
	}

	public function show_localhost_notice() {
		$notice = __( 'This is a local/development environment. A bundled sample file is shown here instead of the real attachment — on the live site, your actual file will display.', 'product-document-viewer-for-woocommerce' );

		echo '<span class="pdvwc-local-info">';
			echo '<i>' . esc_html( '𝐢' ) . '</i>';
			echo '<span class="notice">' . esc_html( $notice ) . '</span>';
		echo '</span>';
	}
}

new PDVWC_Plugin();