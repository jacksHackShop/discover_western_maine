<?php

abstract class gmsp_TabBase {
	public function __construct ( $tabName, $pluginContext){
		$this->_tabName = $tabName;
		$this->_pluginContext = $pluginContext;
	}
	
	public function getTabName() {
		return $this->_tabName;
	}
	
	public function getPluginContext() {
		return $this->_pluginContext;
	}
	
	abstract public function display();
	
	private $_tabName;
	private $_pluginContext;
};

?>