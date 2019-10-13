<?php
/**
 * List articles with their PSI results.
 *
 * https://news.pubmedia.us/data/data-list-articles/
 *
 * @package newsstats
 */

get_header();

$pubs_data = netrics_get_pubs_query_data();
?>

	<div id="primary" class="content-area" style="margin: 0 1rem;">

		<main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php get_template_part( 'template-parts/content', 'page' ); ?>

		<?php endwhile; // End of the loop. ?>

            <table class="tabular" style="">
                <caption>U.S. daily newspapers: Averages of Google Pagespeed results (2019-08)</caption>
                <?php netrics_pagespeed( $pubs_data ); ?>
                <tfoot>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;">3,073 articles from 1,043 newspapers</td>
                    </tr>
                </tfoot>
            </table>

            <h2>Articles and PSI results (2019-10)</h2>

            <?php // netrics_pagespeed_corr( $pubs_data );  ?>

<?php

$list = '';

$query_args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order'     => 'ASC',
    'nopaging'  => true,
    // 'posts_per_page' => 25,
);
$query = new WP_Query( $query_args );

$list = '<ol>';
$done = $none = $papers = $articles = $errors = $scores = 0;
foreach ( $query->posts as $post ) {
    $post_id = $post->ID;
    $site    = get_post_meta( $post_id, 'nn_pub_url', true );
    $rss     = get_post_meta( $post_id, 'nn_pub_rss', true );
    $feed    = ($rss) ? " | <a href=\"$rss\">feed</a>" : '';
    $flags   = ( is_user_logged_in() ) ? get_the_term_list( $post_id, 'flag', '<br>flags: ', '/', '' ) : '';
    $tags    = ( is_user_logged_in() ) ? get_the_term_list( $post_id, 'post_tag', '<br>tags: ', '/', ' ' ) : '';
    $edit    = ( is_user_logged_in() ) ? ' | <a href="' . get_edit_post_link( $post_id ) . '">[edit]</a>' : '';
    $urls    = "<small>(<a href=\"{$post->guid}\">$post_id</a> | <a href=\"$site\">site</a>$feed$edit)$tags$flags</small>";

    // $list  .= "<li>{$post->post_title} $urls";
    $list  .= "<li>$post_id {$post->post_title}";

    $items  = get_post_meta( $post_id, 'nn_articles_new', true );
    if ( $items && 1 < count( $items ) ) {
        $list .= '</li><ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';

        if ( isset( $item['pagespeed']['error'] ) ) {
            $done++;
        }

        // Count articles and errors.
        $articles += count( $items );
        foreach ( $items as $key => $item ) {
            if ( isset( $item['pagespeed']['error'] ) ) {
                $errors  += $item['pagespeed']['error'];
            }

            if ( isset( $item['pagespeed']['score'] ) ) {
                $scores++;
            }
        }

        $papers++; // Pub has articles.
    } else {
        $list .= ' <em>(0 articles)</em></li>';
        $none++; // Pub has no articles.
    }

    /*
    unset( $items );
    $items  = get_post_meta( $post_id, 'nn_articles_201908', true );
    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-08<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }
    */
}

echo "<p>Running tests for <output>$papers</output> papers on <output>$articles</output> articles. (Could not pull articles from <output>$none</output> papers.)<br>
Done: <output>$done</output> papers with <output>$scores</output> results and <output>$errors</output> errors.</p>";

echo $list;


// $query->rewind_posts();
wp_reset_postdata();

?>



		</main><!-- #main -->

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
