// Custom map styles for GMSP plugin
jQuery(document).ready(function() {
	gmspGetCustomMapStyles();
});
function gmspGetCustomMapStyles() {
	
	if (typeof gmsp_MapTypes !== 'undefined' && typeof gmsp_MapTypesJSA !== 'undefined') {
		if (typeof gmsp_MapTypes !== 'object') { 
			try {
				gmsp_MapTypes = JSON.parse(gmsp_MapTypes); 
			}
			catch(err) {
				console.log('Could not parse '+gmsp_MapTypes);
			}
		}

		for (var key in gmsp_MapTypesJSA) {
			try {
				var mapStyleJsArray = "["+gmsp_MapTypesJSA[key].replace(/\\/g, "").trim().slice(1,-1)+"]";
				var mapStyleJsObject = JSON.parse(mapStyleJsArray);
			}
			catch(err) {
				try {
					mapStyleJsArray = mapStyleJsArray.replace(/featureType/g, '"featureType"').replace(/elementType/g, '"elementType"').replace(/stylers/g, '"stylers"').replace(/color/g, '"color"');
					var mapStyleJsObject = JSON.parse(mapStyleJsArray);
				}
				catch(err) {
					console.log('Could not parse '+key);
					gmsp_MapTypes[key] = new google.maps.StyledMapType('roadmap');
					break;
				}
			}
			gmsp_MapTypes[key] = new google.maps.StyledMapType(mapStyleJsObject);
		}	
	}
	
}