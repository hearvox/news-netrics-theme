<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();
?>

<style type="text/css">
    table {
         font-size: 0.8rem;
         width: 95%;
    }

    td { padding: 0.2rem 0.5rem; }

    .text-right { text-align: right; }

</style>

	<div id="primary" class="content-area">


<?php



/*
Mean    0.19
Maximum     0.99
Minimum     0.01
Range   0.98
Quartile 1  0.10
Q2/Median   0.18
Quartile 3  0.22
Interquartile Range     0.12
Standard Deviation  0.15
SD Population


$speed     = $pubs_data['speed'];
$tti       = $pubs_data['tti'];
$size      = $pubs_data['size'];
$requests  = $pubs_data['requests'];
$dom       = $pubs_data['dom'];

$pgspeed['score'] * 100;
'<td>' . number_format( nstats_mean( $pgspeed['score'] ) * 100, 1, '.', ',' ) . '</td>';
'<td>' . number_format( nstats_mean( $array ) * 100, 1, '.', ',' ) . '</td>';



number_format( nstats_mean( $pubs_data['score'] ) * 100, 1, '.', ',' );

round( $pgspeed['speed'] / 1000, 1 );
round( $pgspeed['tti']  / 1000, 1 );
size_format( $pgspeed['size'] );
number_format( $pgspeed['dom'] );
number_format( $pgspeed['requests'] );
*/

?>





<?php


?>



<pre>


</pre>
<textarea>


</textarea>

<?php



?>

        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
