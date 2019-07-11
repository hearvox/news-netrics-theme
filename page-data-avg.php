<?php
/**
 * Test code
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

<table class="tabular">
    <caption>Newspaper average PageSpeed results, with Alexa rank and BuiltWith counts</caption>
    <thead>
        <tr style="font-style: italic;">
            <th style="text-align: left;">Domain</th>
            <th>Score (%)</th>
            <th>Speed Index (sec.)</th>
            <th>Interactive (sec.)</th>
            <th>Size (MB)</th>
            <th>HTTP requests</th>
            <th>DOM nodes</th>
            <th>Circulation</th>
            <th>Site Rank</th>
            <th>Print Since</th>
            <th>Online Since</th>
            <th>Techs</th>
            <th>AdTech</th>
            <th>Track</th>
            <th>Script</th>

            <th>Results</th>
            <th style="padding-left: 1rem;">Site ID</th>
            <th style="text-align: left; padding-left: 1rem;">Site URL</th>
        </tr>
    </thead>
    <tbody>
<?php

$pubs_avgs = '';
$query     = newsstats_get_pub_posts( 3000 );

foreach ( $query->posts as $post ) {

    $articles = get_post_meta( $post->ID, 'nn_articles_201905', true);

    if ( $articles && isset( $articles[0]['pagespeed'] ) ) {

        // @todo Skip if no pagespeed.
        $pagespeed = wp_list_pluck( $articles, 'pagespeed' );
        $errors    = wp_list_pluck( $pagespeed, 'error' );

        if ( in_array( 0, $errors) ) { // Has results (error = 0).

            foreach ( $pagespeed as $key => $result ) { // Remove if no results.
                if ( $result['error'] ) {
                    unset( $pagespeed[$key] );
                }
            }

            $count_results = count( $pagespeed ); // Number of articles with results.
            $post_id       = $post->ID;
            $post_meta     = get_post_meta( $post_id );

            $pubs_avgs .= '<tr>';
            $pubs_avgs .= '<td style="text-align: left;">' . $post->post_title . '</td>';

            // Pagespeed data.
            $score    = nstats_mean( wp_list_pluck( $pagespeed, 'score' ) ) * 100;
            $speed    = nstats_mean( wp_list_pluck( $pagespeed, 'speed' ) ) / 1000;
            $tti      = nstats_mean( wp_list_pluck( $pagespeed, 'tti' ) ) / 1000;
            $size     = nstats_mean( wp_list_pluck( $pagespeed, 'size' ) ) / 1000000;
            $requests = nstats_mean( wp_list_pluck( $pagespeed, 'requests' ) );
            $dom      = nstats_mean( wp_list_pluck( $pagespeed, 'dom' ) );

            $pubs_avgs .= '<td>' . number_format( $score, 1, '.', ',' ) . '</td>';
            $pubs_avgs .= '<td>' . number_format( $speed, 1, '.', ',' ) . '</td>';
            $pubs_avgs .= '<td>' . number_format( $tti, 1, '.', ',' ) . '</td>';
            $pubs_avgs .= '<td>' . number_format( $size, 1, '.', ',' ) . '</td>';
            $pubs_avgs .= '<td>' . number_format( $requests, 1, '.', ',' ) . '</td>';
            $pubs_avgs .= '<td>' . number_format( $dom, 1, '.', ',' ) . '</td>';

            $circ = ( isset( $post_meta['nn_circ'] ) && $post_meta['nn_circ'][0] )
                ? number_format( $post_meta['nn_circ'][0], 0, '.', ',' ) : 0;
            // $circ = ( get_post_meta( $post->ID, 'nn_pub_circ_ep', true) ) ? number_format( get_post_meta( $post->ID, 'nn_pub_circ_ep', true), 0, '.', ',' ) : '';

            // Get site data (inclding Alexa and BuiltWith).
            $nn_site  = get_post_meta( $post_id, 'nn_site', true);
            // $rank = ( isset( $post_meta['nn_rank'] ) && $post_meta['nn_rank'][0] ) ? number_format( $post_meta['nn_rank'][0], 0, '.', ',' ) : null;

            // Get Alexa data.
            $since = ( isset ( $nn_site['alexa']['since'] ) && $nn_site['alexa']['since'] )
                ? date_parse_from_format( 'd-M-Y', $nn_site['alexa']['since'] ) : false;
            $year  = ( $since ) ? absint( $since['year'] ) : '';

            $pubs_avgs .= '<td>' . get_post_meta( $post_id,'nn_circ', true ) . '</td>';
            $pubs_avgs .= '<td>' . get_post_meta( $post_id,'nn_rank', true ) . '</td>';
            $pubs_avgs .= '<td>' . get_post_meta( $post_id, 'nn_pub_year', true) . '</td>';
            $pubs_avgs .= '<td>' . $year . '</td>';


            // BuiltWith data.
            if ( isset( $nn_site['builtwith'] ) ) {
                unset( $nn_site['builtwith']['date'] );
                unset( $nn_site['builtwith']['error'] );

            }

            $techs   = ( isset ( $nn_site['builtwith'] ) ) ? array_sum( $nn_site['builtwith'] ) : '';
            $ads     = ( isset ( $nn_site['builtwith']['ads'] ) ) ? $nn_site['builtwith']['ads'] : '';
            $tracks  = ( isset ( $nn_site['builtwith']['analytics'] ) ) ? $nn_site['builtwith']['analytics'] : '';
            $scripts = ( isset ( $nn_site['builtwith']['javascript'] ) ) ? $nn_site['builtwith']['javascript'] : '';

            $pubs_avgs .= '<td>' . $techs . '</td>';
            $pubs_avgs .= '<td>' . $ads . '</td>';
            $pubs_avgs .= '<td>' . $tracks . '</td>';
            $pubs_avgs .= '<td>' . $scripts . '</td>';
            $pubs_avgs .= '<td>' . $count_results . '</td>';
            $pubs_avgs .= '<td>' . $post->ID . '</td>';
            $pubs_avgs .= '<td style="text-align: left; padding-left: 1rem;">' . get_post_meta( $post_id, 'nn_pub_url', true) . '</td>';
            $pubs_avgs .= '</tr>';

        }

    }

}

echo $pubs_avgs;

/*
Import into Google Sheet:
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=1731776477
https://news.pubmedia.us/data-avg/
=importhtml(S1,"table",1)

Copy for display:
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=30371215

*/

?>
    </tbody>
</table>

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
