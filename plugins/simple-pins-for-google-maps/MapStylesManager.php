<?php

/* Make sure we don't expose any info if called directly */
if ( !function_exists( 'add_action' ) ) {
	die("This application is not meant to be called directly!");
}

class gmsp_MapStylesManager {
	public function gmsp_showMapStyles() {
	
		$storedMapTypes = get_option( "gmsp_MapTypes", false);
						
		$this->_html .= '<h2>Available Map Styles</h2>
			<form id="available_map_types" method="POST" action="%%ADMIN_POST_URL%%">';
			
		foreach( $storedMapTypes as $name => $mapType) {
			$name_esc = preg_replace("/[^a-zA-Z0-9]+/", "_", $name);//str_replace(array('-', ' ', '!', '?', '.', '#', '/', '\\' ,'|', '\'', '\"', '£', '"'), '_', $name);
			$rand_num = rand(1,100);
			$this->_html .= "<div style=\"float:left;margin-right:20px;margin-bottom:20px;position:relative;border:1px solid white;padding:10px;\"><div>$name</div>
			<div id=\"map-$name_esc\" style=\"width:250px;height:150px;\"></div>
			<span id=\"remove_map_style_$name_esc\"></span>
			<script>
			  var map;
			  function initMap".$name_esc.$rand_num."() {
				map = new google.maps.Map(document.getElementById('map-$name_esc'), {
				  center: {lat: 40.707981745095005, lng: -74.00525061621092},
				  zoom: 10
				});

				try {
					var tempMapType = '".preg_replace('/\s+/', '', str_replace('\"', '"', $mapType))."';
					tempMapType = JSON.parse(tempMapType);
					if (tempMapType instanceof Array) {
						if ('$name'.toUpperCase() === 'SATELLITE') {
							map.setMapTypeId('satellite');
						}
						else if ('$name'.toUpperCase() === 'ROADMAP') {
							map.setMapTypeId('roadmap');
						}
						else if ('$name'.toUpperCase() === 'HYBRID') {
							map.setMapTypeId('hybrid');
						}
						else if ('$name'.toUpperCase() === 'TERRAIN') {
							map.setMapTypeId('terrain');
						}
						else {
							map.mapTypes.set('$name', new google.maps.StyledMapType(tempMapType) );
							map.setMapTypeId('$name');
						}
					}
					else {
						throw new UserException('InvalidJsArray');
					}
				}
				catch(err) {
					document.getElementById('map-$name_esc').innerHTML = \"<br>Looks like the JavaScript Style Array <br> you pasted was not correct. <br> Go to <a href='https://snazzymaps.com/'>SnazzyMaps.com</a>, copy the needed JavaScript Style Array and save it again with the same name.<br><br>You can also delete this map style.\";
				}
			  }
			jQuery(document).ready( function() { 
				initMap".$name_esc.$rand_num."();
			 });
			document.getElementById('remove_map_style_$name_esc').addEventListener('click', function() {
				var confirmRes = confirm(
					'Map style $name will be also deleted from all maps using it. Continue?'
					);

				if ( confirmRes ) {
					document.getElementById('gmspMapTypeToRemove').value = '$name';
					document.getElementById('available_map_types').submit();
				}
				
			});
			</script>
			</div>";
		}	
				
		$this->_html .= "
				<input type=\"hidden\" name=\"action\" value=\"gmsp_remove_map_type\">
				<input type=\"hidden\" id=\"gmspMapTypeToRemove\" name=\"gmspMapTypeToRemove\">			
			</form><div style=\"clear:both;\"></div>";
		

		
		$this->_html .= "<h2>Add New Map Style</h2>
		<form id=\"customMapType\" method=\"POST\" action=\"%%ADMIN_POST_URL%%\">
			<table class=\"form-table\">
				<tr>
					<th><label for=\"gmspMapTypeName\"><a href=\"https://snazzymaps.com\" target=\"new\">Snazzy Maps</a> Map Type Name: </label></th>
					<td><input type=\"text\" name=\"gmspMapTypeName\"></td>
				</tr>
				<tr>
					<th><label for=\"gmspMapType\"><a href=\"https://snazzymaps.com\" target=\"new\">Snazzy Maps</a> <br />Javascript Style Array:</label></th> 
					<td><textarea name=\"gmspMapType\"></textarea></td>
				</tr>
				<tr>
					<th colspan=\"2\">
						<input type=\"submit\" class=\"button button-primary\" id=\"add_map_type\" value=\"Add Map Style\">
						<input type=\"hidden\" name=\"action\" value=\"gmsp_add_new_map_type\">							
					</th>
				</tr>
			</table>
		</form>";
				
		return $this->_html;
	}
	
	private $_html = "";
	
}


?>