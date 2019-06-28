<?php
/**
 * Test code
 *
 *
 * @package newsstats
 */

get_header();

$pub_data = newsstats_get_all_publications();
$html    = '';
$i       = 1;

$array = print_r( $pub_data, true );
// echo $array;
// var_dump( $pub_data, true );

$html .= "<table><thead><tr>\n";
$html .= "<td></td><th>ID</th><th>domain</th><th>publication</th><th>circ</th><th>year</th><th>owner</th>";
$html .= "<th>city</th><th>pop</th><th>lat|lon</th><th>county</th><th>state</th></tr>\n";
$html .= "</thead><tbody><tr>\n";

foreach ( $pub_data as $pub ) {
    $pub_circ  = ( $pub['pub_circ'] ) ? number_format( $pub['pub_circ'] ) : '';
    $city_pop  = ( $pub['city_pop'] ) ? number_format( $pub['city_pop'] ) : '';

    $html .= "<tr><td class=\"text-right\">$i</td>\n";
    $html .= "<td class=\"text-right\">{$pub['pub_id']}</td>\n";
    $html .= "<td><a href=\"{$pub['pub_link']}\">{$pub['pub_title']}</a></td>\n";
    $html .= "<td>{$pub['pub_name']}</td>\n";
    $html .= "<td class=\"text-right\">$pub_circ</td>\n";
    $html .= "<td class=\"text-right\">{$pub['pub_year']}</td>\n";
    $html .= "<td>{$pub['pub_owner']}</td>\n";
    $html .= "<td>{$pub['city']}</td>\n";
    $html .= "<td class=\"text-right\">$city_pop</td>\n";
    $html .= "<td class=\"text-right\">{$pub['city_latlon']}</td>\n";
    $html .= "<td>{$pub['county']}</td>\n";
    $html .= "<td>{$pub['state']}</td></tr>\n";

    $i++;
}
$html .= "</tbody></table>";

?>
<style type="text/css">
    table {
         font-size: 0.8rem;
         width: 95%;
    }

    td { padding: 0.2rem 0.5rem; }

    .text-right { text-align: right; }

</style>

<?php echo $html; ?>

	<div id="primary" class="content-area">





		<main id="main" class="site-main" role="main">



<?php
// XML data
$url = 'https:/hearingvoices.com/';

// $xmldata = simplexml_load_file( $url ) or die("Failed to load");
// $data = get_remote_html( $url );

function get_remote_html( $url ) {

        // Get remote HTML file
        $response =  wp_remote_get( $url, array(
            'sslverify' => false,
            'timeout'   => 60,
        ));

        // Check for error
        if ( is_wp_error( $response ) ) {
            return;
        }

        // Parse remote HTML file
        $data = wp_remote_retrieve_body( $response );

            // Check for error
            if ( is_wp_error( $data ) ) {
                return;
            }


    return $response;

}

/*
libxml_use_internal_errors(true);
$xml = simplexml_load_string( wp_remote_retrieve_body( $response ) );
if ($xml === false) {
    echo "Failed loading XML: ";
    foreach(libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
} else {
    print_r( $xml );
}
*/


?>


			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>




			<?php endwhile; // End of the loop. ?>


<pre>
</pre>
<textarea>
 <?php // print_r( $data ); ?>

</textarea>


<?php
/*
$args = array (
    'orderby'        => 'title',
    'order'          => 'ASC',
    'posts_per_page' => 50,
    'category_name'  => 'daily',
    'fields'         => 'ids',
);
$post_ids = new WP_Query( $args );

foreach ( $post_ids->posts as $post_id ) {
        $domain = get_the_title( $post_id );
        $url    = 'http://' . $domain;
        $rss    = detect_feed( $url );

        if ( $rss ) {
            $rss = str_replace( '&s=start_time&sd=desc&k%5B%5D=%23topstory', '', $rss); // BLOX feeds
            echo "<li>$domain feed: <a href=\"$rss\">$rss</a></li>";
        } else {
            echo "<li><a href=\"$url\">$domain</a></li>";
        }
}
*/
?>



<?php

