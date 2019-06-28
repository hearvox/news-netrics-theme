<?php
/*
Template Name: Sonic IDs Map

http://hearingvoices.com/sonic-ids-test/
*/

get_header();

$pub_data = newsstats_get_all_publications()

?>

<style>
    /* Always set the map height explicitly to define the size of the div
    * element that contains the map. */
    #map {
        height: 100%;
    }
    /* Optional: Makes the sample page fill the window. */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
</style>




<!--
// NN: AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc
// HV: AIzaSyA5clgBbvCkszTpr0UjyF0cG_Hr21Kd9Pg
// Gl: AIzaSyCkUOdZ5y7hMm0yrcCQoCvLwzdM6M8s5qk
-->

<div id="map" style="border: 1px solid red; height: 1000px; width: 100%;"></div>

<aside id="lists" style="font-size: 0.85em;"></aside>

<!-- script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCf1_AynFKX8-A4Xh1geGFZwq1kgUYAtZc&callback=initMap" async defer></script -->

<script>

/*
var map;
function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: -34.397, lng: 150.644},
    zoom: 8
  });
}

var news_map_data = <?php echo json_encode( $pub_data ); ?>;
;

*/

/* Load map (called by <script> callback) */
function news_map_init() {
    var map = new google.maps.Map(
        document.getElementById('map'), {
            center: new google.maps.LatLng(39.8283,-98.5795), // Geo center of the 48 States.
            zoom: 5,
            mapTypeId: 'terrain'
    });


    soundmap_set_markers(map);
}

