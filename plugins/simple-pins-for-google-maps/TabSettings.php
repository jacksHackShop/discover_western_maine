<?php

require_once("TabBase.php");

class gmsp_TabSettings extends gmsp_TabBase {
	public function __construct( $pluginContext) {
		gmsp_TabBase::__construct(  "settings", $pluginContext );
	}
	
	public function display() {
		$strTemplateTabSettings = "<h2> Google Maps API key </h2>";
		
		$strTemplateTabSettings .= "<p>Your custom Google Maps API key. Visit %%API_KEY_GEN_URL%% to generate your key.</p>
		<form id=\"apiKeyForm\" method=\"POST\" action=\"%%ADMIN_POST_URL%%\">
			<table class=\"form-table\">
				<tr>
				<th>
					<input type=\"text\" name=\"apiKey\" id=\"apiKey\" size=\"35\" %%API_KEY%% 
						oninput='document.getElementById(\"save_api_key\").disabled = true;' autofocus required>
				</th>
				<td>
					<input type=\"button\" class=\"button button-secondary\" value=\"Check API key\" onclick=\"checkKey()\">
					<input type=\"hidden\" name=\"action\" value=\"gmsp_save_api_key\">							
				</td>
				</tr>
				<tr>
					<th colspan=\"2\"><input type=\"checkbox\" name=\"gmsp_force_api_load\" id=\"gmsp_force_api_load\" value=\"%%FORCE_API_LOAD%%\" onclick=\"checkKey()\" %%FORCE_API_LOAD_DEFAULT%% > Force loading Google Maps API via this plugin (select this checkbox if you receive any API-key related errors on your website) </th>
				</tr>
				<tr>
					<td colspan=\"2\">
						<div id=\"apiTest\" style=\"float:left\">
						</div>
						<span class=\"spinner\" style=\"float:left\">
						</span>
						<span class=\"dashicons dashicons-yes\"
							style=\"visibility: hidden;float:left\">
						</span>
						<span class=\"dashicons dashicons-no-alt\"
							style=\"visibility: hidden;float:left\">
						</span>
						<div style=\"clear:both\"></div>
					</td>
				</tr>
			</table>
			<input type=\"submit\" class=\"button button-primary\" id=\"save_api_key\" value=\"Save\" disabled>
		</form>
			<script>
				var apiKeyValid = false;
				var validityCheck;
				
				function checkKey() {
					var apiTest = document.getElementById('apiTest');
					var APIkey = document.getElementById('apiKey').value;
					var script = document.createElement('script');
					var srcGMApi = \"https://maps.googleapis.com/maps/api/js?key=\"+APIkey+\"&callback=validityCheckMsg\";
					script.src = srcGMApi;
					apiTest.appendChild(script);	
					
					document.getElementsByClassName('dashicons-no-alt')[0].style.visibility = 'hidden';
					document.getElementsByClassName('dashicons-yes')[0].style.visibility = 'hidden';

					apiTest.innerHTML = 'Please wait. We are checking your API key. This might last up to 10 seconds.';
					document.getElementsByClassName('spinner')[0].style.visibility = 'visible';
					
					apiKeyValid = true;					
				}
				
				function gm_authFailure() { 					
					var apiTest = document.getElementById('apiTest');
					while(apiTest.hasChildNodes()) {
						apiTest.removeChild(apiTest.firstChild);
					}
					apiTest.innerHTML = 'API key is invalid!';
					document.getElementsByClassName('spinner')[0].style.visibility = 'hidden';
					document.getElementsByClassName('dashicons-no-alt')[0].style.visibility = 'visible';
					apiKeyValid = false;
				}
				
				function validityCheckMsg() {
					var apiTest = document.getElementById('apiTest');
					validityCheck = setTimeout( 
					function() {
						if (apiTest.innerHTML !== 'API key is invalid!') {
							apiTest.innerHTML = 'API key is valid.';
							document.getElementsByClassName('spinner')[0].style.visibility = 'hidden';
							document.getElementsByClassName('dashicons-yes')[0].style.visibility = 'visible';
							document.getElementById('save_api_key').disabled = false;
						}
					}
					, 10000 );
				}
				
			</script>
		
		";
	
		$strTemplateTabSettings = str_replace( "%%API_KEY_GEN_URL%%",
			$this->getPluginContext()->getApiKeyObtainUrl(),
			$strTemplateTabSettings);
	
		$strTemplateTabSettings = str_replace( "%%ADMIN_POST_URL%%",
			$this->getPluginContext()->getAdminPostUrl(),
			$strTemplateTabSettings );
			
		$strTemplateTabSettings = str_replace( "%%API_KEY%%",
			$this->getPluginContext()->getApiKey(),
			$strTemplateTabSettings );
		
		$strTemplateTabSettings = str_replace( "%%FORCE_API_LOAD%%",
			$this->getPluginContext()->getForceApiLoad(),
			$strTemplateTabSettings );		
			
		$strTemplateTabSettings = str_replace( "%%FORCE_API_LOAD_DEFAULT%%",
			$this->getPluginContext()->getForceApiLoad() === '1' ? 'checked' : '',
			$strTemplateTabSettings );				

		return $strTemplateTabSettings;
	}
};