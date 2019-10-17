<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();
?>
<style type="text/css">
thead th {
  position: sticky;
  top: 95px;
}
</style>
	<div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>
        </main><!-- #main -->
<?php
$pubs_avgs = '';
$query     = newsstats_get_pub_posts( 2000 );
$metrics   = netrics_get_pagespeed_metrics();
$html      = '';
$results = $papers = 0;

foreach ( $query->posts as $post ) {
    $post_id    = $post->ID;
    $pub_avgs   = get_post_meta( $post->ID, 'nn_psi_avgs', true);
    $month_avgs = end( $pub_avgs );

    if ( isset( $month_avgs['score'] ) ) {

        $html .= '<tr>';
        $html .= '<td style="text-align: left;">' . $post->post_title . '</td>';

        // PSA averages
        foreach ( $metrics as $metric ) {
            $html .= '<td>' . netrics_pagespeed_format( $metric, $month_avgs[ $metric ], 1 ) . '</td>';
        }

        $html .= '<td>' . $month_avgs['results'] . '</td>';
        $html .= '<td>' . get_post_meta( $post_id,'nn_circ', true ) . '</td>';
        $html .= '<td>' . get_post_meta( $post_id,'nn_rank', true ) . '</td>';

        // BuiltWith data.
        $nn_site = get_post_meta( $post_id, 'nn_site', true );
        if ( isset( $nn_site['builtwith'] ) ) {
            unset( $nn_site['builtwith']['date'] );
            unset( $nn_site['builtwith']['error'] );
        }

        $techs   = ( isset ( $nn_site['builtwith'] ) ) ? array_sum( $nn_site['builtwith'] ) : '';
        $ads     = ( isset ( $nn_site['builtwith']['ads'] ) ) ? $nn_site['builtwith']['ads'] : '';
        $tracks  = ( isset ( $nn_site['builtwith']['analytics'] ) ) ? $nn_site['builtwith']['analytics'] : '';
        $scripts = ( isset ( $nn_site['builtwith']['javascript'] ) ) ? $nn_site['builtwith']['javascript'] : '';

        $html .= '<td>' . $techs . '</td>';
        $html .= '<td>' . $ads . '</td>';
        $html .= '<td>' . $tracks . '</td>';
        $html .= '<td>' . $scripts . '</td>';
        $html .= '<td>' . $post_id . '</td>';
        $html .= '<td style="text-align: left; padding-left: 1rem;">' . get_post_meta( $post_id, 'nn_pub_url', true) . '</td>';
        $html .= '</tr>';

        // Count papers and results.
        $results += $month_avgs['results'];
        $papers++;
    }
}

/*
Import into Google Sheet:
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=1731776477
https://news.pubmedia.us/data-avg/
=importhtml(S1,"table",1)

Copy for display:
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=30371215

*/

?>
<table class="tabular thead-sticky">
    <caption>Average PageSpeed Insights results for <output><?php echo $papers; ?></output> U.S. daily newspapers (<output><?php echo $results ?></output> articles: <?php echo key( array_slice( $pub_avgs, -1, 1, true ) ); ?>), with Alexa rank and BuiltWith counts</caption>
    <thead>
        <tr style="font-style: italic;">
            <th style="text-align: left;">Domain</th>
            <th>DOM nodes</th>
            <th>HTTP requests</th>
            <th>Size (MB)</th>
            <th>Speed Index (sec.)</th>
            <th>Interactive (sec.)</th>
            <th>Score (%)</th>
            <th>Results</th>
            <th>Circulation</th>
            <th>Site Rank</th>
            <th>Techs</th>
            <th>AdTech</th>
            <th>Track</th>
            <th>Script</th>
            <th style="padding-left: 1rem;">Site ID</th>
            <th style="text-align: left; padding-left: 1rem;">Site URL</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $html; ?>
    </tbody>
</table>

<pre>
</pre>
	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
