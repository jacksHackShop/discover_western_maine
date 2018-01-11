<?php

require_once("TabBase.php");

class gmsp_TabExportImport extends gmsp_TabBase {
	public function __construct( $pluginContext) {
		gmsp_TabBase::__construct(  "export_import", $pluginContext );
	}
	
	public function display() {
		
		if ($this->getPluginContext()->getProVersionUsage() === "gmsp_pro") {
			require_once('ExportImport.php');
			$export_import = new gmsp_ExportImport();
			$strTemplateIconManager = $export_import->gmsp_exporter_options_page();
		}
		
		else {
			$strTemplateIconManager = "<h2> The ability to export/import your maps and markers is available in our GMSP PRO version. Please check bunte-giraffe.de for details. </h2>";
		}
		
		return $strTemplateIconManager;
		
	}
};