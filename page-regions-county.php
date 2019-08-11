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

<style type="text/css">


</style>

	<div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>
        </main><!-- #main -->

        <figure id="table_div" class="google-table">
            <p>Loading… (3K counties takes a few seconds) <img src="https://news.pubmedia.us/wp-content/themes/newsstats/img/ajax-loader.gif" width="220" height="19"><br>
            <div class="loader"></div></p>
        </figure>

	</div><!-- #primary -->

<?php
/*

Set post meta (/news-netrics/includes/taxonomies.php): netrics_get_region_data();

https://www.cjr.org/local_news/american-news-deserts-donuts-local.php
https://www.usnewsdeserts.com/

https://developers.google.com/speed/pagespeed/insights/?url=https%3A%2F%2Fnews.pubmedia.us%2Fregion-county%2F&tab=mobile




[179] => Array
        (
            [term_id] => 1587
            [name] => Mohave County
            [slug] => mohave-county-az
            [term_group] => 0
            [term_taxonomy_id] => 1587
            [taxonomy] => region
            [description] => Mohave County, Arizona
            [parent] => 1398
            [count] => 3
            [filter] => raw
            [geoid] => 0500000US04015
            [population] => 209550
            [pop_density] => 15
            [circ_sum] => 23610
        )

/region/bedford-county-pa/

*/



$counties_data = get_post_meta( 7594, 'nn_counties', true );
$json   = '';

foreach ( $counties_data as $county ) {
    // Data.
    $population   = $county['population'];
    $pop_density  = $county['pop_density'];
    $publications = $county['count'];
    $circulation  = ( $county['circ_sum'] ) ? $county['circ_sum'] : null;

    // Data calculations: ratios and precentages.
    $pub_per_pop       = ( $publications ) ? $publications / ( $population / 1000000 ) : null; // Pub/Pop.-1M.
    $circ_per_pub      = ( $circulation ) ? ( $circulation / 1000 ) / $publications : null; // Circ.-1K/Pub.
    $circ_pop_pc       = ( $circulation ) ? ( $circulation / $population ) * 100 : null;
    $news_cover        =
        ( $pub_per_pop * 6 + // Adjust range/weight.
        $circ_pop_pc   * 2 + // Adjust range/weight.
        $circ_per_pub ) / 5;

    // Rows for Google Table chart (use double quotes to avoid single-quotes in county names).
    $json .= ( $publications ) // Link if county has paper.
        ? "[{v:\"{$county['name']}\",f:\"<a href='/region/{$county['slug']}'>{$county['name']}</a>\"},"
        : "[\"{$county['name']}\",";
    $json .= "\"{$county['state']}\",$publications,$population,$pop_density,$pub_per_pop,$circulation,$circ_pop_pc,$circ_per_pub,$news_cover],\n";
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
        data.addColumn('string', 'County');
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

    // Google Visualization: Table chart.
    var wrapper = new google.visualization.ChartWrapper({
        chartType:   'Table',
        containerId: 'table_div',
        dataTable: data,
        options: {
            'allowHtml': true,
            'sortColumn': 2,
            'sortAscending': false,
            'frozenColumns': 1,
            'showRowNumber': true,
            'width': '100%',
            'height': '1500px',
            /*
            'page': 'enable',
            'pageSize': 50,
            'pagingButtons': 'both',
            */
        },
    });
    // Attach controls to charts.
    wrapper.draw();
}
</script>
<?php // get_sidebar(); ?>
<?php get_footer(); ?>
