<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */


$archive       = get_queried_object();
$taxonomy_name = $archive->taxonomy;
$context       = Timber::context();

// Set vars
$context['title'] = get_the_archive_title();

// If term archive
if ( isset( $context['archive_term'] ) && ! empty( $context['archive_term']['descr'] ) ) {
	$context['descr'] = $context['archive_term']['descr'];
}

$posts = new Timber\PostQuery();


// Set data for overview
$context['overview'] = [];

// Set data for overview
$i = 0;
foreach ( $posts as $post ) {
	$i ++;
	$items[ $i ] = prepare_card_content( $post );
}

$context['overview']['items']    = $items;
$context['overview']['template'] = 'card--tipkaart';
$context['overview']['modifier'] = '4col';


// Get all data from the term
$cat    = get_term( $archive->term_id );
$author = get_term_meta( $archive->term_id );

// Get fields
$image = get_field( 'tipgever_foto', $archive );

// Set up contact links
$contact = array();
$ci      = 0;

if ( ! empty( $author['tipgever_telefoonnummer'][0] ) ) {
	// niets dan cijfers, spatie en streepje in telefoonnummer
	$tipgever_telefoonnummer = trim( $author['tipgever_telefoonnummer'][0] );
	$tipgever_telefoonnummer = preg_replace( '/[^0-9- ]/', '', $tipgever_telefoonnummer );
	$telefoonnummer_link     = preg_replace( '/[^0-9]/', '', $tipgever_telefoonnummer );
	$contact['phone']        = array(
		'linktext'  => $tipgever_telefoonnummer,
		'href'      => 'tel:' . $telefoonnummer_link,
		'arialabel' => sprintf( _x( 'Call %s', 'Tipgever telefoonnummer', 'gctheme' ), $tipgever_telefoonnummer ),
		'icontitle' => _x( 'phone', 'Icon title', 'gctheme' )
	);
}

if ( ! empty( $author['tipgever_mail'][0] ) ) {
	$contact['email'] = array(
		'linktext'  => $author['tipgever_mail'][0],
		'href'      => 'mailto:' . $author['tipgever_mail'][0],
		'arialabel' => sprintf( _x( 'Mail %s', 'Tipgever mailadres', 'gctheme' ), $author['tipgever_mail'][0] ),
		'icontitle' => _x( 'email', 'Icon title', 'gctheme' )
	);
}

// Set author vars
$context['author']['title']    = $archive->name;
$context['author']['function'] = ( $author['tipgever_functietitel'][0] ? $author['tipgever_functietitel'][0] : '' );
$context['author']['image']    = ( $image ? $image['sizes']['medium'] : '' );
$context['author']['descr']    = ( $cat->description ? $cat->description : '' );
$context['author']['contact']  = ( $contact ? $contact : '' );

// Set overview
$fullname = explode( ' ', trim( $archive->name ) );

// Set 4 column grid for tipgevers. Default is col-3
$context['overview']['modifier'] = 'col-4';
$context['overview']['title']    = 'Tips van ' . $fullname[0];

/*
 *
echo '<pre>';
var_dump( $context['overview'] );
echo '</pre>';
 */

$templates = array(
	'archive-tip-tax.twig',
	'archive.twig',
	'index.twig',
);


Timber::render( $templates, $context );