/*
$request = wp_remote_get( 'https://pippinsplugins.com/edd-api/products' );

if( is_wp_error( $request ) ) {
    return false; // Bail early
}

$body = wp_remote_retrieve_body( $request );

$data = json_decode( $body );

if( ! empty( $data ) ) {

    echo '<ul>';
    foreach( $data->products as $product ) {
        echo '<li>';
            echo '<a href="' . esc_url( $product->info->link ) . '">' . $product->info->title . '</a>';
        echo '</li>';
    }
    echo '</ul>';
}

class Netrics_API_WP {
    const BASE_URL = "https://api.example.com/";
    const GET_DATA_METHOD = "get/data/";
    const POST_DATA_METHOD = "post/data/";

    static function get_data($id) {
        $url = BASE_URL.GET_DATA_METHOD.urlencode($id);
        $response = wp_remote_get($url)

        // Decode the response into an associative array and return it
        return json_decode(wp_remote_retrieve_body($response), true);
    }

    static function post_data($data) {
        $url = BASE_URL.POST_DATA_METHOD;

        // Set the content type to application/json and add a body
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data)));

        return json_decode(wp_remote_retrieve_body($response), true);
    }
}

class Netrics_API_cURL {
    static $curl_handle = NULL;


    const BASE_URL = "https://api.example.com/";
    const GET_DATA_METHOD = "get/data/";
    const POST_DATA_METHOD = "post/data/";

    static function get_data($id) {
        $url = BASE_URL.GET_DATA_METHOD.urlencode($id);

        if (curl_installed() {
            //curl is installed and we can use it

            //Initialize the curl handle if it is not initialized yet
            if (!isset($curl_handle)) {
			    $curl_handle = curl_init();
			}

            // Copy it and fill it with your parameters
            $ch = curl_copy_handle($curl_handle);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");


            // Enable keep alive
            $header = array(
				'Connection: keep-alive',
				'Keep-Alive: 300'
			);

            // Set the user agent which tells you the wordpress and php version and that curl is used
            // This will help you when debugging problems
            curl_setopt($ch, CURLOPT_USERAGENT, 'Curl/WordPress/'.$wp_version
                       .'/PHP'.phpversion().'; ' . home_url());

            // Do not echo result
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Set header
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            // Set HTTP version to 1.1 to allow keepalive connections
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $response = curl_exec($ch);
			$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_status != 200) {
                //TODO: Handle error
			}
            curl_clone($ch);
            return json_decode($response, true);
        } else {
            //Curl is not installed. fallback to WP HTTP API

            $response = wp_remote_get($url)

            // Decode the response into an associative array and return it
            return json_decode(wp_remote_retrieve_body($response), true);
        }
    }

    static function post_data($data) {
        $url = BASE_URL.POST_DATA_METHOD;

        if (curl_installed() {
            //curl is installed and we can use it

            //Initialize the curl handle if it is not initialized yet
            if (!isset($curl_handle)) {
			    $curl_handle = curl_init();
			}

            // Copy it and fill it with your parameters
            $ch = curl_copy_handle($curl_handle);
            curl_setopt($ch, CURLOPT_URL, $url);

            // Encode payload and set post body
            $data_string = json_encode($data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		   	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	   		array_push($header, 'Content-Type: application/json');
   			array_push($header, 'Content-Length: ' . strlen($data_string));

            // Enable keep alive
            $header = array(
				'Connection: keep-alive',
				'Keep-Alive: 300'
			);

            // Set the user agent which tells you the wordpress and php version and that curl is used
            // This will help you when debugging problems
            curl_setopt($ch, CURLOPT_USERAGENT, 'Curl/WordPress/'.$wp_version.'/PHP'
                       .phpversion().'; ' . home_url());

            // Do not echo result
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Set header
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            // Set HTTP version to 1.1 to allow keepalive connections
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $response = curl_exec($ch);
			$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_status != 200) {
                //TODO: Handle error
			}
            curl_clone($ch);
            return json_decode($response, true);
        } else {
            //Curl is not installed. fallback to WP HTTP API


            // Set the content type to application/json and add a body
            $response = wp_remote_post($url, array(
                'headers' => array(
                    'Content-Type' => 'application/json'
                 ),
                 'body' => json_encode($data)));

            return json_decode(wp_remote_retrieve_body($response), true);
        }
    }

    private static function curl_installed(){
		return function_exists('curl_version');
	}
}

*/

?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
