<?php

/**
 * This is the template that renders the testimonial block.
 *
 * @param   array $block The block settings and attributes.
 * @param   bool $is_preview True during AJAX preview.
 */

 global $pakb_loop, $pakb_helper;

 echo '<div class="uk-margin-medium-bottom">';
 echo $pakb_helper->the_search();
 echo '</div>';
