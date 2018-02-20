<?php

/*
 * Title                   : Pinpoint World
 * File                    : application/config/config-views.php
 * Author                  : Dot on Paper
 * Copyright               : © 2017 Dot on Paper
 * Website                 : https://www.dotonpaper.net
 * Description             : Views config file. The file is mandatory.
 */

global $dot_css;
global $dot_js;

$dot_css = array('beta' => array('https://fonts.googleapis.com/icon?family=Material+Icons',
				 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic'),
		 'live' => array('https://fonts.googleapis.com/icon?family=Material+Icons',
				 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic'),
		 'page' => array('framework/assets/gui/css/reset.css',
				 'application/assets/gui/css/style-font-businesses.css',
				 'application/assets/gui/css/style-font-pw.css',
				 'application/assets/gui/css/style.css',
				 'application/assets/gui/css/style-nav.css',
				 'application/assets/gui/css/style-header.css',
				 'application/assets/gui/css/style-footer.css'));

$dot_js = array('beta' => array('https://code.jquery.com/jquery-latest.min.js'),
		'live' => array('https://code.jquery.com/jquery-latest.min.js'),
		'page' => array('application/assets/js/main.php'));