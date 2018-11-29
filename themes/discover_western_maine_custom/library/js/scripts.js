/*
 * Bones Scripts File
 * Author: Eddie Machado
 *
 * This file should contain any js scripts you want to add to the site.
 * Instead of calling it in the header or throwing it inside wp_head()
 * this file will be called automatically in the footer so as not to
 * slow the page load.
 *
 * There are a lot of example functions and tools in here. If you don't
 * need any of it, just remove it. They are meant to be helpers and are
 * not required. It's your world baby, you can do whatever you want.
*/


/*
 * Get Viewport Dimensions
 * returns object with viewport dimensions to match css in width and height properties
 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
*/
function updateViewportDimensions() {
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
	return { width:x,height:y };
}
// setting the viewport width
var viewport = updateViewportDimensions();


/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
*/
var waitForFinalEvent = (function () {
	var timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) { uniqueId = "Don't call this twice without a uniqueId"; }
		if (timers[uniqueId]) { clearTimeout (timers[uniqueId]); }
		timers[uniqueId] = setTimeout(callback, ms);
	};
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;


/*
 * Here's an example so you can see how we're using the above function
 *
 * This is commented out so it won't work, but you can copy it and
 * remove the comments.
 *
 *
 *
 * If we want to only do it on a certain page, we can setup checks so we do it
 * as efficient as possible.
 *
 * if( typeof is_home === "undefined" ) var is_home = $('body').hasClass('home');
 *
 * This once checks to see if you're on the home page based on the body class
 * We can then use that check to perform actions on the home page only
 *
 * When the window is resized, we perform this function
 * $(window).resize(function () {
 *
 *    // if we're on the home page, we wait the set amount (in function above) then fire the function
 *    if( is_home ) { waitForFinalEvent( function() {
 *
 *	// update the viewport, in case the window size has changed
 *	viewport = updateViewportDimensions();
 *
 *      // if we're above or equal to 768 fire this off
 *      if( viewport.width >= 768 ) {
 *        console.log('On home page and window sized to 768 width or more.');
 *      } else {
 *        // otherwise, let's do this instead
 *        console.log('Not on home page, or window sized to less than 768.');
 *      }
 *
 *    }, timeToWaitForLast, "your-function-identifier-string"); }
 * });
 *
 * Pretty cool huh? You can create functions like this to conditionally load
 * content and other stuff dependent on the viewport.
 * Remember that mobile devices and javascript aren't the best of friends.
 * Keep it light and always make sure the larger viewports are doing the heavy lifting.
 *
*/

/*
 * We're going to swap out the gravatars.
 * In the functions.php file, you can see we're not loading the gravatar
 * images on mobile to save bandwidth. Once we hit an acceptable viewport
 * then we can swap out those images since they are located in a data attribute.
*/
function loadGravatars() {
  // set the viewport using the function above
  viewport = updateViewportDimensions();
  // if the viewport is tablet or larger, we load in the gravatars
  if (viewport.width >= 768) {
  jQuery('.comment img[data-gravatar]').each(function(){
    jQuery(this).attr('src',jQuery(this).attr('data-gravatar'));
  });
	}
} // end function


/*
 * Put all your regular jQuery in here.
*/
jQuery(document).ready(function($) {

  /*
   * Let's fire off the gravatar function
   * You can remove this if you don't need it
  */
  loadGravatars();


}); /* end of as page load scripts */

/*Start mason*/

jQuery( document ).ajaxComplete(function() {
  preselect_new_phone_fields();
  // commenting out for new terms of service
  // add_house_rules_section_to_new_calendars();
  // make sure the calendar is showing, cause plugin is dumb
  document.getElementById('primary_calendar').style.display = 'block'; 
});

function preselect_new_phone_fields() {
  var $phone_fields = jQuery('.dopbsp-phone-code');

  for( var i = 0; i < $phone_fields.length; i++ ){
    if( !$phone_fields[i].old ){
      $phone_fields[i].old = true;
      $phone_fields[i].children[0].value="United States";
      $phone_fields[i].children[1].children[0].innerHTML = "US";
    }
  }
}
function add_house_rules_section_to_new_calendars(){
  var side_bar_row_cells = 
      jQuery('.DOPBSPCalendar-wrapper form table.dopbsp-sidebar-content table.dopbsp-sidebar-content tbody tr .dopbsp-column4');

  for( var i = 0; i < side_bar_row_cells.length; i++ ){
    if( !side_bar_row_cells[i].old ){
      side_bar_row_cells[i].old = true;
      var target_row = side_bar_row_cells[i].children[ side_bar_row_cells[i].children.length - 1 ];
      target_row.innerHTML = "<div class='dopbsp-module'> <a target='_blank' href='http://patrickstewartsong.ytmnd.com/'>House Rules</a></div>"; 
    }
  }
}

