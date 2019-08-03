<?php
/**
 * Template part for displaying single CPT posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */

$custom_fields = get_post_custom();
$post_id       = get_the_ID();

$rss_url  = ( isset( $custom_fields['nn_pub_rss'][0] ) ) ? $custom_fields['nn_pub_rss'][0] : false;
$rss_link = ( $rss_url ) ? ' | <a href="' . esc_url( $rss_url ) . '">RSS feed</a>' : '';

$site_url  = ( isset( $custom_fields['nn_pub_url'][0] ) ) ? $custom_fields['nn_pub_url'][0] : false;
$site_link = ( $site_url ) ? ' <a href="' . esc_url( $site_url ) . '">Website</a>' : '';

$pub_name    = ( isset( $custom_fields['nn_pub_name'][0] ) ) ? $custom_fields['nn_pub_name'][0] : '';
$pub_year    = ( isset( $custom_fields['nn_pub_year'][0] ) && $custom_fields['nn_pub_year'][0] )
    ? absint( $custom_fields['nn_pub_year'][0] ) : '--';
$pub_circ    = ( isset( $custom_fields['nn_circ'][0] ) && $custom_fields['nn_circ'][0] )
	? number_format( absint( $custom_fields['nn_circ'][0] ) ) : '--';
$pub_rank    = ( isset( $custom_fields['nn_rank'][0] ) && $custom_fields['nn_rank'][0] )
    ? number_format( absint( $custom_fields['nn_rank'][0] ) ) : '--';

$term_region = get_the_terms( $post_id, 'region' );
$term_id     = ($term_region) ? $term_region[0]->term_id : false;

$term_pop  = ( get_term_meta( $term_id, 'nn_region_pop', true ) )
	? get_term_meta( $term_id, 'nn_region_pop', true ) : false;
$term_pop  = ( $term_pop ) ? number_format( floatval( $term_pop ) ) : '';

$args_region = array(
	'format'    => 'id',
	'separator' => ' &gt; ',
);
$regions = get_term_parents_list( $term_id, 'region', $args_region ) ;

$city       = $term_region[0]->name;
$terms_reg  = get_ancestors( $term_id, 'region', 'taxonomy' );
$state_id   = end( $terms_reg );
$state_arr  = get_term_by( 'id', absint( $state_id ), 'region' );
$state      = $state_arr->name;
$latlon     = get_term_meta( $term_id, 'nn_region_latlon', true );

$map_api = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc';
$map_loc = '&q=' . urlencode( $city ) . '+' . $state;
$map_ctr = '&amp;center=' . str_replace( '|', ',', $latlon);
$map_src = $map_api .$map_loc . $map_ctr;

// Alexa Web Info Service
// Use get_post_meta to unserialize, which above get_post_custom() doesn't.
$nn_site    = get_post_meta( $post_id , 'nn_site' );
$awis_desc  = ( isset( $nn_site[0]['alexa']['desc']  ) && $nn_site[0]['alexa']['desc'] )
    ? '&mdash; ' . $nn_site[0]['alexa']['desc'] : '';
$awis_rank  = ( isset( $nn_site[0]['alexa']['rank'] ) && $nn_site[0]['alexa']['rank'] )
    ? number_format( floatval($nn_site[0]['alexa']['rank'] ) ) : '--';
$awis_since = ( isset( $nn_site[0]['alexa']['since']  ) && $nn_site[0]['alexa']['since'] )
    ? date_parse_from_format( 'd-M-Y', $nn_site[0]['alexa']['since'] ) : false;
$awis_year  = ( $awis_since ) ? absint( $awis_since['year'] ) : '--';
$awis_links = ( isset( $nn_site[0]['alexa']['links']  ) && $nn_site[0]['alexa']['links'] )
    ? number_format( (int) $nn_site[0]['alexa']['links'] ) : '--';


/*

$city    = $term_region[0]->name;
$state   = end( get_ancestors( $term_id ) );
$latlon  = explode( get_term_meta( $term_id, 'nn_region_latlon', true ) );
$map_api = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc';
$map_loc = '&q=' . urlencode( $city ) . '+' . $state[0]->name;
$map_ctr = '&center=' . $latlon[0] . ',' . $latlon[1];
$map_src = $map_api .$map_loc . $map_ctr ;
*/

