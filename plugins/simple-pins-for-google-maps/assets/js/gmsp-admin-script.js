var geocoder;
var markers;
var map;
var mapProp;
var bounds;
var infowindow = new google.maps.InfoWindow({ });

String.prototype.stripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}

function initialize() {
	var mapProp = {
		center:new google.maps.LatLng( 38, 5),
		zoom:1,
		maxZoom:20,
		mapTypeId:google.maps.MapTypeId.ROADMAP
	};

	if (document.getElementById("googleMap")) {
		map = new google.maps.Map( document.getElementById("googleMap"), mapProp);
	}
	else {
		return;
	}
	bounds = new google.maps.LatLngBounds();	
	
	map.addListener('click', function(event) { setSingleMarkerOnClick(event.latLng.lat(), event.latLng.lng()) } );

	geocoder = new google.maps.Geocoder();
	markers = [];
	
	google.maps.event.addListener(map,'center_changed',function() {
		var mapCenter = document.getElementById('mapCenter');
		
		if (mapCenter) {
			mapCenter.value = map.getCenter().lat()+', '+map.getCenter().lng();	
		}
		
	} );
	
	google.maps.event.addListener(map,'zoom_changed',function() {
		var mapZoom = document.getElementById('mapZoom');
		var zoomValue = document.getElementById('zoomValue');
		
		if (mapZoom) {
			mapZoom.value = map.getZoom();	
		}
		
		if (zoomValue) {
			zoomValue.value = map.getZoom();	
		}
	} );
}

google.maps.event.addDomListener( window, 'load', initialize);	
window.onload = function() {document.getElementById('editCheck') ? document.getElementById('editCheck').value = '' : '';}

function getCoordsByAddress() {
	var postalAddress = document.getElementById( 'postal_address' ).value;
	
	
	geocoder.geocode( {'address': postalAddress}, 
		function(results, status) {
			if (status == 'OK') {
				document.getElementById('marker_name').value = postalAddress;
				document.getElementById('marker_longitude').value = results[0].geometry.location.lng();
				document.getElementById('marker_latitude').value = results[0].geometry.location.lat();
				var myLatlng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());

				var marker = new google.maps.Marker({
					position: myLatlng,
					title:postalAddress,
					draggable: true
				});
				
				google.maps.event.addListener(marker, 'dragend', function() 
					{
						getAdressByCoords(marker.getPosition().lat(), marker.getPosition().lng());
					} 
				);

				
				setMapOnAll(null);
				markers = [];
				markers.push(new Array(0, marker));
				setMapOnAll(map);
				map.setZoom(13);
				map.setCenter(myLatlng);
				
			} else {
				alert('Cannot find coordinates by given address: ' + status);
			}
			
			map.addListener('click', function(event) { setSingleMarkerOnClick(event.latLng.lat(), event.latLng.lng()) } );
		}
	);
}

function getAdressByCoords(lat, lng) {
		
	geocoder.geocode({'location': {lat: lat, lng: lng}}, function(results, status) {
		if (status === 'OK') {
		  if (results[1]) {
			postalAddressField = document.getElementById('postal_address');
			postalAddressField.value = results[1].formatted_address;
			document.getElementById('marker_longitude').value = lng;
			document.getElementById('marker_latitude').value = lat;
		  } else {
			window.alert('No results found');
		  }
		} else if (status === 'OVER_QUERY_LIMIT'){
			  window.alert('The webpage has gone over the requests limit in too short a time. Please wait a few moments before geocoding another address.');
		} else if (status === 'ZERO_RESULTS') {
			  window.alert('Cannot find any address by the given coordinates.');
		}		
		else {
			  window.alert('Geocoder failed due to ' + status);
		}
	  });
}

function setSingleMarkerOnClick(lat, lng) {
	if (document.getElementById("marker_latitude")) {
		document.getElementById("marker_latitude").value = lat;
	}
	else {
		return;
	}
	var editedMarkerField = document.getElementById('marker_id');
	if (editedMarkerField && editedMarkerField.value) {
		var tempMarker = document.getElementById(editedMarkerField.value);
		if (tempMarker) {
			jQuery(tempMarker).trigger('click');
			editedMarkerField.value = '';
		}
	}
	document.getElementById("marker_longitude").value = lng;
	var myLatlng = new google.maps.LatLng(lat, lng);
	getAdressByCoords(lat, lng);
	setMapOnAll(null);
	markers=[];
	addMarker(myLatlng);
	setMapOnAll(map);
	showCustomMarkerOnSelect();
}

