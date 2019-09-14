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
	<?php
	$post_id = get_the_ID();
	$pubs_data = netrics_get_pubs_pagespeed_query( $wp_query );
	$articles_1908 = get_post_meta( $post_id, 'nn_articles_201908', true );
	$articles = get_post_meta( $post_id, 'nn_articles', true);
	echo "\narticles\n";
	print_r( $articles );
	echo "\narticles_1908\n";
	print_r( $articles_1908 );
	echo "\npubs_data\n";
	print_r( $pubs_data );


	?>
    </pre>
</details>



<?php get_sidebar(); ?>
<?php get_footer(); ?>
