<?php

class gmsp_PluginContext {
	public function __construct(){
	}
	
	public function setActiveTabName( $tabName){
		$this->_activeTabName = $tabName;
	}
	
	public function setDefaultTabName( $defaultTabName) {
		$this->_defaultTabName = $defaultTabName;
	}
	public function getDefaultTabName() {
		return $this->_defaultTabName;
	}

	public function getActiveTabName() {
		return $this->_activeTabName;
	}
	
	public function setAdminPostUrl( $adminPostUrl) {
		$this->_adminPostUrl = $adminPostUrl;
	}
	public function getAdminPostUrl() {
		return $this->_adminPostUrl;
	}
	
	public function setIconForInfo( $iconForInfo) {
		$this->_iconForInfo = $iconForInfo;
	}
	public function getIconForInfo() {
		return $this->_iconForInfo;
	}

	public function setIconForMapPin( $iconForMapPin) {
		$this->_iconForMapPin = $iconForMapPin;
	}	
	public function getIconForMapPin() {
		return $this->_iconForMapPin;
	}

	public function setAllTumbs( $allThumbs) {
		$this->_allThumbs = $allThumbs;
	}	
	public function getAllTumbs() {
		return $this->_allThumbs;
	}

	public function setRedirectValue( $redirectValue) {
		$this->_redirectValue = $redirectValue;
	}	
	public function getRedirectValue() {
		return $this->_redirectValue;
	}

	public function setStoredMarkers( $storedMarkers) {
		$this->_storedMarkers = $storedMarkers;
	}	
	public function getStoredMarkers() {
		return $this->_storedMarkers;
	}

	public function setApiKey( $apiKey) {
		$this->_apiKey = $apiKey;
	}	
	public function getApiKey() {
		return $this->_apiKey;
	}

	public function setApiKeyObtainUrl( $apiKeyObtainUrl) {
		$this->_apiKeyObtainUrl = $apiKeyObtainUrl;
	}
	public function getApiKeyObtainUrl() {
		return $this->_apiKeyObtainUrl;
	}
	
	public function setProVersionUsage ( $gmspVersion ) {
		$this->_gmspVersion = $gmspVersion;
	}
	
	public function getProVersionUsage() {
		return $this->_gmspVersion;
	}
	
	public function setForceApiLoad($forceApiLoad) {
		$this->_forceApiLoad = $forceApiLoad;
	}
	
	public function getForceApiLoad() {
		return $this->_forceApiLoad;
	}
	
	private $_apiKeyObtainUrl = null;
	private $_apiKey = null;
	private $_storedMarkers = null;
	private $_redirectValue = null;
	private $_allThumbs = null;
	private $_iconForMapPin = null;
	private $_iconForInfo = null;
	private $_activeTabName = null;
	private $_adminPostUrl = null;
	private $_defaultTabName = null;
	private $_gmspVersion = null;
	private $_forceApiLoad = null;
};

?>