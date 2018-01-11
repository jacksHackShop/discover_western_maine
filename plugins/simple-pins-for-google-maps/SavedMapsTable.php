<?php
/** \file SavedMapsTable.php
 * Provides a list of saved maps and tools to manage and alter them.
 */
 
/* Make sure we don't expose any info if called directly */
if ( !function_exists( 'add_action' ) ) {
	die("This application is not meant to be called directly!");
}

define( "MAP_IN_PROGRESS_ID", "gmsp-map-in-progress");

function gmsp_TRACE( $debugMessage, $debugActive) {
	if( !$debugActive ){
		return;
	}

	echo "DBG: ". $debugMessage . "<br>";
}

class gmsp_SavedMapsList {

/* ctors / dtors */
	public function __construct( 
		$savedMapsOptionName,
		$debugActive = false
	) {
		$this->_debugActive = $debugActive;
	
		gmsp_TRACE( "SavedMapLists::ctor()", $this->_debugActive);
	
		$this->_columnNames = array(
			"Title" => array( "id" => "map_title", 
				"class" => "manage-column column-title column-primary",
				"value" => "Map name"
			),
			"Markers" => array( "id" => "markers", 
				"class" => "manage-column column-markers",
				"value" => "Markers"
			),
			"Properties" => array( "id" => "properties",
				"class" => "manage-column column-properties",
				"value" => "Properties"
			),
			"Shortcode" => array( "id" => "shortcodes", 
				"class" => "manage-column column-shortcodes",
				"value" => "Shortcode"
			),
			"Actions" => array( "id" => "action",
				"class" => "manage-column column-actions",
				"value" => "Actions"
			)
		);
		
		$this->_savedMaps = get_option("gmsp-Maps", false);
		
		$this->_templateMapRecord = "
			<tr>
				<td colspan=\"5\">
				<form id=\"%%MAP_ID%%\" method=\"POST\" action=\"". admin_url( 'admin-post.php' ). "\"  style=\"display:table; width:100%;clear:both;\">
				<div class=\"map_name_cell\">
					<input id=\"user_selected_map_name\"
						type=\"text\"
						name=\"user_selected_map_name\"
						%%MAP_NAME%%
						onclick=\"onButtonEditMap( '%%MAP_ID%%');\"
						required
					>
					<input type=\"hidden\"
						value=\"%%MAP_ID%%\"
						name=\"map_id\">
				</div>
				<div class=\"map_markers_cell\">
					<!--[if !IE]>--><div class=\"gmsp-tagchecklist\" style=\"width:100%;height:150px;overflow:auto;\" id=\"markersFor%%MAP_ID%%\"><!--<![endif]-->
					<!--[if IE ]><div class=\"gmsp-tagchecklist-ie\" style=\"width:100%;height:150px;overflow:auto;\" id=\"markersFor%%MAP_ID%%\"><![endif]-->
						%%MAP_MARKER_TAGS%%
					</div>
					<div id=\"list_of_saved_markers_%%MAP_ID%%\" style=\"width:96%; display: none\">
						<ul id=\"list_of_saved_markers\" name=\"saved_markers\" class=\"cat-checklist\" style=\"width:100%\">
							%%LIST_OF_SAVED_MARKERS%%
						</ul>
						<p class=\"submit\" id=\"actionsFor%%MAP_ID%%\">
							<input type=\"submit\"
								id=\"save-edited-map\"
								class=\"button button-primary\"
								name=\"save-edited-map\"
								value=\"Add to Map\">
							<input type=\"hidden\"
								name=\"_wp_http_referer\" value=\"%%REFERRER_URL%%\">
						</p>
					</div>
				</div>
				<div class=\"map_properties_cell\">
					<div class=\"show-only-on-editing%%MAP_ID%% gmsp-properties\">
						<input type=\"radio\"
							id=\"autocenter_autozoom%%MAP_ID%%\"
							name=\"autocenter_autozoom%%MAP_ID%%\"
							value=\"1\" %%MAP_AUTOCENTER_AUTOZOOM%%>							
						<div class=\"tooltip\"> Autocenter, autozoom
							<span class=\"tooltiptext\">
								Automatically adapts zoom level and center of your map to ensure all markers are visible
							</span>
						</div>
					</div>
					<div class=\"show-only-on-editing%%MAP_ID%% gmsp-properties\">
						<input type=\"radio\" id=\"manualcenter_zoom%%MAP_ID%%\"
							name=\"autocenter_autozoom%%MAP_ID%%\"
							value=\"0\" %%MAP_MANUAL_ZOOM%%>
						<div class=\"tooltip\"> Set zoom & center manually
							<span class=\"tooltiptext\">
								Change zoom and center on the map preview or enter custom values in the fields below
							</span>
						</div>
						
					</div>
					<div class=\"gmsp-properties\">
						<label for=\"mapType%%MAP_ID%%\">Map Style:</label>
						<select name=\"map_type%%MAP_ID%%\" id=\"mapType%%MAP_ID%%\" onchange=\"
						if ( typeof gmsp_MapTypes !== 'undefined' && this.value.toUpperCase() in gmsp_MapTypes) {gmspChangeDynamicallyMapType(this.value);} 
						else {map.setMapTypeId('roadmap');}\" disabled>
							%%MAP_TYPE%%
							%%MAP_TYPES%%
						</select> <br>
					</div>
					<div class=\"gmsp-properties\">
						Center Coordinates:
						<input id=\"mapCenter%%MAP_ID%%\"
							name=\"orig_center\"
							value=\"%%MAP_CENTER_COORDS%%\"
							style=\"border:none; background:none; box-shadow:none; padding:0;width:100%;font-size:13px\"> <br>
					</div>
					<div class=\"gmsp-properties\">
						<label for=\"mapZoom%%MAP_ID%%\">Zoom Level:</label>
						<input id=\"mapZoom%%MAP_ID%%\"
							type=\"range\"
							min=\"0\"
							max=\"20\"
							step=\"1\"
							oninput=\"gmspSetZoomDynamically(this);jQuery(this).next().attr('id', 'zoomValue');outputUpdate('zoomValue', value);\"
							name=\"orig_zoom\"
							value=\"%%MAP_ZOOM_FACTOR%%\"
							style=\"border:none; background:none; box-shadow:none; padding:0;font-size:13px\" disabled>
						<output for=\"mapZoom%%MAP_ID%%\" id=\"zoom_value%%MAP_ID%%\"></output>
						<input id=\"mapZoomDummy%%MAP_ID%%\" type=\"hidden\" value=\"%%MAP_ZOOM_FACTOR%%\" name=\"orig_zoom\">
					</div>
				</div>
				<div class=\"map_shortcode_cell\">				
					<textarea readonly
						style=\"border:none; background:none; box-shadow:none; padding:0;width:100%;font-size:13px;resize:none;height:60px;\"
						onclick=\"this.select();document.execCommand('copy');\">%%MAP_SHORT_CODE%%</textarea>
				</div> 
				<div class=\"map_actions_cell\">
					<a class=\"button button-secondary\"
						style=\"display:block;width:60px;margin:5px;margin-bottom:10px;\"
						id=\"buttonEdit%%MAP_ID%%\"
						onclick=\"onButtonEditMap('%%MAP_ID%%');\"> Edit
					</a>

					<a class=\"button button-secondary\"
						style=\"display:none;margin:5px;\"
						id=\"buttonCancelEditingMap%%MAP_ID%%\"
						style=\"display: none;\"
						onclick=\"onButtonCancelEditingMap(event);\"> Cancel
					</a>

					 <input type=\"submit\"
						style=\"display:block;margin:5px;\"
						class=\"button button-primary\"
						name=\"save-edited-map\"
						onclick=\"preventEditingMultipleRows('%%MAP_ID%%', event);\"
						value=\"Save\">
						
					<input type=\"submit\"
						class=\"button button-primary gmsp-pro-version-hidden\"
						name=\"clone-edited-map\"
						id=\"clone-edited-map%%MAP_ID%%\"
						style=\"display:none;margin:5px;\"
						onclick=\"preventEditingMultipleRows('%%MAP_ID%%', event);\"
						value=\"Clone\">

					 <input type=\"submit\"
						class=\"button button-primary\"
						name=\"delete-edited-map\"
						style=\"display:block;margin:5px;\"
						onclick=\"preventEditingMultipleRows('%%MAP_ID%%', event, changesConfirmation); \"
						value=\"Delete\">
					 
					 <input type=\"hidden\"
						name=\"action\"
						value=\"gmsp_save_map_with_selected_markers\">

					 <input type=\"hidden\"
						name=\"map-id\"
						value=\"%%MAP_ID%%\">

					<input type=\"hidden\"
						name=\"_wp_http_referer\"
						value=\"%%REFERRER_URL%%\">
				</div>
			</form>
			<div class=\"show-only-on-editing%%MAP_ID%%\">Map preview:
			<input type=\"radio\" value=\"100%\" name=\"preview%%MAP_ID%%\">100% x 768px
			<input type=\"radio\" value=\"800px\" name=\"preview%%MAP_ID%%\">800px x 600px
			<input type=\"radio\" value=\"640px\" name=\"preview%%MAP_ID%%\" checked>640px x 480px
			<input type=\"radio\" value=\"320px\" name=\"preview%%MAP_ID%%\">320px x 240px
			<div id=\"googleMap%%MAP_ID%%\"></div>
			</div> 
			</td>
			</tr>
			";
	}
	
/* Public iface */	
	public function displaySavedMapsList() {
		
		gmsp_TRACE( "SavedMapLists::displaySavedMapsList()", $this->_debugActive);

		$this->_redirectUrl = urlencode( $_SERVER['REQUEST_URI'] );
		
		$strSavedMapsList = "<table class=\"wp-list-table widefat fixed striped\">";
		
		$strSavedMapsList .= $this->displayListHeader();
		
		$strSavedMapsList .= "<tbody  id=\"maps_table\">";
				
		$storedMarkers = get_option( "gmsp_Markers", false);
		
		foreach( array_reverse($this->_savedMaps) as $mapId => $mapProperties) {
			$strSavedMapsList .= $this->displayMap( $mapId, $mapProperties, $storedMarkers);			
		}

		$strSavedMapsList .= "</tbody>";
		$strSavedMapsList .= "</table>";
		
		return $strSavedMapsList;
	}

/* Private methods */
	private function displayMapInProgress( $mapId, $mapProperties, $storedMarkers) {
		
		gmsp_TRACE( "SavedMapLists::displayMapInProgress()", $this->_debugActive);

		$markerOptionTags = $markerInfo = "";
		$tmpTemplateMapRecord = $this->_templateMapRecord;
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_NAME%%",
			"placeholder=\"Your map name here ...\"",
			$tmpTemplateMapRecord
		);
		
