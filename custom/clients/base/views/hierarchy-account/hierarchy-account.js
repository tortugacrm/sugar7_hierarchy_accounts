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
    
    loadData: function (options) {
	  var accountid = this.model.get("id");
	  var self = this;
	  
	  nv.addGraph(function() {

		var nodeRenderer = function(d) {
		  if (!d.image || d.image === '') {
			d.image = 'user.svg';
		  }
		  //return '<img src="custom/clients/base/views/hierarchy-account/img/' + d.image + '" class="rep-avatar" width="32" height="32"><div class="rep-name">' + d.name + '</div><div class="rep-title">' + d.title + '</div>';
		  return '<table><tr><td><i class="icon-building"></i></td><td><div class="rep-name">' + d.name + '</div><div class="rep-title">' + d.title + '</div></td></tr></table>';
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
