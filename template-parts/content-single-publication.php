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

$psi_articles   = get_post_meta( $post_id, 'nn_articles', true ); // History of Pub's articles and results.
$psi_pub_all    = get_post_meta( $post_id, 'nn_psi_avgs', true ); // History of Pub's results.
$psi_pub_month  = end( $psi_pub_all ); // Most recent results for this Pub.
$psi_site_all   = get_transient( 'netrics_psi' ); // History of results for all Pubs.
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
            <figcaption class="score-all">All papers: <output><?php echo $score_all; ?></output><br></figcaption>
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
        <aside class="googlesitekit-pagespeed-report__scale"><span>Score:</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--fast">90-100 (fast)</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--average">50-89 (average)</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--slow">0-49 (slow)</span></aside>
        <?php if ( $psi_pub_month ) { ?>
        <table class="tabular">
            <caption>PageSpeed Insights average and article results (<?php echo $psi_pub_month['results']; ?> articles: <?php echo key( array_slice( $psi_pub_all, -1, 1, true ) ); ?>)</caption>
            <thead>
                <td></td>
                <?php echo netrics_pagespeed_thead() ?>
            </thead>
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
        <figure id="col_chart" class="alignnone" style="width:100%; height: 400px; margin-bottom: 2rem;"></figure>
        <?php
        $bars = '';
        foreach ( $psi_pub_all as $month => $psi ) {
            $tti_all = round( ( $psi_site_all[ $month ]['tti'] ) / 1000, 1 );
            $score_all = round( ( $psi_site_all[ $month ]['score'] ) * 100, 1 );

            if ( $psi ) {
                $tti_pub = round( ( $psi['tti'] - $psi['speed'] ) / 1000, 1 );

                // Column chart data.
                $bars .= "['" . date("n/y", strtotime( $month ) ) . "', ";
                $bars .= round( $psi['speed'] / 1000, 1 ) . ', ';
                $bars .= "{v:$tti_pub,f:" . round( $psi['tti'] / 1000, 1 ) . "}, ";
                $bars .= number_format( $psi['score'] * 100, 1, '.', ',' ) . ', ';
                $bars .= $score_all . "],\n";
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
                    // ['Month', 'Speed', 'TTI', 'TTI avg. for all papers'],
                    ['Month', 'Speed', 'TTI', 'Score', 'Score for all papers'],
                    <?php echo $bars; ?>
                ]);

                // Gauge: #dc3811, #ff9901, #0f9617. New PSI: #ff4e41 #ffa400 #0cce6b
                var options_trend = {
                    // title: 'Speed-Index and Time-to-Interactive averages (seconds)',
                    title: 'Speed-Index and Time-to-Interactive averages: seconds. Score: 0–100.',
                    // vAxis: {title: 'Seconds'},
                    // hAxis: {title: 'Month'},
                    bar: {groupWidth: bar_width},
                    // colors: ['#ffa400', '#ff4e41', '#11a9dc'],
                    colors: ['#ffa400', '#ff4e41', '#904713', '#968585'],
                    legend: { position: 'bottom' },
                    isStacked: true,
                    seriesType: 'bars',
                    // series: {2: {type: 'line', lineWidth: 2, pointSize: 3}},
                    series: {2: {type: 'line', lineWidth: 4, pointSize: 5}, 3: {type: 'line', lineWidth: 2, pointSize: 3}},
                };

                var bar_chart = new google.visualization.ComboChart(document.getElementById('col_chart'));
                bar_chart.draw(data_trend, options_trend);
            }
        </script>

        <?php } else { ?>
        <p>No PageSpeed results to date (<?php echo key( array_slice( $psi_pub_all, -1, 1, true ) ); ?>).</p>
        <?php } ?>

    </section><!-- .content-col -->

	<footer class="entry-footer">
        <?php if ( isset( $custom_fields['nn_pub_url'][0] ) ) { // Screenshot of pub homepage. ?>
            <a href="<?php echo esc_url( $custom_fields['nn_pub_url'][0] ); ?>"><img style="margin-right: 0.5em; display: inline-block; width: 500px; height: 375px; border: 1px solid #ccc; vertical-align: top; margin-top: 16px;" class="screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=500&h=375" width="500" height="375" alt="Homepage screenshot" /></a>
        <?php } ?>

        <?php
        // Google Map data and display.
        $map_api = 'https://www.google.com/maps/embed/v1/place?key=';
        $map_key =  netrics_get_option( 'gmaps' );
        $map_loc = '&q=' . urlencode( $city['city_term']->name ) . '+' . $city['state_term']->name;
        $map_ctr = '&amp;center=' . str_replace( '|', ',', $city['city_meta']['nn_region_latlon'][0]); // Term meta: lat|lon.
        $map_src = $map_api . $map_key . $map_loc . $map_ctr;
        ?>
		<iframe style="display: inline-block; width: 500px; height: 375px; border: 1px solid #ccc;" width="500" height="375" frameborder="0" style="border:0" src="<?php echo esc_url( $map_src ); ?>"></iframe>

        <?php the_post_navigation( array( 'prev_text' => '&laquo; %title', 'next_text' => '%title &raquo;' ) ); ?>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
