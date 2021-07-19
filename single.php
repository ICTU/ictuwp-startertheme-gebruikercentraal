<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();
$post = Timber::query_post();
$context['post'] = $post;


if ('ja' === get_field('downloads_tonen') && get_field('download_items')) {

  $context['downloads'] = download_block_get_data();

}

if ('ja' === get_field('gerelateerde_content_toevoegen')) {

  $context['related'] = related_block_get_data();

}

if ('ja' === get_field('links_tonen')) {

  $context['links'] = links_block_get_data();

}

$spotlightblocks = spotlight_block_get_data();

if ($spotlightblocks) {

  $context['spotlight'] = $spotlightblocks;

}

// Get the hero image
$context['hero_image'] = get_hero_image();
$context['type'] = $post->post_type;

function is_gutenberg() {
  $timber_post = Timber::query_post();

  if (function_exists('has_blocks') && has_blocks($timber_post->ID)) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

$context['is_gutenberg'] = is_gutenberg() ? 'TRUE' : 'FALSE';

if ('post' === $post->post_type) {

  // de 'post_date' wordt enigszins getoond in de date-badge, zonder jaar
  // dateformatting is d-m-Y om ervoor te zorgen dat de datum
  // als een geldige datum begrepen wordt in date-bate.html.twig
  $context['post_date'] = get_the_time('d-m-Y', get_the_id());


  // publicatiedatum voor berichten. Is met name van belang voor oudere berichten
  $context['meta'][] = [
    'title' => _x('Publish date', 'Meta: value voor publicatiedatum', 'gctheme'),
    'classname' => 'datum',
    'descr' => get_the_time(get_option('date_format'), get_the_id()),
    // hier gebruiken we dateformatting uit de instellingen
  ];

  // auteur vermelden
  $author_archive_link = get_author_posts_url($timber_post->post_author);
  $context['meta'][] = [
    'classname' => 'auteur',
    'title' => _x('Author', 'Meta: value voor auteur', 'gctheme'),
    'descr' => get_the_author_meta('display_name', $timber_post->post_author),
    'url' => $author_archive_link,
  ];

  $cat_for_post = get_the_terms(get_the_id(), 'category');
  $categories = [];
  if ($cat_for_post && !is_wp_error($cat_for_post)) {
    foreach ($cat_for_post as $term) {
      $categories[] = '<a href="' . get_term_link($term) . '">' . $term->name . '</a>';
    }
  }
  if ($categories) {
    $context['meta'][] = [
      'title' => _x('Category', 'Meta: value voor categorie', 'gctheme'),
      'classname' => 'category',
      'descr' => implode(', ', $categories),
    ];
  }
  /*
   * datzelfde truukje zouden we ook kunnen doen voor de tags bij een bericht.
   * maar dat is te veel van het goede
    $tag_for_post = get_the_terms( get_the_id(), 'post_tag' );
    $tags         = array();
    if ( $tag_for_post && ! is_wp_error( $tag_for_post ) ) {
      foreach ( $tag_for_post as $term ) {
        $tags[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
      }
    }
    if ( $tags ) {
      $context['meta'][] = [
        'title' => _x( 'Tag', 'Meta: value voor tag', 'gctheme' ),
        'classname' => 'tag',
        'descr' => implode( ', ', $tags ),
      ];
    }
   */


}
elseif ('event' === $post->post_type) {

  $EM_Event = em_get_event($post->ID, 'post_id');
  $EM_Bookings = $EM_Event->get_bookings();

  $event_start_datetime = strtotime($EM_Event->event_start_date . ' ' . $EM_Event->event_start_time);
  $event_end_datetime = strtotime($EM_Event->event_end_date . ' ' . $EM_Event->event_end_time);

  $context['start_date'] = $event_start_datetime;
  $context['end_date'] = $event_end_datetime;


  // als start-datum en eindatum op dezelfde dag
  if (date_i18n(get_option('date_format'), $context['start_date']) === date_i18n(get_option('date_format'), $context['end_date'])) {
    // dan start- en eindtijd tonen
    $eventtimes = sprintf(_x('%s - %s', 'Meta voor event: label voor start- en eindtijd', 'gctheme'), date_i18n(get_option('time_format'), $event_start_datetime), date_i18n(get_option('time_format'), $event_end_datetime));

    $context['meta'][] = [
      'title' => _x('Event date', 'Meta: value voor evenementdatum', 'gctheme'),
      'classname' => 'datum',
      'descr' => date_i18n(get_option('date_format'), $event_start_datetime),
    ];

    $context['meta'][] = [
      'classname' => 'times',
      'title' => _x('Times', 'Meta voor event: value voor start- en eindtijd', 'gctheme'),
      'descr' => $eventtimes,
    ];
  }
  else {
    $eventdates = sprintf(_x('%s - %s', 'Meta voor event: label voor start- en eindtijd', 'gctheme'), date_i18n(get_option('date_format'), $event_start_datetime), date_i18n(get_option('date_format'), $event_end_datetime));
    $context['meta'][] = [
      'title' => _x('Event date', 'Meta: value voor evenementdatum', 'gctheme'),
      'classname' => 'datum',
      'descr' => $eventdates,
    ];

  }

  if (($EM_Event->get_bookings()
        ->get_available_spaces() <= 0) && ($EM_Event->get_bookings()->tickets->tickets)) {
    // heeft mogelijkheid tot reserveren, maar alle plekken zijn bezet
    $item['full'] = _x('Fully booked', 'Meta voor event: value voor geen plek meer beschikbaar', 'gctheme');
    $context['meta'][] = [
      'classname' => 'aanmeldingen',
      'title' => _x('Availability', 'Meta voor event: label voor geen plek meer beschikbaar', 'gctheme'),
      'descr' => _x('Fully booked', 'Meta voor event: value voor geen plek meer beschikbaar', 'gctheme'),
    ];
  }

  if ('url' === $EM_Event->event_location_type) {
    $context['meta'][] = [
      'classname' => 'location',
      'title' => _x('Location', 'Meta voor event: label voor locatie', 'gctheme'),
      'descr' => _x('Online', 'Meta voor event: label voor online', 'gctheme'),
    ];
  }
  elseif ($EM_Event->location_id) {
    // dit ding heeft een locatie
    $lcatie = $EM_Event->output('#_LOCATIONNAME');

    $context['meta'][] = [
      'classname' => 'location',
      'title' => _x('Location', 'Meta voor event: label voor locatie', 'gctheme'),
      'descr' => $lcatie,
    ];
  }

  if (count($EM_Bookings->bookings) > 0) {

    $context['meta'][] = [
      'classname' => 'registrations',
      'title' => _x('Aanmeldingen', 'Meta voor event: label voor aanmeldingen', 'gctheme'),
      'descr' => sprintf(_n('%s attendee', '%s attendees', count($EM_Bookings->bookings), 'gctheme'), count($EM_Bookings->bookings)),
    ];
  }

}


if (post_password_required($timber_post->ID)) {
  Timber::render('single-password.twig', $context);
}
else {
  Timber::render([
    'single-' . $timber_post->post_type . '.twig',
    'single-' . $timber_post->slug . '.twig',
    'single.twig',
  ], $context);
}

function attendeelist_get_the_bookingpersonname($theobject) {

  $socialmedia = '';
  $returnstring = '';
  $name = '';
  $bookinginfo = '';

  if ($theobject) {

    $bookinginfo = [];
    if (isset($theobject->meta['booking'])) {
      $bookinginfo = $theobject->meta['booking'];
    }
    $countryinfo = $theobject->get_person()->custom_user_fields['dbem_country'];

    if (isset($bookinginfo['show_name_attendeelist']) && ($bookinginfo['show_name_attendeelist'] !== '0')) {

      if ($theobject->get_person()->get_name()) {
        $name = $theobject->get_person()->get_name();
      }
      else {
        $user_id = $theobject->get_person()->ID;
        $user_info = get_userdata($user_id);
        if ($user_info->display_name) {
          $name = $user_info->display_name;
        }
        elseif ($user_info->user_nicename) {
          $name = $user_info->user_nicename;
        }
        elseif ($user_info->first_name || $user_info->last_name) {
          $name = $user_info->first_name . ' ' . $user_info->last_name;
        }
      }

      if ($name) {

        $listitemcount = 0;
        $returnstring = '<span itemprop="name">' . $name . '</span>';
        $xtra = '';

        if (isset($bookinginfo['organisation']) && trim($bookinginfo['organisation'])) {
          $xtra = '<span itemprop="memberOf" class="additionalinfo">' . esc_html(trim($bookinginfo['organisation'])) . '</span>';
        }

        if ($countryinfo['value'] && ($countryinfo['value'] != 'none selected')) {
          $xtra .= '<span class="additionalinfo" itemprop="nationality">' . esc_html($countryinfo['value']) . '</span>';
        }

        if ($xtra) {
          $returnstring .= '<br>' . $xtra;
        }

        if (isset($bookinginfo['linkedin_profile']) && trim($bookinginfo['linkedin_profile'])) {
          if (!filter_var($bookinginfo['linkedin_profile'], FILTER_VALIDATE_URL) === FALSE) {
            $socialmedia .= '<li><a href="' . $bookinginfo['linkedin_profile'] . '" class="linkedin" title="' . __('LinkedIn-profiel', 'gctheme') . ' van ' . esc_html($theobject->get_person()
                ->get_name()) . '" itemprop="url"><span class="visuallyhidden">' . __('LinkedIn-profiel', 'gctheme') . '</span></a></li>';
            $listitemcount++;
          }
        }

        if (isset($bookinginfo['twitter_handle']) && trim($bookinginfo['twitter_handle'])) {
          $socialmedia .= '<li><a href="' . GC_TWITTER_URL . sanitize_title($bookinginfo['twitter_handle']) . '" class="twitter" title="' . __('Twitter-account', 'gctheme') . ' van ' . esc_html($theobject->get_person()
              ->get_name()) . '" itemprop="url"><span class="visuallyhidden">' . __('Twitter-account', 'gctheme') . '</span></a></li>';
          $listitemcount++;
        }

        if ($socialmedia) {
          $returnstring = '<ul class="social-media" data-listitemcount="' . $listitemcount . '">' . $socialmedia . '</ul>' . $returnstring;
        }
      }
    }
  }

  return $returnstring;

}

//========================================================================================================
