<?php
/*
 * This file is part of the 'Accounts Hierarchy Dashlet'.
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
 
if(!defined('sugarEntry'))define('sugarEntry', true);


$viewdefs['base']['view']['hierarchy-account'] = array(

    'dashlets' => array(
        array(
            'label' => 'LBL_DASHLET_HIERARCHY_NAME',
            'description' => 'LBL_DASHLET_HIERARCHY_NAME_DESC',
            
            'config' => array(
            ),

            'preview' => array(
            ),
            
            'filter' => array(
                'module' => array(
                    'Accounts',
                ),
                'view' => array(
					'record',
				),
            ),

            
        ),
    ),
    
    'config' => array(
    ),
    
);
