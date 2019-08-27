<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */

$custom_fields = get_post_custom();
$post_id       = get_the_ID();
$categories    = get_the_category();
$state = '';
$town  = '';
$media = '';
$csm    = '';

foreach ( $categories as $category ) {

	if ( $category->category_parent == 0 ) { // Town
		$town = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . $category->name . '</a>';
	}

	if ( $category->category_parent == 1129 ) { // Cat: '0State'
		$state = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . $category->name . '</a>';
	}

	if ( $category->category_parent == 1128 ) { // Cat: '0Print' (media type)
		$media = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . $category->name . '</a>';
	}

}

$cms_obj = get_the_terms( $post_id , 'cms' );
$cms = ( $cms_obj )
	? '<a href="' . esc_url( get_term_link( $cms_obj[0]->term_id, 'cms' ) ) . '">' . $cms_obj[0]->name . '</a>'
	: '';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clear' ); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<strong class="entry-title" style="clear: none;"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></strong>' ); ?>
	</header><!-- .entry-header -->
	<table class="tabular clear">
		<tbody>
			<tr class="border-bottom border-top">
				<td><?php if ( ! empty ( $custom_fields['alexa_rank'][0] ) ) { echo number_format( $custom_fields['alexa_rank'][0] ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['circ'][0] ) ) { echo number_format( $custom_fields['circ'][0] ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['wpt_loadtime'][0] ) ) { echo number_format( $custom_fields['wpt_loadtime'][0] / 1000, 1, '.', '' ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['wpt_bytes'][0] ) ) { echo number_format( $custom_fields['wpt_bytes'][0] / 1000000, 2, '.', '' ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['wpt_requests'][0] ) ) { echo $custom_fields['wpt_requests'][0]; } ?></td>
			</tr>
			<tr class="hover-none">
				<td rowspan="3" class="border-bottom" style="text-align: left">
					<a href="//<?php esc_url( the_title() ) ?>"><img class="alignleft" src="http://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=132&h=99" width="132" height="99" alt="Homepage" /></a></td>
				<td colspan="2" rowspan="3" class="entry-meta border-bottom" style="text-align: left; vertical-align: top;">
					<?php echo the_content(); ?>
					<?php echo $town; ?> <?php echo $state; ?><br />
					<?php echo $media; ?><?php if ( $cms ) { echo " <small>($cms)</small>"; } ?>
				</td>
				<td><small>Static: </small><?php if ( ! empty ( $custom_fields['gps_bytes_html'][0] ) ) { echo number_format( $custom_fields['gps_bytes_html'][0] / 1000000, 2, '.', '' ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['gps_resources_static'][0] ) ) { echo $custom_fields['gps_resources_static'][0]; } ?></td>
			</tr>
			<tr>
				<td><small>CSS: </small><?php if ( ! empty ( $custom_fields['gps_bytes_css'][0] ) ) { echo number_format( $custom_fields['gps_bytes_css'][0] / 1000000, 2, '.', '' ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['gps_resources_css'][0] ) ) { echo $custom_fields['gps_resources_css'][0]; } ?></td>
			</tr>
			<tr class="border-bottom">
				<td><small>JS: </small><?php if ( ! empty ( $custom_fields['gps_bytes_js'][0] ) ) { echo number_format( $custom_fields['gps_bytes_js'][0] / 1000000, 2, '.', '' ); } ?></td>
				<td><?php if ( ! empty ( $custom_fields['gps_resources_js'][0] ) ) { echo $custom_fields['gps_resources_js'][0]; } ?></td>
			</tr>
		</tbody>
	</table>
</article><!-- #post-## -->
<!-- =file: content -->
