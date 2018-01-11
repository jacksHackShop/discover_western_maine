<?php

require_once("TabBase.php");


class gmsp_TabMarkers extends gmsp_TabBase {
	public function __construct( $pluginContext) {
		gmsp_TabBase::__construct(  "add_marker", $pluginContext );
	}

	public function display() {	
		$strTemplateAddMarker = "<div id=\"poststuff\">
			<div id=\"postbox-container\" class=\"postbox-container\" style=\"width: 380px; padding-right: 50px\">
				<div class=\"meta-box-sortables ui-sortable\" id=\"normal-sortables\">
					<div class=\"postbox\" id=\"add_markers\">
						<button type=\"button\" class=\"handlediv button-link\" aria-expanded=\"true\">
							<span class=\"screen-reader-text\"> Toggle </span>
							<span class=\"toggle-indicator\" aria-hidden=\"true\"> </span>
						</button>
						<div title=\"Click to toggle\" class=\"handlediv\"><br></div>
						<h3 class=\"hndle\"><span>Add marker</span></h3>
						<div class=\"inside\">
							<div id=\"get_coords_by_address\">
								<form id=\"form_coord_by_address\">
									<table class=\"form-table\">
										<tbody>
											<tr>
												<th>
													<input type=\"text\" id=\"postal_address\" autofocus placeholder=\"Marker Address ...\">
												</th>
												<td>
													<input type=\"submit\"
														id=\"get_coordinates\"
														class=\"button button-primary\"
														value=\"Get coordinates\"
														onclick=\"event.preventDefault();getCoordsByAddress()\">
												</td>
											</tr>
										</tbody>
									</table>
								</form>
							</div>
							<div id=\"selected_item_properties\">
								<form id=\"form_add_selected_marcer\" method=\"POST\" action=\"%%ADMIN_POST_URL%%\">
									<table class=\"form-table\">
										<tbody>
											<tr>
												<th scope=\"row\">
													<label for=\"marker_name\"> 
														Marker name: <sup style=\"color:red;\"> * </sup>
													</label>
												</th>
												<td> 
													<input type=\"text\" id=\"marker_name\" name=\"marker_name\" required> </input>
												</td>
											</tr>
											<tr>
												<th scope=\"row\">
													<label for=\"marker_latitude\"> Latitude: 
														<sup style=\"color:red;\"> * </sup>
													</label>
												</th>
												<td> 
													<input type=\"text\" id=\"marker_latitude\" name=\"marker_latitude\" required> 
													</input>
												</td>
											</tr>
											<tr>
												<th scope=\"row\">
													<label for=\"marker_longitude\">
														Longitude:<sup style=\"color:red;\"> * </sup>
													</label>
												</th>
												<td>
													<input type=\"text\" id=\"marker_longitude\" name=\"marker_longitude\" required>
													</input>
												</td>
											</tr>
											<tr style=\"background-color:#eaeaea; border-bottom: 5px solid white; cursor:pointer;\" onclick=\"addMarkerTabInfoWindowBE(event, this);\">
												<td colspan=\"2\">
													<a href=\"#\" id=\"add_marker_info\" style=\"font-weight:700\"> Add info-window
													<img src=\"%%ICON_FOR_INFO%%\" style=\"float:right;padding-right:20px\"> </a>
												</td>
											</tr>
											<tr id=\"info-window\" style=\"display:none\"> 
												<td colspan=\"2\">
												%%WPEDITOR%%
													<!--textarea id=\"marker_info_body\" name=\"marker_info_body\" oninput=\"showInfoWindowOnChangeBE(this);\"></textarea-->	
												<input id=\"info_win_width\"
													type=\"range\"
													min=\"10\"
													max=\"450\"
													value=\"200\"
													oninput=\"if (tinymce.activeEditor.getContent() !== '') { tinymce.activeEditor.execCommand('bold');outputUpdate('info_win_width_output', value+'px'); }\"
													name=\"info_win_width\"
													style=\"border:none; background:none; box-shadow:none; padding:0; margin-top:20px; margin-right: 20px; font-size:13px;width:80%; float:left\">
												<output for=\"info_win_width\" id=\"info_win_width_output\" style=\"margin-top:20px;float:left;font-weight:700;\">200px</output>
												</td>
											</tr>
											<tr style=\"background-color:#eaeaea; border-bottom:10px solid white;cursor:pointer\" onclick=\"addMarkerIconBE(event, this);showCustomMarkerOnSelect();\">
												<td colspan=\"2\">
													<a href=\"#\" id=\"add_marker_icon\" style=\"font-weight:700\"> Add marker-icon
													<img src=\"%%ICON_FOR_MAP_PIN%%\" style=\"float:right;padding-right:20px\"></a>
												</td>
											</tr>
											<tr id=\"marker-icon\" style=\"display:none\">
												<td colspan=\"2\" >
													<div style=\"height:160px; overflow-y:scroll; resize:vertical;border:1px solid #eaeaea;\"> %%GET_ALL_THUMBNAILS%%
													</div>
												</td>
											</tr>
										</tbody>
									</table>

									<p class=\"submit\">
										 <input type=\"submit\" id=\"add_selected_marker\" class=\"button button-primary\" value=\"Save Marker\" style=\"float:right; margin-left:10px;\"> </input>
										 <input type=\"submit\" id=\"clone_selected_marker\" name=\"clone_selected_marker\" class=\"button button-secondary\" value=\"Save As New\" style=\"visibility:hidden\"> </input>
										 <input type=\"button\" id=\"cancel_editing_selected_marker\" class=\"button button-secondary\" value=\"Cancel\" onclick=\"onButtonCancelEditingMap(event);\" style=\"visibility:hidden;margin-left:10px;\"></input>
										  <input type=\"hidden\" id=\"marker_id\" name=\"marker_id\" value=\"\">
										 <input type=\"hidden\" name=\"action\" value=\"gmsp_save_prepared_marker\">
										 <input type=\"hidden\" name=\"_wp_http_referer\" value=\"%%REDIRECT_VALUE%%\">
									</p>
									
									<div class=\"clear\"> </div>
								</form>
							</div>	
						</div>
					</div>

					<div class=\"postbox\">
						<button type=\"button\" class=\"handlediv button-link\" aria-expanded=\"true\">
							<span class=\"screen-reader-text\"> Toggle </span>
							<span class=\"toggle-indicator\" aria-hidden=\"true\"> </span>
						</button>
						<div title=\"Click to toggle\" class=\"handlediv\"> <br> </div>
						<h3 class=\"hndle\">
							<span> Saved markers </span>
						</h3>
						<div class=\"inside\">
							<form id=\"form_process_selected_marker\"
								method=\"POST\"
								action=\"%%ADMIN_POST_URL%%\">
								<table class=\"form-table\">
									<tbody>
										<tr>
											<th>
												<ul id=\"list_of_saved_markers\" name=\"saved_markers\"
													class=\"cat-checklist\" style=\"width:100%\">
													%%MARKER_OPTION_TAGS%%
												</ul> 
												%%MARKER_INFO%%
												<input type=\"hidden\" id=\"mapCenter\" name=\"mapCenter\" value=\"\">
												<input type=\"hidden\" id=\"mapZoom\" name=\"mapZoom\" value=\"13\">
											</th>
										</tr>
									</tbody>
								</table>

								<p class=\"submit\">
									<input type=\"submit\" class=\"button button-secondary\"
										id=\"delete_selected_marker\"
										value=\"Delete Marker\"
										onclick=\"changesConfirmation(event, 'Warning: marker may already be in use and will be permanently removed from all your maps. Continue?'); \"
										name = \"delete_selected_marker\">
									</input>

									<input type=\"submit\" class=\"button button-primary\"
										id = \"create_map_with_selected_marker\"
										value = \"Create map with selected\"
										name = \"create_map_with_selected_marker\">
									</input>

									<input type=\"hidden\" name=\"action\"
										value=\"gmsp_delete_selected_marker\">
										
									<input type=\"hidden\" name=\"_wp_http_referer\"
										value=\"%%REDIRECT_VALUE%%\">
								</p>
								<div class=\"clear\"> </div>
							</form>					
						</div>
					</div>
					<div class=\"postbox\">
						<button type=\"button\" class=\"handlediv button-link\" aria-expanded=\"true\">
							<span class=\"screen-reader-text\"> Toggle </span>
							<span class=\"toggle-indicator\" aria-hidden=\"true\"> </span>
						</button>
						<div title=\"Click to toggle\" class=\"handlediv\">
							<br>
						</div>
						<h3 class=\"hndle\">
							<span> Quick Feedback </span>
						</h3>
						<div class=\"inside\">
							<form id = \"feedback_form\"
							method = \"POST\"
								action=\"%%ADMIN_POST_URL%%\">
								<div> Anything bugs you? We'll do our best to fix it asap. </div>
								<textarea name=\"gmsp_feedback_text\" id=\"gmsp_feedback_text\" style=\"width:100%; margin: 10px 0px 10px 0px\"> </textarea> <br/>
								<p class=\"submit\">
									<input type=\"submit\" class=\"button button-primary\"
												id = \"submit_feedback\"
												value = \"Submit feedback\"
												name = \"submit_feedback\">
									</input>
									
									<input type=\"hidden\" name=\"action\"
										value=\"gmsp_submit_feedback\">
									
									<input type=\"hidden\" name=\"_wp_http_referer\"
										value=\"%%REDIRECT_VALUE%%\">
								</p>
								<div class=\"clear\"> </div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id=\"coord_selector\" style=\"float:left\">
			<div id=\"googleMap\" style=\"width:500px;height:380px;\"> </div>
		";
		if ($this->getPluginContext()->getProVersionUsage() === "gmsp_free") {
			$strTemplateAddMarker .= "
			<div>
				<form id=\"form_edit_selected_marker\" method=\"POST\" action=\"%%ADMIN_POST_URL%%\">
					<table class=\"form-table\">
						<tbody>
							<tr>
								<th scope=\"row\"> <label for=\"marker_name_info\"> Marker name: </label> </th>
								<td> <input type=\"text\" id=\"marker_name_info\" name=\"marker_name_info\" disabled> </input> </td>
							</tr>
							<tr>
								<th scope=\"row\"> <label for=\"marker_address_info\"> Marker address:</label> </th>
								<td> <input type=\"text\" id=\"marker_address_info\" name=\"marker_address_info\" disabled> </input> </td>
							</tr>
							<tr>
								<th scope=\"row\">
									<label for=\"marker_latitude_info\"> Latitude: </label>
								</th>
								<td>
									<input type=\"text\" id=\"marker_latitude_info\" name=\"marker_latitude_info\" disabled>
									</input>
								</td>
							</tr>
							<tr>
								<th scope=\"row\"><label for=\"marker_longitude_info\"> Longitude: </label> </th>
								<td> <input type=\"text\" id=\"marker_longitude_info\" name=\"marker_longitude_info\" disabled> </input>
								</td>
							</tr>
							<tr>
								<th scope=\"row\"><label for=\"marker_info_window_info\">Info popup:</label></th>
								<td> <input type=\"hidden\" id=\"marker_info_window_info_hidden\" name=\"marker_info_window_info\" disabled> </input>
								<p id=\"marker_info_window_info\"></p>
								</td>
							</tr>
							<tr>
							<th colspan=\"2\">To edit marker, please update to <a href=\"http://bunte-giraffe.de/product/google-maps-simple-pins-pro-plugin/\" target=\"_new\">GMSP PRO ($16)</a></th>
							</tr>
						</tbody>
					</table>
					<div class=\"clear\"> </div>
				</form>
			</div>
			";
		}
		
		$strTemplateAddMarker .= "</div>";
	
		$strTemplateAddMarker = str_replace( "%%ADMIN_POST_URL%%",
			$this->getPluginContext()->getAdminPostUrl(),
			$strTemplateAddMarker );
		
		$strTemplateAddMarker = str_replace( "%%ICON_FOR_INFO%%",
			$this->getPluginContext()->getIconForInfo(),
			$strTemplateAddMarker );
		
		$strTemplateAddMarker = str_replace( "%%ICON_FOR_MAP_PIN%%",
			$this->getPluginContext()->getIconForMapPin(),			
			$strTemplateAddMarker );

		$strTemplateAddMarker = str_replace( "%%GET_ALL_THUMBNAILS%%",
			$this->getPluginContext()->getAllTumbs(),
			$strTemplateAddMarker );
		
		$strTemplateAddMarker = str_replace( "%%REDIRECT_VALUE%%",
			$this->getPluginContext()->getRedirectValue(),
			$strTemplateAddMarker);			
						
		if ($this->getPluginContext()->getProVersionUsage() === "gmsp_pro") {
			$markerOptionTemplate = 
				"<li>
					<label class='selectit'>
						<input id=\"%%MARKER_ID%%\"
							value=\"%%MARKER_ID%%\"
							type=\"checkbox\"
							name=\"checked_markers[]\"
							onclick=\"gmspToggleNewEditMarker('%%MARKER_ID%%'); showMarkerInfo('%%MARKER_ID%%', '', 'true'); \"> %%MARKER_NAME%%
					</label>
				 </li>\n";
		}
		else {
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
		}

		$markerInfoTemplate =
			"<input type=\"hidden\"
				id=\"%%MARKER_ID%%_%%MARKER_PROPERTY_NAME%%\"
				value=\"%%MARKER_NAME%%\">";

		$onMap = array();
		$markerInfo = "";
		$markerOptionTags = "";
		
		$storedMarkers = get_option( "gmsp_Markers", false);
		
		if ( !empty($storedMarkers) ) {
				
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

			$strTemplateAddMarker = str_replace( "%%MARKER_OPTION_TAGS%%",
				$markerOptionTags,
				$strTemplateAddMarker);
				
			$strTemplateAddMarker = str_replace( "%%MARKER_INFO%%",
				$markerInfo,
				$strTemplateAddMarker);
										
			$strTemplateAddMarker = str_replace( "%%WPEDITOR%%",
				gmsp_plugin_wp_editor(),
				$strTemplateAddMarker);
			
						
		}
		else {
			$strTemplateAddMarker = str_replace( "%%MARKER_OPTION_TAGS%%",
				'',
				$strTemplateAddMarker);
				
			$strTemplateAddMarker = str_replace( "%%MARKER_INFO%%",
				'',
				$strTemplateAddMarker);
				
		}

		return $strTemplateAddMarker;
	}
};