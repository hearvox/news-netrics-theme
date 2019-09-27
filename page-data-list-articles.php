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
        $list .= '</li>2019-09<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }

    unset( $items );

    /*
    $items  = get_post_meta( $post_id, 'nn_articles_201908', true );
    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-08<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }

    unset( $items );

    $items  = get_post_meta( $post_id, 'nn_articles_201907', true );
    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-07<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }

    unset( $items );

    $items  = get_post_meta( $post_id, 'nn_articles_201906', true );
    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-06<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }

    unset( $items );

    $items  = get_post_meta( $post_id, 'nn_articles_201905', true );
    if ( $items && 1 < count( $items ) ) {
        $list .= '</li>2019-05<ol>';
        $list .= netrics_pagespeed_results_list( $query, $items );
        $list .= '</ol>';
    }
    */
}

echo $list;
// $query->rewind_posts();
wp_reset_postdata();

?>



		</main><!-- #main -->

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
