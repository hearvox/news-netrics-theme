<?php
/**
 * Page template for /news-table/
 *
 *
 * @package newsstats
 */

get_header();

?>

    <div id="primary" class="content-area">

    <?php
    $metrics = netrics_get_pagespeed_metrics(); // Array of PageSpeed metric names (score, size, etc.)
    $json    = '';

    // Get Owner score data.
    $args = array(
        'post_type'      => 'publication',
        'posts_per_page' => 4000,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $query = new WP_Query( $args );

    // Make array of Publications data.
    foreach ( $query->posts as $post ) {
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



    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div id="dashboard_div" class="chart-dashboard" style="border: 1px solid #ccc; width: 100%">
        <h1>U.S. Daily Newspapers</h1>
        <div id="stringFilter_control_div" style="margin: 1rem 3rem;"></div>
        <figure id="table_div" style="display: block; padding-top: 30px; width: 100%">
            Loading… <img src="https://news.pubmedia.us/wp-content/themes/newsstats/img/ajax-loader.gif" width="220" height="19">
        </figure>
    </div><!-- #dashboard_div -->
<script type="text/javascript">
google.charts.load('current', {'packages':['table', 'controls']});
google.charts.setOnLoadCallback(drawMainDashboard);

function drawMainDashboard() {
    var dashboard = new google.visualization.Dashboard(
        document.getElementById('dashboard_div'));

    // Dashboard filter: search input.
    var StringFilter = new google.visualization.ControlWrapper({
        'controlType': 'StringFilter',
        'containerId': 'stringFilter_control_div',
        'options': {
            'filterColumnIndex': 1,
            'ui': {
                'label': 'Filter by name:',
            }
        }
    });

    // Google Visualization: Table chart.
    var table = new google.visualization.ChartWrapper({
        'chartType': 'Table',
        'containerId': 'table_div',
        'options': {
            'allowHtml': true,
            'sortColumn': 0,
            'sortAscending': true,
            'showRowNumber': true,
            'width': '100%',
            'height': '100%',
        },
    });

var data = google.visualization.arrayToDataTable([
        [   {label: 'Domain', id: 'domain', type: 'string'},
            {label: 'Name&mdash; results link: &#9432;', id: 'name', type: 'string'},
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

    // Attach controls to charts.
    dashboard.bind(StringFilter, table);
    dashboard.draw(data);

/*
    // Data cols and rows.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Domain');
    data.addColumn('string', 'Name (results &raquo;)');
    data.addColumn('number', 'Circulation');
    data.addColumn('number', 'Site Rank');
    data.addColumn('string', 'State');
    data.addColumn('string', 'City');
    data.addColumn('number', 'Population');
    data.addColumn('string', 'Owner');
    data.addColumn('string', 'CMS');
    data.addColumn('number', 'DOM');
    data.addColumn('number', 'Requests');
    data.addColumn('number', 'Size (MB)');
    data.addColumn('number', 'Speed (s)');
    data.addColumn('number', 'TTI (s)');
    data.addColumn('number', 'Score');
    data.addColumn('string', 'name');
    data.addRows([
<?php // echo $json; ?>
    ]);

    // Format number to one decimal place; apply to specified columns.
    var numdecFormat = new google.visualization.NumberFormat({fractionDigits: 1});
    numdecFormat.format(data, 9);
    numdecFormat.format(data, 10);
    numdecFormat.format(data, 11);
    numdecFormat.format(data, 12);
    numdecFormat.format(data, 13);
    numdecFormat.format(data, 14);


    $json .= '\'<a title="' . esc_html( $post->post_title ) . '" href="'
        . esc_url( $post_meta['nn_pub_url'][0] ) . '">' . esc_html( $post->post_title ) . '</a>\',';

    $json .= "'" . esc_html( $post_meta['nn_pub_name'][0] ) . "'],\n";
    {label: 'name', id: 'name-val', type: 'string'} ],
    'view': {'columns': [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]}

*/


}
</script>



	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php // get_template_part( 'template-parts/content', 'page' ); ?>

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