$psi_1905 = netrics_site_pagespeed( $post_id, 'nn_articles_201905' ); // si: 13.9, tti: 30.1, speed: 18.8
$psi_1906 = netrics_site_pagespeed( $post_id, 'nn_articles_201906' ); // si: 13.3, tti: 30.3, speed: 18.8
$psi_1907 = netrics_site_pagespeed( $post_id, 'nn_articles_201907' ); // si: 13.5, tti: 29.7, speed: 20.4

$lines = '';
if ( $psi_1905 ) {
    // Line chart data.
    $lines .= "['5/19', ";
    $lines .= round( $psi_1905['speed'] / 1000, 1 ) . ', ';
    $lines .= round( $psi_1905['tti'] / 1000, 1 ) . ",13.9,30.1],\n";
}

if ( $psi_1906 ) {
    // Line chart data.
    $lines .= "['6/19', ";
    $lines .= round( $psi_1906['speed'] / 1000, 1 ) . ', ';
    $lines .= round( $psi_1906['tti'] / 1000, 1 ) . ",13.3,30.3],\n";
}

if ( $psi_1907 ) {
    // Line chart data.
    $lines .= "['7/19', ";
    $lines .= round( $psi_1907['speed'] / 1000, 1 ) . ', ';
    $lines .= round( $psi_1907['tti'] / 1000, 1 ) . ",13.5,29.7],\n";
}

$articles_1905 = get_post_meta( $post_id, 'nn_articles_201905', true );
$articles_1906 = get_post_meta( $post_id, 'nn_articles_201906', true );
$articles_1907 = get_post_meta( $post_id, 'nn_articles_201907', true );

/*$date_1 = $date_2 = '';
$item_1 = $errors_1  = 0; // Array element.
$g_metrics = array(
    'score' => null,
    'speed' => null,
    'tti' => null,
    'size' => null,
    'dom' => null,
    'requests' => null,
);


if ( $articles_1905 && 1 < count( $articles_1905 ) ) {

    foreach ( $articles_1905 as $article ) {

        if ( isset( $article['pagespeed']['error'] ) ) {

            $pgspeed = $article['pagespeed'];

            if ( ! $pgspeed['error'] ) {
                $date_1 = $article['pagespeed']['date'];


                foreach ($g_metrics as $key => $value) {
                    $g_metrics[$key][$item_1] = $pgspeed[$key];
                } // foreach $g_metrics
                $item_1++;
            } else {
                $errors_1++;
            } // if ! $pgspeed['error']

        } // if $article['pagespeed']['error']
    } // foreach $articles_1905

} // if $articles_1905

$item_2   = 0;
$errors_2 = 0;
if ( $articles_1906 && 1 < count( $articles_1906 ) ) {

    foreach ( $articles_1906 as $article ) {

        if ( isset( $article['pagespeed']['error'] ) ) {

            $pgspeed = $article['pagespeed'];

            if ( ! $pgspeed['error'] ) {
                $date_2 = $article['pagespeed']['date'];

                foreach ($g_metrics as $key => $value) {
                    $g_metrics[$key][$item_2] = $pgspeed[$key];
                }

            } else {
                $errors_2++;
            } // if ! $pgspeed['error']

            $item_2++;
        }
    }
}*/


