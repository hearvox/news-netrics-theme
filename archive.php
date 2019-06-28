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
					// the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
            // Count CMSs on Owner archives.
            if ( is_tax( 'owner' ) ) {
                global $wp_query;
                $post_ids = wp_list_pluck( $wp_query->posts, 'ID' ); // IDs of Owner posts.
                $cms_arr  = array(); // Array for CMS count.
                $owner_id = get_queried_object()->term_id; // Owner term.
                $terms    = get_terms( array( 'taxonomy'   => 'cms', 'object_ids' => $post_ids ) );

                // Get post counts for this Owner for each CMS.
                foreach ( $terms as $term ) {
                    $args_tax = array(
                        'post_type'      => 'publication',
                        'posts_per_page' => 500,
                        // 'no_found_rows' => true,
                        'update_post_meta_cache' => false,
                        'update_post_term_cache' => false,
                        'fields' => 'ids',
                        'tax_query' => array(
                            'relation'  => 'AND',
                            array(
                                'taxonomy' => 'owner',
                                'field'    => 'term_id',
                                'terms'    => $owner_id, // Limit to Owner posts.
                            ),
                            array(
                                'taxonomy' => 'cms',
                                'field'    => 'term_id',
                                'terms'    => $term->term_id,
                            ),
                        ),
                    );
                    $query_tax = new WP_Query( $args_tax );

                    // Array of CMSs and post counts (for this Owner).
                    $cms_arr[ $term->name ] = absint( $query_tax->found_posts );
                    arsort( $cms_arr ); // Sort by value descending.

                }

                $cms_list = '';
                foreach ($cms_arr as $cms => $count ) {
                    $cms_list .= "$cms ($count), ";
                }
            }


			// On first page (only).
			$paged = (get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			// Output HTML tables of site-wide Pagespeed averages:.
			if ( 1 == $paged ) {
				global $wp_query;
				$pubs_data = netrics_get_pubs_pagespeed_query( $wp_query );
				if ( $pubs_data ) {
			?>
			<table class="tabular">
				<caption><?php the_archive_title(); ?> daily newspapers: Averages of Google Pagespeed results (2019-05)</caption>
				<?php echo netrics_pagespeed_mean( $pubs_data ); ?>
				<tfoot>
        			<tr>
            			<th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
            			<td colspan="6" style="text-align: left;"><?php echo count( $pubs_data['score'] ) ?> articles from <?php echo $wp_query->found_posts; ?> newspapers</td>
        			</tr>
                    <?php if ( is_tax( 'owner' ) ) { ?>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'CMSs:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;"><?php echo esc_html( rtrim( $cms_list, ', ') ); ?></td>
                    </tr>
                <?php } ?>
    			</tfoot>
			</table>
				<?php } ?>
			<?php } ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'archive' ); ?>

		<?php endwhile; // End of the loop. ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

            <nav class="nav-pagination justify">
                <?php echo paginate_links(); ?>
            </nav><!-- .nav-pagination -->

            <details>
            	<summary>(Test: data arrays)</summary>
                <pre>
                <?php

                // var_dump( $cms_arr );
                ?>

                 $queried_object: <?php
                 $queried_object = get_queried_object();
                 var_dump( $queried_object );
                 ?>

                $pubs_data: <?php // if ( isset( $pubs_data ) ) { print_r( $pubs_data ); }; ?>
                </pre>
            </details>

		</main><!-- #main -->
	</div><!-- #primary -->

<!-- =file: archive -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