/* END MASON */


/* START CHRIS CODE */


// Start gallery code
document.addEventListener("DOMContentLoaded", function(event) {
  var galleries = document.getElementsByClassName('image-gallery') || [];

  var SMOOTH_SCROLL_STEP = 30;

  for( var i = 0; i < galleries.length; i++ ){
      var this_gallery = galleries[i];
      this_gallery.children[0].addEventListener('click',change_gallery_target.bind( this_gallery, -1 ));
      this_gallery.children[2].addEventListener('click',change_gallery_target.bind( this_gallery, 1 ));        
  }

  // if toggle calendar exsists, set up listener for it
  var calendar_toggle = document.getElementById('toggle_calendar');
  if (calendar_toggle){
      calendar_toggle.addEventListener('click', function(e){
          if(calendar_toggle.checked){
              document.getElementById('primary_calendar').style.display = 'none';  
              document.getElementById('alternate_calendar').style.display = 'block';
          }
          else {
              document.getElementById('primary_calendar').style.display = 'block';  
              document.getElementById('alternate_calendar').style.display = 'none';
          }
      });
  }

  // set up listeners for home page property summeries
  var start_events = ['mouseenter', 'touchstart'];
  var thumbnails = document.getElementsByClassName('property-thumbnail');
  for(var i = 0 ; i < thumbnails.length; i++) {
      var element = thumbnails[i]; 
      start_events.forEach(function (event){
          element.addEventListener(event, function(e) {
              document.getElementById('property-text').innerHTML = e.target.dataset.text;
              document.getElementById('property-text').classList.remove('show-review');
              var property_links = document.getElementsByClassName('mobile-property-link');
	        for (var i = 0; i < property_links.length; i++) {
	         	property_links[i].addEventListener('touchstart', function(touch_event) {
	         		touch_event.target.click();
	         	});
	        }
          });
      });
      element.addEventListener('mouseout', function (e) {
          document.getElementById('property-text').innerHTML = document.getElementById('property-text').dataset.text;
          document.getElementById('property-text').classList.add('show-review');

      });


      document.getElementById('property-text').dataset.text = document.getElementsByClassName('review')[0].innerHTML;
  }
  // if there are property thumbnails, load the review text
  if (thumbnails.length > 0) {
      document.getElementById('property-text').innerHTML = document.getElementById('property-text').dataset.text;
  }

  // set up listeners for about page
  var selectors = document.getElementsByClassName('selector');
  for (var i = 0; i < selectors.length; i++) {
      selectors[i].addEventListener('click', function(e){
          // clear active class from the buttons
          for (var j = 0; j < selectors.length; j++) {
              selectors[j].classList.remove('active');
          }
          // reapply it to the target
          e.target.classList.add('active');
          var active =  document.getElementById('active-section');
          active.classList.add('hide');
          var content = document.getElementById(e.target.textContent);
          setTimeout(function(){grow(active, content)},300);
      });
  }
  if (selectors.length > 0){
      selectors[0].click();
  }

  // if we have a slick gallery, load it
  if (document.getElementsByClassName('slick-gallery').length > 0){
      jQuery('.slick-gallery').slick({
          lazyLoad: "progressive",
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          adaptiveHeight: true,
          asNavFor: '.slick-nav'
      });
      jQuery('.slick-nav').slick({
          lazyLoad: "progressive",
          slidesToShow: 3,
          slidesToScroll: 1,
          asNavFor: '.slick-gallery',
          prevArrow: document.getElementById('slick-custom-prev'),
          nextArrow: document.getElementById('slick-custom-next'),
          dots: false,
          variableWidth: true,
          centerMode: true,
          focusOnSelect: true
      });

      document.getElementsByClassName('slick-gallery')[0].addEventListener('click', function(e){
          jQuery('.slick-gallery').slick('slickNext');
      });
  
  }

  if (galleries.length > 0){
      var auto_scroll_interval = window.setInterval(function(){
          change_gallery_target.apply(document.getElementsByClassName('image-gallery')[0], [1]);
      }, 8000);
      imageGalleryPreload('image-gallery');
  }

  $ = $ || jQuery;

  $('.calendars_wrapper').each(function(){
    var d = new Date();
    filter_to_month( this, d.getMonth() + 1 );
  });

});

function month_filter_click( clicked, month_id ){
  $ = $ || jQuery;
  filter_to_month( $(clicked).closest('.calendars_wrapper'), month_id );
}

function filter_to_month( this_cal, month_id ){
  $ = $ || jQuery;
  $(this_cal).find('.calendar_month').removeClass('active');
  $(this_cal).find('.event').removeClass('open visible');
    $(this_cal).find('.calendar_month[data-month="' + month_id + '"]').addClass('active');
    $(this_cal).find('.event[data-month="' + month_id + '"]').addClass('visible');
}

