<?php
/**
 * Single template for DinoPack template CPTs (Header, Footer, Side Panel).
 * Wraps Elementor content in a consistent wrapper for styling and targeting.
 * In Elementor editor/preview: minimal document so theme header/footer are hidden.
 *
 * @package DinoPack
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$post_type = get_post_type();
$type_slug = str_replace( 'dinopack-', '', $post_type );
$type_slug = str_replace( '-', '_', $type_slug );
$wrapper_class = 'dinopack-template-wrapper dinopack-template-wrapper--' . esc_attr( $type_slug );

$is_elementor_preview = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode();

if ( $is_elementor_preview ) {
	// Editor/preview: minimal document, content only (theme header/footer hidden).
	?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
} else {
	get_header();
}
?>

<div id="dinopack-template-<?php echo esc_attr( $post_type ); ?>" class="<?php echo esc_attr( $wrapper_class ); ?>" data-dinopack-type="<?php echo esc_attr( $type_slug ); ?>">
	<div class="dinopack-template-inner">
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
</div>

<?php
if ( $is_elementor_preview ) {
	wp_footer();
	?></body>
</html><?php
} else {
	get_footer();
}
