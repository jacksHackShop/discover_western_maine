<?php

require_once("TabBase.php");

class gmsp_TabMapStylesManager extends gmsp_TabBase {
	public function __construct( $pluginContext) {
		gmsp_TabBase::__construct(  "map_styles_manager", $pluginContext );
	}
	
	public function display() {
		
		require_once('MapStylesManager.php');
		$map_styles = new gmsp_MapStylesManager;
		$strTemplateMapStylesManager = $map_styles->gmsp_showMapStyles();
		
		$strTemplateMapStylesManager = str_replace( "%%ADMIN_POST_URL%%",
			$this->getPluginContext()->getAdminPostUrl(),
			$strTemplateMapStylesManager );

				
		return $strTemplateMapStylesManager;
		
	}
};