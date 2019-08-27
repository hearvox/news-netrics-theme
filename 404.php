<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package newsstats
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'We can&rsquo;t find your page.', 'newsstats' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php esc_html_e( '(Error 404.) Find a publication by search, owner, or CMS', 'newsstats' ); ?></p>

					<?php get_search_form(); ?>
					<style type="text/css">
						.tax-dropdown {
							display: inline-block;
							max-width: 36em;
							vertical-align: top;
						}
					</style>

					<nav class="tax-dropdown">
						<h2 class="widget-title"><?php esc_html_e( 'Publication Owners', 'newsstats' ); ?></h2>
						<ul style="margin-left: 0.5rem;">
						<?php
							wp_list_categories( array(
								'taxonomy'   => 'owner',
								'show_count' => 1,
								'title_li'   => '',
								'number'     => 300,
							) );
						?>
						</ul>
					</nav>

					<nav class="tax-dropdown">
						<h2 class="widget-title"><?php esc_html_e( 'Website CMSs', 'newsstats' ); ?></h2>
						<ul style="margin-left: 0.5rem;">
						<?php
							wp_list_categories( array(
								'taxonomy'   => 'cms',
								'show_count' => 1,
								'title_li'   => '',
								'number'     => 200,
							) );
						?>
						</ul>

						<h2 class="widget-title"><?php esc_html_e( 'States', 'newsstats' ); ?></h2>
						<ul style="margin-left: 0.5rem;">
						<?php
						wp_list_categories( array(
						    'taxonomy'   => 'region', // cms|owner|region
						    'child_of'   => 0,
						    'order'      => 'ASC', // ASC|DESC
						    'orderby'    => 'name', // name|count(|slug|ID)
						    'show_count' => 1,
						    'pad_counts' => 1,
						    'title_li'   => '', // <a href="/">CT</a>
						    'number'     => 4000, // 200|300|4000
						    'depth'      => 1, // 1-states, 2-counties, 3-cities
						) );
						?>
						</ul>
					</nav>

					<?php // the_widget( 'WP_Widget_Tag_Cloud' ); ?>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->
<!-- =file: 404 -->
<?php get_footer(); ?>
