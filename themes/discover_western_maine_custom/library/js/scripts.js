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

  //JACK GOOGLE CALENDAR HACK

  //jQuery("#primary_calendar").load('https://calendar.google.com/calendar/embed?showTitle=0&amp;showPrint=0&amp;showCalendars=0&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=fd7ike4kdciuq9362qr02j2he0o52gc5%40import.calendar.google.com&amp;color=%231B887A&amp;src=0sf8fkp61gku81s7hdkavqa7hee1j9oq%40import.calendar.google.com&amp;color=%231B887A&amp;src=1nbrf2hqkmuarf9gcfvolkqgbh30iclu%40import.calendar.google.com&amp;color=%231B887A&amp;ctz=America%2FNew_York');
/*
  var cabin_cal_url = 'https://calendar.google.com/calendar/embed';
  var parameters = 'showTitle=0&showPrint=0&showCalendars=0&height=600&wkst=1&bgcolor=%23FFFFFF&src=fd7ike4kdciuq9362qr02j2he0o52gc5%40import.calendar.google.com&color=%231B887A&src=0sf8fkp61gku81s7hdkavqa7hee1j9oq%40import.calendar.google.com&color=%231B887A&src=1nbrf2hqkmuarf9gcfvolkqgbh30iclu%40import.calendar.google.com&color=%231B887A&ctz=America%2FNew_York';

  var args = JSON.parse('{"' + decodeURI(parameters).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
  args.csurl = cabin_cal_url;
  jQuery('#primary_calendar').load(
    'http://localhost/main_away/wp-content/themes/discover_western_maine_custom/proxy.php', args
  );

*/

});

// about page animation
function fadeIn(element, content_ele){
    element.innerHTML = content_ele.innerHTML;
    element.classList.remove('hide');
}

function grow(element, content_ele){
    element.style.height = content_ele.offsetHeight + 'px';
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