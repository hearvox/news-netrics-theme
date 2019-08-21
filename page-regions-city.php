<?php
/**
 * Data for Region: Couty (taxonomy)
 * https://news.pubmedia.us/regions/regions-city/
 *
 *
 * @package newsstats
 */

get_header();
?>

<style type="text/css">
.google-visualization-controls-label,
.google-visualization-controls-rangefilter-thumblabel,
.google-visualization-controls-slider-horizontal {
    display: inline-block;
    font-weight: 500;
}

.google-visualization-controls-label {
    font-style: italic;
}

.google-visualization-controls-rangefilter-thumblabel {
    border: 1px dotted #333;
    color: darkred;
    font-family: "Lucida Console", Monaco, Consolas, "Andale Mono", "DejaVu Sans Mono", monospace;
    text-align: right;
    width: 7rem;
}

.wide-slider { white-space: nowrap; }

.wide-slider .google-visualization-controls-slider-horizontal {
    width: 800px;
}

.wide-slider .google-visualization-controls-rangefilter-thumblabel {}

.wide-slider .google-visualization-controls-label {}
</style>

<?php
$city_data = get_post_meta( 7594, 'nn_cities', true );
$total_paper = $total_city = $total_circ = $total_pop = 0;
$json   = '';

foreach ( $city_data as $city ) {
    // Data.
    $population   = $city['population'];
    $pop_density  = $city['pop_density'];
    $publications = $city['count'];
    $circulation  = ( $city['circ_sum'] ) ? $city['circ_sum'] : 0;

    // Data calculations: ratios and precentages.
    $pub_per_pop       = ( $publications ) ? $publications / ( $population / 1000000 ) : null; // Pub/Pop.-1M.
    $circ_per_pub      = ( $circulation && $publications ) ? ( $circulation / 1000 ) / $publications : null; // Circ.-1K/Pub.
    $circ_pop_pc       = ( $circulation ) ? ( $circulation / $population ) * 100 : null;
    $news_cover        =
        ( $pub_per_pop * 6 + // Adjust range/weight.
        $circ_pop_pc   * 2 + // Adjust range/weight.
        $circ_per_pub ) / 5;

    // Rows for Google Table chart (use double quotes to avoid single-quotes in city names).
    $json .= ( $publications ) // Link if city has paper.
        ? "[{v:\"{$city['name']}\",f:\"<a href='/region/{$city['slug']}'>{$city['name']}</a>\"},"
        : "[\"{$city['name']}\",";
    $json .= "\"{$city['state']}\",$publications,$population,$pop_density,$pub_per_pop,$circulation,$circ_pop_pc,$circ_per_pub,$news_cover],\n";

    // Sum totals.
    $total_paper += $publications;
    $total_circ  += $circulation;
    $total_pop   += $population;
    $total_city++;
}

$g_chart_cols = array(
    'name'            => array( 'label' => 'Name', 'type' => 'string', 'format' => null ),
    'state'           => array( 'label' => 'State', 'type' => 'string', 'format' => null ),
    'paper'           => array( 'label' => 'Papers', 'type' => 'number', 'format' => 'numdecFormat' ),
    'pop'             => array( 'label' => 'Population', 'type' => 'number', 'format' => 'numpcFormat' ),
    'pop_density'     => array( 'label' => 'Pop/sq-mi', 'type' => 'number', 'format' => 'numdecFormat' ),
    'pub_per_pop'     => array( 'label' => 'Papers/Pop-1M', 'type' => 'number', 'format' => 'numdecFormat' ),
    'circ'            => array( 'label' => 'Circulation', 'type' => 'number', 'format' => 'numdecFormat' ),
    'circ_pop_pc'     => array( 'label' => 'Circ/Pop%', 'type' => 'number', 'format' => 'numpcFormat' ),
    'circ_per_pub'    => array( 'label' => 'Circ/Paper', 'type' => 'number', 'format' => 'numKFormat' ),
    'news_cover'      => array( 'label' => 'Cover%', 'type' => 'number', 'format' => 'numdecFormat' ),
);

function mk_g_chart() {
    $json_cols = $json_rows = '';
    global $g_chart_cols;

    $this_chart = array();

    foreach ( $this_chart as $value ) {
        $col = $g_chart_cols[ $value ];
        $json_cols .= "data.addColumn('{$col['type']}', '{$col['label']}'),\n";
    }

}

