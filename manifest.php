<?php
/*
 * This file is part of the 'Accounts Hierarchy Dashlet' module.
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


$manifest = array (
  'built_in_version' => '7.5.0.1',
  'acceptable_sugar_versions' => 
  array (
    0 => '',
  ),
  'acceptable_sugar_flavors' => 
  array (
    0 => 'PRO',
    1 => 'ENT',
    2 => 'ULT',
  ),
  'readme' => '',
  'key' => 'HIA',
  'author' => 'Olivier Nepomiachty',
  'description' => '',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'Accounts Hierarchy Dashlet',
  'published_date' => '2015-06-02 08:00:00',
  'type' => 'module',
  'version' => '1.2.3.0',
  'remove_tables' => 'prompt',
);


$installdefs = array (
  'id' => 'HIAC_20150522_1',
    
  // ###################
  // copy 
  // ###################
  'copy' => 
  array (
	// view
    0 => 
    array (
      'from' => '<basepath>/custom/clients/base/views/hierarchy-account',
      'to' => 'custom/clients/base/views/hierarchy-account',
    ),
    1 => 
    array (
      'from' => '<basepath>/custom/clients/base/views/hierarchy-contact',
      'to' => 'custom/clients/base/views/hierarchy-contact',
    ),
	// api
    10 => 
    array (
      'from' => '<basepath>/custom/clients/base/api/hierarchyApi.php',
      'to' => 'custom/clients/base/api/hierarchyApi.php',
    ),
    // language
    20 => 
    array (
      'from' => '<basepath>/language/application/en_us.hierarchy_accounts.php',
      'to' => 'custom/Extension/application/Ext/Language/en_us.hierarchy_accounts.php',
    ),

  ),



);
