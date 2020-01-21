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
                // netrics_print_pubs_avgs_table();

                if ( is_tax( 'region' ) ) {
                    $population   = get_term_meta( $wp_query->queried_object_id, 'nn_region_pop', true );
				?>
                <p><?php echo esc_html( $wp_query->queried_object->description ); ?> has a population of <output><?php echo number_format( $population ); ?></output> with <output><?php echo absint( $wp_query->found_posts ); ?></output> daily <?php echo _n( 'newspaper', 'newspapers', $wp_query->found_posts, 'newsnetrics' ) ?>.</p>
                <?php } ?>

			<?php
			// $paged = (get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			// if ( 1 == $paged ) {}  // On first page (only).
            // if ( is_tax( 'cms' ) ) {}
            // single_term_title( '', false );

            if ( is_tax() ) {
                $queried_object = get_queried_object();
                $term_pub_ids   = netrics_get_term_pub_ids( $queried_object );
                $pubs_avgs      = netrics_pubs_psi_avgs( $term_pub_ids->posts );
                netrics_print_pubs_avgs_table( $pubs_avgs );
            } else {
                netrics_print_pubs_avgs_table();
            }
            ?>

        </header><!-- .page-header -->

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'archive' ); ?>

		<?php endwhile; // End of the loop. ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

        <?php rewind_posts(); ?>

            <nav class="nav-pagination justify content-col">
                <?php echo paginate_links(); ?>
            </nav><!-- .nav-pagination -->

		</main><!-- #main -->
	</div><!-- #primary -->

    <details>
        <summary><small>(Test: data arrays)</small></summary>
        <pre>
<?php

?>
        </pre>
    </details>
<!-- =file: archive -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