// about page animation
function fadeIn(element, content_ele){
    element.innerHTML = content_ele.innerHTML;
    element.classList.remove('hide');
}

function grow(element, content_ele){
    element.style.height = (content_ele.offsetHeight + 150) + 'px';
    setTimeout(function(){fadeIn(element, content_ele)},300);
}







// returns the appropriate image size based on the size if the gallery div
function getImageSize(element){
    var width = element.offesetWidth;
    if (width < 490) {
        return 'thumbnail';
    } else if (width < 740) {
        return 'medium';
    } else if (width < 1400) {
        return 'large';
    } else {
        return 'hd';
    }

}

// preloads images for image gallery
function imageGalleryPreload(gallery_class){
    var galleries = document.getElementsByClassName(gallery_class);
    for (var i = 0; i < galleries.length; i++) {
        var gallery_images = JSON.parse(galleries[i].dataset.imageSet);
        var size = getImageSize(galleries[i]);
        for (var j = 0; j < gallery_images.length; j++) {
            var image = new Image;
            image.src =gallery_images[j]['image']['sizes'][size];
        }
    }
}

function change_gallery_target( change_by ){
  var this_gallery = this;
  var current_index = this_gallery.dataset.imageTarget * 1;
  var image_list = JSON.parse(this_gallery.dataset.imageSet);
  // wrap around image set
  var index = (current_index + change_by) % image_list.length;  
  if (index < 0){
    index = image_list.length - 1;
  }
  this_gallery.dataset.imageTarget = index;

  new_img_div = this_gallery.getElementsByClassName('gallery-image inactive')[0];
  current_img_div = this_gallery.getElementsByClassName('gallery-image active')[0];
  new_img_div.style.backgroundImage = 'url('+image_list[index]['image']['sizes']['hd']+')';
  new_img_div.dataset.imageTarget = index;
  new_img_div.getElementsByClassName('review-text')[0].innerHTML = image_list[index]['text'];
  if (image_list[index]['is_a_review']){
    new_img_div.getElementsByClassName('five-star')[0].classList.add('active');
  } else {
    new_img_div.getElementsByClassName('five-star')[0].classList.remove('active');
  }
  document.getElementById('property-text').dataset.text = new_img_div.getElementsByClassName('review')[0].innerHTML ;
  // check if the review is displayed and if it is update the html
  if (document.getElementById('property-text').classList.contains('show-review')) {
    document.getElementById('property-text').innerHTML = document.getElementById('property-text').dataset.text;
  }
  // swap the divs
  new_img_div.classList.remove('inactive');
  new_img_div.classList.add('active');
  current_img_div.classList.remove('active');
  current_img_div.classList.add('inactive');
}

// end gallery code
/* END CHRIS CODE */










/*############################################################################*/
/*############################################################################*/
/*##################################START MAP#################################*/
/*############################################################################*/
/*############################################################################*/


function init_maps(){
  var site_wide_map_style = new google.maps.StyledMapType(styled_map_args, {name: 'site_wide_map_style'});

  jQuery('.gmap_listing_explorer_wrapper').each(function(){
    build_map_listing_explorer(this, site_wide_map_style);
  });
  document.setTimeout(function(){
    jQuery('a.gmap_directions_link_wrapper').each(function(){
      build_directions_link_map(this, site_wide_map_style);
    });
  }, 1);
}

var build_directions_link_map = function( dom_element, map_style ){
  var $dom_element = jQuery(dom_element);
  var this_map_div = $dom_element.find('.map')[0] || $dom_element.append( '<div class="map"></div>' )[0];
  var starting_loc = { lat : parseFloat($dom_element.data("centerMarker")["position"]['lat']), lng : parseFloat($dom_element.data("centerMarker")["position"]['lng']) } || {lat: 42.7756411, lng: -71.1429567};
  var marker_data = {};
  marker_data['pin_icon'] = $dom_element.data("centerMarker")["icon"] || [];
  marker_data['position'] = starting_loc;
  var marker_title = $dom_element.data("centerMarker")['address'] || 'Hello World';

  this_map = new google.maps.Map(this_map_div, {
    center: starting_loc,
    zoom: 11,
    disableDefaultUI: true
  });
  this_map.mapTypes.set('styled_map', map_style);
  this_map.setMapTypeId('styled_map');
  add_marker( this_map, marker_data );

  dom_element.target = "_blank";
  dom_element.href = 'https://maps.google.com/?q='+starting_loc["lat"]+','+starting_loc["lng"];
}

