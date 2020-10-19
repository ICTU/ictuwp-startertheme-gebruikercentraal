<?php

//========================================================================================================

add_filter( 'em_event_output_placeholder', 'gc_eventmanager_add_placeholders', 1, 3 );

/*
 * Deze functie breidt de placeholder-functionaliteit van de Event Manager uit.
 * Een overzicht van bestaande placeholders vind je op
 * https://wp-events-plugin.com/documentation/placeholders/
 *
 * #_GCATTENDEELIST wordt gebruikt voor een single event. Voor de inhoud van een single
 * event zie:
 * [admin] > Evenementen > Instellingen > tab:Opmaak > section:Enkele Evenementpagina
 */

function gc_eventmanager_add_placeholders( $replace, $EM_Event, $result ) {

	global $EM_Event;

	switch ( $result ) {

		case '#_GCATTENDEELIST':

			$return      = '';
			$EM_Bookings = $EM_Event->get_bookings();

			if ( count( $EM_Bookings->bookings ) > 0 ) {

				$guest_bookings       = get_option( 'dbem_bookings_registration_disable' );
				$guest_booking_user   = get_option( 'dbem_bookings_registration_user' );
				$bookings_total       = count( $EM_Bookings->bookings );
				$nr_anon_bookings     = 0;
				$usercounter          = 0;
				$confirmedusercounter = 0;
				$nonanon_userlist     = [];
				$check_if_exists      = array();


				// Check of bij de laatste reservering het veld 'show_name_attendeelist' aanwezig is.
				// Dit gaat expliciet om de laatste aanmelding, want als we deze check zouden uitvoeren op de eerste
				// aanmelding dan komen wijzigingen achteraf niet door. Als bij het laatste record dit veld WEL aanwezig is,
				// dan is het blijkbaar WEL de bedoeling om de deelnemerlijst te tonen.
				// Als dat veld NIET aanwezig is, concluderen we dus maar voor het gemak dat de aanwezigenlijst NIET getoond hoeft te worden
				$lastbooking = $EM_Bookings->bookings[ ( $bookings_total - 1 ) ];

				if ( ! isset( $lastbooking->booking_meta['booking']['show_name_attendeelist'] ) ) {
					// blijkbaar zit het veld show_name_attendeelist niet in de form fields,
					// dus we hoeven de hele aanwezigenlijst sowieso niet te tonen
					// verder doen we niks
				} else {
					// het veld zit wel in de formfields, dus we gaan door alle inschrijvingen heen om de namenlijst te construeren

					foreach ( $EM_Bookings as $EM_Booking ) {

						$boookinginfo = [];
						if ( isset( $EM_Booking->meta['booking'] ) ) {
							$boookinginfo = $EM_Booking->meta['booking'];
						}
						$name = '';
						$usercounter ++;

						if ( $EM_Booking->booking_status == 1 ) {

							$confirmedusercounter ++;

							if ( $guest_bookings && $EM_Booking->get_person()->ID == $guest_booking_user ) {

								$thename = gc_eventmanager_get_extended_bookingpersonname( $EM_Booking );

								if ( $thename ) {
									$nonanon_userlist[] = $thename;
								} else {
									$nr_anon_bookings ++;
								}
							} else {
								if ( ! in_array( $EM_Booking->get_person()->ID, $check_if_exists ) ) {

									$thename = gc_eventmanager_get_extended_bookingpersonname( $EM_Booking );

									$check_if_exists[ $EM_Booking->get_person()->ID ] = $EM_Booking->get_person()->ID;

									if ( $thename ) {
										$nonanon_userlist[] = $thename;
									} else {
										$nr_anon_bookings ++;
									}
								}
							}
						}
					}

					$attendeecounter = sprintf( _n( '%s attendee', '%s attendees', $confirmedusercounter, 'gctheme' ), $confirmedusercounter );

					if ( $nr_anon_bookings > 0 ) {
						// some users prefer not to be listed on the attendeeslist
						$attendeecounter .= ' (' . sprintf( _n( '%s attendee not shown', '%s attendees not shown', $nr_anon_bookings, 'gctheme' ), $nr_anon_bookings ) . ')';
					}

					if ( $nonanon_userlist ) {
						// er zijn items toegevoegd aan de lijst $nonanon_userlist, i.e. er zijn deelnemers die het goed vinden dat hun naam getoond wordt

						$return = '<div class="attendees-list" id="attendeeslist">';
						$return .= '<h2>' . __( 'Other attendees', 'gctheme' ) . '<span class="event-aanmeldingen">' . $attendeecounter . '</span></h2>';
						$return .= '<ul class="attendees-list__list">';
						foreach ( $nonanon_userlist as $name ) {
							if ( $name ) {
								$return .= '<li class="person">' . $name . '</li>';
							}
						}
						$return .= '</ul>';
						$return .= '</div>';

					}
				}

				return $return;

			} else {
				return '';
			}
			break;

	}

	return $replace;

}

