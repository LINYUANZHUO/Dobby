<?php

/**
* Initialization Theme
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.1
*/
add_action( 'load-themes.php', 'dobby_init_theme' );

function dobby_init_theme(){
  global $pagenow;
  if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
    wp_redirect( admin_url( 'themes.php?page=dobby-theme' ) );
    exit;
  }
}

/**
* Loads the Options Panel
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.1
*/
if (!function_exists('optionsframework_init')) {
  define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/options/');
  require_once dirname(__FILE__) . '/options/options-framework.php';
  $optionsfile = locate_template( 'options.php' );
  load_template( $optionsfile );
}

add_filter( 'optionsframework_menu', 'dobby_options_menu_filter' );

function dobby_options_menu_filter( $menu ) {
  $menu['mode'] = 'menu';
  $menu['page_title'] = __( 'Theme Options', 'dobby' );
  $menu['menu_title'] = __( 'Theme Options', 'dobby' );
  $menu['menu_slug'] = 'dobby-theme';
  return $menu;
}

add_action('admin_init','dobby_optionscheck_change_santiziation', 100);

function dobby_optionscheck_change_santiziation() {
    remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
    add_filter( 'of_sanitize_textarea', 'dobby_custom_sanitize_textarea' );
}
function dobby_custom_sanitize_textarea($input) {
    global $allowedposttags;
    $custom_allowedtags["embed"] = array(
        "src" => array(),
        "type" => array(),
        "allowfullscreen" => array(),
        "allowscriptaccess" => array(),
        "height" => array(),
        "width" => array()
      );
    $custom_allowedtags["script"] = array( "type" => array(),"src" => array() );
    $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
    $output = wp_kses( $input, $custom_allowedtags);
    return $output;
}

add_action( 'optionsframework_after', 'optionscheck_display_sidebar' );

function optionscheck_display_sidebar() { ?>
  <div id="optionsframework-sidebar">
    <div class="metabox-holder">
      <div class="postbox">
        <h3>Options Panel Sidebar</h3>
          <div class="inside">
            <p>Here's where you could display some relevant information about the options panel.</p>
            <p>For instance, you might want to add a donate button.</p>
            <p>Or perhaps you want to let people know where to <a href="http://wptheming.com">go for support</a>.</p>
          </div>
      </div>
    </div>
  </div>
<?php }

/**
* Initialization Plugins
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
require_once( get_template_directory() . '/inc/plugins.php');

add_action( 'tgmpa_register', 'dobby_register_required_plugins' );

function dobby_register_required_plugins() {

  $plugins = array(

    array(
      'name'               => 'Html Compress',
      'slug'               => 'dobby-htmlcompress',
      'source'             => get_template_directory() . '/inc/plugins/dobby-htmlcompress.zip',
      'required'           => true,
      'version'            => '1.0.3',
      'external_url'       => 'https://www.vtrois.com/theme-dobby.html',
    ),
    
    array(
      'name'               => 'Page Permalink',
      'slug'               => 'dobby-pagepermalink',
      'source'             => get_template_directory() . '/inc/plugins/dobby-pagepermalink.zip',
      'required'           => true,
      'version'            => '1.0.5',
      'external_url'       => 'https://www.vtrois.com/theme-dobby.html',
    ),

    // array(
    //   'name'               => 'Adminbar',
    //   'slug'               => 'adminbar',
    //   'source'             => 'https://github.com/jrfnl/WP-adminbar-comments-to-pending/archive/master.zip',
    //   'version'            => '1.0.0',
    //   'required'           => false,
    //   'external_url'       => 'https://www.vtrois.com/theme-dobby.html',
    // ),

    array(
      'name'      => 'Disable Embeds',
      'slug'      => 'disable-embeds',
      'required'  => false,
      'version'   => '1.3.0',
    ),

    array(
      'name'      => 'Wordpress Sweep',
      'slug'      => 'wp-sweep',
      'required'  => true,
      'version'   => '1.0.12',
    ),

    array(
      'name'      => 'No Category Base',
      'slug'      => 'no-category-base-wpml',
      'required'  => true,
      'version'   => '1.3',
    ),

    array(
      'name'      => 'Redis Object Cache',
      'slug'      => 'redis-cache',
      'required'  => false,
      'version'   => '1.3.5',
    ),

    array(
      'name'      => 'XML Sitemap',
      'slug'      => 'xml-sitemap-feed',
      'required'  => true,
      'version'   => '4.7.5',
    ),   

  );

  $config = array(
    'id'           => 'dobby',
    'default_path' => '',
    'menu'         => 'dobby-plugins',
    'parent_slug'  => 'themes.php',
    'capability'   => 'edit_theme_options',
    'has_notices'  => false,
    'dismissable'  => true,
    'dismiss_msg'  => '',
    'is_automatic' => false,
    'message'      => '',
  );

  tgmpa( $plugins, $config );
}

/**
* i18n theme languages
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.1
* @todo Only Chinese and English are currently supported
*/
add_action ('after_setup_theme', 'dobby_theme_languages');