/* Data for the markers: [file,lat,lon,title,person,city,state,producer,length] */
var news_map_data = [
    ["Yellowstone-McLatchy", 45.0318802, -110.7057663, "A Yellowstone Youth", "Pat McLatchy", "Gardiner", "MT", "Clay Scott", "1:24"],
    ["Cowboy-BurtWithrow", 45.2793709, -113.1206190, "Alpine Cowboy", "Burt Withrow", "Pioneer Mountains", "MT", "Barrett Golding", "0:45"],
    ["AppleCider-RockyCreekFarm", 45.6602950, -110.9448550, "Apple Cider", "Ryan Mitchell", "Bozeman", "MT", "Larissa Vaienti", "1:04"],
    ["Avalanche-Chabot", 45.6782927, -111.0371001, "Avalanche Center", "Doug Chabot", "Bozeman", "MT", "Larissa Vaienti", "1:20"],
    ["BisonRanch-KroonBros", 45.8575000, -111.3311111, "Bison Ranch", "Kroon Brothers", "Manhattan", "MT", "Barrett Golding", "0:56"],
    ["Bonneville-Burkland", 47.5080600, -111.2951970, "Bonnevile Salt Flats", "Betty Burkland", "Great Falls", "MT", "James Kehoe", "1:19"],
    ["RodeoDougDavis-MilesCityBuckingHorse", 46.4083362, -105.8405582, "Bucking Horse Sale Rodeo Rider", "Doug Davis", "Miles City", "MT", "Barrett Golding", "1:19"],
    ["Buddhist-Voight", 45.7832856, -108.5006904, "Buddhist Prayer", "Mary Voight", "Billings", "MT", "Barrett Golding", "1:36"],
    ["WellknownBuffaloVtr-CrowRez", 45.5266730, -107.4167493, "Care of Children", "Peggy White Wellknown Buffalo", "Garryowen", "MT", "Clay Scott", "1:29"],
    ["Artist-DGHouse", 45.669036, -111.1907126, "Cherokee Artist", "D.G. House", "Bozeman", "MT", "Barrett Golding", "1:03"],
    ["Xmas-EmersonSchool", 45.677716, -111.040673, "Christmas Kids", "2nd Graders, Emerson School", "Bozeman", "MT", "Barrett Golding", "1:31"],
    ["CigarBoxGit-Eyre", 47.7457730, -114.2303810, "Cigar Box Guitar", "Nathan Eyre", "Polson", "MT", "Barrett Golding", "1:25"],
    ["Ceramic-DeWeese", 45.6677778, -111.1825000, "Clay Objects", "Josh De Weese", "Four Corners", "MT", "Barrett Golding", "1:10"],
    ["Colstrip-SeniorCenter", 45.8922222, -106.6288889, "Coal Town", "Betty Lou Hancock", "Colstrip", "MT", "Barrett Golding", "1:13"],
    ["Coyote-Greg-Keeler", 45.6668940, -111.0511416, "Coyote Tales", "Greg Keeler", "Montana State University", "MT", "Larissa Vaienti", "1:15"],
    ["CrowFair", 45.6013889, -107.4597222, "Crow Fair", "Gathering the Tribes", "Crow Agency", "MT", "Steve Rathe", "1:03"],
    ["DogOwners-PeetsHill", 45.6738998, -111.0293315, "Dog Owners", "Peets Hill", "Bozeman", "MT", "Larissa Vaienti", "1:05"],
    ["ElkHunt-Strung", 45.8888159, -111.9233199, "Elk Hunting", "Norman Strung", "Cottonwood Canyon", "MT", "Barrett Golding", "1:29"],
    ["EndTimes-Eslick", 46.5958056, -112.0270306, "End Times", "Bishop David Eslick", "Helena", "MT", "Barrett Golding", "1:01"],
    ["Geysers-Hutchinson", 44.9764870, -110.6998729, "Geyser Geologist", "Rick Hutchinson", "Mammoth", "WY", "Barrett Golding", "1:30"],
    ["Geysers-YNP", 44.4602197, -110.8284903, "Geysers Gazers", "Yellowstone National Park", "Old Faithful", "WY", "Barrett Golding", "0:58"],
    ["GhostTownSound-StevePowell", 47.5080600, -111.2951970, "Ghost Town Sound", "Steve Powell", "Great Falls", "MT", "Larissa Vaienti", "1:15"],
    ["Goats-Brown", 45.7700795, -111.1651524, "Goat Milk", "Melvin Brown", "Belgrade", "MT", "Larissa Vaienti", "1:00"],
    ["HalfCircleRanch-Armstrong", 45.8385220, -111.0765703, "Half Circle Ranch", "Harry Armstrong", "Belgrade", "MT", "Barrett Golding", "0:59"],
    ["Hutterite-Hofer", 48.6065395, -108.9462246, "Hutterite Pastor", "Eli Hofer", "Blaine", "MT", "Clay Scott", "1:00"],
    ["InterMtnOpera", 45.6791409, -111.0355359, "Intermountain Opera", "Rehearsal", "Bozeman", "MT", "Larissa Vaienti", "1:24"],
    ["Bass-Roberti", 45.7786111, -111.1788889, "Jazz Bass", "Kelly Roberti", "Bozeman", "MT", "Barrett Golding", "1:21"],
    ["SlideGuitar-Dubuque", 47.6932004, -114.1631275, "Lap Steel Guitar", "Dan Dubuque", "Polson", "MT", "Clay Scott", "1:21"],
    ["GuitarClass-Parkening", 45.6663877, -111.0527407, "Master Class", "Christopher Parkening", "Bozeman", "MT", "Barrett Golding", "1:29"],
    ["Mushroom-Peacock", 45.3686140, -110.7349943, "Mushroom Picking", "Doug Peacock", "Emigrant", "MT", "Scott Carrier", "1:20"],
    ["NaturalRadio-McGreevy", 48.7596128, -113.7870225, "Natural Radio Recordist", "Steve McGreevy", "Glacier National Park", "MT", "Barrett Golding", "1:30"],
    ["OlympicSkaters-ButteHiAltSportsCtr", 46.0038232, -112.5347776, "Olympic Skaters", "High Altitude Sports Center", "Butte", "MT", "Barrett Golding", "1:38"],
    ["Church-Anderson", 46.1030055, -109.9537970, "Prairie Church", "Lucille Anderson", "Melville", "MT", "Barrett Golding", "1:31"],
    ["RainbowFamily-BarryAdams", 45.6582587, -113.3830278, "Rainbow Family", "Barry 'Plunker' Adams", "Beaverhead Forest", "MT", "Barrett Golding", "1:16"],
    ["RanchAnimals-RoxanneLinderman", 45.7762463, -111.1770945, "Ranch Animals", "Roxanne Linderman", "Belgrade", "MT", "Larissa Vaienti", "1:09"],
    ["Ranch-Hoiland", 45.6627139, -110.1160228, "Ranch Dog", "John Hoiland & Nippy", "McLeod", "MT", "Barrett Golding", "1:00"],
    ["Reenactors-Cottingham", 47.8183031, -110.6674365, "Reenactors", "Jed Cottingham", "Fort Benton", "MT", "Barrett Golding", "1:22"],
    ["Warrior-Fox", 48.4824967, -108.7654351, "Returning Warrior", "Jamie Fox", "Fort Belknap Agency", "MT", "Clay Scott", "1:21"],
    ["Rolling-Connelly", 45.6834599, -111.0504990, "Rolling Exhibition", "Kevin Connelly", "Bozeman", "MT", "Barrett Golding", "1:31"],
    ["RookieLeague-Brewers", 46.5995004, -112.0273167, "Rookie League", "Helena Brewers", "Helena", "MT", "Barrett Golding", "1:24"],
    ["Smokejumpers-Cotrell", 46.9282639, -114.0944565, "Smokejumpers", "Dan Cotrell", "Missoula", "MT", "Barrett Golding", "1:21"],
    ["Softball-Splash", 47.6878211, -114.1742621, "Softball Tournament", "Splash Classic", "Polson", "MT", "Barrett Golding", "1:09"],
    ["SonambientSculpture-CeliaBertoia", 45.6834599, -111.0504990, "Sonambient Sculpture", "Celia Bertoia", "Bozeman", "MT", "Larissa Vaienti", "1:36"],
    ["SoundScupltures-Zentz", 45.6691159, -108.7715328, "Sound Scupltures", "Pat Zentz", "Laurel", "MT", "Barrett Golding", "1:14"],
    ["Sundance-Fraser", 45.638852, -110.974449, "Sundance", "Scott Fraser", "Bear Canyon", "MT", "Barrett Golding", "1:27"],
    ["MT-Taiko-Dulin", 45.6837720, -111.0778390, "Taiko Drums", "Melissa Dulin", "Bozeman", "MT", "Larissa Vaienti", "1:21"],
    ["LochsaForest-Moore", 46.5107008, -114.7178851, "The Lochsa", "Bud Moore", "Lolo", "MT", "Barrett Golding", "1:13"],
    ["Yodel-Reedy", 46.0761240, -111.8974074, "The Yodeler", "Brigid Reedy", "Boulder River", "MT", "Clay Scott", "1:30"],
    ["ToungeRiverStories-Scanlon", 45.4195463, -106.4923850, "Tongue River Stories", "Martha Scanlan", "Birney", "MT", "Clay Scott", "1:22"],
    ["WampumBelt-WindyBoy", 48.2741873, -109.7815489, "Wampum Belt", "Jonathan Windy Boy", "Rocky Boy Reservation", "MT", "Barrett Golding", "1:17"],
    ["Wolves-West", 44.662149, -111.1041092, "Wolves", "Grizzly & Wolf Discovery Center", "West Yellowstone", "MT", "Barrett Golding", "1:31"],
    ["WoundedVets-Forseth", 45.8343543, -109.9540644, "Wounded Vets/Project Healing Waters", "Lt Eivind Forseth", "Big Timber", "MT", "Barrett Golding", "1:07"],
    ["Writing-Harrison", 45.3799612, -110.6819335, "Writing", "Jim Harrison", "Pray", "MT", "Scott Carrier", "1:07"],
    ["Hutterites-Girls", 48.5316667, -108.7844444, "North Harlem Colony", "Young Hutterite Women", "Harlem", "MT", "Clay Scott", "1:29"]
];

