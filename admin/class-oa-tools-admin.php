<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package OA Tools
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     Kevin McKernan <kevin@mckernan.in>
 */
class OA_Tools_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The current version of this plugin.
	 */
	private $version;

	/**
	 * An instance of OA_Tools_Mailgun.
	 *
	 * @since    1.0.0
	 *
	 * @var class An instance of OA_Tools_Mailgun
	 */
	public $mailgun;

	/**
	 * An instance of OA_Tools_Slack.
	 *
	 * @since    1.0.0
	 *
	 * @var class An instance of OA_Tools_Slack
	 */
	public $slack;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->mailgun     = new OA_Tools_Mailgun();
		$this->slack       = new OA_Tools_Slack();
		require_once plugin_dir_path( __FILE__ ) . 'acf-fields.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/*
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ).'css/oa-tools-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/*
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ).'js/oa-tools-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Export ACF Fields to JSON.
	 *
	 * @param string $path ACF Save path.
	 */
	public function acf_json_save_point( $path ) {

		// Update path.
		$path = plugin_dir_path( __FILE__ ) . '/admin/acf-json';

		// Return.
		return $path;
	}

	/**
	 * Import ACF Fields from JSON.
	 *
	 * @param string $paths ACF Save path.
	 */
	public function acf_json_load_point( $paths ) {

		// Append path.
		$paths[] = plugin_dir_path( __FILE__ ) . '/admin/acf-json';

		// Return.
		return $paths;
	}

	/**
	 * Actions on position save.
	 *
	 * When a position is saved, we create the list for it in Mailgun if it
	 * does not yet exist. We also add them to the LEC list. If a person is
	 * defined for the position, then we also add that person's email address
	 * to the position list.
	 *
	 * @since 1.0.0
	 * @param int  $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $updated Whether this is an existing post being updated or not.
	 */
	public function position_save_action( $post_id, $post, $updated ) {
		$slug = 'oaldr_position	';
		if ( $slug !== $post->post_type ) {
	        return;
	    }
		$position_email = get_field( 'position_email', $post_id );
		$person 		= get_field( 'person', $post_id );
		$copied_emails 	= get_field( 'copied_emails', $post_id );
		$person 		= $person[0];
		$title 			= get_the_title();
		$lists 			= $this->mailgun->get_lists();
		$mg_domain 		= get_theme_mod( 'oaldr_mailgun_domain' );
		$mg_main_list 	= get_theme_mod( 'oaldr_mailgun_main_list' );
		if ( $position_email ) {
			if ( ! in_array( $position_email, $lists ) ) {
				$this->mailgun->create_list( $position_email, $title );
			}
			$this->mailgun->add_list_member( $mg_main_list, $position_email, $title );
		}
		if ( $copied_emails ) {
			foreach ( $copied_emails as $recipient ) {
				$this->mailgun->add_list_member( $position_email, $recipient['email'], $recipient['name'] );
			}
		}
		if ( $person ) {
			$person_email = get_field( 'person_email', $person );
			$fname = get_field( 'first_name', $person );
			$lname = get_field( 'last_name', $person );
			$copied_emails_person = get_field( 'copied_emails', $person );
			$inList = $this->mailgun->check_list_for_member( $position_email, $person_email );
			if ( ! $inList ) {
				$this->mailgun->add_list_member( $position_email, $person_email, $fname.' '.$lname );
			}
			if ( $copied_emails_person ) {
				foreach ( $copied_emails_person as $recipient ) {
					$this->mailgun->add_list_member( $position_email, $recipient['email'], $recipient['name'] );
				}
			}
		}
	}

	/**
	 * Actions on person save.
	 *
	 * When a person is saved, we get the positions that they are assigned to,
	 * and add their email to each position list, if it is not already in the
	 * specified list.
	 *
	 * @since 1.0.0
	 * @param int  $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $updated Whether this is an existing post being updated or not.
	 */
	public function person_save_action( $post_id, $post, $updated ) {
		$slug = 'oaldr_person';
		if ( $slug !== $post->post_type ) {
	        return;
	    }
		$fname        = get_field( 'first_name', $post_id );
		$lname        = get_field( 'last_name', $post_id );
		$person_email = get_field( 'person_email', $post_id );
		$parent_email = get_field( 'parent_email', $post_id );
		$title        = get_the_title();
		if ( $person_email ) {
			$options = array(
				'post_type'      => 'oaldr_position',
				'posts_per_page' => 50,
				'meta_query'     => array(
					array(
					  'key' 	=> 'person',
					  'value' 	=> $post_id,
					  'compare' => 'LIKE',
					),
				),
			);
			$query = new WP_Query( $options );
			if ( $query->has_posts() ) {
				$slack_invite = $this->slack->invite_member( $post_id, $fname, $lname, $person_email );
				foreach ( $query->posts as $post ) {
					$position_email = get_field( 'position_email', $post->id );
					$inList         = $this->mailgun->check_list_for_member( $position_email, $person_email );
					if ( ! $inList ) {
						$this->mailgun->add_list_member( $position_email, $person_email, $fname.' '.$lname );
					}
				}
			}
		}
	}
}
