<?php
/**
 * Template part for displaying single CPT posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */

$custom_fields = get_post_custom();
$post_id       = get_the_ID();

$rss_url  = ( isset( $custom_fields['nn_pub_rss'][0] ) ) ? $custom_fields['nn_pub_rss'][0] : false;
$rss_link = ( $rss_url ) ? ' | <a href="' . esc_url( $rss_url ) . '">RSS feed</a>' : '';

$site_url  = ( isset( $custom_fields['nn_pub_url'][0] ) ) ? $custom_fields['nn_pub_url'][0] : false;
$site_link = ( $site_url ) ? ' <a href="' . esc_url( $site_url ) . '">Website</a>' : '';

$pub_name    = ( isset( $custom_fields['nn_pub_name'][0] ) ) ? $custom_fields['nn_pub_name'][0] : '';
$pub_year    = ( isset( $custom_fields['nn_pub_year'][0] ) && $custom_fields['nn_pub_year'][0] )
    ? absint( $custom_fields['nn_pub_year'][0] ) : '--';
$pub_circ    = ( isset( $custom_fields['nn_circ'][0] ) && $custom_fields['nn_circ'][0] )
	? number_format( absint( $custom_fields['nn_circ'][0] ) ) : '--';
$pub_rank    = ( isset( $custom_fields['nn_rank'][0] ) && $custom_fields['nn_rank'][0] )
    ? number_format( absint( $custom_fields['nn_rank'][0] ) ) : '--';

$term_region = get_the_terms( $post_id, 'region' );
$term_id     = ($term_region) ? $term_region[0]->term_id : false;

$term_pop  = ( get_term_meta( $term_id, 'nn_region_pop', true ) )
	? get_term_meta( $term_id, 'nn_region_pop', true ) : false;
$term_pop  = ( $term_pop ) ? number_format( floatval( $term_pop ) ) : '';

$args_region = array(
	'format'    => 'id',
	'separator' => ' &gt; ',
);
$regions = get_term_parents_list( $term_id, 'region', $args_region ) ;

$city       = $term_region[0]->name;
$terms_reg  = get_ancestors( $term_id, 'region', 'taxonomy' );
$state_id   = end( $terms_reg );
$state_arr  = get_term_by( 'id', absint( $state_id ), 'region' );
$state      = $state_arr->name;
$latlon     = get_term_meta( $term_id, 'nn_region_latlon', true );

$map_api = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc';
$map_loc = '&q=' . urlencode( $city ) . '+' . $state;
$map_ctr = '&amp;center=' . str_replace( '|', ',', $latlon);
$map_src = $map_api .$map_loc . $map_ctr;

// Alexa Web Info Service
// Use get_post_meta to unserialize, which above get_post_custom() doesn't.
$nn_site    = get_post_meta( $post_id , 'nn_site' );
$awis_desc  = ( isset( $nn_site[0]['alexa']['desc']  ) && $nn_site[0]['alexa']['desc'] )
    ? '&mdash; ' . $nn_site[0]['alexa']['desc'] : '';
$awis_rank  = ( isset( $nn_site[0]['alexa']['rank'] ) && $nn_site[0]['alexa']['rank'] )
    ? number_format( floatval($nn_site[0]['alexa']['rank'] ) ) : '--';
$awis_since = ( isset( $nn_site[0]['alexa']['since']  ) && $nn_site[0]['alexa']['since'] )
    ? date_parse_from_format( 'd-M-Y', $nn_site[0]['alexa']['since'] ) : false;
$awis_year  = ( $awis_since ) ? absint( $awis_since['year'] ) : '--';
$awis_links = ( isset( $nn_site[0]['alexa']['links']  ) && $nn_site[0]['alexa']['links'] )
    ? number_format( (int) $nn_site[0]['alexa']['links'] ) : '--';


/*

$city    = $term_region[0]->name;
$state   = end( get_ancestors( $term_id ) );
$latlon  = explode( get_term_meta( $term_id, 'nn_region_latlon', true ) );
$map_api = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc';
$map_loc = '&q=' . urlencode( $city ) . '+' . $state[0]->name;
$map_ctr = '&center=' . $latlon[0] . ',' . $latlon[1];
$map_src = $map_api .$map_loc . $map_ctr ;
*/

$psi_1905 = netrics_site_pagespeed( $post_id, 'nn_articles_201905' ); // si: 13.9, tti: 30.1, speed: 18.8
$psi_1906 = netrics_site_pagespeed( $post_id, 'nn_articles_201906' ); // si: 13.3, tti: 30.3, speed: 18.8
$psi_1907 = netrics_site_pagespeed( $post_id, 'nn_articles_201907' ); // si: 13.5, tti: 29.7, speed: 20.4

$lines = $bars = '';
if ( $psi_1905 ) {
    // Column chart data.
    $bars .= "['5/19', ";
    $bars .= round( $psi_1905['speed'] / 1000, 1 ) . ', ';
    $bars .= round( $psi_1905['tti'] / 1000, 1 ) . ",30.1],\n";

}

if ( $psi_1906 ) {
    // Column chart data.
    $bars .= "['6/19', ";
    $bars .= round( $psi_1906['speed'] / 1000, 1 ) . ', ';
    $bars .= round( $psi_1906['tti'] / 1000, 1 ) . ",30.3],\n";
}

