<?php
/**
 * Plugin Name:     EDD Open Stats
 * Plugin URI:      https://wordpress.org/plugins/edd-open-stats
 * Description:     @todo
 * Version:         1.0.0
 * Author:          Daniel Iser
 * Author URI:      http://danieliser.com
 * Text Domain:     edd-open-stats
 *
 * @package         EDD\OpenStats
 * @author          Daniel Iser
 * @copyright       Copyright (c) 2015
 *
 *
 *
 * - Find all instances of @todo in the plugin and update the relevant
 *   areas as necessary.
 *
 * - All functions that are not class methods MUST be prefixed with the
 *   plugin name, replacing spaces with underscores. NOT PREFIXING YOUR
 *   FUNCTIONS CAN CAUSE PLUGIN CONFLICTS!
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Open_Stats' ) ) {

    /**
     * Main EDD_Open_Stats class
     *
     * @since       1.0.0
     */
    class EDD_Open_Stats {

        /**
         * @var         EDD_Open_Stats $instance The one true EDD_Open_Stats
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Open_Stats
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Open_Stats();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_OPEN_STATS_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_OPEN_STATS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_OPEN_STATS_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include scripts
            //require_once EDD_OPEN_STATS_DIR . 'includes/scripts.php';
            //require_once EDD_OPEN_STATS_DIR . 'includes/functions.php';

            require_once EDD_OPEN_STATS_DIR . 'includes/shortcodes.php';
            // require_once EDD_OPEN_STATS_DIR . 'includes/widgets.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         */
        private function hooks() {
            // Register settings
            //add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_OPEN_STATS_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_open_stats_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-open-stats' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-open-stats', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-open-stats/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-open-stats/ folder
                load_textdomain( 'edd-open-stats', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-open-stats/languages/ folder
                load_textdomain( 'edd-open-stats', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-open-stats', false, $lang_dir );
            }
        }


        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
            $new_settings = array(
                array(
                    'id'    => 'edd_open_stats_settings',
                    'name'  => '<strong>' . __( 'Open Stats Settings', 'edd-open-stats' ) . '</strong>',
                    'desc'  => __( 'Configure Open Stats Settings', 'edd-open-stats' ),
                    'type'  => 'header',
                )
            );

            return array_merge( $settings, $new_settings );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_Open_Stats
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Open_Stats The one true EDD_Open_Stats
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function edd_open_stats_load() {
    if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
        if( ! class_exists( 'EDD_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        EDD_Open_Stats::instance();
    }
}
add_action( 'plugins_loaded', 'edd_open_stats_load' );


/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function edd_open_stats_activation() {
    /* Activation functions here */
}
register_activation_hook( __FILE__, 'edd_open_stats_activation' );
