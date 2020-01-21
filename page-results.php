<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();

$netrics_psi = get_transient( 'netrics_psi' ); // Monthly history of site-wide PSI averages.

?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

            <section class="content-col">
                <h2>News website performance</h2>
                <!-- PSI Score: CSS gauge dial -->
                <figure id="score" class="alignright content-col" style="width: 180px; height: 180px; margin-right: 0;">
                    <?php
                    // Gauge/Score CSS Chart data and display.
                    $psi_month = end( $netrics_psi ); // Latest month site-wide PSI averages.
                    $score_all = ( isset( $psi_month['score'] ) ) ? number_format( $psi_month['score'] * 100, 1, '.', ',' ) : '?';
                    $score_deg = ( isset( $psi_month['score'] ) ) ? ( $score_all - 50 ) * 2.7 : -160;
                    ?>
                    <img class="score-needle" src="/wp-content/themes/newsstats/img/gauge-needle.svg" alt="" style="transform: rotate(<?php echo $score_deg; ?>deg); z-index: 10;">
                    <output id="score-num"><?php echo $score_all; ?></output>
                </figure>
                <p>News Netrics presents performance metrics for U.S daily newspapers websites. Every month we run Google’s PageSpeed Insights (PSI) mobile tests on three articles per paper, then average the results, for each publication and for all the publications.</p>
                <p>The mean of the PSI scores (<?php echo $psi_month['date']; ?>) for U.S. dailies was <output><?php echo $score_all; ?></output>.</p>
                <aside class="googlesitekit-pagespeed-report__scale" style="clear: both;"><span>Score:</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--fast">90-100 (fast)</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--average">50-89 (average)</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--slow">0-49 (slow)</span></aside>
                <!-- PSI latest month: HTML table -->
                <h3>Latest PSI results</h3>
                <?php netrics_print_pubs_avgs_table(); ?>
           </section>

            <section>
                <!-- PSI speed, tti, score: Google stacked column chart -->
                <h3 class="content-col">Monthly PSI results</h3>
                <figure id="col_chart" class="alignnone" style="width:100%; height: 400px; margin-bottom: 2rem;"></figure>
                <?php
                $bars        = ''; // Data for stacked column chart.
                foreach ( $netrics_psi as $month => $psi ) { // History of site-wide PSI averages (transient 'netrics_psi').

                    if ( $psi ) {
                        $tti_speed = round( ( $psi['tti'] - $psi['speed'] ) / 1000, 1 );
                        $score     = number_format( $psi['score'] * 100, 1, '.', ',' );

                        // Column chart data.
                        $bars .= "['" . date("n/y", strtotime( $month ) ) . "', ";
                        $bars .= round( $psi['speed'] / 1000, 1 ) . ', ';
                        $bars .= "{v:$tti_speed,f:" . round( $psi['tti'] / 1000, 1 ) . "}, " . $score . "],\n";
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
                            ['Month', 'Speed', 'TTI', 'Score'],
                            <?php echo $bars; ?>
                        ]);

                        // Gauge: #dc3811, #ff9901, #0f9617. New PSI: #ff4e41 #ffa400 #0cce6b
                        var options_trend = {
                            title: 'Speed-Index and Time-to-Interactive: seconds. Score: 0–100.',
                            // vAxis: {title: 'Seconds'},
                            // hAxis: {title: 'Month'},
                            bar: {groupWidth: bar_width},
                            colors: ['#ffa400', '#ff4e41', '#111111'],
                            legend: { position: 'bottom' },
                            isStacked: true,
                            seriesType: 'bars',
                            series: {2: {type: 'line', lineWidth: 3, pointSize: 4}},
                        };

                        var bar_chart = new google.visualization.ComboChart(document.getElementById('col_chart'));
                        bar_chart.draw(data_trend, options_trend);
                    }
                </script>
            </section>

            <section class="content-col">

            </section>

        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/content', 'page' ); ?>
        <?php endwhile; // End of the loop. ?>

        </main><!-- #main -->

        <!-- Top 20 PSI scores: Google table chart -->
        <section class="content-col">
            <h2 id="top-scores">Top 20 Scores</h2>
            <p>Congrats to the best-performing U.S. newspaper websites in 2020-01.<br>
                <small>(Table includes papers with &gt;40K circulation, sorted by PSI score — average results of 3 articles per paper.)</small></p>
        </section>
        <figure id="table_div" style="display: block; padding-top: 30px; width: 100%"></figure>

        <?php
        $json     = '';
        $states   = $owners = $cmss = array();
        $psi_high = 0; // Count of scores >= 50.

        // Get Top 20 Scores with 40K+ circulation.
        $args = array(
            'post_type'      => 'publication',
            'posts_per_page' => 20,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                'score' => array(
                    'key' => 'nn_psi_score',
                    'compare' => 'EXIST'
                    )
                ),
                array(
                    'key'     => 'nn_circ',
                    'value'   => 40000,
                    'type'    => 'numeric',
                    'compare' => '>=',
                )
            ),
            'orderby' => array( 'score' => 'DESC', 'title' => 'ASC' ),
        );
        $query_score = new WP_Query( $args );

        // Make array of Publications data.
        foreach ( $query_score->posts as $post ) {
            $post_id   = $post->ID;
            $post_meta = get_post_meta( $post_id );
            $month     = '2020-01';

            // Add PUB and PSI data to chart.
            // Get Region values: city, county and state (tax terms), and city population (term meta).
            $term_city   = get_the_terms( $post_id, 'region' );
            $city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
                ? get_term_meta( $term_city[0]->term_id ) : false;
            $city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
                ? $city_meta['nn_region_pop'][0] : 0;
            $term_county = ( $term_city && isset( $term_city[0]->parent ) )
                ? get_term( $term_city[0]->parent ) : false;
            $term_state  = ( $term_county && isset( $term_county->parent ) )
                ? get_term( $term_county->parent ) : false;

            // Get owner and CMS tax term objects.
            $term_owner = get_the_terms( $post_id, 'owner' );
            $term_cms   = get_the_terms( $post_id, 'cms' );

            $states[] = $term_state->name; // Add state to array of all states.
            $owners[] = $term_owner[0]->name; // Add owner to array of all owners.
            $cmss[]   = $term_cms[0]->name; // Add CMS to array of all CMSs.

            // JSON with sanitized data values for rows in Google chart.
            $json .= '[';
            // Domain (external link) and name (internal link).
            $json .= '{v:\'' . esc_html( $post->post_title ) .
                '\',f:\'<a href="' . esc_url( $post_meta['nn_pub_url'][0] ) . '">' . esc_html( $post->post_title ) . '</a>\'},';
            $json .= '{v:\'' . esc_html( $post_meta['nn_pub_name'][0] ) .
                '\',f:\'<a href="' . get_post_permalink( $post_id ) . '">' . esc_html( $post_meta['nn_pub_name'][0] ) . '</a>\'},';
            // Circulation and rank meta.
            $json .= absint( get_post_meta( $post_id, 'nn_circ', true ) )  . ',';
            $json .= absint( get_post_meta( $post_id, 'nn_rank', true ) )  . ',';
            // Region tax terms (linked): state, county, city and city population (term meta).
            $json .= ( $term_state && isset( $term_state->name ) )
                ? '\'<a href="' . get_term_link( $term_state->term_id ) . '">' . esc_html( $term_state->name ) . '</a>\',' : ',';
            $json .= ( $term_city && isset( $term_city[0]->name ) )
                ? '\'<a href="' . get_term_link( $term_city[0]->term_id ) . '">' . esc_html( $term_city[0]->name ) . '</a>\',' : ',';
            // $json .= esc_html( $city_pop )  . ','; // Remove pop. from table.
            // Owner and CMS tax terms (linked).
            $json .= ( $term_owner && isset( $term_owner[0]->name ) )
                ? '\'<a href="' . get_term_link( $term_owner[0]->term_id ) . '">' . esc_html( $term_owner[0]->name ) . '</a>\',' : "'',";
            $json .=( $term_cms && isset( $term_cms[0]->name ) )
                ? '\'<a href="' . get_term_link( $term_cms[0]->term_id ) . '">' . esc_html( $term_cms[0]->name ) . '</a>\',' : "'(unknown)',";

            // Add PageSpeed averages to JSON.
            // $psi_data = netrics_site_pagespeed( $post_id ); // PSI averages, old vers.
            $psi_avgs = get_post_meta( $post_id, 'nn_psi_avgs', true );
            $month    = key( array_slice( $psi_avgs, -1, 1, true ) );
            $psi_data = $psi_avgs[ $month ];

            // Array of PageSpeed metric names (score, size, etc.)
            $metrics_psi = netrics_get_pagespeed_metrics();
            $metrics     = array_diff( $metrics_psi, array( 'dom' ) ); // Skip these metrics.



            foreach ($metrics as $metric ) {
                $num = ( isset( $psi_data[ $metric ] ) ) ? $psi_data[ $metric ] : null;

                switch ( $metric ) {
                    case 'score':
                        $num = $num * 100;
                        if ( $num >= 50 ) {
                            $psi_high++; // Count of scores >= 50.
                        }
                        break;
                    case 'speed':
                        $num = $num / 1000;
                        break;
                    case 'tti':
                        $num = $num / 1000;
                        break;
                    case 'size':
                        $num = $num / 1000000;
                        break;
                    default:
                        $num = $num;
                        break;
                }

                $json .= "{v:$num, f:'" . number_format( $num, 1 ) . '\'},';
            }
            $json .= "],\n";
        }

        /**
         * Format a Google Chart data cell with raw string value and HTML linked format.
         *
         *
         */
        function netrix_google_chart_link ( $string, $url ) {
            $cell = "{v:'$string','<a href=\"$url\"'>$string</a>'},";

            return $cell;
        }

        wp_reset_postdata();
        ?>

        <script type="text/javascript">
        google.charts.load('current');
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // var table = new google.visualization.Table(document.getElementById('table_div'));

            // Data cols and rows.
            var data = google.visualization.arrayToDataTable([
                [   {label: 'Domain (link to paper)', id: 'domain', type: 'string'},
                    {label: 'Name (link to PSI results)', id: 'name', type: 'string'},
                    {label: 'Circulation', id: 'circ', type: 'number'},
                    {label: 'Site Rank', id: 'rank', type: 'number'},
                    {label: 'State', id: 'state', type: 'string'},
                    {label: 'City', id: 'city', type: 'string'},
                    // {label: 'Population', id: 'pop', type: 'number'},
                    {label: 'Owner', id: 'owner', type: 'string'},
                    {label: 'CMS', id: 'cms', type: 'string'},
                    // {label: 'DOM', id: 'dom', type: 'number'},
                    {label: 'Requests', id: 'requests', type: 'number'},
                    {label: 'Size (MB)', id: 'size', type: 'number'},
                    {label: 'Speed (s)', id: 'speed', type: 'number'},
                    {label: 'TTI (s)', id: 'tti', type: 'number'},
                    {label: 'Score', id: 'score', type: 'number'} ],
        <?php echo $json; ?>
            ]);

        /*
            // Format number to one decimal place; apply to specified columns.
            var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
            numdecFormat.format(data, 3);
        */

            // Google Visualization: Table chart.
            var wrapper = new google.visualization.ChartWrapper({
                chartType:   'Table',
                containerId: 'table_div',
                dataTable: data,
                options: {
                    'allowHtml': true,
                    'sortColumn': 12,
                    'sortAscending': false,
                    'showRowNumber': true,
                    'width': '100%',
                    'height': '100%',
                },
            });
            // Attach controls to charts.
            wrapper.draw();
        }
        </script>
        <aside class="googlesitekit-pagespeed-report__scale" style="clear: both;"><span>Score:</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--fast">90-100 (fast)</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--average">50-89 (average)</span><span class="googlesitekit-pagespeed-report__scale-range googlesitekit-pagespeed-report__scale-range--slow">0-49 (slow)</span><br>
        This month <?php printf( _n( '<output>%s</output> paper', '<output>%s</output> papers', $psi_high, 'netrics' ), number_format_i18n( $psi_high ) );?></output> scored above "slow".</aside>

    </div><!-- #primary -->

<details>
    <summary><small>(Test: data arrays)</small></summary>

    <pre>




<?php
// echo "$bars\n";

echo "netrics_psi (transient):\n";
print_r( $netrics_psi );

$count_states = array_count_values( $states );
$mode_states  = array_search( max( $count_states ), $count_states );


echo "mode_states: $mode_states\n";
echo '$count_states: ';
print_r( $count_states );

$count_owners = array_count_values( $owners );
$mode_owners  = array_search( max( $count_owners ), $count_owners );

echo "mode_owners: $mode_owners\n";
echo '$count_owners: ';
print_r( $count_owners );
?>

<ol>
<?php
echo "query_score->posts (top 20 scores):\n";
foreach ( $query_score->posts as $post ) {
    echo "<li>{$post->ID}\t{$post->post_title}\t" . get_post_meta( $post->ID, 'nn_circ', true ) . "\t" . get_post_meta( $post->ID, 'nn_psi_score', true ) . '</li>';
}

// echo $json;
?>
</ol>


<em><?php echo get_num_queries(); ?> queries took <?php timer_stop( 1 ); ?> seconds using <?php echo round( memory_get_peak_usage() / 1024 / 1024, 3 ); ?> MB peak memory.</em>
    </pre>
</details>

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
