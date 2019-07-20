<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<aside class="entry-meta">
			<?php the_date( 'Y-m-d' ) ?> | Re: <?php the_category( ', ', '> ' ) ?><?php the_tags( null,  ', ', '' ) ?>
		</aside>
	</header><!-- .entry-header -->

	<section class="entry-content">
		<?php echo the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php the_post_navigation( array( 'prev_text' => '&laquo; %title', 'next_text' => '%title &raquo;' ) ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

