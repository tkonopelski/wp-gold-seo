<?php
/**
 * Plugin Name: Gold SEO
 * Plugin URI: http://konopelski.info
 * Description: WP pluging for SEO meta description and Open Graph
 * Version:  1.3
 * Author: Tom Konopelski
 * Author URI: http://konopelski.info
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    die('direct access disabled');
}

$goldSeo = new GoldSeo();


/**
 * Class GoldSeo
 *
 * Author: Tom Konopelski
 * Author URI: http://konopelski.info
 * License: GPL2
 */
class GoldSeo {

    private $version = '1.3';

    private $name='Gold SEO';

    private $shortName='GoldSEO';

    /**
     * Plugin slug
     * @var string
     */
    private $plugin_slug = 'gold-seo';

    /**
     * Settings array
     * @var array
     */
    private $settings = array();

    /**
     * Default max content size
     * @var int
     */
    private $maxcontentsize = 100;


    function __construct() {

        add_post_type_support( 'page', 'excerpt' );

        if ( is_admin() ){
            add_action( 'admin_menu', array($this, 'addAdminMenu' ));
            add_action( 'admin_init', array($this, 'registerSettings') );
            add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array($this, 'addSettingsLink') );
        } else {
            add_action( 'wp_head', array($this, 'setMeta'), 1 );
        }
    }


    /**
     * Sets meta description
     *
     */
    function setMeta() {

        if ( is_404() || is_search() ) {
            return;
        }

        $settings = get_option($this->plugin_slug . '-settings');
        $this->settings = json_decode($settings, true);
        $this->setMaxContentSize();

        $description = '';
        if( is_single() or is_page() ) {

            if (has_excerpt()){
                $description = get_the_excerpt();
                $description =  $this->stripShortcodes($description);
            } else {
                global $post;
                $mainPost = get_post($post->ID);
                if (isset($mainPost->post_content[5])) {
                    $description = $this->stripShortcodes($mainPost->post_content);
                    if ($this->settings['maxcontentsize']>0) {
                        $description = mb_substr($description, 0, $this->settings['maxcontentsize']);
                    }
                }
            }
        }
        elseif( is_tag() or is_category() or is_tax()){
            $description = $this->stripShortcodes( term_description() );
        } else {
           return;
        }

        if (is_front_page() || is_home() ) {
            $description = get_bloginfo( 'description' );
        }

        $description = trim(preg_replace('/\s+/', ' ', $description));

        if (strlen($description) > 2) {
            echo '<meta name="description" content="'.(esc_html( $description)).'" />';
            echo "\n";
        }

        # keywords
        if (isset($this->settings['metatags']) && strlen($this->settings['metatags']) > 2) {
            echo '<meta name="keywords" content="'. esc_html($this->settings['metatags']) .'" />';
            echo "\n";
        }

        if (!isset($this->settings['ogtitle']) || $this->settings['ogtitle']==='yes') {
            echo '<meta property="og:title" content="'. esc_html(get_the_title()) .'" />';
            echo "\n";
        }

        if (isset($this->settings['ogimage'][5])) {
            echo '<meta property="og:image" content="'. esc_html($this->settings['ogimage']) .'" />';
            echo "\n";
        }

        if (isset($this->settings['oglocale'][1])) {
            echo '<meta property="og:locale" content="'. esc_html($this->settings['oglocale']) .'" />';
            echo "\n";
        }

        if (isset($this->settings['ogtype'][1])) {
            echo '<meta property="og:type" content="'. esc_html($this->settings['ogtype']) .'" />';
            echo "\n";
        }

        if ((!isset($this->settings['ogdescription']) || $this->settings['ogdescription']==='yes') && strlen($description) > 2) {
            echo '<meta property="og:description" content="'. esc_html($description) .'" />';
            echo "\n";
        }

        if (!isset($this->settings['ogurl']) || $this->settings['ogurl']==='yes') {
            global $wp;
            echo '<meta property="og:url" content="'. home_url( $wp->request ) .'" />';
            echo "\n";
        }

        if (!isset($this->settings['generatorTag']) || $this->settings['generatorTag']==='yes') {
            remove_action('wp_head', 'wp_generator');
        }
    }


    /**
     * Sets max content size
     */
    private function setMaxContentSize() {

        if (!isset($this->settings['maxcontentsize']) || !is_numeric($this->settings['maxcontentsize']) || $this->settings['maxcontentsize'] < 1) {
            $this->settings['maxcontentsize'] = $this->maxcontentsize;
        }
    }


    /**
     * Admin WP menu
     */
    function addAdminMenu() {

        add_options_page(
            __( $this->name, $this->plugin_slug ),
            __( $this->name, $this->plugin_slug ),
            'manage_options',
            $this->plugin_slug,
            array( $this, 'displayAdminSettings' )
        );

    }

    /**
     * Add settings link
     *
     * @param $links
     * @return mixed
     */
    function addSettingsLink( $links ) {
        $settings_link = '<a href="options-general.php?page=gold-seo">' . __( 'Settings' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }


    /**
     * Save settings
     *
     */
    function registerSettings() {
        if (isset($_POST['soldseoSaveSettings'])) {

            $save = array();
            $save['stripmethod'] = sanitize_text_field($_POST['stripmethod']);
            $save['usecontent'] = sanitize_text_field($_POST['usecontent']);
            $save['maxcontentsize'] = intval( sanitize_text_field($_POST['maxcontentsize']) );
            $save['ogtitle'] = sanitize_text_field($_POST['ogtitle']);
            $save['ogdescription'] = sanitize_text_field($_POST['ogdescription']);
            $save['oglocale'] = sanitize_text_field($_POST['oglocale']);
            $save['ogurl'] = sanitize_text_field($_POST['ogimage']);
            $save['ogimage'] = sanitize_text_field($_POST['ogimage']);
            $save['ogtype'] = sanitize_text_field($_POST['ogtype']);
            $save['metatags'] = sanitize_text_field($_POST['metatags']);
            $save['generatorTag'] = sanitize_text_field($_POST['generatorTag']);

            $save = json_encode($save);
            update_option( $this->plugin_slug . '-settings', trim( $save ) );
        }

    }


    /**
     * Get settings
     *
     */
    function getSettings() {

        $save = array();
        $save['stripmethod'] = '';
        $save['usecontent'] = '';
        $save['maxcontentsize'] = 100;
        $save['ogtitle'] = '';
        $save['ogdescription'] = '';
        $save['oglocale'] = '';
        $save['ogurl'] = '';
        $save['ogimage'] = '';
        $save['ogtype'] = '';
        $save['metatags'] = '';
        $save['generatorTag'] = '';

        $settings = get_option($this->plugin_slug . '-settings');
        $settings = json_decode($settings, true);

        if (!is_array($settings)) {
             $settings = array();
        }
        $settings = array_merge($save, $settings);

        return $settings;
    }


    /**
     * Display plugin settings page
     *
     */
    function displayAdminSettings() {

        $settings = $this->getSettings();

        if (!isset($settings['maxcontentsize']) || !is_numeric($settings['maxcontentsize']) || $settings['maxcontentsize'] < 1) {
            $settings['maxcontentsize'] = $this->maxcontentsize;
        }
        $locates = file_get_contents(plugin_dir_path(__FILE__). 'locates.json');
        $locates = json_decode($locates, true);
        $version = $this->version;
        include plugin_dir_path(__FILE__) . 'goldseo-settings.php';
    }


    /**
     * Strip WP shortcodes
     *
     * @param $content
     * @return string
     */
    private function stripShortcodes($content) {

        $content = esc_html($content);

        $stripmethod = 'nostrip';
        if (isset($this->settings['stripmethod'])) {
            $stripmethod = $this->settings['stripmethod'];
        }

        if ($stripmethod==='stripall') {

            $content = strip_tags($content);
            $content = strip_shortcodes($content);
            return $content;

        } elseif ($stripmethod==='stripkeep') {

            $content = strip_tags($content);
            $content = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $content);
            return $content;

        } elseif ($stripmethod==='nostrip') {

            $content = strip_tags($content);
            return $content;
        }

        return '';
    }

}