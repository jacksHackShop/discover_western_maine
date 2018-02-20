<?php

/*
* Title                   : Pinpoint Booking System WordPress Plugin (PRO)
* Version                 : 2.1.1
* File                    : views/search/views-backend-search-results-list.php
* File Version            : 1.0.1
* Created / Last Modified : 25 August 2015
* Author                  : Dot on Paper
* Copyright               : © 2012 Dot on Paper
* Website                 : http://www.dotonpaper.net
* Description             : Back end search results list views class.
*/

    if (!class_exists('DOPBSPViewsFrontEndSearchResultsList')){
        class DOPBSPViewsFrontEndSearchResultsList extends DOPBSPViewsFrontEndSearchResults{
            /*
             * Constructor
             */
            function __construct(){
            }
            
            /*
             * Returns search results list.
             * 
             * @param args (array): function arguments
             *                      * calendars (array): list of calendars
             * 
             * @return search results list HTML
             */
            function template($args = array()){
                global $DOPBSP;
                
                $calendars = $args['calendars'];
                $page = $args['page'];
                $results = $args['results'];
?>
                <ul class="dopbsp-list">
<?php              
                if (count($calendars) > 0){
                    for ($i=($page-1)*$results; $i<($page*$results > count($calendars) ? count($calendars):$page*$results); $i++){
                        $this->item($calendars[$i]);
                    }
                }
                else{         
?>
                    <li class="dopbsp-no-data"><?php echo $DOPBSP->text('SEARCH_FRONT_END_NO_AVAILABILITY'); ?></li>
<?php
                }
?>
                </ul>
<?php 
                $this->pagination(array('no' => count($calendars),
                                        'page' => $page,
                                        'results' => $results));
            }
            
            function item($calendar){
                global $DOPBSP;
                $post = get_post($calendar->post_id);
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($calendar->post_id), 'medium');
                $check_in = $_POST['check_in'] != '' ? '?check_in='.$_POST['check_in']:'';
                $check_out = $_POST['check_out'] != '' ? '&check_out='.$_POST['check_out']:'';
                $start_hour = $_POST['start_hour'] != '' ? '&start_hour='.$_POST['start_hour']:'';
                $end_hour = $_POST['end_hour'] != '' ? '&end_hour='.$_POST['end_hour']:'';
                $no_items = $_POST['no_items'] != '' ? '&no_items='.$_POST['no_items']:'';
                $language = '';
                
                if(defined('ICL_LANGUAGE_CODE')) {
                    
                    if($check_in != '') {
                        $language = '&lang='.ICL_LANGUAGE_CODE;
                    } else {
                        $language = '?lang='.ICL_LANGUAGE_CODE;
                    }
                }
                
                if(isset($_GET['lang'])) {
                    
                    if($check_in != '') {
                        $language = '&lang='.$_GET['lang'];
                    } else {
                        $language = '?lang='.$_GET['lang'];
                    }
                }
?>
                <li>
                    <!--
                        Image
                    -->
                    <div class="dopbsp-image">
                        <a href="<?php echo get_permalink($calendar->post_id).$check_in.$check_out.$start_hour.$end_hour.$no_items.$language; ?>" target="_parent" style="background-image: url(<?php echo $image[0]; ?>);">
                            <img src="<?php echo $image[0]; ?>" alt="<?php echo $calendar->name; ?>" title="<?php echo $calendar->name; ?>" />
                        </a>
                    </div>

                    <!--
                        Content
                    -->
                    <div class="dopbsp-content">
                        <!--
                            Title
                        -->
                        <h3>
                            <a href="<?php echo get_permalink($calendar->post_id).$check_in.$check_out.$start_hour.$end_hour.$no_items.$language; ?>" target="_parent"><?php echo $calendar->name; ?></a>
                        </h3>

                        <!--
                            Address
                        -->
                        <div class="dopbsp-address"><?php echo $calendar->address_alt == '' ? $calendar->address:$calendar->address_alt; ?></div>

                        <!--
                            Price
                        -->
                        <div class="dopbsp-price-wrapper">
                            <?php printf($DOPBSP->text('SEARCH_FRONT_END_RESULTS_PRICE'), '<span class="dopbsp-price">'.($DOPBSP->classes->price->set($calendar->price_min,
                                                                                                                                                      $DOPBSP->classes->currencies->get($calendar->currency),
                                                                                                                                                      $calendar->currency_position)).'<span>'); ?>
                        </div>

                        <!--
                            Text
                        -->
                        <div class="dopbsp-text">
                            <?php 
                                $description = $post->post_excerpt == '' ? wp_strip_all_tags(strip_shortcodes($post->post_content)):wp_strip_all_tags(strip_shortcodes($post->post_excerpt)); 
                                $description = preg_replace("/\[([^\[\]]++|(?R))*+\]/", "", $description);
                                echo $description;
                            ?>
                        </div>

                        <!--
                            View
                        -->
                        <a href="<?php echo get_permalink($calendar->post_id).$check_in.$check_out.$start_hour.$end_hour.$no_items.$language; ?>" target="_parent" class="dopbsp-view"><?php echo $DOPBSP->text('SEARCH_FRONT_END_RESULTS_VIEW'); ?></a>
                    </div>
                </li>
<?php
            }
        }
    }