function addMarker(location, infoWindow, image, icon, dragMarker) {
	var drag = (dragMarker === false) ? dragMarker : true;
	var marker = new google.maps.Marker({
	  position: location,
	  //map: map,
	  draggable:drag,
	  animation: google.maps.Animation.DROP,
	  contentString: infoWindow ? infoWindow : '',
	  infoImg: image ? image : ''
	});
	var i = new Image();
	i.src = icon;

	i.onload = function () {
		marker.setIcon(icon);
	}
	i.onerror = function () {
		marker.setIcon(null); 
	}
	markers.push(new Array(0,marker));
	if (drag != false) {
		google.maps.event.addListener(marker, 'dragend', function() 
		{
			getAdressByCoords(marker.getPosition().lat(), marker.getPosition().lng());
		} );
	}

}

function setMapOnAll(map) {
	bounds = new google.maps.LatLngBounds();
	
	// if (typeof GMSP_BACKEND_CLUSTERING !== 'undefined' && GMSP_BACKEND_CLUSTERING === '1' && typeof markersForClustering !== 'undefined') {	
		// for (var i = 0; i < markersForClustering.length; i++) {
			// bounds.extend(markersForClustering[i].getPosition());
		// }
	// }
	// else {			
		for (var i = 0; i < markers.length; i++) {
			markers[i][1].setMap(map);
			bounds.extend(markers[i][1].getPosition());
		}
	// }
	
	
	if (map && map.getCenter().lat() == 0 && map.getCenter().lng() == 180) {
		map.setZoom(1);
		map.setCenter({lat:38, lng:5});
	}

}

function fitMapBounds(mapId) {
	setMapOnAll(map);	
	map.fitBounds(bounds);
}

function gmspSetZoomDynamically(thisObj) {
	map.setZoom(parseInt(thisObj.value));
}
		
