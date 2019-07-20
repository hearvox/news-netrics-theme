<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package newsstats
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clear' ); ?> style="border-top: 1px solid #c9cdda;">
	<header class="entry-header">
		<span class="alignright"><?php edit_post_link( __( 'edit', 'textdomain' ), ' <em>[', ']</em>' ); ?></span>
		<?php the_title( sprintf( '<h2 class="entry-title" style="clear: none;"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?><br>
	</header><!-- .entry-header -->
	<?php the_excerpt() ?>
</article><!-- #post-## -->
<!-- =file: content-archive-post -->
