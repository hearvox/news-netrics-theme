<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package newsstats
 */

?>

	</div><!-- #content -->
    <hr>
	<footer id="colophon" class="site-footer" role="contentinfo">
        <nav class="footer-nav">
            <form id="category-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" style="display: inline-block; width: 450px;">
                <?php wp_dropdown_categories( 'show_count=1&taxonomy=owner&name=owner&value_field=slug&orderby=name&show_option_all=Owners:' ); ?><br>
                <input type="submit" name="submit" value="View Owner" />
            </form>
            <form id="category-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" style="display: inline-block; width: 200px;">
                <?php wp_dropdown_categories( 'show_count=1&taxonomy=cms&name=cms&value_field=slug&orderby=name&show_option_all=CMS:' ); ?>
                <input type="submit" name="submit" value="View CMS" />
            </form>
        </nav>
		<div class="site-info">
            <p><?php get_search_form(); ?></p>
			<p>&copy;<?php echo date( 'Y' ); ?> <a href="https://www.rjionline.org/">Reynolds Journalism Institute</a> and <a href="https://journalism.missouri.edu/">Missouri School of Journalism</a></p>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
