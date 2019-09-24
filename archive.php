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

                if ( is_tax( 'region' ) ) {
                    $population   = get_term_meta( $wp_query->queried_object_id, 'nn_region_pop', true );
				?>
                <p><?php echo esc_html( $wp_query->queried_object->description ); ?> has a population of <output><?php echo number_format( $population ); ?></output> with <output><?php echo absint( $wp_query->found_posts ); ?></output> daily <?php echo _n( 'newspaper', 'newspapers', $wp_query->found_posts, 'newsnetrics' ) ?>.</p>
                <?php } ?>

			</header><!-- .page-header -->

			<?php
            $map_data = array();

			// On first page (only).
			$paged = (get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			// Output HTML tables of site-wide Pagespeed averages:.
			if ( 1 == $paged ) {
				// global $wp_query;
				$pubs_data = netrics_get_pubs_query_data();
				if ( $pubs_data ) {
			?>
			<table class="tabular">
				<caption><?php the_archive_title(); ?> daily newspapers: Averages of Google Pagespeed results (2019-08)</caption>
				<?php echo netrics_pagespeed( $pubs_data ); ?>
				<tfoot>
        			<tr>
            			<th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
            			<td colspan="6" style="text-align: left;">3,073 articles from <?php echo $wp_query->found_posts; ?> newspapers</td>
        			</tr>
    			</tfoot>
			</table>
                    <?php if ( is_tax( 'owner' ) ) { ?>
            <div id="map" style="border: 1px solid #f6f6f6; height: 600px; width: 100%;"></div>
                    <?php } ?>
				<?php } ?>
			<?php } ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'archive' ); ?>

		<?php endwhile; // End of the loop. ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

        <?php rewind_posts(); ?>

            <nav class="nav-pagination justify">
                <?php echo paginate_links(); ?>
            </nav><!-- .nav-pagination -->

		</main><!-- #main -->
	</div><!-- #primary -->

    <details>
        <summary><small>(Test: data arrays)</small></summary>
        <pre>
        <?php
        // var_dump( $cms_arr );
        ?>
        </pre>
    </details>
<!-- =file: archive -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