function showMarkerInfo(markerId, mapId, editable) {			
	var selectedMarkerName = document.getElementById(markerId+'_name') ? document.getElementById(markerId+'_name') : document.getElementById(markerId.replace(mapId,'')+'_name'); 
	var selectedMarkerLat = document.getElementById(markerId+'_lat') ? document.getElementById(markerId+'_lat') : document.getElementById(markerId.replace(mapId,'')+'_lat');
	var selectedMarkerLong = document.getElementById(markerId+'_ln') ? document.getElementById(markerId+'_ln') : document.getElementById(markerId.replace(mapId,'')+'_ln');
	var selectedMarkerInfo = document.getElementById(markerId+'_info') ? document.getElementById(markerId+'_info') : document.getElementById(markerId.replace(mapId,'')+'_info');
	var selectedMarkerImg = document.getElementById(markerId+'_img') ? document.getElementById(markerId+'_img') : document.getElementById(markerId.replace(mapId,'')+'_img');
	var selectedMarkerInfoWidth = document.getElementById(markerId+'_info_width') ? document.getElementById(markerId+'_info_width') : document.getElementById(markerId.replace(mapId,'')+'_info_width');
	var selectedMarkerImgCrop = document.getElementById(markerId+'_crop') ? document.getElementById(markerId+'_crop') : document.getElementById(markerId.replace(mapId,'')+'_crop');
	var selectedMarkerIcon = document.getElementById(markerId+'_icon') ? document.getElementById(markerId+'_icon') : document.getElementById(markerId.replace(mapId,'')+'_icon');
	var tinyMCEInfo = document.getElementById(markerId+'_rte') ? document.getElementById(markerId+'_rte') : document.getElementById(markerId.replace(mapId,'')+'_rte');
	var idField = document.getElementById('marker_id');
	
	infowindow.close();
	
	if (editable === 'true') {
		var nameField = document.getElementById('marker_name');
		var addressField = document.getElementById('postal_address');
		var latField = document.getElementById('marker_latitude');
		var lnField = document.getElementById('marker_longitude');
		var infoImg = document.getElementById('marker_info_img');
		var infoTitle = document.getElementById('marker_info_title');
		var infoBody = document.getElementById('marker_info_body_mce');
		var infoWinWidth = document.getElementById('info_win_width');
		var dragMarker = true;
	}
	else {
		var nameField = document.getElementById('marker_name_info');
		var addressField = document.getElementById('marker_address_info');
		var latField = document.getElementById('marker_latitude_info');
		var lnField = document.getElementById('marker_longitude_info');
		var infoField = document.getElementById('marker_info_window_info');
		var infoWinWidth = document.getElementById('marker_info_win_width_info');
		var dragMarker = false;
		if (idField) idField.value = "no-edit";
	}
	var myLatlng = new google.maps.LatLng(Number(selectedMarkerLat.value), Number(selectedMarkerLong.value));
	
	if (document.getElementById(markerId).checked !== true) {
		var counter = 0;
		while (markers.length > counter) {
			if (markers[counter][0] === markerId) {
				markers[counter][1].setMap(null);
				markers.splice(counter, 1);
				fitMapBounds(mapId);
			}
			counter++;
		}
		setMapOnAll(map);
		
		if (idField) idField.value = '';
		if (addressField) addressField.value = '';
		if (nameField) nameField.value = ''; 
		if (latField) latField.value = '';
		if (lnField) lnField.value = '';
		if (infoField) infoField.innerHTML = '';
		if (infoImg) infoImg.value  = '';
		if (infoTitle) infoTitle.value = '';
		if (infoBody) infoBody.value = '';
		if (infoWinWidth) { 
			infoWinWidth.value = '250';
			document.getElementById('info_win_width_output').innerHTML = '250px';
		}
		if ('undefined' !== typeof tinymce) {
			tinymce.get('marker_info_body_mce').focus();
			tinymce.activeEditor.setContent('');
		}
	}
	else {
		
		var marker = new google.maps.Marker({
		  position: {lat: Number(selectedMarkerLat.value), lng: Number(selectedMarkerLong.value)},
		  draggable: dragMarker
		});
		
		var selectedMarkerInfoWinWidth = (null !== selectedMarkerInfoWidth) ? selectedMarkerInfoWidth.value + 'px' : 'auto';
		
		if (infoWinWidth) {
			if (null !== selectedMarkerInfoWidth) { 
				infoWinWidth.value = selectedMarkerInfoWidth.value;
			}
			else {
				infoWinWidth.value = '250';
			}
			document.getElementById('info_win_width_output').innerHTML = infoWinWidth.value + 'px';
		}
		
		var i = new Image();
		i.src = (selectedMarkerIcon && (selectedMarkerIcon.value.indexOf( 'gmspNoneSelected') === -1)) ? selectedMarkerIcon.value : '';

		i.onload = function () {
			marker.setIcon(selectedMarkerIcon.value);
		}
		i.onerror = function () {
			marker.setIcon(null); 
		}

		
		if (dragMarker != false) {
			google.maps.event.addListener(marker, 'dragend', function() 
			{
				getAdressByCoords(marker.getPosition().lat(), marker.getPosition().lng());
			} );
		}
									
		markers.push(new Array(markerId,marker));
		bounds.extend(marker.getPosition());	
		map.setCenter(myLatlng);
		marker.setMap(map);
		if (tinyMCEInfo && tinyMCEInfo.value !== "0") {
			infowindow.open(map, marker);
			infowindow.setContent('<div style="width: '+selectedMarkerInfoWinWidth + ';">' + selectedMarkerImg.value + selectedMarkerInfo.value + '</div>');
			
		} else {
			if (selectedMarkerInfo && selectedMarkerInfo.value !== '') {
				infowindow.open(map, marker);
				infowindow.setContent(
					'<div style="max-height:120px; max-width:300px; width: '+selectedMarkerInfoWinWidth + ';">'+
					(selectedMarkerImg && selectedMarkerImg.value ? '<div style="float:left;margin:0 10px 0 0;width:64px;height:64px;background-image:url('+selectedMarkerImg.value+');background-repeat:no-repeat;background-size:'+
					(selectedMarkerImgCrop ? selectedMarkerImgCrop.value : 'cover')+';background-position-y: 50%;background-position-x: 50%;"></div>' : '')+'<div style="float:left;max-width:200px;max-height:250px">'+
					selectedMarkerInfo.value.replace('\\"', '\"').replace("\\'", "\'")+'</div></div>');
			}
		}
				
		if ( markers.length > 1 ) {
			map.fitBounds(bounds);
		}
		else {
			map.setZoom(13);
		}
		
		if (addressField) {
			geocoder.geocode({'location': {lat: Number(selectedMarkerLat.value), lng: Number(selectedMarkerLong.value)}}, function(results, status) {
				if (status === 'OK') {
				  if (results) {
					addressField.value = ("undefined" !== typeof results[1]) ? results[1].formatted_address : results[0].formatted_address;
				  } else {
					window.alert('No results found');
				  }
				} else if (status === 'OVER_QUERY_LIMIT'){
					  document.getElementById(markerId).checked = false;
					  console.log(markers);
					  setMapOnAll(null);
					  markers.pop();
					  setMapOnAll(map);
					  window.alert('The webpage has gone over the requests limit in too short a time. Please wait a few moments before geocoding another address.');
				} else if (status === 'ZERO_RESULTS') {
					  window.alert('Cannot find any address by the given coordinates.');
				}		
				else {
					  window.alert('Geocoder failed due to ' + status);
					  setMapOnAll(null);
					  markers = [];
				}
			  });
		}
		
		if (nameField) { 
			nameField.value = selectedMarkerName.value.stripSlashes(); 
		}
		if (latField) { 
			latField.value = selectedMarkerLat.value;
		}
		if (lnField) { 
			lnField.value = selectedMarkerLong.value;
		}
		if (infoField) {
			infoField.innerHTML = selectedMarkerInfo 
				? '<div style="max-height:120px; max-width:300px; height:120px">'+
				  (selectedMarkerImg && selectedMarkerImg.value 
					? '<div style="float:left;margin:0 10px 0 0;width:64px;height:64px;background-image:url('+selectedMarkerImg.value+');background-repeat:no-repeat;background-size:'
					  +(selectedMarkerImgCrop ? selectedMarkerImgCrop.value : 'cover')+';background-position-y: 50%;background-position-x: 50%;"></div>' : '')+'<div style="float:left;max-width:200px">'+selectedMarkerInfo.value.replace('\\"', '\"').replace("\\'", "\'")+'</div></div>' 
					: 'not set';
		}
		if (selectedMarkerImg && selectedMarkerImg.value) {			
			var legacyInfoImg = '<img class="alignleft" src="'+selectedMarkerImg.value+'">';
		}
		else {
			var legacyInfoImg = '';
		}
		// if (infoTitle) {
			// infoTitle.value = (selectedMarkerInfo && selectedMarkerInfo.value.match(/<b>.+<\/b><br>/g)) ? selectedMarkerInfo.value.match(/<b>.+<\/b><br>/g)[0].replace(/<\/?[^>]+(>|$)/g, "") : '';
		// }
		if (infoBody) {
			//infoBody.value = (selectedMarkerInfo && selectedMarkerInfo.value) ? selectedMarkerInfo.value.replace(/<b>.+<\/b><br>/g,'') : '';
			tinymce.get('marker_info_body_mce').focus();
			tinymce.activeEditor.setContent(legacyInfoImg+selectedMarkerInfo.value);

		}

		if (idField && (idField.value !== "no-edit")) {
			idField.value = markerId;
		}

	}
						
}
		
