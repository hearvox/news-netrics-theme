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
$state  = '';
$town   = '';
$media  = '';
$csm    = '';
$server = '';

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

/*

<div class="media-meta">
	<em>CMS/Server:</em> <?php if ( ! empty ( $custom_fields['cms'][0] ) ) { echo $custom_fields['cms'][0]; } else { echo '(unknown)'; } ?>/<?php if ( ! empty ( $custom_fields['server'][0] ) ) { echo $custom_fields['server'][0]; } else { echo '(unknown)'; } ?>
</div>

 */

$cms_obj = get_the_terms( $post_id , 'cms' );
$cms = ( $cms_obj )
	? '<a href="' . esc_url( get_term_link( $cms_obj[0]->term_id, 'cms' ) ) . '">' . $cms_obj[0]->name . '</a>'
	: '(unknown)';

$server_obj = get_the_terms( $post_id , 'server' );
$server = ( $server_obj )
	? '<a href="' . esc_url( get_term_link( $server_obj[0]->term_id, 'server' ) ) . '">' . $server_obj[0]->name . '</a>'
	: '(unknown)';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		<a href="//<?php esc_url( the_title() ) ?>"><img class="alignright screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=200&h=150" width="200" height="150" alt="Homepage screenshot" /></a>
		<div class="entry-meta">
			<div class="media-meta">
			<?php echo the_content(); ?> <?php echo $town; ?> <?php echo $state; ?>
			</div>

			<div class="media-meta padding-top">
				<em>Media type:</em> <?php echo $media; ?>
			</div>
			<div class="media-meta"><em>Circulation:</em> <?php if ( ! empty ( $custom_fields['circ'][0] ) ) { echo number_format( $custom_fields['circ'][0] ); } else { echo '(unknown)'; } ?>
			</div>
			<div class="media-meta"><em>Global Rank:</em> <?php if ( ! empty ( $custom_fields['alexa_rank'][0] ) ) { echo number_format( $custom_fields['alexa_rank'][0] ); } ?> <?php if ( ! empty ( $custom_fields['alexa_rank_us'][0] ) ) { echo '<small>(<em>USA:</em> ' . number_format( $custom_fields['alexa_rank_us'][0] ) . ')</small>'; } ?>
			</div>
			<div class="media-meta">
				<em>CMS/Server:</em> <?php echo $cms; ?> / <?php echo $server; ?>
			</div>
			<p class="media-meta"><em>Description:</em> <?php if ( ! empty ( $custom_fields['alexa_desc'][0] ) ) { echo $custom_fields['alexa_desc'][0]; } else { echo '(none)'; } ?></p>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<table class="tabular">
			<caption><h3>Website performance tests 2015-10</h3></caption>
			<thead>
			</thead>
			<tbody>
				<tr>
					<th colspan="2" style="padding: 0.4rem; text-align: left;"><em>WebPagetest.org</em></th>
					<th>TTFB</th>
					<th>Speed</th>
					<th>Load</th>
				</tr>

				<tr>
					<td colspan="2" style="padding-left: 2em;  text-align: left;">Times <small>(seconds)</small></td>
					<td><?php if ( ! empty ( $custom_fields['wpt_ttfb'][0] ) ) { echo number_format( $custom_fields['wpt_ttfb'][0] / 1000, 1, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['wpt_speedindex'][0] ) ) { echo number_format( $custom_fields['wpt_speedindex'][0] / 1000, 1, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['wpt_loadtime'][0] ) ) { echo number_format( $custom_fields['wpt_loadtime'][0] / 1000, 1 ); } ?></td>
				</tr>
				<tr>
					<th colspan="3">DOM</th>
					<th>Size <small>(MB)</small></th>
					<th>Requests</th>
				</tr>
				<tr>
					<td colspan="2" style="padding-left: 2em;  text-align: left;">Page DOM (HTML nodes), Size (weight), Requests (files)</small></td>
					<td><?php if ( ! empty ( $custom_fields['wpt_dom'][0] ) ) { echo $custom_fields['wpt_dom'][0]; } ?></td>
					<td><?php if ( ! empty ( $custom_fields['wpt_bytes'][0] ) ) { echo number_format( $custom_fields['wpt_bytes'][0] / 1000000, 2, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['wpt_requests'][0] ) ) { echo $custom_fields['wpt_requests'][0]; } ?></td>
				</tr>
				<tr>
					<th colspan="3" style="padding: 0.4rem; text-align: left;"><em>Google PageSpeed Insights</em></th>
					<th>Size <small>(MB)</small></th>
					<th>Requests</th>
				</tr>
				<tr>
					<td colspan="4"><?php if ( ! empty ( $custom_fields['gps_bytes'][0] ) ) { echo number_format( $custom_fields['gps_bytes'][0] / 10000, 2, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_resources'][0] ) ) { echo $custom_fields['gps_resources'][0]; } ?></td>
				</tr>
				<tr>
					<th style="padding-left: 2em; text-align: left;">By file type</th>
					<th>HTML/Static</th>
					<th>Other</th>
					<th>CSS</th>
					<th>JS</th>
				</tr>
				<tr>
					<td style="padding-left: 4em; text-align: left;">Requests</td>
					<td><?php if ( ! empty ( $custom_fields['gps_resources_static'][0] ) ) { echo $custom_fields['gps_resources_static'][0]; } ?></td>
					<td></td>
					<td><?php if ( ! empty ( $custom_fields['gps_resources_css'][0] ) ) { echo $custom_fields['gps_resources_css'][0]; } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_resources_js'][0] ) ) { echo $custom_fields['gps_resources_js'][0]; } ?></td>
				</tr>
				<tr>
					<td style="padding-left: 4em; text-align: left;">Size <small>(MB)</small></td>
					<td><?php if ( ! empty ( $custom_fields['gps_bytes_html'][0] ) ) { echo number_format( $custom_fields['gps_bytes_html'][0]/ 1000000, 2, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_bytes_other'][0] ) ) { echo number_format( $custom_fields['gps_bytes_other'][0]/ 1000000, 2, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_bytes_css'][0] ) ) { echo number_format( $custom_fields['gps_bytes_css'][0]/ 1000000, 2, '.', '' ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_bytes_js'][0] ) ) { echo number_format( $custom_fields['gps_bytes_js'][0] / 1000000, 2, '.', '' ); } ?></td>
				</tr>
				<tr>
					<th colspan="2" style="padding-left: 2em; text-align: left;">Scores <small>(0-100)</small></th>
					<th>Desktop</th>
					<th>Mobile</th>
					<th>Mobile UX</th>
				</tr>
				<tr>
					<td colspan="3"><?php if ( ! empty ( $custom_fields['gps_desktop'][0] ) ) { echo $custom_fields['gps_desktop'][0]; } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_mobi_ux'][0] ) ) { echo $custom_fields['gps_mobi_ux'][0]; } ?></td>
					<td><?php if ( ! empty ( $custom_fields['gps_mobi_ux'][0] ) ) { echo $custom_fields['gps_mobi_ux'][0]; } ?></td>
				</tr>
				<tr>

				</tr>
				<tr>
					<th style="padding: 0.4rem; text-align: left;"><em>SimilarWeb</em></th>
					<th>Visits</th>
					<th>Page-Per</th>
					<th>Time On</th>
					<th>Bounce</th>
				</tr>
					<td colspan="2"><?php if ( ! empty ( $custom_fields['sw_visits'][0] ) ) { echo number_format( $custom_fields['sw_visits'][0] ); } ?></td>
					<td><?php if ( ! empty ( $custom_fields['sw_pgspervisit'][0] ) ) { echo $custom_fields['sw_pgspervisit'][0]; } ?></td>
					<td><?php if ( ! empty ( $custom_fields['sw_timeonsite'][0] ) ) { echo $custom_fields['sw_timeonsite'][0]; } ?></td>
					<td><?php if ( ! empty ( $custom_fields['sw_bounce'][0] ) ) { echo $custom_fields['sw_bounce'][0]; } ?>%</td>
				</tr>
				<tr>
					<th style="padding: 0.4rem; text-align: left;"><em>BuiltWith</em> (techs)</th>
					<th>Ads</th>
					<th>Media</th>
					<th>Analytics</th>
					<th>Total</th>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td colspan="5"><?php if ( ! empty ( $custom_fields['bw_techs'][0] ) ) { echo $custom_fields['bw_techs'][0]; } ?></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<p>
		<?php
		global $post;
		$rss = detect_feed( 'http://' . $post->post_title );
		$rss = str_replace( '&s=start_time&sd=desc&k%5B%5D=%23topstory', '', $rss); // BLOX feeds

		?>
		<p>Feed: <a href="<?php echo $rss; ?>"><?php echo $rss; ?></a></p>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php the_post_navigation( array( 'prev_text' => '&laquo; %title &laquo;', 'next_text' => '&raquo; %title &raquo;' ) ); ?>

		<div class="media-meta"><em>Date online:</em> <?php if ( ! empty ( $custom_fields['alexa_date'][0] ) ) { echo $custom_fields['alexa_date'][0]; } ?></div>
		<div class="media-meta"><em>Owner:</em> <?php if ( ! empty ( $custom_fields['alexa_owner'][0] ) ) { echo $custom_fields['alexa_owner'][0]; } ?></div>

		<p style="padding-top: 2em;"><img class="screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=700&h=525" width="700" height="525" alt="Homepage screenshot" /></p>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

