(function() {
	if (GMSP_MAPS != '' && GMSP_MARKERS != '') {
		var map_values=[];
		var marker_values=[];
		for (var key in GMSP_MAPS) {
			if (typeof GMSP_MAPS[key] !== undefined) {
				//console.log(GMSP_MAPS[key]);
				map_values.push({text: GMSP_MAPS[key][1], value: GMSP_MAPS[key][0]});
			}
		}
		marker_values.push({text: '', value: ''});
		for (var key in GMSP_MARKERS) {
			if (typeof GMSP_MARKERS[key] !== undefined) {
				marker_values.push({text: GMSP_MARKERS[key][1], value: GMSP_MARKERS[key][0]});
			}
		}
		//console.log(map_values);
		//console.log(marker_values);
		tinymce.PluginManager.add('gmsp_tc_button', function( editor, url ) {
			editor.addButton( 'gmsp_tc_button', {
				title: 'Insert GMSP map',
				icon: 'icon dashicons-location',
				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert a GMSP map',
						body: [
											{
							type: 'listbox',
							name: 'map',
							label: 'Map Name',
							'values': 
								map_values
							
						},
						{
							type: 'textbox',
							name: 'width',
							label: 'Map width',
							value: '400'
						},
						{
							type: 'listbox',
							name: 'units',
							label: 'Width % or px',
							'values': [
							{text: 'px', value: 'px'},
							{text: '%', value: '%'}
							]					
						},
						{
							type: 'textbox',
							name: 'height',
							label: 'Map height (px)',
							value: '400'
						},
						{
							type: 'listbox',
							name: 'openInfo',
							label: 'When map loads, open info-window for marker: ',
							classes: 'hide-overflow',
							'values': 
								marker_values
						},
						{
							type: 'checkbox',
							name: 'nocontrols',
							label: 'Hide map controls'
						},
						{
							type: 'checkbox',
							name: 'noscroll',
							label: 'Disable scroll'
						},
						{
							type: 'checkbox',
							name: 'markerClustering',
							label: 'Enable marker clustering '
						}
						
						],
						onsubmit: function( e ) {
							editor.insertContent( '[gmsp_map id="' + e.data.map + '" w="' + e.data.width + e.data.units + '" ' + 'h="' + e.data.height + 'px"' + ' nocontrols="' + (e.data.nocontrols & 1) + '" noscroll="' + (e.data.noscroll & 1) + '" open="' + (e.data.openInfo ) +  '" cluster="' + (e.data.markerClustering & 1) + '" ]');
						}
					});
				}
			});
		});
		}
	else {
		//alert('No maps');
		return;
	}

})();
