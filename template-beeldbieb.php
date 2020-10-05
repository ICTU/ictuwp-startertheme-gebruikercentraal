<?php
/*
 * Template Name: Beeldbieb
 * Description: Bekijk Beeldbied
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


use App\GetFiles;

$source = getFiles::files($dir.'/files');

$context              = Timber::context();
$context['site_name']      = $timber_post;
$context['title']   = [];
$context['source'] = $source;

$dir = dirname(__FILE__);



$loader = new FilesystemLoader($dir.'/app/templates');
$twig = new Environment($loader, ['debug' => TRUE]);

$twig->addExtension(new \Twig\Extension\DebugExtension());



Timber::render( [ 'index.html.twig', 'page.twig' ], $context );
