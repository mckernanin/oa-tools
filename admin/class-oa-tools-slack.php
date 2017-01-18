<?php
/**
 * Class responsible for interfacing with the Slack API.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 * @package		OA Tools
 */

/**
 * Class responsible for interfacing with the Mailgun API.
 */
class OA_Tools_Slack {

	/**
	 * Slack API Key
	 *
	 * @since    1.1.0
	 *
	 * @var string Slack API Key
	 */
	public $slack_api_key;

	/**
	 * Slack Domain
	 *
	 * @since    1.1.0
	 *
	 * @var string Slack Domain
	 */
	public $slack_domain;

	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->slack_api_key = get_theme_mod( 'oaldr_slack_api_key' );
		$this->slack_domain  = get_theme_mod( 'oaldr_slack_subdomain' );
	}

	/**
	 * Display a WordPress Dashboard error message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Text/HTML content of the error message.
	 */
	public function error_message( $message ) {
		echo '<div class="error notice">';
		echo '<p>';
		esc_html_e( 'OA Tools - ' . $message, 'oa_tools' );
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Invite a user to the Slack Team.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_id The post ID of the person we're creating an account for.
	 * @param string $fname The first name of the user to be created.
	 * @param string $lname The last name of the user to be created.
	 * @param string $email The email address of the user to be created.
	 *
	 * @return array
	 */
	public function invite_member( $post_id, $fname, $lname, $email ) {
		$slack_created = get_post_meta( $post_id, '_slack_created' );
		$this->notify( 'invite member' . $email );
		if ( ! $slack_created ) {
			$url  = 'https://'. $this->slack_domain .'.slack.com/api/users.admin.invite?t='.time();
			$args = array(
				'body' => array(
					'email'      => $email,
		            'first_name' => $fname,
					'last_name'  => $lname,
		            'token'      => $this->slack_api_key,
		            'set_active' => true,
		            '_attempts'  => '1',
				),
			);
			$response = wp_remote_post( $url, $args );
			if ( true === $response ) {
				update_post_meta( $post_id, '_slack_created', true );
				$this->notify( $email . 'invited' );
			} else {
				update_post_meta( $post_id, '_slack_created', false );
				$this->notify( $email . 'couldn\'t be invited' );
			}
			return $response;
		}
	}

	// (string) $message - message to be passed to Slack
	// (string) $room - room in which to write the message, too
	// (string) $icon - You can set up custom emoji icons to use with each message
	public static function notify($message, $room = "engineering", $icon = ":longbox:") {
        $room = ($room) ? $room : "engineering";
        $data = "payload=" . json_encode(array(
                "channel"       =>  "#{$room}",
                "text"          =>  $message,
                "icon_emoji"    =>  $icon
            ));

		// You can get your webhook endpoint from your Slack settings
        $ch = curl_init("https://hooks.slack.com/services/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");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
