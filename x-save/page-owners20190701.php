<?php
/**
 * The template for displaying Owner Groups data and performance results.
 *
 * @link https://news.pubmedia.us/owners/
 *
 * @package newsstats
 */

get_header(); ?>

<style type="text/css">

#table_owners { margin: 1rem 0; }

.google-visualization-table-td { white-space: nowrap; }

.filter-results {
    font-size: 0.85rem;
    text-align: right;
    width: 400px;

}
</style>

	<div id="primary" class="content-area">

            <?php
            $terms  = get_terms( 'owner' );
            $num    = null;
            $json   = '';
            $html   = '';

            $count_owners = $total_circ = $total_papers = 0;

            foreach ( $terms as $term ) {
                if ( $term->count < 2 ) {
                    // continue;
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
                    $num = null;
                    $json .= "['{$term->name}',{$term->count},";

                    // JSON data for each CMS, used by Google Chart visualizations.
                    foreach ( $pubs_data as $key => $data ) {

                        if ( 'results' === $key ) {
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
                    $json .= "],\n";

                    $html .= '<table class="tabular">';
                    $html .= '<caption><a href="' . get_term_link( $term ) . "\">{$term->name}</a>: ";
                    $html .= 'PageSpeed average results (2019-05)</caption>';
                    $html .= '<thead><td style=\"width: 11rem;\"></td>' . netrics_pagespeed_thead() . '</thead>';
                    $html .= '<tbody>' . netrics_pagespeed_tbody( $pubs_data ) . '</tbody>';
                    $html .= '<tfoot><tr><th scope="row">Results for:</th>';
                    $html .= '<td colspan="6" style="text-align: left;">' . array_sum( $pubs_data['results'] );
                    $html .= " articles from {$query->found_posts} newspapers</td>";
                    $html .= '</tr></tfoot></table>';
                } // if ( $pubs_data )

                $count_owners++;
                $total_circ   += array_sum( $pubs_data['circ'] );
                $total_papers += $term->count;

                wp_reset_postdata();

            } // foreach ( $terms as $term )

            wp_reset_postdata();
            ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div id="dashboard_div" style="border: 1px solid #ccc; width: 100%">

    <section class="controls" style="padding: 0 2rem; display: inline-block; width: 24rem; vertical-align: top;">
        <h2 style="font-size: 1.2rem; margin-bottom: 0;">Owners: Daily Newspapers</h2>
        <p id="slider_papers"></p>
        <table class="tabular">
            <thead>
                <tr>
                    <td></td>
                    <th scope="col">Viewing</th>
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
                    <td>--</td>
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
    <div id="chart_div" style="display: inline-block; width: 700px;"></div>
    <div id="table_div" style="padding-top: 30px"></div>

</div>
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
                'minValue': 1,
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


        // Dashboard filter by score: slider.
        var sliderRank = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'slider_rank',
            'options': {
                'filterColumnIndex': 3,
                'ui': {
                    'labelStacking': 'vertical',
                    'label': 'Filter by average Rank:',
                    'format':  { 'pattern':'#,###' },
                }
            }
        });

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

        var table = new google.visualization.ChartWrapper({
            'chartType': 'Table',
            'containerId': 'table_div',
            'options': {
                sortColumn: 9,
                sortAscending: false,
                showRowNumber: true,
                 width: '100%',
                height: '100%',
            }
        });

        var data = new google.visualization.DataTable();
            data.addColumn('string', 'Owner');
            data.addColumn('number', 'Papers');
            data.addColumn('number', 'Circ.');
            data.addColumn('number', 'Rank');
            data.addColumn('number', 'DOM');
            data.addColumn('number', 'Requests');
            data.addColumn('number', 'Size (MB)');
            data.addColumn('number', 'Speed (s)');
            data.addColumn('number', 'TTI (s)');
            data.addColumn('number', 'Score');
            data.addRows([
<?php echo $json; ?>
        ]);

        // Print number with to one decimal place; apply to specified columns
        var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
        numdecFormat.format(data, 3);
        numdecFormat.format(data, 4);
        numdecFormat.format(data, 5);
        numdecFormat.format(data, 6);
        numdecFormat.format(data, 7);
        numdecFormat.format(data, 8);
        numdecFormat.format(data, 9);

        dashboard.bind([sliderPapers, sliderCirc, sliderScore, sliderRank], [pie, table]);
        dashboard.draw(data);

        // get average and sum
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
                // get the sum
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

            <h2>Detailed PageSpeed Insights combined averages for each Owner's daily newspapers</h2>
            <?php echo $html; ?>

            <!-- details>
                <summary>(Test: data arrays)</summary>
                <pre>
                </pre>
            </details -->

        </main><!-- #main -->

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<!-- =file: page-cms -->
<?php get_footer(); ?>

