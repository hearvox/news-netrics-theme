<?php
/**
 * Article data
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

<table class="tabular thead-sticky">
    <caption>Articles with PageSpeed results (2019-07)</caption>
    <thead>
        <tr style="font-style: italic;">
            <th style="text-align: left;">Domain</th>
            <th>Score (%)</th>
            <th>Speed Index (sec)</th>
            <th>Time to Interactive (sec)</th>
            <th>Size (MB)</th>
            <th>HTTP requests</th>
            <th>DOM nodes</th>
            <th style="padding-left: 1rem;">Site ID</th>
            <th style="text-align: left; padding-left: 1rem;">Article URL</th>
        </tr>
    </thead>
    <tbody>

<?php

$html = '';

$args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order'     => 'ASC',
    'nopaging'  => true,
    'posts_per_page' => 2000,
);
$query = new WP_Query( $args );

foreach ( $query->posts as $post ) {
    $post_id = $post->ID;
    $html   .= "";
    $articles = get_post_meta( $post_id, 'nn_articles_201907', true );

    if ( $articles ) {
        foreach ( $articles as $article ) {
            if ( isset( $article['pagespeed']['error'] ) ) {
                $pgspeed = $article['pagespeed'];

                if ( ! $pgspeed['error'] ) {
                    $html .= '<tr>';
                    $html .= '<td style="text-align: left;">' . $post->post_title . '</td>';
                    $html .= '<td>' . $pgspeed['score'] * 100 . '</td>';
                    $html .= '<td>' . round( $pgspeed['speed'] / 1000, 1 ) . '</td>';
                    $html .= '<td>' . round( $pgspeed['tti']  / 1000, 1 ) . '</td>';
                    // $html .= '<td>' . size_format( $pgspeed['size'], 1 ) . '</td>';
                    $html .= '<td>' . round( $pgspeed['size'] / 1000000, 2 ) . '</td>';
                    $html .= '<td>' . number_format( $pgspeed['requests'] ) . '</td>';
                    $html .= '<td>' . number_format( $pgspeed['dom'] ) . '</td>';
                    $html .= '<td style="padding-left: 1rem;">' . $post_id . '</td>';
                    $html .= '<td style="padding-left: 1rem; text-align: left; white-space: nowrap;">' . $article['url'] . '</td>';
                    $html .= '</tr>';

                }
            }
        }
    }
}


echo $html;

/*
Import into Google Sheet (not sortable):
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=0
https://news.pubmedia.us/data-avg/
=importhtml(J1,"table",1)

Copy for display (sortable):
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=1869038060
*/
?>

    </tbody>
</table>

<?php



?>

    </div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