function onButtonEditMap(mapId) {
	if (preventEditingMultipleRows(mapId) === 'clean-edit') {
		
		/* Hide Edit button, since we are already editing a Map */
		document.getElementById('buttonEdit'+mapId).style.display = 'none';
	
		/* Show list of available markers */
		document.getElementById('list_of_saved_markers_'+mapId).style.display = 'block';

		/* Display Cancel button only when we started editing a map */
		document.getElementById('buttonCancelEditingMap'+mapId).style.display = 'inline-block';
		
		/* Display editing controls */
		var hiddenControls = document.getElementsByClassName('show-only-on-editing'+mapId);
		for (var i = 0; i<hiddenControls.length; i++) {
			hiddenControls[i].style.display = 'block';
		}
		
		/* Enable map type select control */
		var mapTypeControl = document.getElementById( 'mapType' + mapId);
		mapTypeControl.disabled = false;
		
		/* Enable zoom control */
		var zoomControl = document.getElementById( 'mapZoom' + mapId);
		zoomControl.disabled = false;
		var zoomControlDummy = document.getElementById( 'mapZoomDummy' + mapId);
		zoomControl.disabled = true;
		
		/* Show Map preview*/
		if (!(document.getElementById('googleMap'+mapId).children.length > 0)) {
			var mapDiv = document.getElementById('googleMap'+mapId).appendChild(document.createElement('div'));
			var mapCenter = document.getElementById('mapCenter') ? document.getElementById('mapCenter').value : document.getElementById('mapCenter'+mapId).value;
			var mapLat = parseFloat(mapCenter.split(',')[0]);
			var mapLng = parseFloat(mapCenter.split(',')[1]);
			var mapZoom = document.getElementById('mapZoom') ? document.getElementById('mapZoom').value : document.getElementById('mapZoom'+mapId).value;
			// Get map type
			var mapType = mapTypeControl.value;
				
			mapDiv.id = 'googleMap';
			mapDiv.style.width = '640px';
			mapDiv.style.maxWidth = '100%';
			mapDiv.style.height = '480px';
			
			var dynamicMapZoom = document.getElementById('mapZoom'+mapId);
			if (dynamicMapZoom) {
				dynamicMapZoom.id = 'mapZoom';
				dynamicMapZoom.name = 'mapZoom';
			}
			
			var dynamicMapCenter = document.getElementById('mapCenter'+mapId);
			if (dynamicMapCenter) {
				dynamicMapCenter.id = 'mapCenter';
				dynamicMapCenter.name = 'mapCenter';
			}

			initialize();
			
			/* Show Autofit button */
			var autoFitButton = document.getElementById('actionsFor'+mapId).appendChild(document.createElement('a'));
			autoFitButton.className = 'button';
			autoFitButton.onclick = function(){if (markers.length === 1) {gmspCenterZoom(mapId, 15, markers[0][1].getPosition().lat(), markers[0][1].getPosition().lng());} else {fitMapBounds(mapId);} showMarkersInMapPreview(mapId);};
			autoFitButton.textContent = 'Fit to All';

			
			/* Dynamically change zoom and center, show to user */
							
			map.setZoom(parseInt(mapZoom) ? parseInt(mapZoom) : 0);
			map.setCenter({lat: mapLat, lng: mapLng});
			map.setOptions({mapTypeControl : false, streetViewControl : false});

			/* Dynamically change map type */
			gmspChangeDynamicallyMapType(mapType);
			
		}
		
		/* Autocenter-autozoom radio button */
		var autoCenterAutoZoom = document.getElementsByName('autocenter_autozoom'+mapId);
		for (var i=0; i<autoCenterAutoZoom.length; i++) {
			autoCenterAutoZoom[i].addEventListener('click', function() {
				gmspCenterZoom(mapId, mapZoom, mapLat, mapLng);
			});
		}
		
		
		/* Map preview (different sizes) */
		var previewSizes = document.getElementsByName('preview'+mapId);
		
		for (var i=0; i<previewSizes.length; i++) {
			previewSizes[i].addEventListener( 'click', function() {
				var mapDivDynamic = document.getElementById('googleMap');
				if (this.value == '100%') {
					mapDivDynamic.style.width = '100%';
					mapDivDynamic.style.height = '768px';
				}
				else if (this.value == '800px') {
					mapDivDynamic.style.width = '800px';
					mapDivDynamic.style.height = '600px';
				}
				else if (this.value == '640px') {
					mapDivDynamic.style.width = '640px';
					mapDivDynamic.style.height = '480px';
				}
				else if (this.value == '320px') {
					mapDivDynamic.style.width = '320px';
					mapDivDynamic.style.height = '240px';
				}
				google.maps.event.trigger(map, "resize");
				gmspCenterZoom(mapId, mapZoom ? mapZoom : false, mapLat ? mapLat : false, mapLng ? mapLng : false);
			});
		}
		
		/* Show markers on Map preview */
		//showMarkersInMapPreview(mapId);
		gmspCenterZoom(mapId, mapZoom, mapLat, mapLng);
		
		// if (typeof GMSP_BACKEND_CLUSTERING !== 'undefined' && GMSP_BACKEND_CLUSTERING === '1') {
			// markerCluster = new MarkerClusterer(map, markersForClustering,
				// {imagePath: GMSP_URL.siteurl + '/assets/img/m/'});
		// }
		
		/* Pro version clone button*/
		var cloneMapButton = document.getElementById("clone-edited-map"+mapId);
		if (cloneMapButton) 
			cloneMapButton.value = "Save As New";
		
	}
	else {
		console.log('Chose to edit further.');
	}
}

