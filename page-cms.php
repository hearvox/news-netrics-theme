<?php
/**
 * The template for displaying CMS data and performance results.
 *
 * @link https://news.pubmedia.us/cms/
 *
 * @package newsstats
 */

get_header(); ?>

<style type="text/css">
/* https://jsfiddle.net/sivard/6t09jy69/ */

.table .google-visualization-table-tr-odd, .table .google-visualization-table-tr-even {
    background-color: transparent;
}
#ChartOverview .table {
    margin-bottom: 0;
}
#ChartOverview .TotalRow {
    background-color: #e4e9f4;
}
#ChartOverview .TotalRow td {
    background-image:linear-gradient(to bottom, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.7) 30%, rgba(255, 255, 255, 0.5) 60%, rgba(255, 255, 255, 0) 100%);
    font-weight : 700;
}
</style>
	<div id="primary" class="content-area">

        <?php
        $month  = '2020-01';
        $terms  = get_terms( 'cms' );
        $num    = null;
        $name   = '';
        $json   = '';
        $html   = '';

        foreach ( $terms as $term ) {
            if ( 5815 !== $term->term_id ) { // No articles pulled for Escenic CMS papers.

            // Get Owner score data.
            $args = array(
                'post_type'      => 'publication',
                'posts_per_page' => 500,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'cms',
                        'field'    => 'id',
                        'terms'    => $term->term_id,
                    )
                )
            );
            $query = new WP_Query( $args );

            $pubs_data = netrics_get_pubs_query_data( $query, 1, 0 );

            if ( $pubs_data ) {
                $name = '<a href="' . get_term_link( $term->term_id ) . '">' . "{$term->name}</a>";
                $json .= "['{$term->name}',{$term->count},";

                // JSON data for each CMS, used by Google Chart visualizations.
                foreach ( $pubs_data as $key => $data ) {

                    if ( 'results' === $key ) {
                        continue;
                    }

                    switch ( $key ) {
                        case 'circ':
                            $num = array_sum( $data );
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
                $html .= 'PageSpeed average results</caption>';
                $html .= '<thead><td style=\"width: 11rem;\"></td>' . netrics_pagespeed_thead() . '</thead>';
                $html .= '<tbody>' . netrics_pagespeed_tbody( $pubs_data, 0 ) . '</tbody>';
                $html .= '<tfoot><tr><th scope="row">Results for:</th>';
                $html .= '<td colspan="6" style="text-align: left;">' . array_sum( $pubs_data['results'] );
                $html .= " articles from {$query->found_posts} newspapers</td>";
                $html .= '</tr></tfoot></table>';
            } // if ( $pubs_data )
            wp_reset_postdata();
            }

        } // foreach ( $terms as $term )

        wp_reset_postdata();
        ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<section id="dashboard_div" class="chart-dashboard">
    <figure class="chart-pie">
        <div id="slider_div"></div>
        <div id="chart_div"></div>
    </figure>
    <section class="chart-table">
        <h2 style="margin-bottom: 0;">Daily Newspapers using identifiable CMS</h2>
        <p style="margin: 0;">Total <strong>Papers</strong> and <strong>Circulation</strong> for each CMS.<br>
            Average PageSpeed Insights results (2015-05 articles), sorted by <strong>Score</strong>.</p>
        <ul style="margin-top: 0;">
            <li>Total Papers: <span id="sum"></span></li>
            <li>Total CMSs: <span id="cnt"></span></li>
            <li>Avg. Score: <span id="avg"></span></li>
        </ul>
        <figure id="table_div" style="padding-top: 30px"></figure>
    </section>
</section>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'table', 'controls']});
    google.charts.setOnLoadCallback(drawMainDashboard);

    function drawMainDashboard() {
        var dashboard = new google.visualization.Dashboard(
            document.getElementById('dashboard_div'));

        // Dashboard filter: slider.
        var sliderPapers = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'slider_div',
            'options': {
                'filterColumnIndex': 1,
                'ui': {
                    'labelStacking': 'vertical',
                    'label': 'Filter by papers per CMS:',
                    'format':  { 'pattern':'#,###' },
                }

            }
        });

        var pie = new google.visualization.ChartWrapper({
            'chartType': 'PieChart',
            'containerId': 'chart_div',
            'options': {
                'width': 360,
                'height': 360,
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
                'allowHtml': true,
                'sortColumn': 8,
                'sortAscending': false,
                'showRowNumber': true,
                'width': '100%',
                'height': 'auto',
            },
            'view': {'columns': [9, 1, 2, 3, 4, 5, 6, 7, 8]}
        });

        var data = new google.visualization.DataTable();
            data.addColumn('string', 'CMS');
            data.addColumn('number', 'Papers');
            data.addColumn('number', 'Circ.');
            data.addColumn('number', 'DOM');
            data.addColumn('number', 'Requests');
            data.addColumn('number', 'Size (MB)');
            data.addColumn('number', 'Speed (s)');
            data.addColumn('number', 'TTI (s)');
            data.addColumn('number', 'Score');
            data.addColumn('string', 'CMS');
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

        dashboard.bind([sliderPapers], [pie, table]);
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
                column: 8,
                label: 'Average',
                type: 'number',
                aggregation: google.visualization.data.avg
            }, {
                // get the sum
                column: 1,
                label: 'Count',
                type: 'number',
                aggregation: google.visualization.data.count
            }, {
                // get the sum
                column: 1,
                label: 'Sum',
                type: 'number',
                aggregation: google.visualization.data.sum
            }]);
            document.getElementById('avg').innerHTML = group.getValue(0, 1).toFixed(1);
            document.getElementById('cnt').innerHTML = group.getValue(0, 2);
            document.getElementById('sum').innerHTML = group.getValue(0, 3);
        });

    }

</script>

        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>

            <section class="content-col">
                <p><small>Since 2018-06 we've been unable to run tests on <a href="https://news.pubmedia.us/owner/the-mcclatchy-company/">McClatchy</a> papers using the <a href="https://news.pubmedia.us/cms/escenic/">Escenic</a>, so their articles are not in these results.</small></p>
                <p>Detailed PageSpeed Insights averages (<?php echo $month; ?>) for daily newspapers using each CMS.</p>
                <?php echo $html; ?>
            </section>

        </main><!-- #main -->

	</div><!-- #primary -->

<details>
    <summary><small>(Test: data arrays)</small></summary>
    <pre>
        <?php
       // print_r( netrics_site_pagespeed( 4031 ) );
        $pub_avgs_mo = get_post_meta( 4031, 'nn_psi_avgs', true );
        print_r( end( $pub_avgs_mo ) );

        // print_r( $pubs_data );

        ?>
    </pre>
</details>
<?php // get_sidebar(); ?>
<!-- =file: page-cms -->
<?php get_footer(); ?>

