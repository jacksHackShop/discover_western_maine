<?php
	
class gmsp_TabView {
	public function __construct( $pluginContext) {
		$this->_pluginContext = $pluginContext;
	}
	
	public function displayTab( $tabToDisplay) {
		if( !isset( $tabToDisplay) ) {
			return false;
		}
	
		/* @todo: display the tab view interface here */
		$tabViewTemplate = "
			<div class=\"wrap\">
				<h1> Google Maps - Simple Pins Plugin Settings </h1>

				<h2 class=\"nav-tab-wrapper\">
					<a href=\"?page=google_maps_simple_pins&tab=add_marker\"
						class=\"nav-tab %%NAV_TAB_ADD_MARKERS%%\"> Add Markers
					</a>  
					<a href=\"?page=google_maps_simple_pins&tab=add_map\"
						class=\"nav-tab %%NAV_TAB_ADD_MAPS%%\"> Manage Maps
					</a>
					<a href=\"?page=google_maps_simple_pins&tab=icon_manager\"
						class=\"nav-tab %%NAV_TAB_ADD_ICON_MANAGER%%\"> Manage Marker Icons
					</a>
					<a href=\"?page=google_maps_simple_pins&tab=map_styles_manager\"
						class=\"nav-tab %%NAV_TAB_ADD_MAP_STYLES_MANAGER%%\"> Manage Map Styles
					</a>
					<a href=\"?page=google_maps_simple_pins&tab=export_import\"
						class=\"nav-tab %%NAV_TAB_ADD_EXPORT_IMPORT%%\"> Export/Import
					</a>
					<a href=\"?page=google_maps_simple_pins&tab=settings\"
						class=\"nav-tab %%NAV_TAB_ADD_SETTINGS%%\"> Settings
					</a>
				</h2>

			</div>

			%%ACTIVE_TAB_CONTENT%%
		";

		$tabViewTemplate = str_replace( "%%NAV_TAB_ADD_MARKERS%%",
			( $this->_pluginContext->getActiveTabName() == "add_marker" ) ? "nav-tab-active" : "",
				$tabViewTemplate);
		$tabViewTemplate = str_replace( "%%NAV_TAB_ADD_MAPS%%",
			( $this->_pluginContext->getActiveTabName() == "add_map" ) ? "nav-tab-active" : "",
				$tabViewTemplate);
		$tabViewTemplate = str_replace( "%%NAV_TAB_ADD_ICON_MANAGER%%",
			( $this->_pluginContext->getActiveTabName() == "icon_manager" ) ? "nav-tab-active" : "",
				$tabViewTemplate);
		$tabViewTemplate = str_replace( "%%NAV_TAB_ADD_MAP_STYLES_MANAGER%%",
			( $this->_pluginContext->getActiveTabName() == "map_styles_manager" ) ? "nav-tab-active" : "",
				$tabViewTemplate);

		$tabViewTemplate = str_replace( "%%NAV_TAB_ADD_EXPORT_IMPORT%%",
			( $this->_pluginContext->getActiveTabName() == "export_import" ) ? "nav-tab-active" : "",
				$tabViewTemplate);
		$tabViewTemplate = str_replace( "%%NAV_TAB_ADD_SETTINGS%%",
			( $this->_pluginContext->getActiveTabName() == "settings" ) ? "nav-tab-active" : "",
				$tabViewTemplate);

		$tabViewTemplate = str_replace( "%%ACTIVE_TAB_CONTENT%%", 
			$tabToDisplay->display() , $tabViewTemplate
		);

		echo $tabViewTemplate;
		
		return true;
	}
	
	

	private $_pluginContext;
};

?>