function gmspChangeDynamicallyMapType(mapType) {
	var googleDefaultMapTypes = ['SATELLITE','TERRAIN','HYBRID'];
	
	if ( (typeof gmsp_MapTypes !== 'undefined') && ( googleDefaultMapTypes.indexOf(mapType.toUpperCase()) === -1) && (mapType.toUpperCase() in gmsp_MapTypes) ) {
		
		map.mapTypes.set(mapType.toUpperCase(), gmsp_MapTypes[ mapType.toUpperCase() ] );
		map.setMapTypeId(mapType.toUpperCase());
	}
	else if ( mapType.toUpperCase() === 'SATELLITE' ) {
		map.setMapTypeId('satellite');
	}
	else if ( mapType.toUpperCase() === 'TERRAIN' ) {
		map.setMapTypeId('terrain');
	}
	else if ( mapType.toUpperCase() === 'HYBRID' ) {
		map.setMapTypeId('hybrid');
	}
	else {
		map.setMapTypeId('roadmap');
	}

}

function gmspCenterZoom(mapId, mapZoom, mapLat, mapLng) {

	var autoCenterAutoZoomValue = document.getElementById('autocenter_autozoom'+mapId).checked;
	var zoomControl = document.getElementById('mapZoom');

	if (autoCenterAutoZoomValue === true) {
		showMarkersInMapPreview(mapId);
		fitMapBounds(mapId);
		map.setOptions({zoomControl: false, scrollwheel: false, disableDoubleClickZoom: true});
		zoomControl.value = map.getZoom();		
		zoomControl.disabled = true;
	}
	else {
		map.setOptions({zoomControl: true, scrollwheel: true, disableDoubleClickZoom: false});
		if (mapZoom && mapLat && mapLng) {
			map.setZoom(parseInt(mapZoom) ? parseInt(mapZoom) : 0);
			map.setCenter({lat: mapLat, lng: mapLng});
		}
		zoomControl.disabled = false;
		showMarkersInMapPreview(mapId);
	}
	

}

