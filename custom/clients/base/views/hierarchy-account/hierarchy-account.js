/*
 * This file is part of the 'hierarchy-account'.
 * Copyright [2015/5/22] [Olivier Nepomiachty - SugarCRM]
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * Author: Olivier Nepomiachty SugarCRM
 */
({
    plugins: ['Dashlet'],
        
    initDashlet: function () {		
        this.model.on("change:parent_id", this.loadData, this);
    },
    
    sugar_version_greater: function(v1,v2) {
		// return true if current sugar version x1.x2 is greater than v1.v2
		var x = app.metadata.getServerInfo().version.split('.');
		if (parseInt(x[0]) > v1) return true;
		if ((parseInt(x[0]) == v1)&&(parseInt(x[1]) > v2)) return true;
		return false;
	},    
    
    fa_icon: function(ico) {
		/*
		 7.5: <i class="icon-camera-retro"></i>
		 7.6: <i class="fa fa-camera-retro">
		 */
		if (this.sugar_version_greater(7,5)) { // 7.6
			return 'fa fa-' + ico;
		}
		else { // 7.5
			return 'icon-' + ico;
		}
	},
    
    loadData: function (options) {
	  var accountid = this.model.get("id");
	  var self = this;
	  
	  nv.addGraph(function() {
		
		var nodeRenderer = function(d) {
			var node_style = '';
			if (accountid == d.ida) node_style = 'style="border-style: solid; border-width: 2px; border-color: #fffd52;"';
			/*
			if (!d.image || d.image === '')
				d.image = '<i class="icon-user" '+node_style+'></i>';
			else 
				d.image = '<img src="' + d.image + '" class="avatar avatar-btn" '+node_style+' />';
			*/
			//return '<img src="custom/clients/base/views/hierarchy-account/img/' + d.image + '" class="rep-avatar" width="32" height="32"><div class="rep-name">' + d.name + '</div><div class="rep-title">' + d.title + '</div>';
			return '<table><tr><td><i class="' + self.fa_icon('building') + '" '+node_style+'></i></td><td><div class="rep-name">' + d.name + '</div><div class="rep-title">' + d.title + '</div></td></tr></table>';
		};		
		

		var chart = nv.models.tree()
			  .duration(500)
			  .nodeSize({'width': 124, 'height': 56})
			  .nodeRenderer(nodeRenderer)
			  .zoomExtents({'min': 0.25, 'max': 4})
			  .horizontal(false);
		
        app.api.call('GET', app.api.buildURL('hierarchy/account?accountid='+accountid), null, 
		{ 
            success: function (data) {  
                if (this.disposed) {
                    return;
                }
                if (typeof (data.circular_ref_error) == 'string') {
					console.log('# API call hierarchy/account: error circular ref');
					app.alert.show('error', {
						level: 'error', 
						title: app.lang.get('LBL_DASHLET_HIERARCHY_ERROR_CIRCULAR'), 
						messages: data.circular_ref_error,
						autoClose: false
					});
					return;
				}
                console.log('# API call hierarchy/account success');
				d3.select('#org_'+self.cid+' svg')
					.datum(data)
					.transition().duration(700)
					.call(chart);
			},
			error: function() {// error
				console.log('# API call hierarchy/account error');
			}
        });		

		nv.utils.windowResize(function() { chart.resize(); });

		var toggleChart = function(e) {
			//if icon clicked get parent button
			var button = $(e.currentTarget);

			switch (button.data('control')) {
				case 'orientation':
					chart.nodeSize({'width': 124, 'height': 56}).orientation();
					break;

				case 'show-all-nodes':
					chart.showall();
					break;

				case 'zoom-to-fit':
					chart.reset();
					break;

				default:
			}
		};

		$('.toggle-control').on('click', toggleChart);

		return chart;
	  });

		this.render();
		
    },
    

    
})
