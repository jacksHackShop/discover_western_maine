var gmspAllMaps = [];
if (!window.console) console = {log: function() {}};

function showMapByShortCode( params) {	

	if (window.addEventListener) {
		window.addEventListener("load", function(){ gmspLoadMap(params) });
		window.addEventListener("resize", gmspResizeMaps );	}
	else {
		window.attachEvent("onload", function(){ gmspLoadMap(params) });
		window.attachEvent("onresize", gmspResizeMaps );
	}		

}

function gmspLoadMap(params) {			
	var	bounds = new google.maps.LatLngBounds();	
	if (typeof params !== 'undefined') {
		var mapMarkers = (typeof params["markers"] !== 'undefined') ? params["markers"] : '';
		var autoFit = (typeof params["auto"] !== 'undefined')  ? params["auto"] : '';
		var mapType = (typeof params["mapType"] !== 'undefined')  ? params["mapType"] : 'roadmap';
		var mapControls = (params["mapControls"]==='1') ? true : false;
		var mapId = (typeof params["id"] !== 'undefined')  ? params["id"] : '';
		var mapCenter = (typeof params["center"] !== 'undefined')  ? params["center"] : '50.123456,40,123456';
		var mapLat = parseFloat(mapCenter.split(',')[0]);
		var mapLng = parseFloat(mapCenter.split(',')[1]);
		var mapZoom = (typeof params["zoom"] !== 'undefined')  ? parseInt(params["zoom"]) : 5;
		var mapType = (typeof params["mapType"] !== 'undefined')  ? params["mapType"] : 'roadmap';
		var mapControls = (typeof params["mapControls"] !== 'undefined' && params["mapControls"]==='1') ? true : false;
		var mapScroll = (typeof params["mapScroll"] !== 'undefined' && params["mapScroll"]==='1') ? false : true;
		var mapOpenInfoWindowId = (typeof params["openInfo"] !== 'undefined')  ? params["openInfo"] : '';
		var markerClustering = (typeof params["markerClustering"] !== 'undefined')  ? params["markerClustering"] : '0';
	}
	else {
		return false;
	}
	console.log(mapId +'='+mapControls);

	
	var mapProp = {
		center:new google.maps.LatLng( mapLat, mapLng),
		zoom:mapZoom,
		mapTypeId:mapType,
		scrollwheel: mapScroll,
		disableDefaultUI:mapControls
	};
	
	var infowindow = new google.maps.InfoWindow({ });
		
	var map = new google.maps.Map( document.getElementById( mapId), mapProp	);
	var markers = [];
	var i=0;
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

	while ( i < mapMarkers.length ) {
	
		var marker = new google.maps.Marker({
			  position: { lat: parseFloat(mapMarkers[i].lat), lng: parseFloat(mapMarkers[i].ln) },
			  title: mapMarkers[i].name,
			  optimized: false,
			  contentString: (mapMarkers[i].info || mapMarkers[i].rte) ? ((mapMarkers[i].rte) ? mapMarkers[i].info.replace(/\\"/g, '\"').replace(/\\'/g, "\'") : '<div id="gmsp-info-text">'+mapMarkers[i].info.replace(/\\"/g, '\"').replace(/\\'/g, "\'")+'</div>') : '',
			  infoImg: mapMarkers[i].img ? '<div id="gmsp-info-img" style="background-image:url('+mapMarkers[i].img+');background-size:'+(mapMarkers[i].crop ? mapMarkers[i].crop : 'cover')+';"></div>' : '',
			  infoWidth: mapMarkers[i].info_width ? mapMarkers[i].info_width+'px' : 'auto',
			  icon: mapMarkers[i].icon ? { url: mapMarkers[i].icon } : '',
			  animation: google.maps.Animation.DROP
		});
																	
		if (mapMarkers[i].rte){
			marker.addListener('click', function() {
				infowindow.open(map, this);
				infowindow.setContent('<div style="width:' + this.infoWidth + ';">' + this.contentString + '</div>');
			});
		}
		else {
			if (marker.contentString || marker.infoImg) {
				marker.addListener('click', function() {
					infowindow.open(map, this);
					infowindow.setContent('<div id="gmsp-info-window">'+this.infoImg+this.contentString+'</div>');
					});
			}
		}
		
		if ((mapMarkers[i].id) && mapMarkers[i].id === mapOpenInfoWindowId) {
			var openInfoWindowId = i;
		}
		
		markers.push(marker);
			
		i++;
	
	}		
		
	bounds = gmspSetMapOnAll(map, markers, bounds, markerClustering);
	
	if (autoFit === '1') {
		map.fitBounds(bounds);
	}
	
	if (typeof openInfoWindowId !== 'undefined') {	
		setTimeout( function () {
			infowindow.open(map, markers[openInfoWindowId]);
			infowindow.setContent('<div id="gmsp-info-window" style="width:' + markers[openInfoWindowId].infoWidth + ';">'+markers[openInfoWindowId].infoImg+markers[openInfoWindowId].contentString+'</div>');
		}, 1500);
	}
			
		
	gmspAllMaps.push([ map, mapProp.center, mapProp.zoom, autoFit, bounds ]);

}

function gmspSetMapOnAll(map, markers, bounds, cluster) {
			
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(map);
		bounds.extend(markers[i].getPosition());
	}
	
	if (cluster === '1') {	
		var markerCluster = new MarkerClusterer(map, markers,
				{imagePath: GMSP_URL.siteurl + '/assets/img/m/'});
	}
	
	return bounds;
	
}

function gmspResizeMaps(recenterHidden) {
	if (gmspAllMaps) {
		var cachedWidth = jQuery(window).width();
		jQuery(window).resize(function(){
			var newWidth = jQuery(window).width();
			if( newWidth !== cachedWidth ) { 
				for (var i=0; i<gmspAllMaps.length; i++) {
					google.maps.event.trigger(gmspAllMaps[i][0], "resize");
					gmspAllMaps[i][0].setCenter(gmspAllMaps[i][1]);
					gmspAllMaps[i][0].setZoom(gmspAllMaps[i][2]);
					if (gmspAllMaps[i][3] === '1') {
						gmspAllMaps[i][0].fitBounds(gmspAllMaps[i][4]);
					}
				}
				cachedWidth = newWidth;
			}
		});
		
		if( recenterHidden === 'recenterHidden') { 
			for (var i=0; i<gmspAllMaps.length; i++) {
				google.maps.event.trigger(gmspAllMaps[i][0], "resize");
				gmspAllMaps[i][0].setCenter(gmspAllMaps[i][1]);
				gmspAllMaps[i][0].setZoom(gmspAllMaps[i][2]);
				if (gmspAllMaps[i][3] === '1') {
					gmspAllMaps[i][0].fitBounds(gmspAllMaps[i][4]);
				}
			}
		}
		
	}
}


jQuery(document).ready(function () {
	jQuery('#'+'gmsp-map').click( function() {setTimeout(function() {gmspResizeMaps('recenterHidden');}, 500)} );
});
