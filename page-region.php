<?php
/**
 * Data for Region (taxonomy)
 * https://news.pubmedia.us/region/
 *
 *
 * @package newsstats
 */

get_header();
?>


	<div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>
        </main><!-- #main -->

<figure id="table_div" style="display: block; width: 100%"></figure>



	</div><!-- #primary -->

<?php
/*

Set post meta (/news-netrics/includes/taxonomies.php): netrics_get_region_data();

https://www.cjr.org/local_news/american-news-deserts-donuts-local.php
https://www.usnewsdeserts.com/

https://developers.google.com/speed/pagespeed/insights/?url=https%3A%2F%2Fnews.pubmedia.us%2Fregion%2F&tab=mobile
71 mobile / 93 desktop


3 States have different State pops than the sum of their County pops:
st  diff   st-pop  counties-pop
___________________________
nd   1903   760077   758174
ri  48649  1057315  1008666
va  23933  8517685  8493752

{$state['pub_per_pop']}
*/

$states_data = get_post_meta( 7594, 'nn_states', true );
$json   = '';

foreach ( $states_data as $state ) {
    // Data.
    $population   = $state['population'];
    $publications = $state['count'];
    $circulation  = $state['circ_sum'];
    $counties     = $state['counties'];

    // Data calculations: ratios and precentages.
    $counties_with_pub = $counties - $state['county_0_count'];
    $counties_with_pop = $population - $state['county_0_pop'];
    $pub_per_pop       = $publications / $population * 1000000; // Pub/Pop.-10K.
    $pop_per_circ      = $population / $circulation;
    $county_pub_pc     = ($counties) ? $counties_with_pub / $counties * 100 : 0; // Count % of Counties with Pub.
    $county_pub_pop_pc = $counties_with_pop / $population * 100; // Pop. % of Counties with Pub.
    $circ_per_pub      = $circulation / $publications / 1000;
    $circ_pop_pc       = $circulation / $population * 100;
    $news_power        = $pub_per_pop * $circ_per_pub * $circ_pop_pc * $county_pub_pc * $county_pub_pop_pc / 1000000;

    // Rows for Google Table chart.
    $json .= '[{v:\'' . $state['name'] . '\',f:\'<a href="' . $state['term_link'] . '">' . $state['name'] . '</a>\'},';
    $json .= "$publications,$population,$pub_per_pop,$circulation,$circ_pop_pc,$circ_per_pub,";
    $json .= "$counties,$counties_with_pub,$county_pub_pc,$counties_with_pop,$county_pub_pop_pc,$news_power],\n";
}

$g_chart_cols = array(
    'name'            => array( 'label' => 'Name', 'type' => 'string', 'format' => null ),
    'paper'           => array( 'label' => 'Population', 'type' => 'number', 'format' => 'numdecFormat' ),
    'pop'             => array( 'label' => 'Population', 'type' => 'number', 'format' => 'numpcFormat' ),
    'pub_per_pop'     => array( 'label' => 'Papers/Pop-1M', 'type' => 'number', 'format' => 'numdecFormat' ),
    'circ'            => array( 'label' => 'Circulation', 'type' => 'number', 'format' => 'numdecFormat' ),
    'circ_pop_pc'     => array( 'label' => 'Circ/Pop%', 'type' => 'number', 'format' => 'numpcFormat' ),
    'circ_per_pub'    => array( 'label' => 'Circ/Paper', 'type' => 'number', 'format' => 'numKFormat' ),
    'counties'        => array( 'label' => 'Counties', 'type' => 'number', 'format' => 'numdecFormat' ),
    'cnty_pub'        => array( 'label' => 'Counties-w/Paper', 'type' => 'number', 'format' => 'numdecFormat' ),
    'cnty_pub_pc'     => array( 'label' => 'County-w/%', 'type' => 'number', 'format' => 'numpcFormat' ),
    'cnty_pub_pop'    => array( 'label' => 'Pop-w/Paper', 'type' => 'number', 'format' => 'numdecFormat' ),
    'cnty_pub_pop_pc' => array( 'label' => 'Pop-w/Paper', 'type' => 'number', 'format' => 'numpcFormat' ),
    'power'           => array( 'label' => 'Power', 'type' => 'number', 'format' => 'numdecFormat' ),
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


/*
$format = $col['format'] ?? '';
$json_rows .= ',' . ${key};
*/

?>




<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current');
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // var table = new google.visualization.Table(document.getElementById('table_div'));
    // data.addColumn({type:'number', label:'Papers', pattern: '##.00'});

    // Data cols and rows.
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'State');
        data.addColumn('number', 'Papers');
        data.addColumn('number', 'Population');
        data.addColumn('number', 'Papers/Pop-1M'); // Format.
        data.addColumn('number', 'Circulation');
        data.addColumn('number', 'Circ/Pop%'); // Format.
        data.addColumn('number', 'Circ/Paper'); // Format.
        data.addColumn('number', 'Counties');
        data.addColumn('number', 'Counties-w/Paper');
        data.addColumn('number', 'County-w/%'); // Format.
        data.addColumn('number', 'Pop-w/Paper');
        data.addColumn('number', 'Pop-w/%'); // Format.
        data.addColumn('number', 'Power'); // Format.
        data.addRows([
<?php echo $json; ?>
    ]);

    // Format number to one decimal place; apply to specified columns.
    var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
    numdecFormat.format(data, 3);
    numdecFormat.format(data, 12);

    var numpcFormat = new google.visualization.NumberFormat({fractionDigits: 1, suffix: '%'});
    numpcFormat.format(data, 5);
    numpcFormat.format(data, 9);
    numpcFormat.format(data, 11);

    var numKFormat = new google.visualization.NumberFormat({fractionDigits: 1, suffix: 'K'});
    numKFormat.format(data, 6);

    // var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 0});
    // numdecFormat.format(data, 6);


    // Google Visualization: Table chart.
    var wrapper = new google.visualization.ChartWrapper({
        chartType:   'Table',
        containerId: 'table_div',
        dataTable: data,
        options: {
            'allowHtml': true,
            'sortColumn': 1,
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
<?php // get_sidebar(); ?>
<?php get_footer(); ?>