var lists_html = "";
var gmarkers   = [];
var htmls      = [];

/* Adds markers to the map */
function soundmap_set_markers(map) {
    // Origins, anchor positions and coordinates increase in directions X right and Y down.
    // Size in of X,Y originating from top-left of image, at (0,0).
    var image = {
        url:    'https://hearingvoices.com/news/wp-content/themes/js/map/img/map-marker-play-arrow-red.png',
        size:   new google.maps.Size(21, 22),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(0, 22)
    };

    for (var i = 0; i < news_map_data.length; i++) {
        var data = news_map_data[i]; // Get data for Info Window

        // Info Window content
        var html =
            '<section id="sonic-' + i + '" class="soundmap-info" style="background-color: #cecbba; padding: 0.2em;">' +
            '<img src="https://hearingvoices.com/news/wp-content/uploads/sonics/img/KGLT-ID_' +
            data[0] + '.jpg" width="200" height="150" alt="' + data[3] + '" />' +
            '<div>' + data[3] + '</div><div>' + data[5] + ' ' + data[6] + '</div>' +
            '<div><em>by ' + data[7] + '</em> <small>(' + data[8] + ')</small></div>' +
            '</section>';

        infoWindow = new google.maps.InfoWindow({ content: html });

        var marker = new google.maps.Marker({
            position: {lat: data[1], lng: data[2]},
            map:      map,
            icon:     image,
            id:       i,
            title:    data[0],
            info:     html,
        });

        // Save Markers in an array.
        gmarkers.push(marker);

        // Add content to user-clicked Info Window.
        google.maps.event.addListener( marker, 'click', function() {
            infoWindow.setContent( this.info );
            infoWindow.open( map, this );

            var audio_el = document.getElementById('audio-' + this.id);
            // audio_el.play();
        });

    }
}

