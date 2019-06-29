<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package newsstats
 */

get_header(); ?>

<style type="text/css">
#table_owners { margin: 1rem 0; }
.google-visualization-table-td { white-space: nowrap; }
</style>

	<div id="primary" class="content-area">

            <?php
            $counts = 0;
            $cmss   = 0;
            $terms  = get_terms( 'cms' );
            $rows   = '';

            foreach ( $terms as $term ) {
                $counts += $term->count;
                $cmss++;
                // Get Owner score data.
                $args = array(
                    'post_type' => 'publication',
                    'post_per_page' => -300,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'cms',
                            'fields' => 'ids',
                            'terms' => $term,
                        )
                    )
                );
                $query = new WP_Query( $args );

                $pubs_data = netrics_get_pubs_pagespeed_query( $query );

                if ( $pubs_data ) {
                    $rows .= "['{$term->name}',{$term->count},";

                    foreach ( $pubs_data as $key => $data ) {
                        $num = nstats_mean( $data );

                        switch ( $key ) {
                            case 'score':
                                $num = $num * 100;
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
                                break;
                        }

                        $rows .= $num . ',';

                    }
                $rows .= "],\n";
                }
            }
            ?>



            <h2>CMS by score</h2>
            <p><?php echo $counts; ?> daily newspapers use these <?php echo $cmss; ?> CMSs<br>
            Table sorted by their PageSpeed Insights average scores (2015-05 articles).</p>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div id="dashboard_div" style="border: 1px solid #ccc; margin-top: 1em">
    <table class="columns">
        <tr>
            <td>
                <div id="slider_div" style="padding-left: 15px"></div>
            </td>
            <td>


                <p>
            </td>
        </tr>
        <tr>
            <td>
                <div id="chart_div" style="padding-top: 15px"></div>
            </td>
            <td>
                <div id="table_div" style="padding-top: 30px"></div>
                <p style="font-size: 0.8rem;">( Average: <span id="avg"></span> / Count: <span id="cnt"></span> / Sum: <span id="sum"></span>)
            </td>
        </tr>
    </table>
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
            'containerId': 'slider_div',
            'options': {
                'filterColumnIndex': 1,
                'ui': {
                    'labelStacking': 'vertical',
                    'label': 'Filter by papers:',
                    'format':  { 'pattern':'#,###' },
                }

            }
        });

        var pie = new google.visualization.ChartWrapper({
            'chartType': 'PieChart',
            'containerId': 'chart_div',
            'options': {
                'width': 300,
                'height': 300,
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
                sortColumn: 7,
                sortAscending: false,
                showRowNumber: true,
                 width: '100%',
                height: '100%',
            }
        });

    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Owner Group');
        data.addColumn('number', 'Papers');
        data.addColumn('number', 'Dom');
        data.addColumn('number', 'Requests');
        data.addColumn('number', 'MB');
        data.addColumn('number', 'Speed(s)');
        data.addColumn('number', 'TTI(s)');
        data.addColumn('number', 'Score');
        data.addRows([
<?php echo $rows; ?>
    ]);

        // Print number with to one decimal place; apply to specified columns
        var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
        numdecFormat.format(data, 2);
        numdecFormat.format(data, 3);
        numdecFormat.format(data, 4);
        numdecFormat.format(data, 5);
        numdecFormat.format(data, 6);
        numdecFormat.format(data, 7);

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
                column: 1,
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

            <h2>Detailed results</h2>
            <p>PageSpeed Insights results averages for the daily newspapers using these CMSs:
            <?php
            foreach ( $terms as $term ) {

                // Get Owner score data.
                $args = array(
                    'post_type' => 'publication',
                    'post_per_page' => -300,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'cms',
                            'fields' => 'ids',
                            'terms' => $term,
                        )
                    )
                );
                $query = new WP_Query( $args );

                $pubs_data = netrics_get_pubs_pagespeed_query( $query );

                if ( $pubs_data ) {
            ?>
            <table class="tabular">
                <caption><a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a>: PageSpeed average results (2019-05)</caption>
                <?php echo netrics_pagespeed( $pubs_data ); ?>
                <tfoot>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;"><?php echo count( $pubs_data['score'] ) ?> articles from <?php echo $query->found_posts; ?> newspapers</td>
                    </tr>
                </tfoot>
            </table>
                <?php } // if ( $pubs_data ) ?>
            <?php } // foreach ( $terms as $term ) ?>

        </main><!-- #main -->

	</div><!-- #primary -->

    <pre>
        <?php print_r( $pubs_data ); ?>
    </pre>

<?php // get_sidebar(); ?>
<!-- =file: page-sitemap -->
<?php get_footer(); ?>