?>

    <div id="primary" class="content-area">

        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>
        </main><!-- #main -->

        <section id="dashboard_div" class="chart-dashboard" style="border: 1px solid #ccc; width: 100%">
            <aside class="chart-controls">
                <table class="tabular alignright" style="width: auto;">
                    <thead>
                        <tr>
                            <td></td>
                            <th scope="col">Showing</th>
                            <th scope="col">of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Cities</th>
                            <td><output id="count_city"></output></td>
                            <td><?php echo number_format( $total_city ); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Papers</th>
                            <td><output id="sum_paper"></output></td>
                            <td><?php echo number_format( $total_paper ); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Circ.</th>
                            <td><output id="sum_circ"></output></td>
                            <td><?php echo number_format( $total_circ ); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Pop.</th>
                            <td><output id="sum_pop"></output></td>
                            <td><?php echo number_format( $total_pop ); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div>Filter Cities <em>(keyboard arrows step by 1K)</em> by:</strong></div>
                <p id="slider_papers"></p>
                <p id="slider_circ" style="width: 100%"></p>
                <p id="slider_pop" style="width: 100%"></p>
            </aside>
            <figure id="table_div" style="display: block; padding-top: 30px; width: 100%">
                <p>Loading… (1K cities takes a few seconds) <img src="https://news.pubmedia.us/wp-content/themes/newsstats/img/ajax-loader.gif" width="220" height="19"></p>
                <div class="loader"></div>
            </figure>
        </section><!-- #dashboard_div -->

    </div><!-- #primary -->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['table', 'controls']});
google.charts.setOnLoadCallback(drawMainDashboard);

function drawMainDashboard() {
    var dashboard = new google.visualization.Dashboard(
        document.getElementById('dashboard_div'));

    // Dashboard filter: slider.
    var sliderPapers = new google.visualization.ControlWrapper({
        'controlType': 'NumberRangeFilter',
        'containerId': 'slider_papers',
        'options': {
            'filterColumnIndex': 2,
            // 'minValue': 1,
            'ui': {
                'format': { 'pattern':'#,###' },
                'labelStacking': 'vertical',
                'label': 'Papers per City',
                'unitIncrement': 1,
            }
        }
    });

    // Dashboard filter by curculaton: slider.
    var sliderCirc = new google.visualization.ControlWrapper({
        'controlType': 'NumberRangeFilter',
        'containerId': 'slider_circ',
        'options': {
            'filterColumnIndex': 6,
            'ui': {
                'cssClass': 'wide-slider',
                'format':  { 'pattern':'#,###' },
                'label': 'Circulation',
                'labelStacking': 'vertical',
                'unitIncrement': 1000,
            }
        }
    });

    // Dashboard filter by score: slider.
    var sliderPop = new google.visualization.ControlWrapper({
        'controlType': 'NumberRangeFilter',
        'containerId': 'slider_pop',
        'options': {
            'filterColumnIndex': 3,
            'ui': {
                'cssClass': 'wide-slider',
                'format':  { 'pattern':'#,###' },
                'label': 'Population',
                'labelStacking': 'vertical',
                'unitIncrement': 1000,
            }
        }
    });

    // Google Visualization: Table chart.
    var table = new google.visualization.ChartWrapper({
        'chartType': 'Table',
        'containerId': 'table_div',
        'options': {
            'allowHtml': true,
            'sortColumn': 2,
            'sortAscending': false,
            'frozenColumns': 1,
            'showRowNumber': true,
            'width': '100%',
            'height': '1500px',
        },
    });

    // Data cols and rows.
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'City');
        data.addColumn('string', 'State');
        data.addColumn('number', 'Papers');
        data.addColumn('number', 'Population');
        data.addColumn('number', 'Pop/sq-mi');
        data.addColumn('number', 'Papers/Pop-1M'); // Format.
        data.addColumn('number', 'Circulation');
        data.addColumn('number', 'Circ/Pop%'); // Format.
        data.addColumn('number', 'Circ/Paper'); // Format.
        data.addColumn('number', 'Cover%'); // Format.
        data.addRows([
<?php echo $json; ?>
    ]);

    // Format number to one decimal place; apply to specified columns.
    var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
    numdecFormat.format(data, 4);
    numdecFormat.format(data, 5);
    numdecFormat.format(data, 8);
    numdecFormat.format(data, 9);

    var numpcFormat = new google.visualization.NumberFormat({fractionDigits: 1, suffix: '%'});
    numpcFormat.format(data, 7);

    var numKFormat = new google.visualization.NumberFormat({fractionDigits: 1, suffix: 'K'});
    numKFormat.format(data, 8);

    // Attach controls to charts.
    dashboard.bind([sliderPapers, sliderCirc, sliderPop], [table]);
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
            // Get averages, counts, or sums
            column: 2,
            label: 'Paper Sum',
            type: 'number',
            aggregation: google.visualization.data.sum
        }, {
            column: 0,
            label: 'City Count',
            type: 'number',
            aggregation: google.visualization.data.count
        }, {
            column: 3,
            label: 'Population Sum',
            type: 'number',
            aggregation: google.visualization.data.sum
        }, {
            column: 6,
            label: 'Circulation Sum',
            type: 'number',
            aggregation: google.visualization.data.sum
        }]);

        // Write aggregations.
        document.getElementById('sum_paper').innerHTML  = group.getValue(0, 1).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        document.getElementById('count_city').innerHTML = group.getValue(0, 2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        document.getElementById('sum_pop').innerHTML = group.getValue(0, 3).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        document.getElementById('sum_circ').innerHTML = group.getValue(0, 4).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    });
}
</script>

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