?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
        <?php if ( $psi_1905 ) { ?>
        <?php } ?>
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <figure id="chart_div" class="alignright" style="width: 180px; height: 180px; margin-right: 0;"></figure>
		<ul class="media-meta" style="list-style: none; margin: 0; padding: 0;">
            <li><strong><big><?php echo esc_html( $pub_name ) ?></big></strong><?php echo esc_html( $awis_desc ); ?></li>
            <li><?php echo trim( $regions, ' &gt; ' ); ?> <small>(pop. <?php echo esc_html( $term_pop ); ?>)</small></li>
            <li><em>Circulation:</em> <?php echo esc_html( $pub_circ ); ?> / <em>Site rank:</em> <?php echo esc_html( $pub_rank ); ?></li>
            <li><?php the_terms( $post_id, 'owner', '<em>Owner:</em> ' ); ?></li>
			<li><em>In print:</em> <?php echo esc_html( $pub_year ); ?> | <em>Online:</em> <?php echo esc_html( $awis_year ); ?></li>
			<li><em>CMS:</em> <?php the_terms( $post_id, 'cms' ); ?> | <?php echo $site_link; ?><?php echo $rss_link; ?></li>
		</ul>
	</header><!-- .entry-header -->

    <section class="content-col">
    <?php
    $pubs_data = netrics_get_pubs_pagespeed_query( $wp_query );
    if ( $pubs_data ) {
            ?>
        <table class="tabular">
            <caption>PageSpeed Insights average results for <?php echo count( $pubs_data['score'] ) ?> articles (2019-06)</caption>
                <?php echo netrics_pagespeed_mean( $pubs_data, $tbody = false ); ?>
                <?php echo netrics_articles_results_table( $post_id, $articles_1906 ); ?>
            </tbody>
        </table>

        <figure id="line_chart" class="alignnone" style="width: 700px; height: 400px; margin: 0;"></figure>

        <?php } // if ( $pubs_data )
        ?>

        <p><em>Articles 2019-07:</em>
        <?php echo netrics_articles_results( $post_id, $articles_1907 ); ?></p>
        <p><em>Articles 2019-06:</em>
        <?php echo netrics_articles_results( $post_id, $articles_1906 ); ?></p>
        <p><em>Articles 2019-05:</em>
        <?php echo netrics_articles_results( $post_id, $articles_1905 ); ?></p>

    </section><!-- .content-col -->

	<footer class="entry-footer">

		<p style="padding-top: 2em;"><img class="screenshot" src="https://s.wordpress.com/mshots/v1/http%3A%2F%2F<?php echo get_the_title() ?>?w=700&h=525" width="700" height="525" alt="Homepage screenshot" /></p>

		<iframe width="700" height="700" frameborder="0" style="border:0" src="<?php echo esc_url( $map_src ); ?>"></iframe>

        <?php the_post_navigation( array( 'prev_text' => '&laquo; %title', 'next_text' => '%title &raquo;' ) ); ?>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<details>
    <summary><small>(Test: data arrays)</small></summary>
        <pre>
        averages 2019-06 <?php print_r( netrics_site_pagespeed( $post_id, 'nn_articles_201906' ) ) ?><br>
        averages 2019-05 <?php print_r( netrics_site_pagespeed( $post_id, 'nn_articles_201905' ) ) ?><br>
        articles 2019-06: <?php print_r( $articles_1906 ); ?><br>
        <?php if ( is_user_logged_in() ) { ?>
        <?php echo get_the_term_list( $post_id, 'post_tag', $post_id . ' tags: ', '/', '<br>' ) ?>
        <?php echo get_the_term_list( $post_id, 'flag', 'Flags: ', '/', '<br>' ) ?>
        <?php echo $lines; ?>
        <?php } ?>
        <?php // print_r( get_post_meta( $post_id, 'nn_error', false ) ); ?>
        <!--
        site info: <?php // print_r( $nn_site ); ?>
        articles 2019-05: <?php // print_r( $articles_1905 ); ?>
        -->
        </pre>
</details>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php if ( $psi_1906 ) { ?>
<script type="text/javascript">
	// @see https://developers.google.com/chart/interactive/docs/gallery/gauge
    google.charts.load('current', {'packages':['gauge','corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data_score = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Score', <?php echo round( $psi_1906['score'] * 100, 1 ); ?>],
        ]);

        var options_score = {
            width: 180, height: 180,
            redFrom: 0, redTo: 50,
            yellowFrom: 50, yellowTo: 90,
            greenFrom: 90, greenTo: 100,
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data_score, options_score);

        var data_history = google.visualization.arrayToDataTable([
            ['Month', 'Speed', 'TTI','All: TTI','All: Speed'],
            <?php echo $lines; ?>
        ]);

        var options_history = {
            title: 'Speed-Index and Time-to-Interactive averages (seconds)',
            colors: ['#e2431e', '#f1ca3a', '#de6649','#f9da6a'],
            curveType: 'function',
            legend: { position: 'bottom' },
            series: {
                0: { lineWidth: 4, pointSize: 5 },
                1: { lineWidth: 4, pointSize: 5 },
                2: { lineWidth: 1, pointSize: 2 },
                3: { lineWidth: 1, pointSize: 2 }
            },
        };

        var chart = new google.visualization.LineChart(document.getElementById('line_chart'));

        chart.draw(data_history, options_history);


    }

</script>

<?php } ?>