//========================================================================================================
/*
 * deze functie retourneert een naam en eventuele socialmedia-links voor iemand die
 * zich inschrijft voor een event
 */

function gc_eventmanager_get_extended_bookingpersonname( $theobject ) {

	$socialmedia  = '';
	$returnstring = '';
	$name         = '';
	$bookinginfo  = '';

	if ( $theobject ) {

		$bookinginfo = [];
		if ( isset( $theobject->meta['booking'] ) ) {
			$bookinginfo = $theobject->meta['booking'];
		}
		$countryinfo = $theobject->get_person()->custom_user_fields['dbem_country'];

		if ( isset( $bookinginfo['show_name_attendeelist'] ) && ( $bookinginfo['show_name_attendeelist'] !== '0' ) ) {

			if ( $theobject->get_person()->get_name() ) {
				$name = $theobject->get_person()->get_name();
			} else {
				$user_id   = $theobject->get_person()->ID;
				$user_info = get_userdata( $user_id );
				if ( $user_info->display_name ) {
					$name = $user_info->display_name;
				} elseif ( $user_info->user_nicename ) {
					$name = $user_info->user_nicename;
				} elseif ( $user_info->first_name || $user_info->last_name ) {
					$name = $user_info->first_name . ' ' . $user_info->last_name;
				}
			}

			if ( $name ) {

				$listitemcount = 0;
				$returnstring  = '<span itemprop="attendee" itemscope itemtype="http://schema.org/Person"><span itemprop="name">' . $name . '</span>';
				$xtra          = '';


				if ( isset( $bookinginfo['organisation'] ) && trim( $bookinginfo['organisation'] ) ) {
					$xtra = '<span itemprop="memberOf" class="additionalinfo">' . esc_html( trim( $bookinginfo['organisation'] ) ) . '</span>';
				}

				if ( $countryinfo['value'] && $countryinfo['value'] != 'none selected' ) {
					if ( $xtra !== '' ) {
						$xtra .= ', ';
					}
					$xtra .= '<span class="additionalinfo" itemprop="nationality">' . esc_html( $countryinfo['value'] ) . '</span>';
				}

				if ( $xtra ) {
					$returnstring .= $xtra;
				}

				$returnstring .= '</span>';


				if ( isset( $bookinginfo['linkedin_profile'] ) && trim( $bookinginfo['linkedin_profile'] ) ) {
					if ( ! filter_var( $bookinginfo['linkedin_profile'], FILTER_VALIDATE_URL ) === false ) {
						$socialmedia .= '<li class="social-links__item"><a href="' . $bookinginfo['linkedin_profile'] . '" class="link link--social linkedin" itemprop="url"><span class="visuallyhidden">' . __( 'LinkedIn-profiel', 'gctheme' ) . ' van ' . esc_html( $theobject->get_person()->get_name() ) . '</span></a></li>';
						$listitemcount ++;
					}
				}

				if ( isset( $bookinginfo['twitter_handle'] ) && trim( $bookinginfo['twitter_handle'] ) ) {
					$socialmedia .= '<li class="social-links__item"><a href="' . GC_TWITTER_URL . sanitize_title( $bookinginfo['twitter_handle'] ) . '" class="link link--social twitter" itemprop="url"><span class="visuallyhidden">' . __( 'Twitter-account', 'gctheme' ) . ' van ' . esc_html( $theobject->get_person()->get_name() ) . '</span></a></li>';
					$listitemcount ++;
				}

				if ( $socialmedia ) {
					$returnstring .= '<ul class="social-links" data-listitemcount="' . $listitemcount . '">' . $socialmedia . '</ul>';
				}
			}
		}
	}

	return $returnstring;

}

//========================================================================================================