function preventEditingMultipleRows(mapId, event, paramFunction) {
	var editCheck = document.getElementById('editCheck');
	
	if (editCheck.value === '') {
		editCheck.value = mapId;
		if (paramFunction && event) {
			paramFunction( event,
				'Are you sure you want to delete the selected map?');
		};
		return 'clean-edit';
	}
	else if (editCheck.value === mapId) {				
		console.log('same map is being edited');
		if (paramFunction && event) {
			paramFunction(event,
				'Are you sure you want to delete the selected map?');
		};
		return 'same-edit';
	}
	else {
		var confirmRes = confirm(
			'Editing in progress. Your changes to the map ' + 
				editCheck.value +
				' will be lost. Continue?'
			);

		if ( confirmRes ) {
			location.reload(true);
		}
		else {
			if (event) {
				event.preventDefault();
			}

			return false;
		}
	}
}

function showMarkersInMapPreview(mapId) {
		setMapOnAll(null);
		var markersToShow = document.getElementById('markersFor'+mapId).children;
		var selectedMarkers = [];
		var i = 0, markerId = '', icon = '', markerLat = '', markerLng = ''; 
		var markerInfoWinWidth = document.getElementById(markerId+'_info_width');
		markers = [];
		markersForClustering = [];
		jQuery('#list_of_saved_markers input:checked').each(function() {
			selectedMarkers.push(jQuery(this).attr('id'));
		});
				
		while (i < markersToShow.length) {
			selectedMarkers.push(markersToShow[i].id);
			i++;
		}
		
		i = 0;
		
		while (i < selectedMarkers.length) {
			if (typeof selectedMarkers[i] !== undefined && selectedMarkers[i] !== null) {
				markerId = selectedMarkers[i].replace(mapId, '').replace('-','');
				markerLat = parseFloat(document.getElementById(markerId+'_lat').value);
				markerLng = parseFloat(document.getElementById(markerId+'_ln').value);
				markerInfoWinWidth = ((null !== markerInfoWinWidth) && markerInfoWinWidth.value) ? markerInfoWinWidth.value+'px' : 'auto';
				markerInfo = (document.getElementById(markerId+'_info') && document.getElementById(markerId+'_info').value !== '') ? '<div style="float:left; width: '+markerInfoWinWidth+';">'+document.getElementById(markerId+'_info').value.replace('\\"', '\"').replace("\\'", "\'")+'</div>' : '';
				markerImg = (document.getElementById(markerId+'_img') && document.getElementById(markerId+'_img').value !== '') ?  '<div style="float:left;margin-right:10px;width:64px;height:64px;background:url('+document.getElementById(markerId+'_img').value+');background-repeat:no-repeat;background-size:cover;background-position-y: 50%;background-position-x: 50%;"></div>' : '';
				icon = (document.getElementById(markerId+'_icon') && (document.getElementById(markerId+'_icon').value.indexOf( 'gmspNoneSelected') === -1)) ? document.getElementById(markerId+'_icon').value : '';
				addMarker({lat:markerLat,lng:markerLng}, markerInfo, markerImg, icon, false);
			}
			i++;
		}
				
		var infowindow = new google.maps.InfoWindow({ });

		for (var i = 0; i < markers.length; i++) {
			if (markers[i][1].contentString || markers[i][1].infoImg) {
				markers[i][1].addListener('click', function() {
					infowindow.open(map, this);
					infowindow.setContent(this.infoImg+this.contentString);
				});
			}
			// if (typeof GMSP_BACKEND_CLUSTERING !== 'undefined' && GMSP_BACKEND_CLUSTERING === '1') {
				// markersForClustering.push(markers[i][1]);
			// }
		}
		
		setMapOnAll(map);
		
		
				
}

