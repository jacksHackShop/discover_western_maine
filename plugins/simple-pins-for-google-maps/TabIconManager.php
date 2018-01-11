<?php

require_once("TabBase.php");

class gmsp_TabIconManager extends gmsp_TabBase {
	public function __construct( $pluginContext) {
		gmsp_TabBase::__construct(  "icon_manager", $pluginContext );
	}
	
	public function display() {
		
		if ($this->getPluginContext()->getProVersionUsage() === "gmsp_pro") {
			require_once('IconManager.php');
			$pro_icons = new gmsp_IconManager;
			$strTemplateIconManager = $pro_icons->gmsp_showMarkerIcons();
		}
		
		else {
			$strTemplateIconManager = "<h2> The extended icon set and the ability to upload custom icons is available in our GMSP PRO version. Please check bunte-giraffe.de for details. </h2>";
		}
		
		return $strTemplateIconManager;
		
	}
};