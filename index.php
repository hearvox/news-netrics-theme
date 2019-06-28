<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<header>
				<h1 class="page-title">U.S. Daily Newspapers <span class="found-count">(<?php echo $wp_query->found_posts ?>)</span></h1>
			</header>

			<?php
			// On first page (only).
			$paged = (get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			// Output HTML tables of site-wide Pagespeed averages:.
			if ( 1 == $paged ) {
				global $wp_query;
				$pubs_data = newsstats_get_pubs_pagespeed();
				?>
			<table class="tabular">
				<caption>U.S. daily newspapers: Averages of Google Pagespeed results (2019-05)</caption>
				<?php  netrics_pagespeed_mean( $pubs_data ); ?>
				<tfoot>
        			<tr>
            			<th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
            			<td colspan="6" style="text-align: left;">3,070 articles from 1,126 newspapers</td>
        			</tr>
    			</tfoot>
			</table>
			<?php } ?>

			<?php if ( have_posts() ) : ?>

				<?php if ( is_home() && ! is_front_page() ) : ?>
				<?php endif; ?>

			<nav class="nav-pagination justify">
				<?php echo paginate_links(); ?>
			</nav><!-- .nav-pagination -->
			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', 'archive' );
				?>

			<?php endwhile; ?>

		<nav class="nav-pagination justify">
			<?php echo paginate_links(); ?>
		</nav><!-- .nav-pagination -->

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<!-- =file: index -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
