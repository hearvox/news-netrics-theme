<?php
/**
 * Newspapers site homepages
 *
 *
 * @package newsstats
 */

get_header();

$args = array(
    'post_type' => 'publication',
    'orderby'   => 'title',
    'order'     => 'ASC',
    'nopaging'  => true,
    'posts_per_page' => 2000,
);
$query = new WP_Query( $args );
?>
<style type="text/css">
.content-area .alignnone {
    height: 225px;
    width: 300px;
    margin: 0;
    padding: 0;
}
</style>
    <div id="primary" class="content-area" style="background-color: #efefef;">
        <main id="main" class="site-main" role="main">

        </main><!-- #main -->

        <?php foreach ( $query->posts as $post ) {     ?>
            <a href="<?php the_permalink() ?>"><img class="alignnone" src="https://s.wordpress.com/mshots/v1/<?php echo urlencode( get_post_meta( $post->ID, 'nn_pub_url', true ) ); ?>?w=300&h=225" width="300" height="225" alt="<?php the_title(); ?>" /></a>
        <?php } ?>

    </div><!-- #primary -->

<!-- =file: page-front-pages -->
<?php get_footer(); ?>