function gmspSubmitForm(action) {
	document.getElementById('form_delete_selected_marker').action = action;
	document.getElementById('form_delete_selected_marker').submit();
}

function createMapFromSelectedMarkers() {		
	var node = document.createElement('input');
	var att_name = document.createAttribute('name');
	var att_type = document.createAttribute('type');
	att_name.value = 'new_map';      
	att_type.value = 'hidden';
	node.setAttributeNode(att_name);
	node.setAttributeNode(att_type);			
	document.getElementById('form_delete_selected_marker').appendChild(node);
	console.log('appended');
	gmspSubmitForm('?page=google_maps_simple_pins&tab=add_map');
}

function remove_marker(marker_id, map_id){
	var editCheck = document.getElementById('editCheck');
	if (map_id === editCheck.value) {
		var markerParentNode = document.getElementById('markersFor'+map_id);
		var markerToDelete = document.getElementById(marker_id);

		Element.prototype.remove = function() {
			markerParentNode.removeChild(this);
		}
		NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
			for(var i = this.length - 1; i >= 0; i--) {
				if(this[i] && markerParentNode) {
					markerParentNode.removeChild(this[i]);
				}
			}
		}		
		
		markerToDelete.remove();
		showMarkersInMapPreview(map_id);
		fitMapBounds(map_id);

	}
}

function onButtonCancelEditingMap(event) {
	location.reload(true);
}

function changesConfirmation(event, message) {
	if (confirm(message)) {
		return true;			
	}
	else {
		event.preventDefault();
	}
}