var build_map_listing_explorer = function( dom_element, map_style ){
  var this_listings = jQuery(dom_element).find('.listings')[0] || jQuery(dom_element).append( '<div class="listings"></div>' )[0];
  var this_map_div = jQuery(dom_element).find('.map')[0] || jQuery(dom_element).append( '<div class="map"></div>' )[0];
  var marker_data = JSON.parse(dom_element.dataset["markers"]) || [];
  var this_initial_zoom = parseInt(dom_element.dataset["initial_zoom"]) || 11;

  var starting_loc = {lat: 42.7756411, lng: -71.1429567};
  if( marker_data[0] ){
    starting_loc = {lat: parseFloat(marker_data[0]["position"]["lat"]), lng: parseFloat(marker_data[0]["position"]["lng"])}
  }
  this_map = new google.maps.Map(this_map_div, {
    center: starting_loc,
    zoom: 11,
    disableDefaultUI: true
  });
  this_map.mapTypes.set('styled_map', map_style);
  this_map.setMapTypeId('styled_map');

  for( var i = 0; i < marker_data.length; i++ ){
    add_marker(this_map, marker_data[i], i, select_location.bind( dom_element, this_map, i ));
    add_listing(this_listings, this_map, marker_data[i], select_location.bind( dom_element, this_map, i ));
  }
}

var add_marker = function( map, marker_data, listing_id, click_action ){
  //Expected marker_data keyset : [ 'pin_icon', 'position'* : ['lat','lng'], 'title'* ]
  //Set up map pin
  var icon_properties = build_icon_properties(marker_data["pin_icon"]);
  var marker_title = marker_data["title"] || 'Hello World';
  var this_marker = new google.maps.Marker({
    position: {lat: parseFloat(marker_data["position"]["lat"]), lng: parseFloat(marker_data["position"]["lng"])},
    title: marker_title,
    map: map,
    icon: icon_properties
  });
  if( listing_id )
    this_marker.listing_id = listing_id;
  if( click_action )
    this_marker.addListener('click', click_action);
  //end map pin
}

var build_icon_properties = function( image_properties ){
  var icon_props = {//DEFAULT PIN
      'scaledSize': new google.maps.Size(26, 34),
      'origin': null,
      'anchor': new google.maps.Point(13, 0) }
  if( image_properties['url'] && !isNaN(image_properties['height'] + 1.5) && !isNaN(image_properties['width'] + 1.5)  ){
    icon_props['url'] = image_properties['url'];
    icon_props['scaledSize'] = new google.maps.Size(34, image_properties['height'] * 34 / image_properties['width']);
    icon_props['anchor'] = new google.maps.Point(17, image_properties['height'] * 17 / image_properties['width']) ;
  }
  return icon_props;
}

var add_listing = function( dom_target, map, listing_data, click_action ){
  var icon_url = listing_data["pin_icon"]['url'];
  //set up listing
  var $listing_element = 
      jQuery(jQuery.parseHTML(
          '<div class="listing" data-lat="'+listing_data["position"]["lat"]+'" data-lng="'+listing_data["position"]["lng"]+'">'+
            '<div class="short">'+
              '<img class="pin" src="'+icon_url+'">'+
              '<div class="title_block">'+
                '<h2>'+listing_data["title"]+'</h2>'+
                '<p>'+listing_data["times"]+'</p>'+
              '<a class="directions_link" target="_blank" href="https://maps.google.com/?q='+listing_data["position"]["lat"]+','+listing_data["position"]["lng"]+'">Get Directions</a>'+
            '</div></div>'+
            '<div class="long">'+listing_data["more"]+'</div>'+
          '</div>')[0]);
  if( click_action )
    $listing_element.on('click', click_action);
  jQuery(dom_target).append( $listing_element );
  //Store collapsed listing height so we know where scroll targets are during transitions
  $listing_element.data( "collapsedHeight", $listing_element[0].offsetHeight );
  //end listing
}

var select_location = function( map, id ){

  var all_listings = jQuery(this).find('.listing');
  all_listings.removeClass('expanded');
  map.panTo( {lat: parseFloat(all_listings[id].dataset["lat"]), lng: parseFloat(all_listings[id].dataset["lng"])} );
  jQuery(all_listings[id]).addClass('expanded');
  var target_scroll_top = 0;
  if( id !== 0 ){
    target_scroll_top += 4.0;
    for( var i = 0; i < id; i++ )
      target_scroll_top += parseFloat(jQuery(all_listings[i]).data('collapsedHeight'));
  }
  jQuery(this).find('.listings').delay( 800 ).animate({
      scrollTop: target_scroll_top 
    }, 700, 'swing')
}

var styled_map_args = 
[
    {
        "featureType": "all",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "weight": "2.00"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#7c7c7c"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#7b7b7b"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "administrative.locality",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "administrative.neighborhood",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#c2c2c2"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#efefef"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#dfdfdf"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#9b8534"
            },
            {
                "weight": "3"
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [
            {
                "lightness": "100"
            },
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#c8d7d4"
            }
        ]
    }
];