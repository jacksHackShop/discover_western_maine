<?php

require_once("TabBase.php");

class gmsp_TabMaps extends gmsp_TabBase {
	public function __construct( $pluginContext) {
		gmsp_TabBase::__construct(  "add_map", $pluginContext );
	}
	
	public function display() {
	
		$storedMarkers = $this->getPluginContext()->getStoredMarkers();
		
		$markerOptionTags = "";
		$markerInfo = "";
		$onMap = array();
		
		/* Prepare a list of stored markers */
		$markerOptionTemplate = 
			"<li>
				<label class='selectit'>
					<input id=\"%%MARKER_ID%%\"
						value=\"%%MARKER_ID%%\"
						type=\"checkbox\"
						name=\"checked_markers[]\"
						onclick=\"showMarkerInfo('%%MARKER_ID%%');\"> %%MARKER_NAME%%
				</label>
			 </li>\n";

		$markerInfoTemplate =
			"<input type=\"hidden\"
				id=\"%%MARKER_ID%%_%%MARKER_PROPERTY_NAME%%\"
				value=\"%%MARKER_NAME%%\">";

		foreach( $storedMarkers as $markerId => $markerProperties) {
			$markerOptionTagsTmp = str_replace( "%%MARKER_ID%%", $markerId, $markerOptionTemplate);
			$markerOptionTags .= str_replace( "%%MARKER_NAME%%", stripslashes($markerProperties["name"]),  $markerOptionTagsTmp);
				
			foreach( $markerProperties as $key => $value) {
				$markerInfoTmp = str_replace( "%%MARKER_ID%%", $markerId, $markerInfoTemplate);
				$markerInfoTmp = str_replace( "%%MARKER_PROPERTY_NAME%%", $key, $markerInfoTmp);
				$markerInfo .= str_replace( "%%MARKER_NAME%%", htmlentities(stripslashes($value)), $markerInfoTmp );
			}
			array_push( $onMap, $markerProperties["name"] );
		}
	
		$savedMaps = new gmsp_SavedMaps();
		$strTemplateAddMaps = $savedMaps->displaySavedMaps();
		
		$strTemplateAddMaps .= $markerInfo;
		$strTemplateAddMaps .= "<input type=\"hidden\" id=\"editCheck\" value=''>";	
	
		return $strTemplateAddMaps;
	}
};