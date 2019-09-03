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

// PSI results.
$psi_1905 = netrics_site_pagespeed( $post_id, 'nn_articles_201905' ); // si: 13.9, tti: 30.1, speed: 18.8
$psi_1906 = netrics_site_pagespeed( $post_id, 'nn_articles_201906' ); // si: 13.3, tti: 30.3, speed: 18.8
$psi_1907 = netrics_site_pagespeed( $post_id, 'nn_articles_201907' ); // si: 13.5, tti: 29.7, speed: 20.4
$psi_1908 = netrics_site_pagespeed( $post_id, 'nn_articles_201908' ); // si: 14.3, tti: 32.9, speed: 19.6

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <figure id="score" class="alignright" style="width: 180px; height: 200px; margin-right: 0;">
            <?php
            // Gauge/Score CSS Chart data and display.
            $score = ( isset( $psi_1908['score'] ) ) ? number_format( $psi_1908['score'] * 100, 1, '.', ',' ) : '?';
            $deg   = ( isset( $psi_1908['score'] ) ) ? ( $score - 50 ) * 2.7 : -160;
            ?>
            <img class="score-needle" src="/wp-content/themes/newsstats/img/gauge-needle.svg" alt="" style="transform: rotate(<?php echo $deg; ?>deg); z-index: 10;">
            <output id="score-num"><?php echo $score; ?></output>
            <figcaption class="score-all">All papers: <output>19.6</output></figcaption>
        </figure>
        <?php $awis = netrics_get_awis_meta( $post_id ); ?>
        <?php $city = netrics_get_city_meta( $post_id ); ?>
        <?php $geo  = get_term_parents_list( $city['city_term']->term_id, 'region', array( 'format' => 'id', 'separator' => '/') ); ?>
		<ul class="media-meta" style="list-style: none; margin: 0; padding: 0;">
            <li><strong><big><?php echo esc_html( $pub_name ) ?></big></strong><?php echo esc_html( $awis['desc'] ); ?></li>
            <li><?php echo trim( $geo, '/' ); ?> <small>(pop. <?php echo number_format( $city['city_meta']['nn_region_pop'][0] ); ?>)</small></li>
            <li><em>Circulation:</em> <?php echo esc_html( $pub_circ ); ?> / <em>Site rank:</em> <?php echo esc_html( $pub_rank ); ?></li>
            <li><?php the_terms( $post_id, 'owner', '<em>Owner:</em> ' ); ?></li>
			<li><em>In print:</em> <?php echo esc_html( $pub_year ); ?> | <em>Online:</em> <?php echo esc_html( $awis['year'] ); ?></li>
			<li><em>CMS:</em> <?php the_terms( $post_id, 'cms' ); ?> | <?php echo $site_link; ?><?php echo $rss_link; ?></li>
		</ul>
	</header><!-- .entry-header -->

    <section class="content-col">
    <?php
    $pubs_data = netrics_get_pubs_pagespeed_query( $wp_query );
    if ( $pubs_data ) {
        ?>
        <?php $articles_1908 = get_post_meta( $post_id, 'nn_articles_201908', true ); ?>
        <table class="tabular">
            <caption>PageSpeed Insights average results for <?php echo count( $pubs_data['score'] ) ?> articles (2019-08)</caption>
                <?php echo netrics_pagespeed_mean( $pubs_data, $tbody = false ); ?>
                <?php echo netrics_articles_results_table( $post_id, $articles_1908 ); ?>
            </tbody>
        </table>

        <figure id="col_chart" class="alignnone" style="width:100%; height: 400px; margin: 0;"></figure>

        <?php } // if ( $pubs_data ) ?>
    </section><!-- .content-col -->

	<footer class="entry-footer">

		<p style="padding-top: 2em; padding-right: 1em; display: inline-block; width: 500px;"><img class="screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=500&h=375" width="500" height="375" alt="Homepage screenshot" /></p>

        <?php
        // Google Map data and display.
        $map_api = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc';
        $map_loc = '&q=' . urlencode( $city['city_term']->name ) . '+' . $city['state_term']->name;
        $map_ctr = '&amp;center=' . str_replace( '|', ',', $city['city_meta']['nn_region_latlon'][0]); // Term meta: lat|lon.
        $map_src = $map_api . $map_loc . $map_ctr;
        ?>
		<iframe style="display: inline-block; width: 500px;" width="500" height="375" frameborder="0" style="border:0" src="<?php echo esc_url( $map_src ); ?>"></iframe>

        <?php the_post_navigation( array( 'prev_text' => '&laquo; %title', 'next_text' => '%title &raquo;' ) ); ?>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php if ( $psi_1905 || $psi_1906 || $psi_1907 || $psi_1908 ) {
// Google Chart: Stacked Column data.
$lines = $bars = '';
if ( $psi_1905 ) {
    $tti = round( ( $psi_1905['tti'] - $psi_1905['speed'] ) / 1000, 1 );

    // Column chart data.
    $bars .= "['5/19', ";
    $bars .= round( $psi_1905['speed'] / 1000, 1 ) . ', ';
    $bars .= "{v:$tti,f:" . round( $psi_1905['tti'] / 1000, 1 ) . "}, 31.6],\n"; // Score: 19.6

}

if ( $psi_1906 ) {
    $tti = round( ( $psi_1906['tti'] - $psi_1906['speed'] ) / 1000, 1 );

    // Column chart data.
    $bars .= "['6/19', ";
    $bars .= round( $psi_1906['speed'] / 1000, 1 ) . ', ';
    $bars .= "{v:$tti,f:" . round( $psi_1906['tti'] / 1000, 1 ) . "}, 32.6],\n"; // Score: 19.9
}

if ( $psi_1907 ) {
    $tti = round( ( $psi_1907['tti'] - $psi_1907['speed'] ) / 1000, 1 );

    // Column chart data.
    $bars .= "['7/19', ";
    $bars .= round( $psi_1907['speed'] / 1000, 1 ) . ', ';
    $bars .= "{v:$tti,f:" . round( $psi_1907['tti'] / 1000, 1 ) . "}, 31.4],\n"; // Score: 21.0
}

if ( $psi_1908 ) {
    $tti = round( ( $psi_1908['tti'] - $psi_1908['speed'] ) / 1000, 1 );

    // Column chart data.
    $bars .= "['8/19', ";
    $bars .= round( $psi_1908['speed'] / 1000, 1 ) . ', ';
    $bars .= "{v:$tti,f:" . round( $psi_1908['tti'] / 1000, 1 ) . "}, 32.9],\n"; // Score: 19.9
}



?>
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

<?php } else {
    echo "<p>No PageSpeed results.</p>";
}
?>
<!--
Check:
https://news.pubmedia.us/publication/abqjournal-com/

// Articles: titles, URLs, PSI results.
// $articles_1905 = get_post_meta( $post_id, 'nn_articles_201905', true );
// $articles_1906 = get_post_meta( $post_id, 'nn_articles_201906', true );
// $articles_1907 = get_post_meta( $post_id, 'nn_articles_201907', true );
<p><em>Articles 2019-08:</em>
<?php // echo netrics_articles_results( $post_id, $articles_1908 ); ?></p>
<p><em>Articles 2019-07:</em>
<?php // echo netrics_articles_results( $post_id, $articles_1907 ); ?></p>
<p><em>Articles 2019-06:</em>
<?php // echo netrics_articles_results( $post_id, $articles_1906 ); ?></p>
<p><em>Articles 2019-05:</em>
<?php // echo netrics_articles_results( $post_id, $articles_1905 ); ?></p>
-->