function gm_authFailure() { 					
	alert("Your API key is no longer valid. Please go to the Settings tab and check your API key!");
}

jQuery(document).on('ready', function($){
    postboxes.save_state = function(){
        return;
    };
    postboxes.save_order = function(){
        return;
    };
    postboxes.add_postbox_toggles();
		
});

function addMarkerTabInfoWindowBE(event, thisElement) {
	event.preventDefault(); 
	var elem = document.getElementById('info-window'); 
	if (elem.style.display == 'table-row') {
		elem.style.display = 'none';
		document.getElementById('add_marker_info').textContent = 'Add info-window';
		if ('undefined' !== typeof tinymce) {
			tinymce.activeEditor.setContent('');
		}
		infowindow.setContent();
		infowindow.close(map, markers[0][1]);
	}
	else {
		elem.style.display = 'table-row';
		document.getElementById('add_marker_info').textContent = 'Remove info-window';
	}
}

function addMarkerIconBE(event, thisElement) {
	event.preventDefault(); 
	var elem = document.getElementById('marker-icon'); 
	if (elem.style.display == 'table-row') {
		elem.style.display = 'none';
		document.getElementById('add_marker_icon').textContent = 'Add marker icon';
		iconsRadio = document.getElementsByName('custom_icon_marker');
		
		for (var i = 0; i < iconsRadio.length; i++) {
			iconsRadio[i].checked = false;
			markers[0][1].setIcon();
		}
		var defaultIcon = document.createElement("input");                 
		defaultIcon.setAttribute('name', 'custom_icon_marker');
		defaultIcon.setAttribute('value', 'gmspNoneSelected');
		defaultIcon.setAttribute('checked', 'true');
		document.getElementsByName('custom_icon_marker')[0].parentNode.appendChild(defaultIcon);
	}
	else {
		elem.style.display = 'table-row';
		document.getElementById('add_marker_icon').textContent = 'Remove marker icon';
	}
}

function showCustomMarkerOnSelect() {
	var idField = document.getElementById('marker_id');
	if (idField.value !== "no-edit") {
		if (markers && markers[0]) {
			var elems = document.getElementsByName('custom_icon_marker');
			for (var i = 0; i < elems.length; i++) {
				elems[i].addEventListener('click', function() {
					markers[0][1].setIcon(this.previousSibling.src);
				});
			}
		}
	}
}

function showInfoWindowOnChangeBE(thisElement, content) {
	if ('undefined' !== tinymce) {
		if (tinymce.activeEditor.getContent() === '') {
			return;
		}
	}
	var idField = document.getElementById('marker_id');
	var infoWinWidth = (document.getElementById('info_win_width') && document.getElementById('info_win_width').value) ? document.getElementById('info_win_width').value + 'px' : 'auto';
	if ((idField.value !== "no-edit") && (content !== '')) {
		if (markers[0]) {
			var infoTitle = (document.getElementById('marker_info_title') && document.getElementById('marker_info_title').value) ? '<b>'+document.getElementById('marker_info_title').value+'</b><br/>' : '';
			var infoBody = document.getElementById('marker_info_body_mce').value ? document.getElementById('marker_info_body_mce').value : '';
			var infoImgCrop = (document.getElementsByName('marker_info_img_crop').length && document.getElementsByName('marker_info_img_crop')[0].checked) ? document.getElementsByName('marker_info_img_crop')[0].value : 'contain';
			var infoImg = (document.getElementById('marker_info_img') && document.getElementById('marker_info_img').value) ? 
				'<div id="gmsp-info-img" style="float:left; width:80px; height:80px; margin-right:10px; background-repeat:no-repeat; background-image:url('+document.getElementById('marker_info_img').value+');background-size:'+infoImgCrop+';background-position-x:50%;background-position-y:50%;"></div>' : '';
			infowindow.close(map, markers[0][1]);
			infowindow.setContent('<div style="width: '+ infoWinWidth + ';">' + content + '</div>')
			if (infowindow.content) infowindow.open(map, markers[0][1]);
		}
	}
}

function outputUpdate(zoomId, zoom) {
	document.querySelector('#'+zoomId).value = zoom;
}

