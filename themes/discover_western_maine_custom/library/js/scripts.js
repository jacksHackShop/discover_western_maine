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
  document.getElementById('alternate_calendar').style.display = 'none';
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
    this_gallery.children[1].addEventListener('click',change_gallery_target.bind( this_gallery, 1 ));        
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
  if (galleries){
    var auto_scroll_interval = window.setInterval(function(){
      change_gallery_target.apply(document.getElementsByClassName('image-gallery')[0], [1]);
    }, 8000);
  }
});

function change_gallery_target( change_by ){
  var this_gallery = this;
  var current_index = this_gallery.dataset.imageTarget * 1;
  var image_list = JSON.parse(this_gallery.dataset.imageSet);
  // wrap around image set
  var index = (current_index + change_by) % image_list.length;  
  if (index < 0){
    index = image_list.length - 1;
  }
  this_gallery.style.backgroundImage = 'url('+image_list[index]+')';
  this_gallery.dataset.imageTarget = index;
}
// end gallery code
/* END CHRIS CODE */