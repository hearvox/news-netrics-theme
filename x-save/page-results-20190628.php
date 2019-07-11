<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();

$pubs_data = newsstats_get_pubs_pagespeed();
?>

	<div id="primary" class="content-area">


		<main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php get_template_part( 'template-parts/content', 'page' ); ?>

		<?php endwhile; // End of the loop. ?>

            <table class="tabular" style="">
                <caption>U.S. daily newspapers: Averages of Google Pagespeed results (2019-05)</caption>
                <?php netrics_pagespeed( $pubs_data ); ?>
                <tfoot>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;">3,070 articles from 1,126 newspapers</td>
                    </tr>
                </tfoot>
            </table>

            <?php netrics_pagespeed_corr( $pubs_data );  ?>

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
foreach ( $query->posts as $post ) {
    $post_id = $post->ID;
    $site    = get_post_meta( $post_id, 'nn_pub_url', true );
    $rss     = get_post_meta( $post_id, 'nn_pub_rss', true );
    $feed    = ($rss) ? " | <a href=\"$rss\">feed</a>" : '';
    $flags   = ( is_user_logged_in() ) ? get_the_term_list( $post_id, 'flag', '<br>flags: ', '/', '' ) : '';
    $tags    = ( is_user_logged_in() ) ? get_the_term_list( $post_id, 'post_tag', '<br>tags: ', '/', ' ' ) : '';
    $edit    = ( is_user_logged_in() ) ? ' | <a href="' . get_edit_post_link( $post_id ) . '">[edit]</a>' : '';
    $urls    = "<small>(<a href=\"{$post->guid}\">$post_id</a> | <a href=\"$site\">site</a>$feed$edit)$tags$flags</small>";

    $list  .= "<li>{$post->post_title} $urls";
    $items  = get_post_meta( $post_id, 'nn_articles_201905', true );

    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-05<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }


    $items  = get_post_meta( $post_id, 'nn_articles_201906', true );

    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-06<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }


}

echo $list;
// $query->rewind_posts();
wp_reset_postdata();

/*
$list = '<ol>';
foreach ( $query_posts->posts as $post ) {
    $post_id = $post->ID;
    $site    = get_post_meta( $post_id, 'nn_pub_url', true );
    $rss     = get_post_meta( $post_id, 'nn_pub_rss', true );
    $feed    = ($rss) ? " | <a href=\"$rss\">feed</a>" : '';
    $flags   = ( is_user_logged_in() ) ? get_the_term_list( $post_id, 'flag', ' | flags: ', '/', '' ) : '';
    $tags    = ( is_user_logged_in() ) ? get_the_term_list( $post_id, 'post_tag', ' | tags: ', '/', ' ' ) : '';
    $edit    = ( is_user_logged_in() ) ? ' | <a href="' . get_edit_post_link( $post_id ) . '">[edit]</a>' : '';
    $urls    = " <small>/ (<a href=\"$site\">site</a>$feed$tags$flags$edit)</small>";

    $list  .= "<li><a href=\"{$post->guid}\">$post_id</a> {$post->post_title}$urls";
    $articles = get_post_meta( $post_id, 'nn_articles_201905', true );
    if ( $articles && 1 < count( $articles ) ) {

        $list .= '</li>2019-05<ol>';
        foreach ( $articles as $article ) {

            $list .= "<li><a href=\"{$article['url']}\">{$article['title']}</a>";
            if ( isset( $article['pagespeed']['error'] ) ) {

                $pgspeed = $article['pagespeed'];
                $list .=  '<br><small>';

                if ( ! $pgspeed['error'] ) {

                    $list .=  'Score: ' . $pgspeed['score'] * 100;
                    $list .=  ' | Speed/TTI(s): ' . round( $pgspeed['speed'] / 1000, 1 ) . '/' . round( $pgspeed['tti']  / 1000, 1 );
                    $list .=  ' | Size: ' . size_format( $pgspeed['size'], 1 );
                    $list .=  ' | DOM: ' . number_format( $pgspeed['dom'] );
                    $list .=  ' | Requests: ' . number_format( $pgspeed['requests'] );

                } else {
                    $list .= 'Error: ' . $pgspeed['error'];
                }
                $list .=  '</small>';

            }
            $list .=  "</li>";

        }
        $list .= '</ol>';
    }

    $articles_1906 = get_post_meta( $post_id, 'nn_articles_201906', true );
    if ( $articles_1906 && 1 < count( $articles ) ) {

        $list .= '2019-06<ol>';
        foreach ( $articles as $article ) {

            $list .= "<li><a href=\"{$article['url']}\">{$article['title']}</a></li>";

        }
        $list .= '</ol>';

    }

}
$list .= '</ol>';

$query_posts->rewind_posts();


$urls_1906 = '';
foreach ( $query_posts->posts as $post ) {

    $post_id = $post->ID;
    // $rss     = get_post_meta( $post_id, 'nn_pub_rss', true );

    $articles = get_post_meta( $post_id, 'nn_articles_201906', true );
    if ( $articles && 1 < count( $articles ) ) {

        $urls_1906 .= '</li>2019-06<ol>';
        foreach ( $articles as $article ) {

            $urls_1906 .= "<li><a href=\"{$article['url']}\">{$article['title']}</a></li>";

        }
        $urls_1906 .= '</ol>';

    }

}
$urls_1906 .= '</ol>';
*/



/**
 *
 *
 * Tax 'flag' terms (ID):
 * 'feed' (6170)
 *     'xml' (6171)
 *     json' (6172)
 *     'none' (6175)
 *     'fail' (6176)
 * 'articles' (6177)
 *     '201905' (6178)
 *     'check' (6179)
 *
 */

?>



		</main><!-- #main -->

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
