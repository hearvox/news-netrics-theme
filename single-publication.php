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
    echo "\nnn_psi_avgs: ";
    print_r( get_post_meta( $post_id,'nn_psi_avgs', true ) );

    echo "\nnetrics_psi: ";
    print_r( get_transient( 'netrics_psi' ) );

    echo "\nnn_articles: ";
    print_r( get_post_meta( $post_id,'nn_articles', true ) );

	?>
    </pre>
</details>



<?php get_sidebar(); ?>
<?php get_footer(); ?>