if ( $psi_1907 ) {
    // Column chart data.
    $bars .= "['7/19', ";
    $bars .= round( $psi_1907['speed'] / 1000, 1 ) . ', ';
    $bars .= round( $psi_1907['tti'] / 1000, 1 ) . ",29.7],\n";
}

$articles_1905 = get_post_meta( $post_id, 'nn_articles_201905', true );
$articles_1906 = get_post_meta( $post_id, 'nn_articles_201906', true );
$articles_1907 = get_post_meta( $post_id, 'nn_articles_201907', true );

$score = ( isset( $psi_1907['score'] ) ) ? number_format( $psi_1907['score'] * 100, 1, '.', ',' ) : '?';
$deg   = ( isset( $psi_1907['score'] ) ) ? ( $score - 50 ) * 2.7 : -160;

?>

<style type="text/css">

</style>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
        <?php if ( $psi_1905 ) { ?>
        <?php } ?>
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <figure id="score" class="alignright" style="width: 180px; height: 200px; margin-right: 0;">
            <img class="score-needle" src="/wp-content/themes/newsstats/img/gauge-needle.svg" alt="" style="transform: rotate(<?php echo $deg; ?>deg); z-index: 10;">
            <!-- img class="score-needle" src="/wp-content/themes/newsstats/img/gauge-needle-avg.svg" alt="" style="transform: rotate(<?php echo '-80'; ?>deg);" -->
            <output id="score-num"><?php echo $score; ?></output>
            <figcaption class="score-all">All papers: <output>27.6</output></figcaption>
        </figure>
		<ul class="media-meta" style="list-style: none; margin: 0; padding: 0;">
            <li><strong><big><?php echo esc_html( $pub_name ) ?></big></strong><?php echo esc_html( $awis_desc ); ?></li>
            <li><?php echo trim( $regions, ' &gt; ' ); ?> <small>(pop. <?php echo esc_html( $term_pop ); ?>)</small></li>
            <li><em>Circulation:</em> <?php echo esc_html( $pub_circ ); ?> / <em>Site rank:</em> <?php echo esc_html( $pub_rank ); ?></li>
            <li><?php the_terms( $post_id, 'owner', '<em>Owner:</em> ' ); ?></li>
			<li><em>In print:</em> <?php echo esc_html( $pub_year ); ?> | <em>Online:</em> <?php echo esc_html( $awis_year ); ?></li>
			<li><em>CMS:</em> <?php the_terms( $post_id, 'cms' ); ?> | <?php echo $site_link; ?><?php echo $rss_link; ?></li>
		</ul>
	</header><!-- .entry-header -->

    <section class="content-col">
    <?php
    $pubs_data = netrics_get_pubs_pagespeed_query( $wp_query );
    if ( $pubs_data ) {
            ?>
        <table class="tabular">
            <caption>PageSpeed Insights average results for <?php echo count( $pubs_data['score'] ) ?> articles (2019-07)</caption>
                <?php echo netrics_pagespeed_mean( $pubs_data, $tbody = false ); ?>
                <?php echo netrics_articles_results_table( $post_id, $articles_1907 ); ?>
            </tbody>
        </table>

        <figure id="col_chart" class="alignnone" style="width:100%; height: 400px; margin: 0;"></figure>

        <?php } // if ( $pubs_data )
        ?>

        <p><em>Articles 2019-07:</em>
        <?php echo netrics_articles_results( $post_id, $articles_1907 ); ?></p>
        <p><em>Articles 2019-06:</em>
        <?php echo netrics_articles_results( $post_id, $articles_1906 ); ?></p>
        <p><em>Articles 2019-05:</em>
        <?php echo netrics_articles_results( $post_id, $articles_1905 ); ?></p>

    </section><!-- .content-col -->

	<footer class="entry-footer">

		<p style="padding-top: 2em;"><img class="screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=700&h=525" width="700" height="525" alt="Homepage screenshot" /></p>

		<iframe width="700" height="700" frameborder="0" style="border:0" src="<?php echo esc_url( $map_src ); ?>"></iframe>

        <?php the_post_navigation( array( 'prev_text' => '&laquo; %title', 'next_text' => '%title &raquo;' ) ); ?>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php if ( $psi_1906 ) { ?>
<script type="text/javascript">
	// @see https://developers.google.com/chart/interactive/docs/gallery/gauge
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        bar_width = 30; // NOTE: Decrease or remove after a year to better fit more months.

        var data_trend = google.visualization.arrayToDataTable([
            ['Month', 'Speed', 'TTI','TTI avg. for all papers'],
            <?php echo $bars; ?>
        ]);

        // Gauge: #dc3811, #ff9901, #0f9617. New PSI: #ff4e41 #ffa400 #0cce6b
        var options_trend = {
            title: 'Speed-Index and Time-to-Interactive averages (seconds)',
            // vAxis: {title: 'Seconds'},
            // hAxis: {title: 'Month'},
            bar: {groupWidth: bar_width},
            colors: ['#ffa400', '#ff4e41', '#696969'],
            legend: { position: 'bottom' },
            isStacked: true,
            seriesType: 'bars',
            series: {2: {type: 'line', lineWidth: 3, pointSize: 4}},
        };

        var bar_chart = new google.visualization.ComboChart(document.getElementById('col_chart'));
        bar_chart.draw(data_trend, options_trend);
    }

</script>

<?php } ?>


