<?php
extract(shortcode_atts(array(
  'type' => 'oaldr_position',
  'order' => 'DESC',
  'orderby' => 'menu_order',
  'posts' => -1,
  'offset' => 0,
), $atts));
$term_args = array(
  'parent' => '0',
  );
 $terms = get_terms('oaldr_group', $term_args);
 if (!empty($terms) && !is_wp_error($terms)) {
     ?>
  <div class="oaldr-filter-buttons">
    <div class="group">
      <label>Group</label>
      <a class="filter" data-filter="all" href="#">All</a>
      <?php
      foreach ($terms as $term) {
          ?>
        <a class="filter" data-filter=".group-<?php echo $term->slug;
          ?>" href="#"><?php echo $term->name;
          ?></a>
        <?php
      }
     ?>
    </div>
    <div class="group">
      <label>Sort</label>
      <a class="sort active" data-sort="default" href="#">by Position</a>
      <a class="sort" data-sort="lname:asc" href="#">by Last Name</a>
    </div>
  </div>

  <?php

 }
