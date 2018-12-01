<?php

/*
* Title                   : Pinpoint Booking System WordPress Plugin (PRO)
* Version                 : 2.1.1
* File                    : includes/translation/class-translation-text-themes.php
* File Version            : 1.0.4
* Created / Last Modified : 25 August 2015
* Author                  : Dot on Paper
* Copyright               : © 2012 Dot on Paper
* Website                 : http://www.dotonpaper.net
* Description             : Themes translation text PHP class.
*/

    if (!class_exists('DOPBSPTranslationTextThemes')){
        class DOPBSPTranslationTextThemes{
            /*
             * Constructor
             */
            function __construct(){
                /*
                 * Initialize themes text.
                 */
                add_filter('dopbsp_filter_translation_text', array(&$this, 'themes'));
            }

            /*
             * Themes text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function themes($text){
                array_push($text, array('key' => 'PARENT_THEMES',
                                        'parent' => '',
                                        'text' => 'Themes'));
                
                array_push($text, array('key' => 'THEMES_TITLE',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Themes'));
                array_push($text, array('key' => 'THEMES_HELP',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'A collection of themes specially created to be used with the Pinpoint Booking System. PRO version is included with each.'));
                
                array_push($text, array('key' => 'THEMES_LOAD_SUCCESS',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Themes list loaded.'));
                array_push($text, array('key' => 'THEMES_LOAD_ERROR',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Themes list failed to load. Please refresh the page to try again.'));
                
                array_push($text, array('key' => 'THEMES_FILTERS_SEARCH',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Search'));
                array_push($text, array('key' => 'THEMES_FILTERS_SEARCH_TERMS',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Enter search terms'));
                array_push($text, array('key' => 'THEMES_FILTERS_TAGS',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Tags'));
                array_push($text, array('key' => 'THEMES_FILTERS_TAGS_ALL',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'All'));
                
                array_push($text, array('key' => 'THEMES_THEME_PRICE',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Price:'));
                array_push($text, array('key' => 'THEMES_THEME_GET_IT_NOW',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'Get it now'));
                array_push($text, array('key' => 'THEMES_THEME_VIEW_DEMO',
                                        'parent' => 'PARENT_THEMES',
                                        'text' => 'View demo'));
                
                return $text;
            }
        }
    }