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

$server_obj = get_the_terms( $post_id , 'server' );
$server     = ( $server_obj )
	? '<a href="' . esc_url( get_term_link( $server_obj[0]->term_id, 'server' ) ) . '">' . $server_obj[0]->name . '</a>'
	: '(unknown)';

$rss_url  = ( isset( $custom_fields['nn_pub_rss'][0] ) ) ? $custom_fields['nn_pub_rss'][0] : false;
$rss_link = ( $rss_url ) ? ' | <a href="' . esc_url( $rss_url ) . '">RSS feed</a>' : '';

$site_url  = ( isset( $custom_fields['nn_pub_url'][0] ) ) ? $custom_fields['nn_pub_url'][0] : false;
$site_link = ( $site_url ) ? ' <a href="' . esc_url( $site_url ) . '">Website</a>' : '';

$pub_name  = ( isset( $custom_fields['nn_pub_name'][0] ) ) ? $custom_fields['nn_pub_name'][0] : '';
$pub_year  = ( isset( $custom_fields['nn_pub_year'][0] ) ) ? $custom_fields['nn_pub_year'][0] : '';
$pub_circ  = ( isset( $custom_fields['nn_circ'][0] ) )
	? number_format( absint( $custom_fields['nn_circ'][0] ) ) : '--';
$pub_rank  = ( isset( $custom_fields['nn_rank'][0] ) )
	? number_format( absint( $custom_fields['nn_rank'][0] ) ) : '--';

$term_region = get_the_terms( $post_id, 'region' );
$term_id     = ($term_region) ? $term_region[0]->term_id : false;

$term_pop  = ( get_term_meta( $term_id, 'nn_region_pop', true ) )
	? get_term_meta( $term_id, 'nn_region_pop', true ) : false;
$city_pop  = ( $term_pop ) ? ' (<em>Pop.:</em> ' . number_format( floatval( $term_pop ) ) . ')' : '';

$args_region = array(
	'format'    => 'id',
	'separator' => ' / ',
);
$regions = get_term_parents_list( $term_id, 'region', $args_region ) ;

$site_ps = netrics_site_pagespeed( $post_id );
$pgspeed = '';

if ( $site_ps  && isset( $site_ps['score'] ) ) {
	$pgspeed .=  '<em>Score:</em> ' . round( $site_ps['score'] * 100, 1 );
	$pgspeed .=  ' | <em>Speed/TTI:</em> ' . round( $site_ps['speed'] / 1000, 1 ) . 's';
	$pgspeed .=  ' / ' . round( $site_ps['tti']  / 1000, 1 ) . 's';
	$pgspeed .=  ' | <em>Size</em>: ' . size_format( $site_ps['size'], 1 );
} else {
	$pgspeed = '<em>Score:</em> [No Pagespeed results.]';
}

?>



<article id="post-<?php the_ID(); ?>" <?php post_class( 'clear' ); ?> style="border-top: 1px solid #c9cdda;">
	<header class="entry-header">
		<a href="<?php esc_url( the_permalink() ); ?>"><img class="alignright" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=140&h=105" width="140" height="105" alt="<?php the_title(); ?>" /></a>
		<?php the_title( sprintf( '<strong class="entry-title" style="clear: none;"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></strong>' ); ?><br>
		<strong><?php echo $pub_name; ?></strong> <em style="font-size: 0.8rem;">(<?php echo "Circ: $pub_circ / Rank: $pub_rank"; ?>)</em>
		<ul class="media-meta" style="font-size: 0.8rem; list-style: none; margin: 0; padding: 0;">
			<li><em>Location:</em> <?php echo trim( $regions, ' / ' ); ?><?php echo $city_pop; ?></li>
			<li><?php the_terms( $post_id, 'owner', '<em>Owner:</em> ' ); ?> | <?php echo $site_link; ?><?php the_terms( $post_id, 'cms', ' (<em>CMS:</em> ', '/', ')' ); ?><?php edit_post_link( __( 'edit', 'textdomain' ), ' <em>[', ']</em>' ); ?></li>
			<li><?php echo $pgspeed; ?></li>
		</ul>
	</header><!-- .entry-header -->
<!-- img class="" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=200&h=150" width="200" height="150" alt="<?php the_title(); ?>" /></a -->
</article><!-- #post-## -->
<!-- =file: content-archive -->