function dobby_theme_languages(){
  //theme languages
  load_theme_textdomain('dobby', get_template_directory() . '/languages');
  //theme option languages(inc/options)
  load_theme_textdomain('theme-textdomain', get_template_directory() . '/languages');
  //plugin option languages(inc/plugins)
  load_theme_textdomain('tgmpa', get_template_directory() . '/languages');
}

/**
* User list show last login time
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/

add_action( 'wp_login', 'dobby_insert_last_login' );

function dobby_insert_last_login( $login ) {
  global $user_id;
  $user = get_userdatabylogin( $login );
  update_user_meta( $user->ID, 'last_login', current_time( 'mysql' ) );
}

add_filter( 'manage_users_columns', 'dobby_add_last_login_column' );

function dobby_add_last_login_column( $columns ) {
  $columns['last_login'] = __('Last login time' , 'dobby');
  return $columns;
}

add_action( 'manage_users_custom_column', 'dobby_add_last_login_column_value', 10, 3 );

function dobby_add_last_login_column_value( $value, $column_name, $user_id ) {
  $user = get_userdata( $user_id );
  $single = true;
  if ( 'last_login' == $column_name && $user->last_login )
    $value = get_user_meta( $user->ID, 'last_login', $single );
  else $value = __('No record' , 'dobby');
  return $value;
}

/**
* Show id for all thing
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_action('admin_init', 'dobby_ssid_add');

function dobby_ssid_column($cols) {
  $cols['ssid'] = 'ID';
  return $cols;
}

function dobby_ssid_value($column_name, $id) {
  if ($column_name == 'ssid')
    echo $id;
}
 
function dobby_ssid_return_value($value, $column_name, $id) {
  if ($column_name == 'ssid')
    $value = $id;
  return $value;
}

function dobby_ssid_css() {
?>
<style type="text/css">
  #ssid { width: 50px; }
</style>
<?php 
}

function dobby_ssid_add() {
  add_action('admin_head', 'dobby_ssid_css');
 
  add_filter('manage_posts_columns', 'dobby_ssid_column');
  add_action('manage_posts_custom_column', 'dobby_ssid_value', 10, 2);
 
  add_filter('manage_pages_columns', 'dobby_ssid_column');
  add_action('manage_pages_custom_column', 'dobby_ssid_value', 10, 2);
 
  add_filter('manage_media_columns', 'dobby_ssid_column');
  add_action('manage_media_custom_column', 'dobby_ssid_value', 10, 2);
 
  add_filter('manage_link-manager_columns', 'dobby_ssid_column');
  add_action('manage_link_custom_column', 'dobby_ssid_value', 10, 2);
 
  add_action('manage_edit-link-categories_columns', 'dobby_ssid_column');
  add_filter('manage_link_categories_custom_column', 'dobby_ssid_return_value', 10, 3);
 
  foreach ( get_taxonomies() as $taxonomy ) {
    add_action("manage_edit-${taxonomy}_columns", 'dobby_ssid_column');     
    add_filter("manage_${taxonomy}_custom_column", 'dobby_ssid_return_value', 10, 3);
  }
 
  add_action('manage_users_columns', 'dobby_ssid_column');
  add_filter('manage_users_custom_column', 'dobby_ssid_return_value', 10, 3);
 
  add_action('manage_edit-comments_columns', 'dobby_ssid_column');
  add_action('manage_comments_custom_column', 'dobby_ssid_value', 10, 2);
}

/**
* Loading theme resources
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_action('wp_enqueue_scripts', 'dobby_theme_scripts');

function dobby_theme_scripts() {  
  $dir = get_template_directory_uri();

  //css
  wp_enqueue_style( 'bootstrap', $dir . '/css/bootstrap.min.css', array(), DOBBY_VERSION);
  wp_enqueue_style( 'animate', $dir . '/css/animate.min.css', array(), DOBBY_VERSION);
  wp_enqueue_style( 'layer', $dir . '/css/layer.min.css', array(), DOBBY_VERSION);

  //javascript
  wp_enqueue_script( 'jquery', $dir . '/js/jquery.min.js' , array(), DOBBY_VERSION);
  wp_enqueue_script( 'easing', $dir . '/js/jquery.easing.min.js', array(), DOBBY_VERSION);
  wp_enqueue_script( 'sticky', $dir . '/js/sticky.min.js', array(), DOBBY_VERSION);
  wp_enqueue_script( 'wow', $dir . '/js/wow.min.js', array(), DOBBY_VERSION);
  wp_enqueue_script( 'layer', $dir . '/js/layer.min.js', array(), DOBBY_VERSION);

  if (is_singular()) {
    wp_enqueue_script( 'qrcode', $dir . '/js/jquery.qrcode.min.js', array(), DOBBY_VERSION);
  }

  $dataToDobby = array(
    'site' => home_url(),
    'directory' => get_stylesheet_directory_uri(),
  );
  wp_localize_script( 'dobby', 'db', $dataToDobby );

}

/**
* Replace the jquery resources
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_action( 'wp_enqueue_scripts', 'dobby_enqueue_scripts', 1 );

function dobby_enqueue_scripts() {
  wp_deregister_script('jquery');
}

/**
* Enable links
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

/**
* Optimized built-in functions
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_filter( 'emoji_svg_url', '__return_false' );
add_filter( 'show_admin_bar', '__return_false' );

remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content', 'wptexturize'); 
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('embed_head', 'print_emoji_detection_script');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

/**
* Disable open sans
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_filter('gettext_with_context', 'dobby_disable_open_sans', 888, 4 );

function dobby_disable_open_sans( $translations, $text, $context, $domain )
{
    if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
        $translations = 'off';
    }
    return $translations;
}

/**
* Replace the default avatar server
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_filter( 'get_avatar', 'dobby_get_avatar' );

function dobby_get_avatar( $avatar ) {
  $avatar = str_replace( array( 'www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', '3.gravatar.com', 'secure.gravatar.com' ), 'cn.gravatar.com', $avatar );
  return $avatar;
}

/**
* Support webP file upload
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.2
*/
add_filter('upload_mimes','dobby_upload_webp');

