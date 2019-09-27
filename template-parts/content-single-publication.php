<?php
/**
 * Template part for displaying single CPT posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */

// Get meta for circ., year in print, year online, and site rank.
$custom_fields = get_post_custom();
$post_id       = get_the_ID();

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

$psi_articles   = get_post_meta( $post_id, 'nn_articles', true ); // This Pub's PSI monthly results history.
$psi_pub_all    = get_post_meta( $post_id, 'nn_psi_avgs', true ); // This Pub's PSI monthly results history.
$psi_pub_month  = end( $psi_pub_all ); // Most recent results for this Pub.
$psi_site_all   = get_transient( 'netrics_psi' ); // All Pub's PSI monthly results history.
$psi_site_month = end( $psi_site_all ); // Most recent results for all Pubs.

// netrics_pagespeed_format( $metric, $num, 0 )

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header" style="margin-bottom: 2rem;">
        <figure id="score" class="alignright" style="width: 180px; height: 200px; margin-right: 0;">
            <?php
            // Gauge/Score CSS Chart data and display.
            $score_pub = ( isset( $psi_pub_month['score'] ) ) ? number_format( $psi_pub_month['score'] * 100, 1, '.', ',' ) : '?';
            $score_deg = ( isset( $psi_pub_month['score'] ) ) ? ( $score_pub - 50 ) * 2.7 : -160;
            $score_all = number_format( $psi_site_month['score'] * 100, 1, '.', ',' );
            ?>
            <img class="score-needle" src="/wp-content/themes/newsstats/img/gauge-needle.svg" alt="" style="transform: rotate(<?php echo $score_deg; ?>deg); z-index: 10;">
            <output id="score-num"><?php echo $score_pub; ?></output>
            <figcaption class="score-all">All papers: <output><?php echo $score_all; ?></output></figcaption>
        </figure>
        <?php the_title( '<h1 class="entry-title" style="display: inline-block;">', '</h1>' ); ?>
        <?php $awis = netrics_get_awis_meta( $post_id ); ?>
        <?php $city = netrics_get_city_meta( $post_id ); ?>
        <?php $geo  = get_term_parents_list( $city['city_term']->term_id, 'region', array( 'format' => 'id', 'separator' => ' / ') ); ?>
		<ul class="media-meta" style="list-style: none; margin: 0; padding: 0;">
            <li><strong><big><?php echo esc_html( $pub_name ) ?></big></strong><?php echo esc_html( $awis['desc'] ); ?></li>
            <li><?php echo trim( $geo, ' / ' ); ?> <small>(<em>Pop.</em> <?php echo number_format( $city['city_meta']['nn_region_pop'][0] ); ?>)</small></li>
            <li><em>Circulation:</em> <?php echo esc_html( $pub_circ ); ?> | <em>Site rank:</em> <?php echo esc_html( $pub_rank ); ?> | <em>CMS:</em> <?php the_terms( $post_id, 'cms' ); ?></li>
            <li><em>In print:</em> <?php echo esc_html( $pub_year ); ?> | <em>Online:</em> <?php echo esc_html( $awis['year'] ); ?> | <?php the_terms( $post_id, 'owner', '<em>Owner:</em> ' ); ?></li>
		</ul>
	</header><!-- .entry-header -->

    <section class="content-col">
    <?php
    $pubs_data = netrics_get_pubs_pagespeed_query( $wp_query );
    if ( $pubs_data ) {
        ?>
        <?php $articles_1908 = get_post_meta( $post_id, 'nn_articles_201908', true ); ?>

        <table class="tabular">
            <thead>
                <td></td>
                <?php echo netrics_pagespeed_thead() ?>
            </thead>
            <caption>PageSpeed Insights average and article results (<?php echo $psi_pub_month['results']; ?> articles: <?php echo key( array_slice( $psi_pub_all, -1, 1, true ) ); ?>)</caption>
            <tbody>
                <tr>
                    <th scope="row"><?php esc_attr_e( 'Mean', 'newsnetrics' ); ?></th>
                    <td><?php echo number_format( $psi_pub_month['dom'], 1, '.', ',' ); ?></td>
                    <td><?php echo number_format( $psi_pub_month['requests'], 1, '.', ',' ); ?></td>
                    <td><?php echo size_format( $psi_pub_month['size'], 1 ); ?></td>
                    <td><?php echo number_format( $psi_pub_month['speed'] / 1000, 1, '.', ',' ); ?></td>
                    <td><?php echo number_format( $psi_pub_month['tti'] / 1000, 1, '.', ',' ); ?></td>
                    <td><?php echo number_format( $psi_pub_month['score'] * 100, 1, '.', ',' ); ?></td>
                </tr>
                <?php echo netrics_articles_results_table( $post_id, end( $psi_articles ) ); ?>
            </tbody>
        </table>

        <figure id="col_chart" class="alignnone" style="width:100%; height: 400px; margin: 0;"></figure>

        <?php } // if ( $pubs_data ) ?>
    </section><!-- .content-col -->

	<footer class="entry-footer">
        <?php if ( isset( $custom_fields['nn_pub_url'][0] ) ) { // Screenshot of pub homepage. ?>
		<p style="padding-top: 2em; padding-right: 1em; display: inline-block; width: 500px;">
            <a href="<?php echo esc_url( $custom_fields['nn_pub_url'][0] ); ?>"><img class="screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=500&h=375" width="500" height="375" alt="Homepage screenshot" /></a></p>
        <?php } ?>

        <?php
        // Google Map data and display.
        $map_api = 'https://www.google.com/maps/embed/v1/place?key=';
        $map_key =  netrics_get_option( 'gmaps' );
        $map_loc = '&q=' . urlencode( $city['city_term']->name ) . '+' . $city['state_term']->name;
        $map_ctr = '&amp;center=' . str_replace( '|', ',', $city['city_meta']['nn_region_latlon'][0]); // Term meta: lat|lon.
        $map_src = $map_api . $map_key . $map_loc . $map_ctr;
        ?>
		<iframe style="display: inline-block; width: 500px;" width="500" height="375" frameborder="0" style="border:0" src="<?php echo esc_url( $map_src ); ?>"></iframe>

        <?php the_post_navigation( array( 'prev_text' => '&laquo; %title', 'next_text' => '%title &raquo;' ) ); ?>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<?php
$bars = '';
foreach ( $psi_pub_all as $month => $psi ) {

    if ( $psi ) {
        $tti_pub = round( ( $psi['tti'] - $psi['speed'] ) / 1000, 1 );
        $tti_all = round( ( $psi_site_all[ $month ]['tti'] ) / 1000, 1 );

        // Column chart data.
        $bars .= "['" . date("n/y", strtotime( $month ) ) . "', ";
        $bars .= round( $psi['speed'] / 1000, 1 ) . ', ';
        $bars .= "{v:$tti_pub,f:" . round( $psi['tti'] / 1000, 1 ) . "}, " . $tti_all . "],\n"; // Score: 19.6
    }
}
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
