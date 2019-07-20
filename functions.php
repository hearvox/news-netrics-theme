<?php
/**
 * newsstats functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package newsstats
 */

if ( ! function_exists( 'newsstats_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function newsstats_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on newsstats, use a find and replace
	 * to change 'newsstats' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'newsstats', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

    /* Add Excerpt field to Pages */
    add_post_type_support( 'page', 'excerpt' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Menu', 'newsstats' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'newsstats_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // newsstats_setup
add_action( 'after_setup_theme', 'newsstats_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function newsstats_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'newsstats_content_width', 640 );
}
add_action( 'after_setup_theme', 'newsstats_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function newsstats_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'newsstats' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'newsstats_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function newsstats_scripts() {

    // $site_js_path    = ABSPATH . '/wp-content/plugins/news-netrics/js/';
    // $site_js_url     = plugins_url() . '/news-netrics/js/';
    $site_theme_path = get_stylesheet_directory();
    $site_theme_url  = get_stylesheet_directory_uri();

    /* Site-wide stylesheet and script file */
    // To cache bust: Version (last param) is file-mod, @uses headecon_filemod_vers().
    wp_register_style(
        'newsnetrics-css',
        get_stylesheet_uri(),
        array(),
        netrics_filemod_vers( $site_theme_path . '/style.css' )
    );

	// wp_enqueue_style( 'newsstats-style', get_stylesheet_uri()
    wp_enqueue_style( 'newsnetrics-css' );

	wp_enqueue_script( 'newsstats-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'newsstats-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'newsstats_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/* Sort by archives by title */
function newstime_archive_sort( $query ) {
    if ( is_admin() ) {
        return;
    }

    if ( $query->is_main_query() && ! is_post_type_archive( 'post' ) ) {
        $query->set( 'orderby', array( 'title' => 'ASC' ) );
    }

    if ( $query->is_main_query() && is_archive() ) {
        // $query->set( 'post_type', array( 'publication' ) );
    }

    add_filter('get_next_post_sort', 'filter_next_post_sort');
    add_filter('get_next_post_where', 'filter_next_post_where');
    add_filter('get_previous_post_sort', 'filter_previous_post_sort');
    add_filter('get_previous_post_where', 'filter_previous_post_where');
}
add_action( 'pre_get_posts', 'newstime_archive_sort' );

/* Sort single post previous/next links by title
 *
 * @link http://codeandme.net/how-to-set-adjacent-post-by-alphabetical-order/
 */
function filter_next_post_sort( $sort ) {
    $sort = ( is_singular( 'publication' ) ) ? 'ORDER BY p.post_title ASC LIMIT 1' : 'ORDER BY p.post_date DESC LIMIT 1';
    return $sort;
}

function filter_next_post_where( $where ) {
    global $post, $wpdb;

    if ( is_singular( 'publication' ) ) {
        return $wpdb->prepare( "WHERE p.post_title > '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'", $post->post_title );
    } else {
        return $wpdb->prepare( "WHERE p.post_date > '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->post_date, $post->post_type );
    }
}

function filter_previous_post_sort( $sort ) {
    $sort = ( is_singular( 'publication' ) ) ? 'ORDER BY p.post_title DESC LIMIT 1' : 'ORDER BY p.post_date ASC LIMIT 1';
    return $sort;
}

function filter_previous_post_where( $where ) {
    global $post, $wpdb;

    if ( is_singular( 'publication' ) ) {
        return $wpdb->prepare( "WHERE p.post_title < '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'", $post->post_title );
    } else {
        return $wpdb->prepare( "WHERE p.post_date < '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->post_date, $post->post_type );
    }
}

/*

add_filter('get_next_post_sort', 'filter_next_post_sort');
add_filter('get_next_post_where', 'filter_next_post_where');
add_filter('get_previous_post_sort', 'filter_previous_post_sort');
add_filter('get_previous_post_where', 'filter_previous_post_where');



function filter_next_post_where( $where ) {
    global $post, $wpdb;

    if ( is_singular( 'publication' ) ) {
        return $wpdb->prepare( "WHERE p.post_title > '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'", $post->post_title );
    } else {
        return $wpdb->prepare( "WHERE p.post_date < '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->post_date, $post->post_type );
    }
}

function filter_previous_post_sort( $sort ) {
    $sort = ( is_singular( 'publication' ) ) ? 'ORDER BY p.post_title DESC LIMIT 1' : 'ORDER BY p.post_date ASC LIMIT 1';
    return $sort;
}

function filter_previous_post_where( $where ) {
    global $post, $wpdb;

    if ( is_singular( 'publication' ) ) {
        $wpdb->prepare( "WHERE p.post_title < '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'", $post->post_title );
    } else {
        return $wpdb->prepare( "WHERE p.post_date > '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->post_date, $post->post_type );
    }
}


function filter_next_post_sort($sort) {
  $sort = "ORDER BY p.post_title ASC LIMIT 1";
  return $sort;
}
function filter_next_post_where($where) {
  global $post, $wpdb;
  return $wpdb->prepare("WHERE p.post_title > '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'",$post->post_title);
}
function filter_previous_post_sort($sort) {
  $sort = "ORDER BY p.post_title DESC LIMIT 1";
  return $sort;
}
function filter_previous_post_where($where) {
  global $post, $wpdb;
  return $wpdb->prepare("WHERE p.post_title < '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'",$post->post_title);
}





*/

/*******************************
 =REGISTER SCRIPTS
 ******************************/


/*******************************
 =STATISTICS
 ******************************/
/* ------------------------------------------------------------------------ *
 * Display array evaluations
 * ------------------------------------------------------------------------ */

/**
 * Outputs HTML with array averages, quartiles, and standard deviations.
 *
 */
function nstats_array_eval( $array ) {
    ?>
<table class="tabular" style="">
    <tbody>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Mean&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_mean( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Maximum&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_max( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Minimum&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_min( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Range&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_range( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Quartile 1&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_q1( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Q2/Median&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_q2( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Quartile 3&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_q3( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Interquartile Range&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_iqr( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Standard Deviation&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_sd( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'SD Population&nbsp;', 'statsclass' ); ?></th>
             <td><?php echo number_format( nstats_sd_pop( $array ), 2, '.', ',' ); ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Count&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( count( $array ) ); ?></td>
        </tr>
    </tfoot>
</table>

   <?php
}


/**
 * Use filemod timestamp for version number.
 *
 * For setting cache-busting version number in script registrations.
 * wp_register_script( 'handle', $file_url, array(), netrics_filemod_vers( $file_path ) );
 *
 * @param  string $path Path of script/style file
 * @return string $vers File-modification timestamp or WordPress version
 */
function netrics_filemod_vers( $path ) {
    $vers = '';

    if ( file_exists( $path ) ) {
        $vers = filemtime( $path );
    } else {
        $vers = get_bloginfo('version');
    }

    return $vers;
}


/* Multiple select drop-down of taxonomy terms */
// http://wordpress.stackexchange.com/questions/107044/error-sending-array-of-inputs
function get_terms_multi_select( $tax, $args = array(), $rows = 10 ) {
    $output = ''; // Set (or clear) var.
    $tax_arr = get_taxonomy( $tax );
    $tax_single = ( $tax == 'region' )
        ? 'State' : $tax_arr->labels->singular_name;

    $terms = get_terms( $tax, $args ); // Array of tax terms.

    if ( $terms ) {
        $count = count( $terms ) + 1; // Terms in tax (for select size).
        $output .= "<h3 class=\"pub-tax-name\">{$tax_single}</h3>\n";
        $selected_any = ( ! isset( $_POST['tax_input'][$tax] ) || ( $_POST['tax_input'][$tax][0] == '0' ) ) ? ' selected' : ''; // To select option for previous user-choice.
        $output .= "<select multiple name=\"tax_input[{$tax}][]\" id=\"{$tax}\" size=\"$rows\">\n";
        $output .= "\t<option value=\"0\"{$selected_any}> Any {$tax_single}</option>\n";

        foreach ( $terms as $term ) {
            $term_slug = $term->slug;
            $selected = ''; // To select option for previous user-choice.

            if ( isset( $_POST['tax_input'][$tax] ) && in_array( $term->term_id, $_POST['tax_input'][$tax] ) ) {
                $selected = ' selected';
            }

            $termcount = ( $tax == 'region' ) ? '' : " ({$term->count})";

            $output .= "\t<option value=\"{$term->term_id}\"{$selected}> {$term->name}{$termcount}</option>\n";
        }
        $output .= "</select>\n";

        return $output;
    }
}

/* ------------------------------------------------------------------------ *
 * Basic Calculations
 * ------------------------------------------------------------------------ */

/**
 * Calculates percentage check between to numbers.
 *
 *
 * @since    0.1.0
 * @param float $num1 Number
 */
function nstats_percent_change( $num1, $num2 ) {
    $percent_change = ( ( $num2 - $num1 ) / $num1 ) * 100;

    return $percent_change;
}

/* Average numbers in an array.
 *
 * @param
 * @return
 */
function nstats_average( $array ) {

    return array_sum( $array ) / count( $array );

}

/* ------------------------------------------------------------------------ *
 * Averages: Mean, Mode, Median
 * ------------------------------------------------------------------------ */

/**
 * Calculates the mean (average) as a set of numbers.
 *
 *
 * @since    0.1.0
 * @param array $array Array of numbers.
 */
function nstats_mean( $array ) {
    // check_array( $array );
    $mean = array_sum( $array ) / count( $array );
  return $mean;
}

/**
 * Finds the mode number of an array.
 *
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_mode( $array ) {
    // check_array( $array );
    $values = array_count_values( $array );
    $mode   = array_search( max( $values ), $values );

    return $mode;
}

function nstats_median( $array ) {
  return nstats_q2( $array );
}

/* ------------------------------------------------------------------------ *
 * Quantiles: Quartiles, Percentile
 * ------------------------------------------------------------------------ */

/**
 * Calculate Quartiles
 *
 * @link http://blog.poettner.de/2011/06/09/simple-statistics-with-php/
 *
 * @param array $x
 * @return float|bool The correlation or false on error.
 */

function nstats_q1( $array ) {
  return nstats_percentile( $array, 25);
}

function nstats_q2( $array ) {
  return nstats_percentile( $array, 50);
}

function nstats_q3( $array ) {
  return nstats_percentile( $array, 75);
}

// interquartile range (IQR) is the difference between the upper and lower quartiles. (IQR = Q3 - Q1)
function nstats_iqr( $array ) {
    $iqr = nstats_q3( $array ) - nstats_q1( $array );
    return $iqr;
}

function nstats_percentile( $array, $percentile ) {
    sort( $array );
    $index = ( $percentile / 100 ) * count( $array );
    if ( floor( $index ) == $index ) {
         $result = ( $array[ $index-1 ] + $array[ $index] ) / 2;
    }
    else {
        $result = $array[ floor($index) ];
    }
    return $result;
}

/* ------------------------------------------------------------------------ *
 * Range: Minimum, Maximum, Total
 * ------------------------------------------------------------------------ */

/**
 * Finds the highest value in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_max( $array ) {
    rsort( $array );
    $maximum = $array[0];

    return $maximum;
}

/**
 * Finds the lowest value in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_min( $array ) {
    sort( $array );
    $minimum = $array[0];

    return $minimum;
}

/**
 * Finds the range of values in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_range( $array ) {
    $maximum = nstats_max( $array );
    $minimum = nstats_min( $array );

    $range = $maximum - $minimum;

    return $range;
}

/* ------------------------------------------------------------------------ *
 * Variance: Sample Standard Deviation, Population Standard Deviations
 * ------------------------------------------------------------------------ */

/**
 * Finds the sample standard deviation of values in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_sd( $array ) {
    if( count( $array ) < 2 ) {
        return;
    }

    $mean = nstats_mean( $array );

  $sum = 0;
  foreach ( $array as $value) {
    $sum += pow( $value - $mean, 2); // Exponential expression.
  }

  $result = sqrt( (1 / ( count( $array ) - 1 ) ) * $sum );

  return $result;
}

/**
 * Finds the standard deviation (population) of a set of numbers.
 *
 * The average of the squared differences from the mean.
 * (SD, also represented by the Greek letter sigma σ or s)
 *
 */
function nstats_sd_pop( $array ) {
    $mean  = nstats_mean( $array );
    $count = count( $array );
    $sum   = 0;

    foreach( $array as $value) {
        $diff    = $value - $mean;
        $diff_sq = $diff * $diff;
        $sum += $diff_sq;
    }

    $variance = $sum / $count;
    $std_dev  = sqrt( $variance );

    return $std_dev;
}


/* ------------------------------------------------------------------------ *
 * Comparision of two arrays.
 * ------------------------------------------------------------------------ */

/**
 *
 * @link http://php.net/manual/en/function.stats-stat-correlation.php
 *
 * @param array $x
 * @param array $y
 * @return float|bool The correlation or false on error.
 */

function nstats_correlation( $array1, $array2) {

    $length = count( $array1 );
    $mean1  = array_sum( $array1 ) / $length;
    $mean2  = array_sum( $array2 ) / $length;

    $a1  = 0;
    $b1  = 0;
    $axb = 0;
    $a2  = 0;
    $b2  = 0;

    for ( $i=0; $i < $length; $i++ ) {
        // if ( ( isset( $array1[ $i ] )&& $array1[ $i ] ) && ( isset( $array2[ $i ] ) && $array2[ $i ] ) ) {
            $a1  = $array1[ $i ] - $mean1;
            $b1  = $array2[ $i ] - $mean2;
            $axb = $axb + ( $a1 * $b1 );
            $a2  = $a2 + pow( $a1, 2 );
            $b2  = $b2 + pow( $b1, 2 );
        // }
    }


    $correlation = $axb / sqrt( $a2 * $b2 );

    return $correlation;
}


function Correlation($arr1, $arr2)
{
    $correlation = 0;

    $k = SumProductMeanDeviation($arr1, $arr2);
    $ssmd1 = SumSquareMeanDeviation($arr1);
    $ssmd2 = SumSquareMeanDeviation($arr2);

    $product = $ssmd1 * $ssmd2;

    $res = sqrt($product);

    $correlation = $k / $res;

    return $correlation;
}

function SumProductMeanDeviation($arr1, $arr2)
{
    $sum = 0;

    $num = count($arr1);

    for($i=0; $i<$num; $i++)
    {
        $sum = $sum + ProductMeanDeviation($arr1, $arr2, $i);
    }

    return $sum;
}

function ProductMeanDeviation($arr1, $arr2, $item)
{
    return (MeanDeviation($arr1, $item) * MeanDeviation($arr2, $item));
}

function SumSquareMeanDeviation($arr)
{
    $sum = 0;

    $num = count($arr);

    for($i=0; $i<$num; $i++)
    {
        $sum = $sum + SquareMeanDeviation($arr, $i);
    }

    return $sum;
}

function SquareMeanDeviation($arr, $item)
{
    return MeanDeviation($arr, $item) * MeanDeviation($arr, $item);
}

function SumMeanDeviation($arr)
{
    $sum = 0;

    $num = count($arr);

    for($i=0; $i<$num; $i++)
    {
        $sum = $sum + MeanDeviation($arr, $i);
    }

    return $sum;
}

function MeanDeviation($arr, $item)
{
    $average = Average($arr);

    return $arr[$item] - $average;
}

function Average($arr)
{
    $sum = Sum($arr);
    $num = count($arr);

    return $sum/$num;
}

function Sum($arr)
{
    return array_sum($arr);
}



