<?php
/*
Template Name: Sonic IDs Map

http://hearingvoices.com/sonic-ids-test/
*/

get_header();

$pub_data = newsstats_get_all_publications();

// NN: AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc
// HV: AIzaSyA5clgBbvCkszTpr0UjyF0cG_Hr21Kd9Pg
// Gl: AIzaSyCkUOdZ5y7hMm0yrcCQoCvLwzdM6M8s5qk
?>

    <div id="primary" class="content-area">

<div id="map" style="border: 1px solid #f6f6f6; height: 1000px; width: 100%;"></div>


<!-- script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc&callback=initMap" async defer></script -->

<script>
/* Load map (called by <script> callback) */
function news_map_init() {
    var map = new google.maps.Map(
        document.getElementById('map'), {
            center: new google.maps.LatLng(39.8283,-98.5795), // Geo center of the 48 States.
            zoom: 5,
            mapTypeId: 'terrain',
    });

    news_map_set_markers(map);
    // soundmap_set_markers(map);
}

/*
Data for the markers:
[{"pub_id":4031,"pub_title":"adn.com","pub_link":"https:\/\/news.pubmedia.us\/publication\/adn-com\/","pub_domain":"adn.com","pub_name":"Alaska Dispatch News","pub_circ":"3520","pub_url":"https:\/\/www.adn.com\/","pub_rss":"https:\/\/news.google.com\/rss\/search?hl=en-US&gl=US&ceid=US=en&num=5&q=site=adn.com","pub_year":"2014","pub_owner":"Alaska Dispatch Publishing LLC","city":"Anchorage","county":"Anchorage Municipality","state":"AK","city_pop":"253421","city_latlon":"61.1508|-149.1091"},{...}]
*/
var news_map_data = <?php echo json_encode( $pub_data ); ?>;
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
        <article id="content" class="content">
            <h1>Map: U.S. Daily Newspapers</h1>
            <p><a href="https://mapicons.mapsmarker.com/markers/stores/newsagent/">Newspaper map marker</a> courtesy <a href="https://mapicons.mapsmarker.com">Maps Icons Collection</a>.</p>
        </article><!-- .content -->

        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/content', 'page' ); ?>

            <?php endwhile; // End of the loop. ?>
        </main><!-- #main -->
    </div><!-- #primary -->

<!-- =file: page-news-map -->
<?php get_footer(); ?>
