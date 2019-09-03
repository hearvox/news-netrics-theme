<?php
/**
 * The template for displaying all single CPT posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package newsstats
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'single-publication' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>
		<?php endwhile; // End of the loop. ?>



		</main><!-- #main -->
	</div><!-- #primary -->

	<details>
    	<summary><small>(Test: data arrays)</small></summary>
        <pre>
        <?php $post_id = get_the_id(); ?>
        <?php $city = netrics_get_city_meta( $post_id ); ?>
        <?php print_r( $city['city_meta'] ); ?>
        <?php if ( is_user_logged_in() ) { ?>


        <?php print_r( get_post_meta( $post_id, 'nn_psi_avgs' ,true ) ) ?><br>
        <?php // echo get_the_term_list( $post_id, 'post_tag', $post_id . ' tags: ', '/', '<br>' ) ?>
        <?php // echo get_the_term_list( $post_id, 'flag', 'Flags: ', '/', '<br>' ) ?>
        <?php } ?>
        <?php // print_r( get_post_meta( $post_id, 'nn_error', false ) ); ?>
        <!--
        site info: <?php // print_r( $nn_site ); ?>
        articles 2019-05: <?php // print_r( $articles_1905 ); ?>
        -->
        </pre>
	</details>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
