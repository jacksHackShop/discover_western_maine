<?php
/** \file SavedMaps.php
 * Describes a tab with a list of saved maps and "Add new" button
 * A place to put actions and properties that apply to all saved maps
 */
 
/* Make sure we don't expose any info if called directly */
if ( !function_exists( 'add_action' ) ) {
	die("This application is not meant to be called directly!");
}

require_once('SavedMapsTable.php');
 
class gmsp_SavedMaps{
	public function __construct( $debugActive = false) {
		$this->_listOfSavedMaps = new gmsp_SavedMapsList( "gmsp-Maps", $debugActive);
	}
	
	public function displaySavedMaps() {
		return $this->displayAddNewMapButton() .
			$this->_listOfSavedMaps->displaySavedMapsList();
	}
	
	private function displayAddNewMapButton() {
		$tmpAddNewMapButton =
			"<form id=\"form-id-add-new-map\"
				method=\"POST\"
				action=\"". admin_url( 'admin-post.php' ). "\"> 
					<h2> Maps <input type=\"submit\"
						class=\"page-title-action\"
						id=\"delete_selected_marker\"
						value=\"Add new\"></h2>
					<input type=\"hidden\"
						name=\"action\"
						value=\"gmsp_add_new_map\">
					<input type=\"hidden\"
						name=\"_wp_http_referer\"
						value=\"" . urlencode( $_SERVER['REQUEST_URI'] ) . "\">
			</form>";
			
		return $tmpAddNewMapButton;
	}

	private $_listOfSavedMaps = array();
};

?>