<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Add WooCommerce submenu under "WooCommerce"
function wcrl_add_admin_menu() {
    add_submenu_page(
        'woocommerce',
        __( 'Read Little Settings', 'read-a-little-for-woocommerce' ),
        __( 'Read Little Settings', 'read-a-little-for-woocommerce' ),
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
        [
            'label_for'    => $id,
            'description'  => $description,
        ]
    );
    register_setting( 'wcrl_settings_group', $id );
}

// Helper to add text field
function wcrl_add_text_field( $id, $title, $description ) {
    add_settings_field(
        $id,
        $title,
        'wcrl_text_field_callback',
        'wcrl-options',
        'wcrl_button_style_section',
        [
            'label_for'    => $id,
            'description'  => $description,
        ]
    );
    register_setting( 'wcrl_settings_group', $id );
}

// Helper to add color field
function wcrl_add_color_field( $id, $title, $description ) {
    add_settings_field(
        $id,
        $title,
        'wcrl_color_field_callback',
        'wcrl-options',
        'wcrl_button_style_section',
        [
            'label_for'    => $id,
            'description'  => $description,
        ]
    );
    register_setting( 'wcrl_settings_group', $id );
}

// Settings page HTML structure
function wcrl_settings_page_html() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Read Little Settings', 'read-a-little-for-woocommerce' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'wcrl_settings_group' ); ?>
            <?php do_settings_sections( 'wcrl-options' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Button Position', 'read-a-little-for-woocommerce' ); ?></th>
                    <td>
                        <select name="wcrl_button_position">
                            <?php
                            $positions = [
                                'woocommerce_before_single_product_summary' => __( 'Before Single Product Summary', 'read-a-little-for-woocommerce' ),
                                'woocommerce_single_product_summary'        => __( 'Single Product Summary', 'read-a-little-for-woocommerce' ),
                                'woocommerce_before_add_to_cart_form'       => __( 'Before Add to Cart Form', 'read-a-little-for-woocommerce' ),
                                'woocommerce_before_variations_form'        => __( 'Before Variations Form', 'read-a-little-for-woocommerce' ),
                                'woocommerce_before_add_to_cart_button'     => __( 'Before Add to Cart Button', 'read-a-little-for-woocommerce' ),
                                'woocommerce_before_single_variation'       => __( 'Before Single Variation', 'read-a-little-for-woocommerce' ),
                                'woocommerce_single_variation'              => __( 'Single Variation', 'read-a-little-for-woocommerce' ),
                                'woocommerce_before_add_to_cart_quantity'   => __( 'Before Add to Cart Quantity', 'read-a-little-for-woocommerce' ),
                                'woocommerce_after_add_to_cart_quantity'    => __( 'After Add to Cart Quantity', 'read-a-little-for-woocommerce' ),
                                'woocommerce_after_single_variation'        => __( 'After Single Variation', 'read-a-little-for-woocommerce' ),
                                'woocommerce_after_add_to_cart_button'      => __( 'After Add to Cart Button', 'read-a-little-for-woocommerce' ),
                                'woocommerce_after_variations_form'         => __( 'After Variations Form', 'read-a-little-for-woocommerce' ),
                                'woocommerce_after_add_to_cart_form'        => __( 'After Add to Cart Form', 'read-a-little-for-woocommerce' ),
                                'woocommerce_product_meta_start'            => __( 'Product Meta Start', 'read-a-little-for-woocommerce' ),
                                'woocommerce_product_meta_end'              => __( 'Product Meta End', 'read-a-little-for-woocommerce' ),
                                'woocommerce_share'                         => __( 'Share', 'read-a-little-for-woocommerce' ),
                                'woocommerce_after_single_product_summary'  => __( 'After Single Product Summary', 'read-a-little-for-woocommerce' ),
                            ];
                            $selected_position = get_option( 'wcrl_button_position', 'woocommerce_single_product_summary' );
                            foreach ( $positions as $value => $label ) {
                                echo '<option value="' . esc_attr( $value ) . '" ' . selected( $selected_position, $value, false ) . '>' . esc_html( $label ) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Button Text', 'read-a-little-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="wcrl_button_text" value="<?php echo esc_attr( get_option( 'wcrl_button_text', 'Read a Little' ) ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Extra Button Class', 'read-a-little-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="wcrl_button_class" value="<?php echo esc_attr( get_option( 'wcrl_button_class', '' ) ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Button Color', 'read-a-little-for-woocommerce' ); ?></th>
                    <td>
                        <input type="color" name="wcrl_button_color" value="<?php echo esc_attr( get_option( 'wcrl_button_color', '#0073aa' ) ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Hook Priority', 'read-a-little-for-woocommerce' ); ?></th>
                    <td>
                        <select name="wcrl_hook_priority">
                            <option value="10" <?php selected( get_option( 'wcrl_hook_priority', '10' ), '10' ); ?>><?php esc_html_e( 'Low Priority (10)', 'read-a-little-for-woocommerce' ); ?></option>
                            <option value="20" <?php selected( get_option( 'wcrl_hook_priority', '20' ), '20' ); ?>><?php esc_html_e( 'Medium Priority (20)', 'read-a-little-for-woocommerce' ); ?></option>
                            <option value="30" <?php selected( get_option( 'wcrl_hook_priority', '30' ), '30' ); ?>><?php esc_html_e( 'High Priority (30)', 'read-a-little-for-woocommerce' ); ?></option>
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
        __( 'Button Style Options', 'read-a-little-for-woocommerce' ),
        null,
        'wcrl-options'
    );

    // Checkbox fields
    wcrl_add_checkbox_field( 'wcrl_button_rounded', __( 'Rounded Button', 'read-a-little-for-woocommerce' ), __( 'Make the button corners rounded.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_text_field( 'wcrl_button_round_size', __( 'Button Rounding Size', 'read-a-little-for-woocommerce' ), __( 'Set the button round size in pixels. E.g., 10px 20px.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_checkbox_field( 'wcrl_button_transparent_bg', __( 'Transparent Background', 'read-a-little-for-woocommerce' ), __( 'Set the button background to transparent.', 'read-a-little-for-woocommerce' ) );

    // Color fields
    wcrl_add_color_field( 'wcrl_button_font_color', __( 'Button Text Color', 'read-a-little-for-woocommerce' ), __( 'Select the button text color.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_color_field( 'wcrl_button_border_color', __( 'Button Border Color', 'read-a-little-for-woocommerce' ), __( 'Select the button border color.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_color_field( 'wcrl_button_hover_bg_color', __( 'Hover Background Color', 'read-a-little-for-woocommerce' ), __( 'Select the hover background color for the button.', 'read-a-little-for-woocommerce' ) );

    // Dimension fields
    wcrl_add_text_field( 'wcrl_button_width', __( 'Button Width (px)', 'read-a-little-for-woocommerce' ), __( 'Set the width of the button.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_text_field( 'wcrl_button_height', __( 'Button Height (px)', 'read-a-little-for-woocommerce' ), __( 'Set the height of the button.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_text_field( 'wcrl_button_margin', __( 'Button Margin', 'read-a-little-for-woocommerce' ), __( 'Set the margin for the button. E.g., 10px 5px.', 'read-a-little-for-woocommerce' ) );
    wcrl_add_text_field( 'wcrl_button_padding', __( 'Button Padding', 'read-a-little-for-woocommerce' ), __( 'Set the padding for the button.', 'read-a-little-for-woocommerce' ) );

    // Register general settings
    register_setting( 'wcrl_settings_group', 'wcrl_button_text' );
    register_setting( 'wcrl_settings_group', 'wcrl_button_class' );
    register_setting( 'wcrl_settings_group', 'wcrl_button_color' );
    register_setting( 'wcrl_settings_group', 'wcrl_button_position' );
    register_setting( 'wcrl_settings_group', 'wcrl_hook_priority' );
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
