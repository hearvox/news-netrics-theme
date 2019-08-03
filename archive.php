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

/*
<th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
<td colspan="6" style="text-align: left;"><?php echo count( $pubs_data['score'] ) ?> articles from <?php echo $wp_query->found_posts; ?> newspapers</td>

 */

            $map_data = array();

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
            			<th scope="row"><?php esc_attr_e( 'Results from:', 'newsnetrics' ); ?></th>
            			<td colspan="6" style="text-align: left;"><?php echo $wp_query->found_posts; ?> newspapers</td>
        			</tr>
                    <?php if ( is_tax( 'owner' ) ) { ?>
                    <tr>
                        <th scope="row"><?php esc_attr_e( 'CMSs:', 'newsnetrics' ); ?></th>
                        <td colspan="6" style="text-align: left;"><?php echo esc_html( rtrim( $cms_list, ', ') ); ?></td>
                    </tr>
                <?php } ?>
    			</tfoot>
			</table>
            <div id="map" style="border: 1px solid #f6f6f6; height: 600px; width: 100%;"></div>
				<?php } ?>
			<?php } ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'archive' ); ?>

            <?php

            if ( is_tax( 'owner' ) ) {
                $post_id   = $post->ID;
                $post_meta = get_post_meta( $post_id );

                $term_owner  = get_the_terms( $post_id, 'owner' );
                $pub_owner   = ( $term_owner && isset( $term_owner[0]->name ) ) ? $term_owner[0]->name : 'ERROR:owner';

                $term_city   = get_the_terms( $post_id, 'region' );
                $city        = ( $term_city && isset( $term_city[0]->name ) ) ? $term_city[0]->name : 'ERROR:city';
                $city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
                    ? get_term_meta( $term_city[0]->term_id ) : false;
                $city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
                    ? $city_meta['nn_region_pop'][0] : 0;
                $city_latlon = ( $city_meta && isset( $city_meta['nn_region_latlon'][0] ) )
                    ? $city_meta['nn_region_latlon'][0] : '';
                $term_county = ( $term_city && isset( $term_city[0]->parent ) )
                    ? get_term( $term_city[0]->parent ) : false;
                $county      = ( $term_county && isset( $term_county->name ) ) ? $term_county->name : 'ERROR:county';
                $term_state  = ( $term_county && isset( $term_county->parent ) )
                    ? get_term( $term_county->parent ) : false;
                $state       = ( $term_state && isset( $term_state->name ) ) ? $term_state->name : 'ERROR:state';

                $map_data[] = array(
                    'pub_id'      => $post_id,
                    'pub_title'   => $post->post_title,
                    'pub_link'    => get_permalink( $post_id ),
                    'pub_domain'  => $post_meta['nn_pub_site'][0],
                    'pub_name'    => $post_meta['nn_pub_name'][0],
                    'pub_circ'    => $post_meta['nn_circ'][0],
                    'pub_url'     => $post_meta['nn_pub_url'][0],
                    'pub_rss'     => $post_meta['nn_pub_rss'][0],
                    'pub_year'    => $post_meta['nn_pub_year'][0],
                    'pub_owner'   => $pub_owner,
                    'city'        => $city,
                    'county'      => $county,
                    'state'       => $state,
                    'city_pop'    => $city_pop,
                    'city_latlon' => $city_latlon,
                );
            }
            ?>

		<?php endwhile; // End of the loop. ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

        <?php rewind_posts(); ?>
        <?php
        if ( is_tax( 'owner' ) ) { ?>

<script>
/* Load map (called by <script> callback) */
function news_map_init() {
    var map = new google.maps.Map(
        document.getElementById('map'), {
            center: new google.maps.LatLng(39.8283,-98.5795), // Geo center of the 48 States.
            zoom: 4.5,
            mapTypeId: 'terrain',
    });

    news_map_set_markers(map);
    // soundmap_set_markers(map);
}

/*
Data for the markers:
[{"pub_id":4031,"pub_title":"adn.com","pub_link":"https:\/\/news.pubmedia.us\/publication\/adn-com\/","pub_domain":"adn.com","pub_name":"Alaska Dispatch News","pub_circ":"3520","pub_url":"https:\/\/www.adn.com\/","pub_rss":"https:\/\/news.google.com\/rss\/search?hl=en-US&gl=US&ceid=US=en&num=5&q=site=adn.com","pub_year":"2014","pub_owner":"Alaska Dispatch Publishing LLC","city":"Anchorage","county":"Anchorage Municipality","state":"AK","city_pop":"253421","city_latlon":"61.1508|-149.1091"},{...}]
*/
var news_map_data = <?php echo json_encode( $map_data ); ?>;
var lists_html    = "";
var gmarkers      = [];
var map_html      = [];


/* Adds markers to the map */
function news_map_set_markers(map) {

    // Origins, anchor positions and coordinates increase in directions X right and Y down.
    // Size in of X,Y originating from top-left of image, at (0,0).
    var image = {
        url:    'https://news.pubmedia.us/wp-content/themes/newsstats/img/map/newsagent-no-pin-op85-21x22.png',
        size:   new google.maps.Size(21, 22),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(0, 22)
    };

    for (var i = 0; i < news_map_data.length; i++) {
    // for (var i = 0; i < 2; i++) {
        var data = news_map_data[i]; // Get data for Info Window

        // Info Window content
        var pub_circ = ( data['pub_circ'] ) ? parseFloat(data['pub_circ']).toLocaleString() : '';
        var city_pop = ( data['city_pop'] ) ? parseFloat(data['city_pop']).toLocaleString() : '';
        var map_html =
            '<section id="news-map-' + i + '" class="news-map-info">' +
            '<img src="https://s.wordpress.com/mshots/v1/' +
            encodeURIComponent(data['pub_url']) + '?w=200&h=150" width="200" height="150" alt="' + data['pub_name'] + '" />' +
            '<div><a href="' + data['pub_link'] + '">' + data['pub_name'] + '</a></div>' +
            '<div>' + data['pub_domain'] + '</div>' +
            '<div>Circ.: ' + pub_circ + '</div>' + // Convert str to num, add commas.
            '<div>' + data['city'] + ' ' + data['state'] + '</div>' +
            '<div>Pop.: ' + city_pop + '</div>' +
            '</section>';

        infoWindow = new google.maps.InfoWindow({ content: map_html });

        var latlon = data["city_latlon"].split('|');

        var marker = new google.maps.Marker({
            position: {lat: parseFloat(latlon[0]), lng: parseFloat(latlon[1])},
            map:      map,
            icon:     image,
            id:       i,
            title:    data['pub_domain'],
            info:     map_html,
        });


        // document.getElementById('txt').innerHTML = news_map_data;

        // Save Markers in an array.
        gmarkers.push(marker);

        // Add content to user-clicked Info Window.
        google.maps.event.addListener( marker, 'click', function() {
            infoWindow.setContent( this.info );
            infoWindow.open( map, this );
        });


    }

}

//  https://developers.google.com/maps/documentation/javascript/heatmaplayer#add_weighted_data_points
//  https://developers.google.com/maps/documentation/javascript/examples/marker-remove

</script>

<script async defer src="//maps.googleapis.com/maps/api/js?key=AIzaSyA5clgBbvCkszTpr0UjyF0cG_Hr21Kd9Pg&callback=news_map_init"></script>

        <?php } ?>

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

                $pubs_data: <?php if ( isset( $pubs_data ) ) { print_r( $pubs_data ); }; ?>

                $pubs_data_new: <?php print_r( netrics_get_pubs_query_data( $wp_query ) ); ?>
                </pre>
            </details>

		</main><!-- #main -->
	</div><!-- #primary -->

<!-- =file: archive -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