function dobby_upload_webp ( $existing_mimes=array() ) {
  $existing_mimes['webp']='image/webp';
  return $existing_mimes;
}

/**
* The welcome panel notice
*
* @author Vtrois <seaton@vtrois.com>
* @license GPL-3.0
* @since 0.1.4
*/
add_action( 'welcome_panel', 'dobby_welcome_notice' );

function dobby_welcome_notice() {
  ?>
  <style type="text/css">
    .about-description a{
      text-decoration:none;
    }
  </style>
  <div class="notice notice-info">
  <p class="about-description"><?php _e('Welcome to use Dobby theme to create, review the <a target="_blank" rel="nofollow" href="https://www.vtrois.com/theme-dobby.html">instructions</a> before using this theme, If you find BUG please submit <a target="_blank" rel="nofollow" href="https://github.com/Vtrois/Dobby/issues/new?title=Bug%20/%20Suggest%3A%20&body=**%E9%97%AE%E9%A2%98%E6%8F%8F%E8%BF%B0**%0A%0A%EF%BC%88%E7%AE%80%E6%98%8E%E6%89%BC%E8%A6%81%E7%9A%84%E6%8F%8F%E8%BF%B0%E4%B8%80%E4%B8%8B%E9%97%AE%E9%A2%98%EF%BC%89%0A%0A**%E4%BA%A7%E7%94%9F%E7%8E%AF%E5%A2%83**%0A%0A-%20%E8%AE%BE%E5%A4%87%EF%BC%9A%EF%BC%88%E6%89%8B%E6%9C%BA%E3%80%81%E5%B9%B3%E6%9D%BF%E7%AD%89%E7%A7%BB%E5%8A%A8%E8%AE%BE%E5%A4%87%E6%97%B6%E5%A1%AB%E5%86%99%E6%AD%A4%E9%A1%B9%EF%BC%89%0A-%20%E6%B5%8F%E8%A7%88%E5%99%A8%E5%8F%8A%E7%89%88%E6%9C%AC%EF%BC%9A%0A-%20%E6%BC%94%E7%A4%BA%E5%9C%B0%E5%9D%80%EF%BC%9A%0A%0A**%E5%A4%8D%E7%8E%B0%E6%AD%A5%E5%A5%8F**%0A%0A1.%20%0A2.%20%0A">feedback</a>, welcome to join the theme of exchange QQ group : <a target="_blank" rel="nofollow" href="http://shang.qq.com/wpa/qunwpa?idkey=182bd07a135c085c88ab7e3de38f2b2d9a86983292355a4708926b99dcd5b89f">51880737</a>' , 'dobby');?></p></div>
  <?php
}