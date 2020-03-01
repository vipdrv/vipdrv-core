<?php
/**
 * Fusion Builder underscore.js templates.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Adds the pagebuilder metabox.
 */
function fusion_pagebuilder_meta_box() {

	global $post;

	// Add RTL CSS class.
	$rtl_class = ( is_rtl() ) ? 'fusion-builder-layout-rtl' : '';

	do_action( 'fusion_builder_before' );

	wp_nonce_field( 'fusion_builder_template', 'fusion_builder_nonce' );

	// Custom CSS.
	$saved_custom_css = esc_attr( get_post_meta( $post->ID, '_fusion_builder_custom_css', true ) );
	$has_custom_css   = ( ! empty( $saved_custom_css ) ) ? 'fusion-builder-has-custom-css' : '';
	?>

	<div id="fusion_builder_main_container" class="<?php echo esc_attr( $rtl_class ); ?>" data-post-id="<?php echo esc_attr( $post->ID ); ?>"></div>
	<?php
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/app.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/multi-element-sortable-child.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/blank-page.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/container.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/row.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/nested-row.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/modal.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/column.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/nested-column.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/column-library.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/element-library.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/generator-elements.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/element.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/element-settings.php' );
	include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/next-page.php' );

	do_action( 'fusion_builder_after' );
}

/**
 * The template for options.
 *
 * @param array $params The parameters for the option.
 */
function fusion_element_options_loop( $params ) {
	?>
	<ul class="fusion-builder-module-settings {{ atts.element_type }}">

		<# _.each( <?php echo $params; // WPCS: XSS ok. ?>, function(param) { #>

			<# option_value = typeof( atts.added ) !== 'undefined' ? param.value : atts.params[param.param_name] #>

			<# if ( param.type == 'select' || param.type == 'multiple_select' || param.type == 'radio_button_set' || param.type == 'checkbox_button_set' ) { #>
				<# option_value = ( 'undefined' !== typeof( atts.added ) || '' === atts.params[param.param_name] || 'undefined' === typeof(atts.params[param.param_name]) ) ? param.default : atts.params[param.param_name]; #>
			<# }; #>

			<# if ( 'fusion_code' === atts.element_type && 1 === Number( FusionPageBuilderApp.disable_encoding ) ) {
				if ( FusionPageBuilderApp.base64Encode( FusionPageBuilderApp.base64Decode( option_value ) ) === option_value ) {
					option_value = FusionPageBuilderApp.base64Decode( option_value );
				}
			} #>

			<# option_value = _.unescape(option_value); #>

			<# hidden = typeof( param.hidden ) !== 'undefined' ? ' hidden' : '' #>

			<# childDependency = typeof( param.child_dependency ) !== 'undefined' ? ' has-child-dependency' : '' #>
			<li data-option-id="{{ param.param_name }}" class="fusion-builder-option {{ param.type }}{{ hidden }}{{ childDependency }}">

				<div class="option-details">
					<# if ( typeof( param.heading ) !== 'undefined' ) { #>
						<h3>{{ param.heading }}</h3>
					<# }; #>

					<# if ( typeof( param.description ) !== 'undefined' ) { #>
						<p class="description">{{{ param.description }}}</p>
					<# }; #>
				</div>

				<div class="option-field fusion-builder-option-container">
					<?php
					$field_types = array(
						'textarea',
						'textfield',
						'range',
						'colorpickeralpha',
						'colorpicker',
						'select',
						'upload',
						'uploadfile',
						'uploadattachment',
						'tinymce',
						'iconpicker',
						'multiple_select',
						'multiple_upload',
						'checkbox_button_set',
						'radio_button_set',
						'radio_image_set',
						'link_selector',
						'date_time_picker',
						'upload_images',
						'dimension',
						'code',
					);
					?>
					<?php
						$fields = apply_filters( 'fusion_builder_fields', $field_types );
					?>
					<?php foreach ( $fields as $field_type ) : ?>
						<?php if ( is_array( $field_type ) && ! empty( $field_type ) ) : ?>
							<# if ( '<?php echo $field_type[0]; // WPCS: XSS ok. ?>' == param.type ) { #>
							<?php include wp_normalize_path( $field_type[1] ); ?>
							<# }; #>
						<?php else : ?>
						<# if ( '<?php echo $field_type; // WPCS: XSS ok. ?>' == param.type ) { #>
							<?php include wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/templates/options/' . $field_type . '.php' ); ?>
						<# }; #>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</li>

		<# } ); #>

	</ul>
	<?php
}
