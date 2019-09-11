<?php
/**
 * newsstats functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package newsstats
 */

if ( ! function_exists( 'netrics_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function netrics_setup() {
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
add_action( 'after_setup_theme', 'netrics_setup' );

/**
 * Registers an editor stylesheet for the theme.
 */
function netrics_editor_styles() {
    add_theme_support( 'editor-styles' ); // Enable custom stylesheet for WordPress editors
    add_editor_style( 'style-editor.css' ); // load the CSS file from template directory
    add_theme_support( 'align-wide' ); // Support for full and wide blocks
}
add_action( 'admin_init', 'netrics_editor_styles' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function netrics_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'newsstats_content_width', 640 );
}
add_action( 'after_setup_theme', 'netrics_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function netrics_widgets_init() {
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
add_action( 'widgets_init', 'netrics_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function netrics_scripts() {

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
add_action( 'wp_enqueue_scripts', 'netrics_scripts' );

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
function netrics_archive_sort( $query ) {
    if ( is_admin() ) {
        return;
    }

    if ( $query->is_main_query() && is_post_type_archive( 'publication' ) ) {
        $query->set( 'orderby', array( 'title' => 'ASC' ) );
    }

    if ( $query->is_main_query() && ( is_tax( 'cms' ) ) || is_tax( 'region' ) || is_tax( 'owner' ) ) {
        $query->set( 'orderby', array( 'title' => 'ASC' ) );
    }


    if ( $query->is_main_query() && is_archive() ) {
        // $query->set( 'post_type', array( 'publication' ) );
    }

    // Sort single post previous/next links by title
    add_filter('get_next_post_sort', 'netrix_filter_next_post_sort');
    add_filter('get_next_post_where', 'netrix_filter_next_post_where');
    add_filter('get_previous_post_sort', 'netrix_filter_previous_post_sort');
    add_filter('get_previous_post_where', 'netrix_filter_previous_post_where');
}
add_action( 'pre_get_posts', 'netrics_archive_sort' );

/* Sort single post previous/next links by title
 *
 * @link http://codeandme.net/how-to-set-adjacent-post-by-alphabetical-order/
 */
/* */
function netrix_filter_next_post_sort( $sort ) {
    $sort = ( is_singular( 'publication' ) ) ? 'ORDER BY p.post_title ASC LIMIT 1' : 'ORDER BY p.post_date DESC LIMIT 1';
    return $sort;
}

function netrix_filter_next_post_where( $where ) {
    global $post, $wpdb;

    if ( is_singular( 'publication' ) ) {
        return $wpdb->prepare( "WHERE p.post_title > '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'", $post->post_title );
    } else {
        return $wpdb->prepare( "WHERE p.post_date > '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->post_date, $post->post_type );
    }
}

function netrix_filter_previous_post_sort( $sort ) {
    $sort = ( is_singular( 'publication' ) ) ? 'ORDER BY p.post_title DESC LIMIT 1' : 'ORDER BY p.post_date ASC LIMIT 1';
    return $sort;
}

function netrix_filter_previous_post_where( $where ) {
    global $post, $wpdb;

    if ( is_singular( 'publication' ) ) {
        return $wpdb->prepare( "WHERE p.post_title < '%s' AND p.post_type = 'publication' AND p.post_status = 'publish'", $post->post_title );
    } else {
        return $wpdb->prepare( "WHERE p.post_date < '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->post_date, $post->post_type );
    }
}