		/* Populate list of selected markers */
		/* TODO: Add check if there are selected markers */
		$listOfSelectedMarkers = "";
		foreach ( $mapProperties[ "markers-on-map" ] as $selectedMarkerIndex => $selectedMarkerId) {
			if (!empty($storedMarkers[ $selectedMarkerId ]["name"])) {
				$listOfSelectedMarkers .= 
					"<span id=\"" . "gmsp-map-in-progress-" . $selectedMarkerId . "\" style=\"display:block;\">
						<input type=\"hidden\" name=\"gmsp-map-in-progress-list-of-selected-tags[]\" value=\"" . $selectedMarkerId . "\">
						<a id=\"post_tag-check-num-" . $selectedMarkerIndex . "\" " .
							"class=\"gmsp-ntdelbutton\" tabindex=\"" . $selectedMarkerIndex . 
							"\" onclick=\"remove_marker('" . "gmsp-map-in-progress" . "-" . $selectedMarkerId .
								"',". "'gmsp-map-in-progress"."');\"> X
						</a>&nbsp;" . stripslashes($storedMarkers[ $selectedMarkerId ]["name"]) . 
					"</span>";
			}
		}

		$tmpTemplateMapRecord = str_replace( "%%MAP_MARKER_TAGS%%", 
			$listOfSelectedMarkers,
			$tmpTemplateMapRecord
		);		
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_ID%%", 
			'gmsp-map-in-progress',
			$tmpTemplateMapRecord
		);		
		

		/* Populate list of all markers */
		foreach( $storedMarkers as $markerId => $markerProperties) {
			if (!in_array($markerId, $mapProperties["markers-on-map"])) {
				$markerOptionTags .= "<li> <label class='selectit'> <input id=\"". 
					$mapId . $markerId . "\" value=\"" .
					$mapId . $markerId . "\" type=\"checkbox\" name=\"gmsp-map-in-progress-list-of-selected-tags[]\" onclick=\"showMarkersInMapPreview('$mapId');showMarkerInfo('" .
					$mapId . $markerId . "','" . $mapId . "');\"> " .
					stripslashes($markerProperties["name"]) . "</label></li>\n";
			}
		}
		
		$tmpTemplateMapRecord = str_replace( "%%LIST_OF_SAVED_MARKERS%%", 
			$markerOptionTags,
			$tmpTemplateMapRecord
		);
		
		/* Populate map properties */
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_CENTER_COORDS%%", 
			$mapProperties["center-coords"],
			$tmpTemplateMapRecord
		);

		$tmpTemplateMapRecord = str_replace( "%%MAP_ZOOM_FACTOR%%", 
			$mapProperties["zoom-factor"],
			$tmpTemplateMapRecord
		);

		$tmpTemplateMapRecord = str_replace( "%%MAP_SHORT_CODE%%", 
			"Please save the map to get a shortcode",
			$tmpTemplateMapRecord
		);

		$tmpTemplateMapRecord = str_replace( "%%REDIRECT_URL%%", 
			$this->_redirectUrl,
			$tmpTemplateMapRecord
		);		

		$tmpTemplateMapRecord = str_replace( "%%MAP_ID%%", 
			$mapId,
			$tmpTemplateMapRecord
		);		
		
		if ( isset($mapProperties["auto"]) && $mapProperties["auto"]==1 ) {
			$tmpTemplateMapRecord = str_replace( "%%MAP_AUTOCENTER_AUTOZOOM%%", 
				'checked',
				$tmpTemplateMapRecord
			);
			$tmpTemplateMapRecord = str_replace( "%%MAP_MANUAL_ZOOM%%", 
				'',
				$tmpTemplateMapRecord
			);
		}
		else {
			$tmpTemplateMapRecord = str_replace( "%%MAP_AUTOCENTER_AUTOZOOM%%", 
				'',
				$tmpTemplateMapRecord
			);
			$tmpTemplateMapRecord = str_replace( "%%MAP_MANUAL_ZOOM%%", 
				'checked',
				$tmpTemplateMapRecord
			);
		}
		
		$storedMapTypes = get_option( "gmsp_MapTypes", false);
		$mapTypesOptions = ''; $mapTypesArray = $mapTypesJSA = array();
		foreach( $storedMapTypes as $name => $mapType) {
			$mapTypesOptions .= '<option value="'.$name.'">'.ucwords($name).'</option>';
			$mapTypesArray[strtoupper($name)] = "gmsp_".$name."StyledMapType";
			$mapTypesJSA[strtoupper($name)] = json_encode($mapType);
		}
		
		wp_localize_script ( 'gmsp-custom-map-styles', 'gmsp_MapTypes', json_encode($mapTypesArray));
		wp_localize_script ( 'gmsp-custom-map-styles', 'gmsp_MapTypesJSA', json_encode($mapTypesJSA));
			
		$tmpTemplateMapRecord = str_replace( "%%MAP_TYPES%%", 
			$mapTypesOptions,
			$tmpTemplateMapRecord
		);		

		$tmpTemplateMapRecord = str_replace( "%%MAP_TYPE%%", 
			isset($mapProperties["map-type"]) ? '<option value="'.$mapProperties["map-type"].'">'.ucwords($mapProperties["map-type"]).'</option>' : '<option value="roadmap">Roadmap</option>',
			$tmpTemplateMapRecord
		);		
		
		return $tmpTemplateMapRecord;
	}


	private function displayCompleteMap( $mapId, $mapProperties, $storedMarkers) {

		$tmpMapName = stripslashes($mapProperties["name"]);
		
		gmsp_TRACE( "SavedMapLists::displayCompleteMap(name=\"" .
			$tmpMapName ."\")",
			$this->_debugActive
		);
		
		$tmpTemplateMapRecord = $this->_templateMapRecord;

		$tmpTemplateMapRecord = str_replace( "%%MAP_NAME%%",
			"value=\"" . $tmpMapName . "\"",
			$tmpTemplateMapRecord
		);
		
		$listOfSelectedMarkers = "";
		foreach ( $mapProperties["markers-on-map"] as $selectedMarkerIndex => $selectedMarkerId) {
			if (!empty($storedMarkers[ $selectedMarkerId ]["name"])) {
				$listOfSelectedMarkers .= 
				"<span id=\"". $mapId.'-'.$selectedMarkerId ."\" style=\"display:block;\">
					<input type=\"hidden\" name=\"".$mapId."-list-of-selected-tags"."[]\" value=\"". $selectedMarkerId . "\">
					<a id=\"post_tag-check-num-" . $selectedMarkerIndex . "\" " .
						"class=\"gmsp-ntdelbutton\" tabindex=\"" . $selectedMarkerIndex . "\" onclick=\"remove_marker('". $mapId.'-'.$selectedMarkerId ."','".$mapId."');\"> X
					</a>&nbsp;" . stripslashes($storedMarkers[ $selectedMarkerId ]["name"]) . 
				"</span>";
			}
		}
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_MARKER_TAGS%%", 
			$listOfSelectedMarkers,
			$tmpTemplateMapRecord
		);	
		
		
		/* Populate list of all markers */
		$markerOptionTags = "";
		foreach( $storedMarkers as $markerId => $markerProperties) {
			if (!in_array($markerId, $mapProperties["markers-on-map"])) {
				$markerOptionTags .= "<li> <label class='selectit'> <input id=\"". 
					$mapId . $markerId . "\" value=\"" .
					$markerId . "\" type=\"checkbox\" name=\"".$mapId."-list-of-selected-tags"."[]\" onclick=\"showMarkersInMapPreview('$mapId');showMarkerInfo('" .
					$mapId . $markerId . "','" . $mapId . "');\"> " .
					stripslashes($markerProperties["name"]) . "</label></li>\n";
			}
		}
		$tmpTemplateMapRecord = str_replace( "%%LIST_OF_SAVED_MARKERS%%", 
			$markerOptionTags,
			$tmpTemplateMapRecord
		);
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_ID%%", 
			$mapId,
			$tmpTemplateMapRecord
		);		
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_CENTER_COORDS%%", 
			isset($mapProperties["center-coords"]) ? $mapProperties["center-coords"] : '',
			$tmpTemplateMapRecord
		);

		$tmpTemplateMapRecord = str_replace( "%%MAP_ZOOM_FACTOR%%", 
			isset($mapProperties["zoom-factor"]) ? $mapProperties["zoom-factor"] : '',
			$tmpTemplateMapRecord
		);

		$tmpTemplateMapRecord = str_replace( "%%MAP_SHORT_CODE%%", 
			"[gmsp_map id=\"".$mapId."\" w=\"100%\" h=\"400px\"]",
			$tmpTemplateMapRecord
		);

		if ( isset($mapProperties["auto"]) && $mapProperties["auto"]==1 ) {
			$tmpTemplateMapRecord = str_replace( "%%MAP_AUTOCENTER_AUTOZOOM%%", 
				'checked',
				$tmpTemplateMapRecord
			);
			$tmpTemplateMapRecord = str_replace( "%%MAP_MANUAL_ZOOM%%", 
				'',
				$tmpTemplateMapRecord
			);
		}
		else {
			$tmpTemplateMapRecord = str_replace( "%%MAP_AUTOCENTER_AUTOZOOM%%", 
				'',
				$tmpTemplateMapRecord
			);
			$tmpTemplateMapRecord = str_replace( "%%MAP_MANUAL_ZOOM%%", 
				'checked',
				$tmpTemplateMapRecord
			);
		} 
		
		$storedMapTypes = get_option( "gmsp_MapTypes", false);
		$mapTypesOptions = ''; $mapTypesArray = $mapTypesJSA = array();
		foreach( $storedMapTypes as $name => $mapType) {
			$mapTypesOptions .= '<option value="'.$name.'">'.ucwords($name).'</option>';
			$mapTypesArray[strtoupper($name)] = "gmsp_".str_replace('-','_',$name)."StyledMapType";
			$mapTypesJSA[strtoupper($name)] = $mapType;
		}
		
		wp_localize_script ( 'gmsp-custom-map-styles', 'gmsp_MapTypes', json_encode($mapTypesArray));
		wp_localize_script ( 'gmsp-custom-map-styles', 'gmsp_MapTypesJSA', $mapTypesJSA);
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_TYPES%%", 
			$mapTypesOptions,
			$tmpTemplateMapRecord
		);		
		
		$tmpTemplateMapRecord = str_replace( "%%MAP_TYPE%%", 
			isset($mapProperties["map-type"]) ? '<option value="'.$mapProperties["map-type"].'">'.ucwords($mapProperties["map-type"]).'</option>' : '<option value="roadmap">Roadmap</option>',
			$tmpTemplateMapRecord
		);		


		return $tmpTemplateMapRecord;
	}

	
	private function displayMap(  $mapId, $mapProperties, $storedMarkers) {
		gmsp_TRACE( "SavedMapLists::displaySavedMaps()", $this->_debugActive);
		
		if( MAP_IN_PROGRESS_ID == $mapId) {
			gmsp_TRACE( "Rendering map in progress ...<br>", $this->_debugActive);
			return $this->displayMapInProgress( $mapId, $mapProperties, $storedMarkers);
		}
		else {
			gmsp_TRACE( "Rendering saved map ...<br>", $this->_debugActive);
			return $this->displayCompleteMap( $mapId, $mapProperties, $storedMarkers);
		}		
	}

	private function displayListHeader( ) {
		gmsp_TRACE( "SavedMapLists::displayListHeader()", $this->_debugActive);
		
		$strListHeader = "<thead> <tr>";
		foreach( $this->_columnNames as $column) {
			$strListHeader .= "<th scope=\"col\" id=\"" . $column["id"] .
				"\" class=\"". $column["class"] .
				"\">" . $column["value"] . "</th>";
		}
		$strListHeader .= "</tr>	</thead>";
		
		return $strListHeader;
	}
	
/* Private members */
	private $_redirectUrl = "";
	private $_savedMaps = array();
	private $_columnNames = array();
	private $_debugActive = false;
	private $_templateMapRecord = "";
}

?>
