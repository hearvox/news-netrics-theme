<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();

?>

<?php
$metrics = netrics_get_pagespeed_metrics(); // Array of PageSpeed metric names (score, size, etc.)
$json    = '';

// Get Top 25 Scores with 40K+ circulation.
$args = array(
    'post_type'      => 'publication',
    'posts_per_page' => 25,
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
$query_25 = new WP_Query( $args );

// Make array of Publications data.
foreach ( $query_25->posts as $post ) {
    $post_id   = $post->ID;
    $post_meta = get_post_meta( $post_id );

    // Get publication's PageSpeed result averages.
    $psi_data = netrics_site_pagespeed( $post_id ); // PSI averages.

    // Add to chart if results.
    if ( isset( $psi_data['score' ] ) ) {
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

        // JSON with sanitized data values for rows in Google chart.
        $json .= '[';
        // Domain (external link) and name (internal link).
        $json .= '{v:\'' . esc_html( $post->post_title ) .
            '\',f:\'<a href="' . esc_url( $post_meta['nn_pub_url'][0] ) . '">' . esc_html( $post->post_title ) . '</a>\'},';
        $json .= '\'' . esc_html( $post_meta['nn_pub_name'][0] ) . ' <a class="info-link" href="' . get_post_permalink( $post_id ) . '">&#9432;</a>\',';
        // Circulation and rank meta.
        $json .= absint( get_post_meta( $post_id, 'nn_circ', true ) )  . ',';
        $json .= absint( get_post_meta( $post_id, 'nn_rank', true ) )  . ',';
        // Region tax terms (linked): state, county, city and city population (term meta).
        $json .= ( $term_state && isset( $term_state->name ) )
            ? '\'<a href="' . get_term_link( $term_state->term_id ) . '">' . esc_html( $term_state->name ) . '</a>\',' : ',';
        $json .= ( $term_city && isset( $term_city[0]->name ) )
            ? '\'<a href="' . get_term_link( $term_city[0]->term_id ) . '">' . esc_html( $term_city[0]->name ) . '</a>\',' : ',';
        $json .= esc_html( $city_pop )  . ',';
        // Owner and CMS tax terms (linked).
        $json .= ( $term_owner && isset( $term_owner[0]->name ) )
            ? '\'<a href="' . get_term_link( $term_owner[0]->term_id ) . '">' . esc_html( $term_owner[0]->name ) . '</a>\',' : "'',";
        $json .=( $term_cms && isset( $term_cms[0]->name ) )
            ? '\'<a href="' . get_term_link( $term_cms[0]->term_id ) . '">' . esc_html( $term_cms[0]->name ) . '</a>\',' : "'(unknown)',";

        // Add PageSpeed averages to JSON.
        $psi_data = netrics_site_pagespeed( $post_id ); // PSI averages.
        foreach ($metrics as $metric ) {
            $num = ( isset( $psi_data[ $metric ] ) ) ? $psi_data[ $metric ] : null;

            switch ( $metric ) {
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
                    $num = $num;
                    break;
            }

            $json .= "{v:$num, f:'" . number_format( $num, 1 ) . '\'},';
        }
        $json .= "],\n";

    }
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

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php get_template_part( 'template-parts/content', 'page' ); ?>

        </main><!-- #main -->

        <section style="margin: auto; width: 1020px;">
            <h2>Website performance (August 2019)</h2>
        </section>
        <?php $pubs_data = netrics_get_pubs_query_data(); ?>
        <table class="tabular" style="margin-top: 2rem;">
            <caption>All U.S. daily newspapers: Averages of Google Pagespeed results (2019-08)</caption>
            <?php netrics_pagespeed_mean( $pubs_data ); ?>
            <tfoot>
                <tr>
                    <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
                    <td colspan="6" style="text-align: left;">3,073 articles from 1,043 newspapers</td>
                </tr>
            </tfoot>
        </table>

        <section style="margin: auto; width: 1020px;">
            <h2>Top 25 Scores (August 2019)</h2>
            <p>These are the best-performing websites of U.S. newspapers (with &gt;40K circulation, ⓘ = results).</p>
        </section>
        <figure id="table_div" style="display: block; padding-top: 30px; width: 100%"></figure>

        <?php endwhile; // End of the loop. ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current');
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // var table = new google.visualization.Table(document.getElementById('table_div'));

    // Data cols and rows.
    var data = google.visualization.arrayToDataTable([
        [   {label: 'Domain', id: 'domain', type: 'string'},
            {label: 'Name&nbsp;&nbsp;&nbsp; &mdash; &nbsp;&nbsp;results link: &#9432;', id: 'name', type: 'string'},
            {label: 'Circulation', id: 'circ', type: 'number'},
            {label: 'Site Rank', id: 'rank', type: 'number'},
            {label: 'State', id: 'state', type: 'string'},
            {label: 'City', id: 'city', type: 'string'},
            {label: 'Population', id: 'pop', type: 'number'},
            {label: 'Owner', id: 'owner', type: 'string'},
            {label: 'CMS', id: 'cms', type: 'string'},
            {label: 'DOM', id: 'dom', type: 'number'},
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
    numdecFormat.format(data, 4);
    numdecFormat.format(data, 5);
    numdecFormat.format(data, 6);
    numdecFormat.format(data, 7);
    numdecFormat.format(data, 8);
    numdecFormat.format(data, 9);

*/
    // Google Visualization: Table chart.
    var wrapper = new google.visualization.ChartWrapper({
        chartType:   'Table',
        containerId: 'table_div',
        dataTable: data,
        options: {
            'allowHtml': true,
            'sortColumn': 14,
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

    </div><!-- #primary -->

<details>
    <summary><small>(Test: data arrays)</small></summary>

    <pre>
    <ol>
    <?php

    foreach ( $query_25->posts as $post ) {
        echo "<li>{$post->ID}\t{$post->post_title}\t" . get_post_meta( $post->ID, 'nn_circ', true ) . "\t" . get_post_meta( $post->ID, 'nn_psi_score', true ) . '</li>';
    }


    echo $json;
    ?>
    </ol>
    </pre>
</details>

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
