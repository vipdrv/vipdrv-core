<?php
/**
 * WooCommerce Catalog Ordering template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

$query_string = '';
if ( isset( $_SERVER['QUERY_STRING'] ) ) {
	parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params );
	$query_string = '?' . sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) );
}

// Replace it with theme option.
$per_page = 12;
if ( Avada()->settings->get( 'woo_items' ) ) {
	$per_page = Avada()->settings->get( 'woo_items' );
}

$pob = ! empty( $params['product_orderby'] ) ? $params['product_orderby'] : get_option( 'woocommerce_default_catalog_orderby' );

if ( ! empty( $params['product_order'] ) ) {
	$po = $params['product_order'];
} else {
	switch ( $pob ) {
		case 'default':
		case 'menu_order':
		case 'price':
		case 'name':
			$po = 'asc';
			break;
		case 'price-desc':
		default:
			$po = 'desc';
			break;
	}
}

$order_string = esc_attr__( 'Default Order', 'Avada' );

switch ( $pob ) {
	case 'date':
		$order_string = esc_attr__( 'Date', 'Avada' );
		break;
	case 'price':
	case 'price-desc':
		$order_string = esc_attr__( 'Price', 'Avada' );
		break;
	case 'popularity':
		$order_string = esc_attr__( 'Popularity', 'Avada' );
		break;
	case 'rating':
		$order_string = esc_attr__( 'Rating', 'Avada' );
		break;
	case 'name':
		$order_string = esc_attr__( 'Name', 'Avada' );
		break;
}

$pc = ! empty( $params['product_count'] ) ? $params['product_count'] : $per_page;
?>

<div class="catalog-ordering clearfix">
	<div class="orderby-order-container">
		<ul class="orderby order-dropdown">
			<li>
				<span class="current-li">
					<span class="current-li-content">
						<a aria-haspopup="true"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr( $order_string ) . '</strong>' ); ?></a>
					</span>
				</span>
				<ul>
					<li class="<?php echo ( 'menu_order' === $pob ) ? 'current' : ''; ?>">
						<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_orderby', 'default' ) ); ?>"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr__( 'Default Order', 'Avada' ) . '</strong>' ); ?></a>
					</li>
					<li class="<?php echo ( 'name' === $pob ) ? 'current' : ''; ?>">
						<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_orderby', 'name' ) ); ?>"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr__( 'Name', 'Avada' ) . '</strong>' ); ?></a>
					</li>
					<li class="<?php echo ( 'price' === $pob || 'price-desc' === $pob ) ? 'current' : ''; ?>">
						<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_orderby', 'price' ) ); ?>"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr__( 'Price', 'Avada' ) . '</strong>' ); ?></a>
					</li>
					<li class="<?php echo ( 'date' === $pob ) ? 'current' : ''; ?>">
						<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_orderby', 'date' ) ); ?>"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr__( 'Date', 'Avada' ) . '</strong>' ); ?></a>
					</li>
					<li class="<?php echo ( 'popularity' === $pob ) ? 'current' : ''; ?>">
						<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_orderby', 'popularity' ) ); ?>"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr__( 'Popularity', 'Avada' ) . '</strong>' ); ?></a>
					</li>
					<?php if ( 'no' !== get_option( 'woocommerce_enable_review_rating' ) ) : ?>
						<li class="<?php echo ( 'rating' === $pob ) ? 'current' : ''; ?>">
							<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_orderby', 'rating' ) ); ?>"><?php printf( esc_html__( 'Sort by %s', 'Avada' ), '<strong>' . esc_attr__( 'Rating', 'Avada' ) . '</strong>' ); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		</ul>

		<ul class="order">
			<?php if ( isset( $po ) ) : ?>
				<?php if ( 'desc' === $po ) : ?>
					<li class="desc"><a aria-label="<?php esc_attr_e( 'Ascending order', 'Avada' ); ?>" aria-haspopup="true" href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_order', 'asc' ) ); ?>"><i class="fusion-icon-arrow-down2 icomoon-up"></i></a></li>
				<?php else : ?>
					<li class="asc"><a aria-label="<?php esc_attr_e( 'Descending order', 'Avada' ); ?>" aria-haspopup="true" href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_order', 'desc' ) ); ?>"><i class="fusion-icon-arrow-down2"></i></a></li>
				<?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>

	<ul class="sort-count order-dropdown">
		<li>
			<span class="current-li"><a aria-haspopup="true"><?php printf( __( 'Show <strong>%s Products</strong>', 'Avada' ), (int) $per_page ); // WPCS: XSS ok. ?></a></span>
			<ul>
				<li class="<?php echo ( $pc == $per_page ) ? 'current' : ''; ?>">
					<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_count', $per_page ) ); ?>"><?php printf( __( 'Show <strong>%s Products</strong>', 'Avada' ), (int) $per_page ); // WPCS: XSS ok. ?></a>
				</li>
				<li class="<?php echo ( $pc == $per_page * 2 ) ? 'current' : ''; ?>">
					<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_count', $per_page * 2 ) ); ?>"><?php printf( __( 'Show <strong>%s Products</strong>', 'Avada' ), (int) $per_page * 2 ); // WPCS: XSS ok. ?></a>
				</li>
				<li class="<?php echo ( $pc == $per_page * 3 ) ? 'current' : ''; ?>">
					<a href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_count', $per_page * 3 ) ); ?>"><?php printf( __( 'Show <strong>%s Products</strong>', 'Avada' ), (int) $per_page * 3 ); // WPCS: XSS ok. ?></a>
				</li>
			</ul>
		</li>
	</ul>

	<?php $woocommerce_toggle_grid_list = Avada()->settings->get( 'woocommerce_toggle_grid_list' ); ?>
	<?php $product_view = 'grid'; ?>
	<?php if ( isset( $_SERVER['QUERY_STRING'] ) ) : ?>
		<?php parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params ); ?>
		<?php if ( isset( $params['product_view'] ) ) : ?>
			<?php $product_view = $params['product_view']; ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $woocommerce_toggle_grid_list ) : ?>
		<ul class="fusion-grid-list-view">
			<li class="fusion-grid-view-li<?php echo ( 'grid' === $product_view ) ? ' active-view' : ''; ?>">
				<a class="fusion-grid-view" aria-label="<?php esc_attr_e( 'View as grid', 'Avada' ); ?>" aria-haspopup="true" href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_view', 'grid' ) ); ?>"><i class="fusion-icon-grid icomoon-grid"></i></a>
			</li>
			<li class="fusion-list-view-li<?php echo ( 'list' == $product_view ) ? ' active-view' : ''; ?>">
				<a class="fusion-list-view" aria-haspopup="true" aria-label="<?php esc_attr_e( 'View as list', 'Avada' ); ?>" href="<?php echo esc_url_raw( fusion_add_url_parameter( $query_string, 'product_view', 'list' ) ); ?>"><i class="fusion-icon-list icomoon-list"></i></a>
			</li>
		</ul>
	<?php endif; ?>
</div>
