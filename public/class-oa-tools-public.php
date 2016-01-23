<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 *
 * @package    OA_Tools
 * @subpackage OA_Tools/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    OA_Tools
 * @subpackage OA_Tools/public
 * @author     Kevin McKernan <kevin@mckernan.in>
 */
class OA_Tools_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OA_Tools_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OA_Tools_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/oa-tools-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OA_Tools_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OA_Tools_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/oa-tools-public.js', array( 'jquery' ), $this->version, false );
		wp_register_script('mixitup', 'http://cdn.jsdelivr.net/jquery.mixitup/latest/jquery.mixitup.min.js', true);

	}

	public function customizer_settings( $wp_customize ) {
		$wp_customize->add_section( 'oaldr_settings' , array(
		    'title'      => __( 'OA Leadership Settings', 'oaldr' ),
		    'priority'   => 30,
		) );

		$wp_customize->add_setting( 'oaldr_headshot_default' , array(
		    'default'     => '',
		    'transport'   => 'refresh',
		) );

		$wp_customize->add_control(
	       new WP_Customize_Image_Control(
	           $wp_customize,
	           'oaldr_headshot_default_control',
	           array(
	               'label'      => __( 'Upload a placeholder', 'OA Leadership Position Plugin' ),
	               'section'    => 'oaldr_settings',
	               'settings'   => 'oaldr_headshot_default',
	               'context'    => 'your_setting_context' 
	           )
	       )
	   );

		$wp_customize->add_setting( 'oaldr_categorize_positions', array(
			'default'		=> 'lodge',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'refresh'
		) );

		$wp_customize->add_control( 'oaldr_categorize_positions', array(
			'label'		=> __( 'Categorize Positions by?', 'OA Leadership Position Plugin' ),
			'section'	=> 'oaldr_settings',
			'settings'	=> 'oaldr_categorize_positions',
			'type'		=> 'select',
			'choices'	=> array(
				'chapter' => __( 'Chapter', 'OA Leadership Position Plugin' ),			
				'lodge'   => __( 'Lodge', 'OA Leadership Position Plugin' ),
				'section' => __( 'Section', 'OA Leadership Position Plugin' ),
			),
		) );
	}

}
