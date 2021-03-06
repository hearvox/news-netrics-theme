<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', ' <span class="found-count">(' . $wp_query->found_posts . ')</span></h1>' );
				// the_archive_description( '<div class="taxonomy-description">', '</div>' )
				?>
			</header><!-- .page-header -->

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'archive-post' ); ?>

		<?php endwhile; // End of the loop. ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

            <nav class="nav-pagination justify">
                <?php echo paginate_links(); ?>
            </nav><!-- .nav-pagination -->

		</main><!-- #main -->
	</div><!-- #primary -->

<!-- =file: category -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