// Build HTML lists of data items.
for (var i = 0; i < news_map_data.length; i++) {
    var data = news_map_data[i];
    lists_html +=
    '<li><a href="#sonic-' + i +'" onclick="soundmap_open_infowin(' + i + ')" class="map-link">' + data[3] + '<\/a></li>';
}
document.getElementById('lists').innerHTML = '<h3>KGLT Sonics IDs</h3><ol>' + lists_html + '</ol>';

// Open Info Window on HTML link click.
function soundmap_open_infowin(id){
    google.maps.event.trigger(gmarkers[id], 'click');

    // Autoplay audio.
    var audio_el = document.getElementById('audio-' + id);
    // audio_el.play();
}






/* Adds markers to the map */
function news_map_set_markers(map) {

    // Origins, anchor positions and coordinates increase in directions X right and Y down.
    // Size in of X,Y originating from top-left of image, at (0,0).
    var image = {
        url:    'https://hearingvoices.com/news/wp-content/themes/js/map/img/map-marker-play-arrow-red.png',
        size:   new google.maps.Size(21, 22),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(0, 22)
    };




    for (var i = 0; i < news_map_data.length; i++) {
    // for (var i = 0; i < 2; i++) {
        var data = news_map_data[i]; // Get data for Info Window

        // Info Window content
        var map_htmlXXX =
            '<section id="news-map-' + i + '" class="news-map-info">' +
            '<div>' + data['pub_name'] + '</div><div>' + data['city'] + ' ' + data['state'] + '</div>' +
            '</section>';

                // Info Window content
        var html =
            '<section id="sonic-' + i + '" class="soundmap-info" style="background-color: #cecbba; padding: 0.2em;">' +
            '<img src="https://hearingvoices.com/news/wp-content/uploads/sonics/img/KGLT-ID_' +
            data[0] + '.jpg" width="200" height="150" alt="' + data[3] + '" />' +
            '<div>' + data[3] + '</div><div>' + data[5] + ' ' + data[6] + '</div>' +
            '<div><em>by ' + data[7] + '</em> <small>(' + data[8] + ')</small></div>' +
            '</section>';

        infoWindow = new google.maps.InfoWindow({ content: html });

        var latlon = data["city_latlon"].split('|');

        var marker = new google.maps.Marker({
            // position: {lat: parseFloat(latlon[0]), lng: parseFloat(latlon[1])},
            position: {lat: data[1], lng: data[2]},
            map:      map,
            icon:     image,
            id:       i,
            // title:    data['pub_domain'],
            title:    data[0],
            info:     html,
        });

        console.log(marker);
        document.getElementById('txt').innerHTML = marker;

        // Save Markers in an array.
        gmarkers.push(marker);

        // Add content to user-clicked Info Window.
        google.maps.event.addListener( marker, 'click', function() {
            infoWindow.setContent( this.info );
            infoWindow.open( map, this );
        });


    }

}



</script>
<script async defer src="//maps.googleapis.com/maps/api/js?key=AIzaSyA5clgBbvCkszTpr0UjyF0cG_Hr21Kd9Pg&callback=news_map_init"></script>
        <article id="content" class="content">

        </article><!-- .content -->

<!-- =file: page-news-map -->
<?php get_footer(); ?>
