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

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>

			<nav class="tax-dropdown content-col">
				<h2 class="widget-title"><?php esc_html_e( 'Owners', 'newsstats' ); ?></h2>
				<ul style="margin-left: 0.5rem;">
				<?php
					wp_list_categories( array(
						'taxonomy'   => 'owner',
						'show_count' => 1,
						'title_li'   => '',
						'number'     => 300,
						'orderby'    => 'name',
						'order'      => 'ASC',
					) );
				?>
				</ul>
			</nav>

			<nav class="tax-dropdown">
				<h2 class="widget-title"><?php esc_html_e( 'References', 'newsstats' ); ?></h2>
				<ul style="margin-left: 0.5rem;">
				<?php wp_list_pages( array( 'title_li'   => '' ) ); ?>
				</ul>

                <h2 class="widget-title"><?php esc_html_e( 'Recent Posts', 'newsstats' ); ?></h2>
                <ul style="margin-left: 0.5rem;">
                <?php
                    $recent_posts = wp_get_recent_posts( array( 'post_status' => 'publish' ) );
                    foreach( $recent_posts as $recent ){
                        echo '<li><a href="' . get_permalink($recent["ID"]) . '">' .   $recent["post_title"].'</a> </li> ';
                    }
                    wp_reset_query();
                ?>
                </ul>

				<h2 class="widget-title"><?php esc_html_e( 'CMS', 'newsstats' ); ?></h2>
				<ul style="margin-left: 0.5rem;">
				<?php
					wp_list_categories( array(
						'taxonomy'   => 'cms',
						'show_count' => 1,
						'title_li'   => '',
						'number'     => 200,
						'orderby'    => 'name',
						'order'      => 'ASC',
					) );
				?>
				</ul>

				<h2 class="widget-title"><?php esc_html_e( 'States', 'newsstats' ); ?></h2>
				<ul style="margin-left: 0.5rem;">
				<?php
				wp_list_categories( array(
				    'taxonomy'   => 'region', // cms|owner|region
				    'child_of'   => 0,
				    'orderby'    => 'name', // name|count(|slug|ID)
                    'order'      => 'ASC', // ASC|DESC
				    'show_count' => 1,
				    'pad_counts' => 1,
				    'title_li'   => '', // <a href="/">CT</a>
				    'number'     => 4000, // 200|300|4000
				    'depth'      => 1, // 1-states, 2-counties, 3-cities
				) );
				?>
				</ul>
			</nav>

            <hr>

            <form class="pub-form content-col" method="post" action="<?php echo get_post_type_archive_link( 'publication' );?>">
                <h2 class="pub-form-heading">Filter publications by:</h2>
                <input type="hidden" name="action" id="action" value="find">
                <nav class="tax-dropdown pub-tax-sel">
                    <?php echo get_terms_multi_select( 'owner', array( 'orderby' => 'count', 'order' => 'DESC'), 22 ); ?>
                </nav>
                <nav class="tax-dropdown pub-tax-sel">
                    <?php echo get_terms_multi_select( 'cms', array( 'orderby' => 'count', 'order' => 'DESC'), 10 ); ?>
                    <?php echo get_terms_multi_select( 'region', array( 'parent' => 0 ), 10 ); ?>
                </nav>
                <p style="text-align: center;">
                    <button class="pub-filter-button" type="submit">Filter</button>
                    <?php if ( isset( $_POST['action'] ) ) { ?>
                    <a class="pub-all" href="/">All Publications &raquo;</a>
                    <?php } ?>
                </p>
                <?php // var_dump( $_POST['tax_input'] ) ?>
                <?php // echo $wp_query->query_vars['test']; ?>
                <?php // var_dump( $wp_query->query_vars['tax_query'] ) ?>
            </form>

        </main><!-- #main -->

	</div><!-- #primary -->

<!-- =file: page-sitemap -->
<?php // get_sidebar(); ?>
<?php get_footer(); ?>


