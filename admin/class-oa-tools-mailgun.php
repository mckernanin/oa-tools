<?php

/**
 * Class responsible for interfacing with the Mailgun API.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 */
require_once plugin_dir_path(dirname(__FILE__)).'includes/mailgun-api/vendor/autoload.php';
use Mailgun\Mailgun;

define('MAILGUN_API_KEY', 'key-9dd0f7a5bd5934306902fad84f538942');

class OA_Tools_Mailgun
{
    /**
     * Constructor.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
    }

    public function error_message($message)
    {
        echo '<div class="error notice">';
        echo '<p>';
        _e('OA Tools - '.$message, 'oa_tools');
        echo '</p>';
        echo '</div>';
    }

    public function get_list_members($listAddress)
    {
        try {
            # Instantiate the client.
             $mgClient = new Mailgun(MAILGUN_API_KEY);

             # Issue the call to the client.
             $result = $mgClient->get("lists/$listAddress/members", array());
            var_dump($result);
        } catch (Exception $e) {
            $this->error_message('The following error occured when trying to retrieve members from '.$listAddress.': '.$e->getMessage());
        }
    }

    public function get_lists()
    {
        try {
            # Instantiate the client.
             $mgClient = new Mailgun(MAILGUN_API_KEY);
             # Issue the call to the client.
             $result = $mgClient->get('lists', array());
             $lists = array();
             foreach ($result->http_response_body->items as $list) {
                 $lists[] = $list->address;
             }
             return $lists;
        } catch (Exception $e) {
            $this->error_message('The following error occured when trying to retrieve lists: '.$e->getMessage());
        }
    }

    public function create_list($address, $description)
    {
        try {
            # Instantiate the client.
             $mgClient = new Mailgun(MAILGUN_API_KEY);
             # Issue the call to the client.
             $result = $mgClient->post('lists', array(
                 'address' => $address,
             'description' => $description,
             ));
        } catch (Exception $e) {
            $this->error_message('The following error occured when trying to create '.$address.': '.$e->getMessage());
        }
    }

    public function add_list_member($listAddress, $address, $name)
    {
        try {
            # Instantiate the client.
             $mgClient = new Mailgun(MAILGUN_API_KEY);
             # Issue the call to the client.
             $result = $mgClient->post("lists/$listAddress/members", array(
                 'address' => $address,
                 'name' => $name,
             ));
        } catch (Exception $e) {
            $this->error_message('The following error occured when trying to add '.$address.' to '.$listAddress.': '.$e->getMessage());
        }
    }

    public function check_list_for_member($listAddress, $address)
    {
        try {
            # Instantiate the client.
             $mgClient = new Mailgun(MAILGUN_API_KEY);

             # Issue the call to the client.
             $result = $mgClient->get("lists/$listAddress/members/$address", array());
             return true;
        } catch (Exception $e) {
            $this->error_message('Address does not exist in list');
        }
    }

    public function position_save_action($post_id)
    {
        $position_email = get_field('position_email', $post_id);
        $person = get_field('person', $post_id);
        $person = $person[0];
        $title = get_the_title();
        $lists = $this->get_lists();
        if ( $position_email ) {
            if ( !in_array( $position_email, $lists ) ) {
                $this->create_list($position_email, $title);
            }
            $this->add_list_member( 'lec@list.tahosalodge.org', $position_email, $title );
        }
        if ($person) {
            $person_email = get_field('person_email', $person);
            $fname = get_field('first_name', $person);
            $lname = get_field('last_name', $person);
            $inList = $this->check_list_for_member( $position_email, $person_email );
            if (!$inList) {
                $this->add_list_member( $position_email, $person_email, $fname . ' ' . $lname );
            }
        }
    }

    public function person_save_action($post_id)
    {
        $fname = get_field('first_name', $post_id);
        $lname = get_field('last_name', $post_id);
        $person_email = get_field('person_email', $post_id);
        $parent_email = get_field('parent_email', $post_id);
        $title = get_the_title();
        if ($person_email) {
            $options = array(
                'post_type' => 'oaldr_position',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                      'key' => 'person',
                      'value' => $post_id,
                      'compare' => 'LIKE',
                    ),
                ),
            );
            $query = new WP_Query($options);
            foreach ($query->posts as $post) {
                $position_email = get_field('position_email', $post->ID);
                $inList = $this->check_list_for_member( $position_email, $person_email );
                if (!$inList) {
                    $this->add_list_member( $position_email, $person_email, $fname . ' ' . $lname );
                }
            }
        }
    }
}

// $oatools_mailgun = new OA_Tools_Mailgun();
// $oatools_mailgun->get_list_members('lec@list.tahosalodge.org');
// $lists = $oatools_mailgun->get_lists();
// $oatools_mailgun->create_list( 'lec@list.tahosalodge.org', 'Lodge Executive Committee' );
// $oatools_mailgun->add_list_member( 'lec@list.tahosalodge.org', 'bob.crume@gmail.com', 'Bob Crume' );
// $oatools_mailgun->check_list_for_member( 'lec@list.tahosalodge.org', 'kevin@luminary.ws' );
// var_dump($lists);
