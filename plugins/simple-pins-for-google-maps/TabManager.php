<?php

require_once( "TabView.php");
require_once( "PluginContext.php");

class gmsp_TabManager {
	public function __construct( $tabView) {
		$this->_tabView = $tabView;
	}

	public function addTab( $tab, $isActiveTab = false) {
		$this->_tabs[ $tab->getTabName()] = $tab;

		if(	$isActiveTab) {
			$this->_activeTab = $tab;
		}
	}

	public function setActiveTab( $activeTabName) {
		if( !array_key_exists( $activeTabName, $this->_tabs )) {
			return false;
		}
		
		$this->_activeTab = $this->_tabs[ $activeTabName];
		
		return true;
	}

	public function displayActiveTab() {
		if( !$this->_tabView->displayTab( $this->_activeTab ) ) {
			return false;
		}

		return true;
	}

	private $_tabView = null;
	private $_tabs = array();
	private $_activeTab = null;
};

?>