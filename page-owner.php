<?php
/**
 * The template for displaying Owner Groups data and performance results.
 *
 * @link https://news.pubmedia.us/owners/
 *
 * @package newsstats
 */

get_header(); ?>

	<div id="primary" class="content-area">

    <?php
    $terms  = get_terms( 'owner' );
    $num    = null;
    $name   = '';
    $json   = '';
    $html   = '';
    $count_owners = $total_circ = $total_papers = 0;
    $count_arr = array();

    // array_count_values
    $counts = wp_list_pluck( $terms, 'count' );
    $count_vals = array_count_values( $counts );

    foreach ( $terms as $term ) {
        // if ( $term->count < 2 ) { // continue; }

        // Distribution of paper ownership.
        if ( isset ( $count_arr[ $term->count ] ) ) {
            $count_arr[ $term->count ] = $count_arr[ $term->count ] + 1;
        } else {
            $count_arr[ $term->count ] = 1;
        }


        // Get Owner score data.
        $args = array(
            'post_type'      => 'publication',
            'posts_per_page' => 500,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'owner',
                    'field'    => 'id',
                    'terms'    => $term->term_id,
                )
            )
        );
        $query = new WP_Query( $args );

        $pubs_data = netrics_get_pubs_query_data( $query );

        if ( $pubs_data && isset( $pubs_data['score'] ) ) {
            $name = '<a href="' . get_term_link( $term->term_id ) . '">' . "{$term->name}</a>";
            $json .= "['{$term->name}',{$term->count},";

            // JSON data for each CMS, used by Google Chart visualizations.
            foreach ( $pubs_data as $key => $data ) {

                if ( 'results' === $key ) { // Slip 'results' column.
                    continue;
                }

                switch ( $key ) {
                    case 'circ':
                        $num = absint( array_sum( $data ) );
                        break;
                    case 'rank':
                        $num = nstats_mean( $data );
                        break;
                    case 'score':
                        $num = nstats_mean( $data ) * 100;
                        break;
                    case 'speed':
                        $num = nstats_mean( $data ) / 1000;
                        break;
                    case 'tti':
                        $num = nstats_mean( $data ) / 1000;
                        break;
                    case 'size':
                        $num = nstats_mean( $data ) / 1000000;
                        break;
                    case 'results':
                        break;
                    default:
                        $num = nstats_mean( $data );
                        break;
                }

                $json .= $num . ',';

            }
            $json .= "'$name'],\n";

            $html .= '<table class="tabular">';
            $html .= '<caption><a href="' . get_term_link( $term ) . "\">{$term->name}</a>: ";
            $html .= 'PageSpeed average results (2019-08)</caption>';
            $html .= '<thead><td style=\"width: 11rem;\"></td>' . netrics_pagespeed_thead() . '</thead>';
            $html .= '<tbody>' . netrics_pagespeed_tbody( $pubs_data, 0 ) . '</tbody>';
            $html .= '<tfoot><tr><th scope="row">Results for:</th>';
            $html .= '<td colspan="6" style="text-align: left;">' . array_sum( $pubs_data['results'] );
            $html .= " articles from {$query->found_posts} newspapers</td>";
            $html .= '</tr></tfoot></table>';

            $count_owners++;
            $total_circ   += array_sum( $pubs_data['circ'] );
            $total_papers += $term->count;


        } // if ( $pubs_data )

        wp_reset_postdata();

    } // foreach ( $terms as $term )

    wp_reset_postdata();
    ?>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div id="dashboard_div" class="chart-dashboard" style="border: 1px solid #ccc; width: 100%">

            <section class="chart-controls" style="margin-right: 2rem; width: 22rem;">
                <h2 style="font-size: 1.2rem; margin-bottom: 0;"  >Owners: Daily Newspapers</h2>
                <p id="slider_papers"></p>
                <table class="tabular">
                    <thead>
                        <tr>
                            <td></td>
                            <th scope="col">Showing</th>
                            <th scope="col">of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Papers</th>
                            <td><span id="paper_sum"></span></td>
                            <td><?php echo $total_papers; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Owners</th>
                            <td><span id="owner_cnt"></span></td>
                            <td><?php echo $count_owners; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Circ.</th>
                            <td><span id="circ_sum"></span></td>
                            <td><?php echo number_format( $total_circ ); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Avg. Score</th>
                            <td><span id="score_avg"></span></td>
                            <td>27.6</td>
                        </tr>
                    </tbody>
                </table>
                <p id="slider_circ"></p>
                <p id="slider_score"></p>
                <p id="slider_rank"></p>
                <p style="margin: 0;">Table has each owners's combined:
                    <ul style="margin: 0;">
                        <li><strong>Papers</strong> and <strong>Circulation</strong></li>
                        <li>Average global <strong>Rank</strong> (Alexa)</li>
                        <li>Average PageSpeed Insights results, sorted by <strong>Score</strong> (2015-05, 3 articles per paper).</li>
                    </ul>
                </p>
            </section>
            <figure id="chart_div"></figure>
            <figure id="table_div" style="display: block; padding-top: 30px; width: 100%">
                <p>Loading… <img src="https://news.pubmedia.us/wp-content/themes/newsstats/img/ajax-loader.gif" width="220" height="19"></p>
            </figure>

        </div><!-- #dashboard_div -->

<script type="text/javascript">
google.charts.load('current', {'packages':['corechart', 'table', 'controls']});
google.charts.setOnLoadCallback(drawMainDashboard);

