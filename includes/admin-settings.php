<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add WooCommerce submenu under "WooCommerce"
function wcrl_add_admin_menu() {
	add_submenu_page(
		'woocommerce',
		__( 'Document Viewer', 'product-document-viewer-for-woocommerce' ),
		__( 'Document Viewer', 'product-document-viewer-for-woocommerce' ),
		'manage_options',
		'wcrl-settings',
		'wcrl_settings_page_html'
	);
}
add_action( 'admin_menu', 'wcrl_add_admin_menu' );

// Helper to add checkbox field
function wcrl_add_checkbox_field( $id, $title, $description ) {
	add_settings_field(
		$id,
		$title,
		'wcrl_checkbox_field_callback',
		'wcrl-options',
		'wcrl_button_style_section',
		array(
			'label_for'   => $id,
			'description' => $description,
		)
	);
	register_setting(
		'wcrl_settings_group',
		$id,
		array(
			'type'              => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
}

// Helper to add text field
function wcrl_add_text_field( $id, $title, $description ) {
	add_settings_field(
		$id,
		$title,
		'wcrl_text_field_callback',
		'wcrl-options',
		'wcrl_button_style_section',
		array(
			'label_for'   => $id,
			'description' => $description,
		)
	);
	register_setting(
		'wcrl_settings_group',
		$id,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
}

// Helper to add color field
function wcrl_add_color_field( $id, $title, $description ) {
	add_settings_field(
		$id,
		$title,
		'wcrl_color_field_callback',
		'wcrl-options',
		'wcrl_button_style_section',
		array(
			'label_for'   => $id,
			'description' => $description,
		)
	);
	register_setting(
		'wcrl_settings_group',
		$id,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
}

// Settings page HTML structure
function wcrl_settings_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Document Viewer Settings', 'product-document-viewer-for-woocommerce' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'wcrl_settings_group' ); ?>
			<?php do_settings_sections( 'wcrl-options' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Button Position', 'product-document-viewer-for-woocommerce' ); ?></th>
					<td>
						<select name="wcrl_button_position">
							<?php
							$positions         = array(
								'woocommerce_before_single_product_summary' => __( 'Before Single Product Summary', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_single_product_summary' => __( 'Single Product Summary', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_before_add_to_cart_form' => __( 'Before Add to Cart Form', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_before_variations_form' => __( 'Before Variations Form', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_before_add_to_cart_button' => __( 'Before Add to Cart Button', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_before_single_variation' => __( 'Before Single Variation', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_single_variation' => __( 'Single Variation', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_before_add_to_cart_quantity' => __( 'Before Add to Cart Quantity', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_after_add_to_cart_quantity' => __( 'After Add to Cart Quantity', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_after_single_variation' => __( 'After Single Variation', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_after_add_to_cart_button' => __( 'After Add to Cart Button', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_after_variations_form' => __( 'After Variations Form', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_after_add_to_cart_form' => __( 'After Add to Cart Form', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_product_meta_start' => __( 'Product Meta Start', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_product_meta_end' => __( 'Product Meta End', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_share' => __( 'Share', 'product-document-viewer-for-woocommerce' ),
								'woocommerce_after_single_product_summary' => __( 'After Single Product Summary', 'product-document-viewer-for-woocommerce' ),
							);
							$selected_position = get_option( 'wcrl_button_position', 'woocommerce_single_product_summary' );
							foreach ( $positions as $value => $label ) {
								echo '<option value="' . esc_attr( $value ) . '" ' . selected( $selected_position, $value, false ) . '>' . esc_html( $label ) . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Button Text', 'product-document-viewer-for-woocommerce' ); ?></th>
					<td>
						<input type="text" name="wcrl_button_text" value="<?php echo esc_html( get_option( 'wcrl_button_text', 'Read a Little' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Extra Button Class', 'product-document-viewer-for-woocommerce' ); ?></th>
					<td>
						<input type="text" name="wcrl_button_class" value="<?php echo esc_attr( get_option( 'wcrl_button_class', '' ) ); ?>" />
						<p class="description"><?php esc_html_e( 'You can enter multiple space-separated classes, e.g. "btn-lg my-class".', 'product-document-viewer-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Button Color', 'product-document-viewer-for-woocommerce' ); ?></th>
					<td>
						<input type="color" name="wcrl_button_color" value="<?php echo esc_attr( get_option( 'wcrl_button_color', '#0073aa' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Hook Priority', 'product-document-viewer-for-woocommerce' ); ?></th>
					<td>
						<?php $current_priority = get_option( 'wcrl_hook_priority', 30 ); ?>
						<select name="wcrl_hook_priority">
							<option value="10" <?php selected( $current_priority, 10 ); ?>><?php esc_html_e( 'Low Priority (10)', 'product-document-viewer-for-woocommerce' ); ?></option>
							<option value="20" <?php selected( $current_priority, 20 ); ?>><?php esc_html_e( 'Medium Priority (20)', 'product-document-viewer-for-woocommerce' ); ?></option>
							<option value="30" <?php selected( $current_priority, 30 ); ?>><?php esc_html_e( 'High Priority (30)', 'product-document-viewer-for-woocommerce' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// Register settings and sections
function wcrl_register_settings() {
	add_settings_section(
		'wcrl_button_style_section',
		__( 'Button Style Options', 'product-document-viewer-for-woocommerce' ),
		null,
		'wcrl-options'
	);

	// Checkbox fields
	wcrl_add_checkbox_field( 'wcrl_hide_button_position', __( 'Hide button - I\'m using shortcode', 'product-document-viewer-for-woocommerce' ), __( 'Hide the default display button, use shortcode instead. Here is the shortcode: [read_little_button]', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_checkbox_field( 'wcrl_button_rounded', __( 'Rounded Button', 'product-document-viewer-for-woocommerce' ), __( 'Make the button corners rounded.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_text_field( 'wcrl_button_round_size', __( 'Button Rounding Size', 'product-document-viewer-for-woocommerce' ), __( 'Set the button round size in pixels. E.g., 10px 20px.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_checkbox_field( 'wcrl_button_transparent_bg', __( 'Transparent Background', 'product-document-viewer-for-woocommerce' ), __( 'Set the button background to transparent.', 'product-document-viewer-for-woocommerce' ) );

	// Color fields
	wcrl_add_color_field( 'wcrl_button_font_color', __( 'Button Text Color', 'product-document-viewer-for-woocommerce' ), __( 'Select the button text color.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_color_field( 'wcrl_button_border_color', __( 'Button Border Color', 'product-document-viewer-for-woocommerce' ), __( 'Select the button border color.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_color_field( 'wcrl_button_hover_bg_color', __( 'Hover Background Color', 'product-document-viewer-for-woocommerce' ), __( 'Select the hover background color for the button.', 'product-document-viewer-for-woocommerce' ) );

	// Dimension fields
	wcrl_add_text_field( 'wcrl_button_width', __( 'Button Width (px)', 'product-document-viewer-for-woocommerce' ), __( 'Set the width of the button.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_text_field( 'wcrl_button_height', __( 'Button Height (px)', 'product-document-viewer-for-woocommerce' ), __( 'Set the height of the button.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_text_field( 'wcrl_button_margin', __( 'Button Margin', 'product-document-viewer-for-woocommerce' ), __( 'Set the margin for the button. E.g., 10px 5px.', 'product-document-viewer-for-woocommerce' ) );
	wcrl_add_text_field( 'wcrl_button_padding', __( 'Button Padding', 'product-document-viewer-for-woocommerce' ), __( 'Set the padding for the button.', 'product-document-viewer-for-woocommerce' ) );

	// Register general settings
	register_setting(
		'wcrl_settings_group',
		'wcrl_button_text',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'View Document',
		)
	);
	register_setting(
		'wcrl_settings_group',
		'wcrl_button_class',
		array(
			'type'              => 'string',
			// sanitize_html_class() only allows a single class token and would
			// mangle space-separated multi-class input (e.g. "btn-lg my-class").
			// Output is already escaped with esc_attr() when printed.
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_setting(
		'wcrl_settings_group',
		'wcrl_button_color',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	register_setting(
		'wcrl_settings_group',
		'wcrl_button_position',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	register_setting(
		'wcrl_settings_group',
		'wcrl_hook_priority',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 30,
		)
	);
}
add_action( 'admin_init', 'wcrl_register_settings', 20 );

// Checkbox field callback
function wcrl_checkbox_field_callback( $args ) {
	$option = get_option( $args['label_for'] );
	echo '<input type="checkbox" id="' . esc_attr( $args['label_for'] ) . '" name="' . esc_attr( $args['label_for'] ) . '" value="1" ' . checked( 1, $option, false ) . '>';
	echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
}

// Text field callback
function wcrl_text_field_callback( $args ) {
	$option = get_option( $args['label_for'] );
	echo '<input type="text" id="' . esc_attr( $args['label_for'] ) . '" name="' . esc_attr( $args['label_for'] ) . '" value="' . esc_attr( $option ) . '">';
	echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
}

// Color field callback
function wcrl_color_field_callback( $args ) {
	$option = get_option( $args['label_for'] );
	echo '<input type="color" id="' . esc_attr( $args['label_for'] ) . '" name="' . esc_attr( $args['label_for'] ) . '" value="' . esc_attr( $option ) . '">';
	echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
}