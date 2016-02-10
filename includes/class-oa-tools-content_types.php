<?php

/**
 * Register post types and taxonomies.
 *
 * @link       http://mckernan.in
 * @since      1.0.0
 */

class OA_Tools_Content_Types
{
    /**
     * Constructor.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        add_action('init', array($this, 'post_type_person'), 0);
        add_action('init', array($this, 'post_type_position'), 0);
        add_action('init', array($this, 'taxonomy_group'), 0);
        $this->taxonomy_register();
    }

    /**
     * Register the person post type.
     *
     * @since    1.0.0
     */
    public function post_type_person()
    {
        $labels = array(
            'name' => 'People',
            'singular_name' => 'Person',
            'menu_name' => 'People',
            'parent_item_colon' => 'Parent Item:',
            'all_items' => 'All People',
            'view_item' => 'View Person',
            'add_new_item' => 'Add Person',
            'add_new' => 'Add Person',
            'edit_item' => 'Edit Person',
            'update_item' => 'Update Person',
            'search_items' => 'Search People',
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash',
        );
        $args = array(
            'label' => 'people',
            'description' => 'People ',
            'labels' => $labels,
            'supports' => array('title', 'thumbnail'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-users',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'rewrite' => false,
            'capability_type' => 'page',
        );
        register_post_type('oaldr_person', $args);
    }

    /**
     * Register the position post type.
     *
     * @since    1.0.0
     */
    public function post_type_position()
    {
        $labels = array(
            'name' => 'Positions',
            'singular_name' => 'Position',
            'menu_name' => 'Positions',
            'parent_item_colon' => 'Parent Item:',
            'all_items' => 'All Positions',
            'view_item' => 'View Position',
            'add_new_item' => 'Add Position',
            'add_new' => 'Add Position',
            'edit_item' => 'Edit Position',
            'update_item' => 'Update Position',
            'search_items' => 'Search Position',
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash',
        );
        $args = array(
            'label' => 'position',
            'description' => 'Positions',
            'labels' => $labels,
            'supports' => array('title', 'thumbnail'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-groups',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'rewrite' => false,
            'capability_type' => 'page',
        );
        register_post_type('oaldr_position', $args);
    }
    public function taxonomy_group()
    {
        $labels = array(
                'name' => _x('Groups', 'Taxonomy General Name', 'text_domain'),
                'singular_name' => _x('Group', 'Taxonomy Singular Name', 'text_domain'),
                'menu_name' => __('Group', 'text_domain'),
                'all_items' => __('All Groups', 'text_domain'),
                'parent_item' => __('Parent Group', 'text_domain'),
                'parent_item_colon' => __('Parent Group:', 'text_domain'),
                'new_item_name' => __('New Group Name', 'text_domain'),
                'add_new_item' => __('Add New Group', 'text_domain'),
                'edit_item' => __('Edit Group', 'text_domain'),
                'update_item' => __('Update Group', 'text_domain'),
                'separate_items_with_commas' => __('Separate groups with commas', 'text_domain'),
                'search_items' => __('Search Group', 'text_domain'),
                'add_or_remove_items' => __('Add or remove groups', 'text_domain'),
                'choose_from_most_used' => __('Choose from the most used groups', 'text_domain'),
                'not_found' => __('Not Found', 'text_domain'),
            );
        $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
            );
        register_taxonomy('oaldr_group', array('oaldr_position'), $args);
    }

    public function taxonomy_chapter()
    {
        $labels = array(
                'name' => _x('Chapters', 'Taxonomy General Name', 'text_domain'),
                'singular_name' => _x('Chapter', 'Taxonomy Singular Name', 'text_domain'),
                'menu_name' => __('Chapter', 'text_domain'),
                'all_items' => __('All Chapters', 'text_domain'),
                'parent_item' => __('Parent Chapter', 'text_domain'),
                'parent_item_colon' => __('Parent Chapter:', 'text_domain'),
                'new_item_name' => __('New Chapter Name', 'text_domain'),
                'add_new_item' => __('Add New Chapter', 'text_domain'),
                'edit_item' => __('Edit Chapter', 'text_domain'),
                'update_item' => __('Update Chapter', 'text_domain'),
                'separate_items_with_commas' => __('Separate groups with commas', 'text_domain'),
                'search_items' => __('Search Chapter', 'text_domain'),
                'add_or_remove_items' => __('Add or remove groups', 'text_domain'),
                'choose_from_most_used' => __('Choose from the most used groups', 'text_domain'),
                'not_found' => __('Not Found', 'text_domain'),
            );
        $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
            );
        register_taxonomy('oaldr_chapter', array('oaldr_person'), $args);
    }

    public function taxonomy_lodge()
    {
        $labels = array(
                'name' => _x('Lodges', 'Taxonomy General Name', 'text_domain'),
                'singular_name' => _x('Lodge', 'Taxonomy Singular Name', 'text_domain'),
                'menu_name' => __('Lodge', 'text_domain'),
                'all_items' => __('All Lodges', 'text_domain'),
                'parent_item' => __('Parent Lodge', 'text_domain'),
                'parent_item_colon' => __('Parent Lodge:', 'text_domain'),
                'new_item_name' => __('New Lodge Name', 'text_domain'),
                'add_new_item' => __('Add New Lodge', 'text_domain'),
                'edit_item' => __('Edit Lodge', 'text_domain'),
                'update_item' => __('Update Lodge', 'text_domain'),
                'separate_items_with_commas' => __('Separate groups with commas', 'text_domain'),
                'search_items' => __('Search Lodge', 'text_domain'),
                'add_or_remove_items' => __('Add or remove groups', 'text_domain'),
                'choose_from_most_used' => __('Choose from the most used groups', 'text_domain'),
                'not_found' => __('Not Found', 'text_domain'),
            );
        $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
            );
        register_taxonomy('oaldr_lodge', array('oaldr_person'), $args);
    }

    public function taxonomy_section()
    {
        $labels = array(
                'name' => _x('Sections', 'Taxonomy General Name', 'text_domain'),
                'singular_name' => _x('Section', 'Taxonomy Singular Name', 'text_domain'),
                'menu_name' => __('Section', 'text_domain'),
                'all_items' => __('All Sections', 'text_domain'),
                'parent_item' => __('Parent Section', 'text_domain'),
                'parent_item_colon' => __('Parent Section:', 'text_domain'),
                'new_item_name' => __('New Section Name', 'text_domain'),
                'add_new_item' => __('Add New Section', 'text_domain'),
                'edit_item' => __('Edit Section', 'text_domain'),
                'update_item' => __('Update Section', 'text_domain'),
                'separate_items_with_commas' => __('Separate groups with commas', 'text_domain'),
                'search_items' => __('Search Section', 'text_domain'),
                'add_or_remove_items' => __('Add or remove groups', 'text_domain'),
                'choose_from_most_used' => __('Choose from the most used groups', 'text_domain'),
                'not_found' => __('Not Found', 'text_domain'),
            );
        $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
            );
        register_taxonomy('oaldr_section', array('oaldr_person'), $args);
    }

    public function taxonomy_register()
    {
        // Pick used category via Customizer
            $selected_taxonomy = get_theme_mod('oaldr_categorize_positions');

        if ($selected_taxonomy == 'oaldr_lodge') {
            add_action('init', array($this, 'taxonomy_lodge'), 0);
        } elseif ($selected_taxonomy == 'oaldr_section') {
            add_action('init', array($this, 'taxonomy_section'), 0);
        } else {
            add_action('init', array($this, 'taxonomy_chapter'), 0);
        }
    }
}