function drawMainDashboard() {
    var dashboard = new google.visualization.Dashboard(
        document.getElementById('dashboard_div'));

    // Dashboard filter: slider.
    var sliderPapers = new google.visualization.ControlWrapper({
        'controlType': 'NumberRangeFilter',
        'containerId': 'slider_papers',
        'state': {'lowValue': 10},
        'options': {
            'filterColumnIndex': 1,
            // 'minValue': 1,
            'ui': {
                'labelStacking': 'vertical',
                'label': 'Filter Owners by Papers owned:',
                'format':  { 'pattern':'#,###' },
            }
        }
    });

    // Dashboard filter by curculaton: slider.
    var sliderCirc = new google.visualization.ControlWrapper({
        'controlType': 'NumberRangeFilter',
        'containerId': 'slider_circ',
        'options': {
            'filterColumnIndex': 2,
            'ui': {
                'labelStacking': 'vertical',
                'label': 'Filter Owners by total Circulation:',
                'format':  { 'pattern':'#,###' },
            }
        }
    });

    // Dashboard filter by score: slider.
    var sliderScore = new google.visualization.ControlWrapper({
        'controlType': 'NumberRangeFilter',
        'containerId': 'slider_score',
        'options': {
            'filterColumnIndex': 9,
            'ui': {
                'labelStacking': 'vertical',
                'label': 'Filter by average Score:',
                'format':  { 'pattern':'##.#' },
            }
        }
    });

    // Google Visualization: Pie chart.
    var pie = new google.visualization.ChartWrapper({
        'chartType': 'PieChart',
        'containerId': 'chart_div',
        'options': {
            'width': 680,
            'height': 680,
            'legend': 'none',
            'chartArea': {'left': 15, 'top': 15, 'right': 0, 'bottom': 0},
            'pieSliceText': 'label'
            },
        'view': {'columns': [0, 1]}
    });

    // Google Visualization: Table chart.
    var table = new google.visualization.ChartWrapper({
        'chartType': 'Table',
        'containerId': 'table_div',
        'options': {
            'allowHtml': true,
            'sortColumn': 9,
            'sortAscending': false,
            'showRowNumber': true,
            'width': '100%',
            'height': '100%',
        },
        // Col 10 is HTML-linked name.
        'view': {'columns': [10, 1, 2, 3, 4, 5, 6, 7, 8, 9]}
    });

    // Data cols and rows.
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Owner');
        data.addColumn('number', 'Paper');
        data.addColumn('number', 'Circ.');
        data.addColumn('number', 'Rank');
        data.addColumn('number', 'DOM');
        data.addColumn('number', 'Requests');
        data.addColumn('number', 'Size (MB)');
        data.addColumn('number', 'Speed (s)');
        data.addColumn('number', 'TTI (s)');
        data.addColumn('number', 'Score');
        data.addColumn('string', 'Owner');
        data.addRows([
<?php echo $json; ?>
    ]);

    // Format number to one decimal place; apply to specified columns.
    var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
    numdecFormat.format(data, 3);
    numdecFormat.format(data, 4);
    numdecFormat.format(data, 5);
    numdecFormat.format(data, 6);
    numdecFormat.format(data, 7);
    numdecFormat.format(data, 8);
    numdecFormat.format(data, 9);

    // Attach controls to charts.
    dashboard.bind([sliderPapers, sliderCirc, sliderScore], [pie, table]);
    dashboard.draw(data);

    // Dynamically recalculate user-filtered aggregations: average, sum, and count.
    google.visualization.events.addListener(table, 'ready', function () {
        var group = google.visualization.data.group(table.getDataTable(), [{
            // we need a key column to group on, but since we want all rows grouped into 1,
            // then it needs a constant value
            column: 0,
            type: 'number',
            modifier: function () {
                return 1;
            }
        }], [{
            // get the average
            column: 9,
            label: 'Score Average',
            type: 'number',
            aggregation: google.visualization.data.avg
        }, {
            // get the COUNT
            column: 1,
            label: 'Owner Count',
            type: 'number',
            aggregation: google.visualization.data.count
        }, {
            // get the sum
            column: 1,
            label: 'Paper Sum',
            type: 'number',
            aggregation: google.visualization.data.sum
        }, {
            // get the sum
            column: 2,
            label: 'Circulation Sum',
            type: 'number',
            aggregation: google.visualization.data.sum
        }]);

        // Write aggregations.
        document.getElementById('score_avg').innerHTML  = group.getValue(0, 1).toFixed(1);
        document.getElementById('owner_cnt').innerHTML = group.getValue(0, 2);
        document.getElementById('paper_sum').innerHTML = group.getValue(0, 3);
        document.getElementById('circ_sum').innerHTML   =
            group.getValue(0, 4).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    });
}
</script>

        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>

            <section class="content-col">
                <h2>PSI averages by Owner</h2>
                <p><small>Since 2019-06 2e've been unable to run tests on <a href="https://news.pubmedia.us/owner/the-mcclatchy-company/">McClatchy</a> papers using the <a href="https://news.pubmedia.us/cms/escenic/">Escenic</a>, so their articles are not in these results.</small></p>
                <p>PageSpeed Insights result combined averages for each Owner's daily newspapers</p>
                <?php echo $html; ?>


            </section>

        </main><!-- #main -->

	</div><!-- #primary -->

<details>
    <summary><small>(Test: data arrays)</small></summary>
    <pre>
        <?php
        ksort( $count_arr );
        ksort( $count_vals );
        print_r( $count_arr );
        print_r( $count_vals );
        print_r( $pubs_data );

        ?>
    </pre>
</details>
<?php // get_sidebar(); ?>
<!-- =file: page-cms -->
<?php get_footer(); ?>

