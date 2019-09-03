<?php
/**
 * Page template for /news-table/
 *
 *
 * @package newsstats
 */

get_header();

?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>

        </main><!-- #main -->

<table class="tabular thead-sticky">
    <caption><h1>U.S. Daily Newspapers</h1></caption>
    <thead>
        <tr>
            <th>Post ID</th>
            <th style="text-align: left;">Domain</th>
            <th style="text-align: left;">Name</th>
            <th style="text-align: left;">City</th>
            <th style="text-align: left;">Population</th>
            <th style="text-align: left;">County</th>
            <th>State</th>
            <th style="text-align: left;">Owner</th>
            <th>Circulation</th>
            <th>Site Rank</th>
            <th>Print Yr</th>
            <th>Online</th>
            <th>CMS</th>
            <th style="text-align: left;">Site URL</th>
            <!-- <th style="text-align: left;">RSS Feed URL</th> -->
        </tr>
    </thead>
    <tbody>
<?php

$pubs_info = '';
$query     = newsstats_get_pub_posts( 3000 );
$errors    = '';

foreach ( $query->posts as $post ) {

        $post_id   = $post->ID;
        $post_meta = get_post_meta( $post_id );

        // Tax terms (and parents for Region: county, state).
        $term_owner  = get_the_terms( $post_id, 'owner' );
        $pub_owner   = ( $term_owner && isset( $term_owner[0]->name ) ) ? $term_owner[0]->name : '';
        $term_cms    = get_the_terms( $post_id, 'cms' );
        $pub_cms     = ( $term_cms && isset( $term_cms[0]->name ) ) ? $term_cms[0]->name : '';
        $term_city   = get_the_terms( $post_id, 'region' );
        $city        = ( $term_city && isset( $term_city[0]->name ) ) ? $term_city[0]->name : '';
        $city_meta   = ( $term_city && isset( $term_city[0]->term_id ) )
            ? get_term_meta( $term_city[0]->term_id ) : false;
        $city_pop    = ( $city_meta && isset( $city_meta['nn_region_pop'][0] ) )
            ? $city_meta['nn_region_pop'][0] : 0;
        $city_latlon = ( $city_meta && isset( $city_meta['nn_region_latlon'][0] ) )
            ? $city_meta['nn_region_latlon'][0] : '';
        $term_county = ( $term_city && isset( $term_city[0]->parent ) )
            ? get_term( $term_city[0]->parent ) : false;
        $county      = ( $term_county && isset( $term_county->name ) ) ? $term_county->name : '';
        $term_state  = ( $term_county && isset( $term_county->parent ) )
            ? get_term( $term_county->parent ) : false;
        $state       = ( $term_state && isset( $term_state->name ) ) ? $term_state->name : '';

        // Get site data (including Alexa and BuiltWith).
        $nn_site  = get_post_meta( $post->ID, 'nn_site', true);

        // Get Alexa data.
        // $rank  = ( isset ( $nn_site['alexa']['rank'] ) && $nn_site['alexa']['rank'] ) ?  $nn_site['alexa']['rank'] : '';
        $since = ( isset ( $nn_site['alexa']['since'] ) && $nn_site['alexa']['since'] )
            ? date_parse_from_format( 'd-M-Y', $nn_site['alexa']['since'] ) : false;
        $year  = ( $since ) ? absint( $since['year'] ) : '';

        // Table rows HTML.
        $pubs_info .= '<tr>';
        $pubs_info .= '<td>' . $post_id . '</td>';
        $pubs_info .= '<td style="text-align: left;">' . $post_meta['nn_pub_site'][0] . '</td>';
        $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $post_meta['nn_pub_name'][0] . '</td>';
        $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $city . '</td>';
        $pubs_info .= '<td>' . $city_pop . '</td>';
        $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $county . '</td>';
        $pubs_info .= '<td>' . $state . '</td>';
        $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $pub_owner . '</td>';
        $pubs_info .= '<td>' . get_post_meta( $post_id,'nn_circ', true ) . '</td>';
        $pubs_info .= '<td>' . get_post_meta( $post_id,'nn_rank', true ) . '</td>';
        $pubs_info .= '<td>' . $post_meta['nn_pub_year'][0] . '</td>';
        $pubs_info .= '<td>' . $year . '</td>';
        $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $pub_cms . '</td>';
        $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $post_meta['nn_pub_url'][0] . '</td>';
        // $pubs_info .= '<td style="text-align: left; white-space: nowrap;">' . $post_meta['nn_pub_rss'][0] . '</td>';
        $pubs_info .= '</tr>';

}

echo $pubs_info;

/*
Import into Google Sheet (not sortable):
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=2114051459
https://news.pubmedia.us/news-table/
=importhtml(P1,"table",1)

Copy for display (sortable):
https://docs.google.com/spreadsheets/d/1WPU3ILa6YAFoKwryXQWudXv_MCzCaseBL-PrjlbfnFg/edit#gid=2069546496
*/

?>
    </tbody>
</table>

	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
