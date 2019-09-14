<?php
/**
 * Test code
 *
 * https://news.pubmedia.us/test/
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

	</div><!-- #primary -->

<pre>
Top 25 Score (circ. 40K+)
<?php
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
$query = new WP_Query( $args );
foreach ( $query->posts as $post ) {
    echo "\n{$post->ID}\t{$post->post_title}\t" . get_post_meta( $post->ID, 'nn_circ', true ) . "\t" . get_post_meta( $post->ID, 'nn_psi_score', true );
}

wp_reset_postdata();
?>

</pre>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
