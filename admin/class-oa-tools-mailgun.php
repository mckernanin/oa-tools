<?php
/**
 * Class responsible for interfacing with the Mailgun API.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package		OA Tools
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/mailgun-api/vendor/autoload.php';
use Mailgun\Mailgun;

define( 'MAILGUN_API_KEY', get_theme_mod( 'oaldr_mailgun_api_key' ) );

/**
 * Class responsible for interfacing with the Mailgun API.
 */
class OA_Tools_Mailgun {
	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Display a WordPress Dashboard error message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Text/HTML content of the error message.
	 */
	public function error_message( $message ) {
		echo '<div class="notice notice-error">';
		echo '<p>';
		esc_html_e( 'OA Tools - ' . $message, 'oa_tools' );
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Display a WordPress Dashboard success message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Text/HTML content of the error message.
	 */
	public function success_message( $message ) {
		echo '<div class="notice notice-success">';
		echo '<p>';
		esc_html_e( 'OA Tools - ' . $message, 'oa_tools' );
		echo '</p>';
		echo '</div>';
	}

	public function custom_log( $message ) {
		$filename = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/mailgun-api/mailgun.log';
		if ( touch ( $filename ) ) {
			error_log( $message, 3, $filename );
		}
	}

	/**
	 * Get an array of members in a list from the Mailgun API
	 *
	 * @since 1.0.0
	 *
	 * @param string $listAddress The full email address of a Mailgun list.
	 *
	 * @return array
	 */
	public function get_list_members( $listAddress ) {
		try {
			// Instantiate the client.
			$mgClient = new Mailgun( MAILGUN_API_KEY );

			// Issue the call to the client.
			$result = $mgClient->get( "lists/$listAddress/members", array() );
			$addresses = array();
			foreach ( $result->http_response_body->items as $member ) {
				$addresses[] = $member->address;
			}
			$this->custom_log( 'get_list_members(): ' . $listAddress );
			return $addresses;
		} catch ( Exception $e ) {
			$this->custom_log( 'The following error occured when trying to retrieve members from '.$listAddress.': '.$e->getMessage() );
		}
	}

	/**
	 * Get an array of lists from the Mailgun API
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_lists() {
		try {
			// Instantiate the client.
			$mgClient = new Mailgun( MAILGUN_API_KEY );
			// Issue the call to the client.
			$result = $mgClient->get( 'lists', array() );
			$lists = array();
			foreach ( $result->http_response_body->items as $list ) {
				$lists[] = $list->address;
			}
			$this->custom_log( 'get_lists() ran');
			return $lists;
		} catch ( Exception $e ) {
			$this->custom_log( 'The following error occured when trying to retrieve lists: '.$e->getMessage() );
		}
	}

	/**
	 * Create a new list Mailgun list
	 *
	 * @since 1.0.0
	 *
	 * @param string $address The email address of the list to create.
	 * @param string $description A title for $address.
	 * @param string $access_level Define the access level for the list, options are: readonly, members, everyone.
	 */
	public function create_list( $address, $description, $access_level = 'everyone' ) {
		try {
			// Instantiate the client.
			$mgClient = new Mailgun( MAILGUN_API_KEY );
			// Issue the call to the client.
			$result = $mgClient->post( 'lists', array(
				'address'      => $address,
				'description'  => $description,
				'access_level' => $access_level,
			));
			$this->custom_log( 'create_list(): ' . $address );
		} catch ( Exception $e ) {
			$this->custom_log( 'The following error occured when trying to create '.$address.': '.$e->getMessage() );
		}
	}

	/**
	 * Add an email to a Mailgun list
	 *
	 * @since 1.0.0
	 *
	 * @param string $listAddress The address of the list to add an address to.
	 * @param string $address The email address to add to $listAddress.
	 * @param string $name A description for $address.
	 */
	public function add_list_member( $listAddress, $address, $name ) {
		$inList = $this->check_list_for_member( $listAddress, $address );
		if ( ! $inList ) {
			try {
				// Instantiate the client.
				 $mgClient = new Mailgun( MAILGUN_API_KEY );
				 // Issue the call to the client.
				 $result = $mgClient->post("lists/$listAddress/members", array(
					 'address' => $address,
					 'name'    => $name,
				 ));
				 $this->custom_log( 'add_list_member(): ' . $listAddress . ' | ' . $address );
				 return $result;
			} catch ( Exception $e ) {
				$this->custom_log( 'The following error occured when trying to add '.$address.' to '.$listAddress.': '.$e->getMessage() );
			}
		} else {
			$this->custom_log( 'Address' . $address . ' is already present in list ' . $listAddress . '.' );
		}
	}

	/**
	 * Check a Mailgun list for an email address
	 *
	 * @since 1.0.0
	 *
	 * @param string $listAddress The full address list to query.
	 * @param string $address The email address to look for in $listAddress.
	 *
	 * @return boolean
	 */
	public function check_list_for_member( $listAddress, $address ) {
		try {
			// Instantiate the client.
			$mgClient = new Mailgun( MAILGUN_API_KEY );

			// Issue the call to the client.
			$result = $mgClient->get( "lists/$listAddress/members/$address", array() );
			$this->custom_log( 'check_list_for_member(): ' . $listAddress . ' | ' . $address );
			return true;
		} catch ( Exception $e ) {
			$this->custom_log( 'Address does not exist in list ' . $address );
			return false;
		}
	}

	/**
	 * Function for testing mailgun calls
	 */
	public function mailgun_test() {
		$oatools_mailgun = new OA_Tools_Mailgun();
		// $oatools_mailgun->get_list_members( 'lec@list.tahosalodge.org');
		$lists = $oatools_mailgun->get_lists();
		// $oatools_mailgun->create_list( 'lec@list.tahosalodge.org', 'Lodge Executive Committee' );
		// $oatools_mailgun->add_list_member( 'lec@list.tahosalodge.org', 'bob.crume@gmail.com', 'Bob Crume' );
		// $oatools_mailgun->check_list_for_member( 'lec@list.tahosalodge.org', 'kevin@luminary.ws' );
		var_dump( $lists );
	}
}
