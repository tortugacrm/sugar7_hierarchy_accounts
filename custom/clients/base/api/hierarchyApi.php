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
 
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

 
class hierarchyApi extends SugarApi
{
	public static $idx = 1;	
	
    public function registerApiRest()
    {
        return array(
             'GetHierarchy1Endpoint' => array(
                //request type
                'reqType' => 'GET',
                //endpoint path
                'path' => array('hierarchy','account'),
                //endpoint variables
                'pathVars' => array('', '', 'data'),
                //method to call
                'method' => 'hierarchy_account',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Dashlet showing the hierarchy for an account',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

             'GetHierarchy2Endpoint' => array(
                //request type
                'reqType' => 'GET',
                //endpoint path
                'path' => array('hierarchy','contact'),
                //endpoint variables
                'pathVars' => array('', '', 'data'),
                //method to call
                'method' => 'hierarchy_contact',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Dashlet showing the hierarchy for a contact',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
                           
         );
    }
 
	// #######################################
	// ############# hierarchy_account #######
	// #######################################
    public function hierarchy_account ($api, $args)
    {
		if (!isset($args['accountid'])) { 
			return '[]'; 
			exit; 
		}
		//$accountid = $args['accountid'];
		self::$idx = 1;
		//$accountid = preg_replace('/[^0-9a-Z-]/', '', $args['accountid']);
		$accountid = preg_replace('/[^a-z0-9-]/i', '', $args['accountid']);
		if ($accountid=='') { 
			return array('error' => 'invalid id'); 
			exit; 
		}
		
		// go to the top of the hierarchy
		// check if any circular reference
		$circular_ref = false;	
		$ids = array($accountid);	
		while (true) {
			$sql = "select name,parent_id from accounts where id='$accountid' and deleted=0";
			$result = $GLOBALS["db"]->query($sql);
			$row = $GLOBALS["db"]->fetchByAssoc($result);
			if (isset($row['parent_id'])) {
				$accountid = $row['parent_id'];
				// circular reference?
				if (in_array($accountid, $ids)) {
					return array('circular_ref_error' => $row['name']); 
					exit; 
				}
				array_push($ids, $accountid);
			}
			else
				break;
		}
		
		$sql = "select id,name from accounts where id='$accountid' and deleted=0";
		$result = $GLOBALS["db"]->query($sql);
		$row = $GLOBALS["db"]->fetchByAssoc($result);
		$hierarchy = array();
		$hierarchy['id'] = self::$idx;
		$hierarchy['ida'] = $accountid;
		//$hierarchy['name'] = $row['name'];
		$hierarchy['name'] = '<a href="#Accounts/'.$row['id'].'">' . $row['name'] . '</a>';
		$hierarchy['title'] = '';
		$hierarchy['image'] = '';
		$hierarchy['x0'] = 0;
		$hierarchy['y0'] = 0;
		$hierarchy['children'] = self::get_account_children1($accountid, $hierarchy['children']);
		return($hierarchy);
	}
	
	private function get_account_children1($accountid, $hierarchy) {
		$sql = "select id, name from accounts where parent_id = '$accountid' and deleted=0";
		$result = $GLOBALS["db"]->query($sql);
		$r = array();
		$i=0;
		$children = array();
		while($row = $GLOBALS["db"]->fetchByAssoc($result)) {
			self::$idx = self::$idx + 1;
			$h['id'] = self::$idx;
			$h['ida'] = $row['id'];
			//$h['name'] = $row['name'];
			$h['name'] = '<a href="#Accounts/'.$row['id'].'">' . $row['name'] . '</a>';
			$h['title'] = '';
			$h['image'] = '';
			//$h['x0'] = 0;
			//$h['y0'] = 0;
			$hierarchy[$i++] = $h;
			$GLOBALS['log']->debug('get_account_children: '.$h['name']);
			$h['children'] = self::get_account_children1($h['ida'], $h['children']);
			array_push($children, $h);
		}
		return($children);
    }

	// #######################################
	// ############# hierarchy_contact #######
	// #######################################
    public function hierarchy_contact ($api, $args)
    {
		if (!isset($args['contactid'])) { 
			return '[]'; 
			exit; 
		}
		//$accountid = $args['accountid'];
		self::$idx = 1;
		//$accountid = preg_replace('/[^0-9a-Z-]/', '', $args['accountid']);
		$contactid = preg_replace('/[^a-z0-9-]/i', '', $args['contactid']);
		if ($contactid=='') { 
			return '[error]'; 
			exit; 
		}
		
		// go to the top of the hierarchy
		// check if any circular reference
		$circular_ref = false;	
		$ids = array($contactid);	
		while (true) {
			$sql = "select concat(first_name,' ',last_name) name, reports_to_id from contacts where id='$contactid' and deleted=0";
			$result = $GLOBALS["db"]->query($sql);
			$row = $GLOBALS["db"]->fetchByAssoc($result);
			if (isset($row['reports_to_id'])) {
				$contactid = $row['reports_to_id'];
				// circular reference?
				if (in_array($contactid, $ids)) {
					return array('circular_ref_error' => $row['name']); 
					exit; 
				}
				array_push($ids, $contactid);
			}
			else
				break;
		}
		
		$sql = "select id,concat(first_name,' ',last_name) name from contacts where id='$contactid' and deleted=0";
		$result = $GLOBALS["db"]->query($sql);
		$row = $GLOBALS["db"]->fetchByAssoc($result);
		$hierarchy = array();
		$hierarchy['id'] = self::$idx;
		$hierarchy['ida'] = $contactid;
		//$hierarchy['name'] = $row['name'];
		$hierarchy['name'] = '<a href="#Contacts/'.$row['id'].'">' . $row['name'] . '</a>';
		$hierarchy['title'] = '';
		$hierarchy['image'] = '';
		$hierarchy['x0'] = 0;
		$hierarchy['y0'] = 0;
		$hierarchy['children'] = self::get_contact_children1($contactid, $hierarchy['children']);
		return($hierarchy);
	}
	
	private function get_contact_children1($contactid, $hierarchy) {
		$sql = "select id, concat(first_name,' ',last_name) name,picture from contacts where reports_to_id = '$contactid' and deleted=0";
		$result = $GLOBALS["db"]->query($sql);
		$r = array();
		$i=0;
		$children = array();
		while($row = $GLOBALS["db"]->fetchByAssoc($result)) {
			self::$idx = self::$idx + 1;
			$h['id'] = self::$idx;
			$h['ida'] = $row['id'];
			//$h['name'] = $row['name'];
			$h['name'] = '<a href="#Contacts/'.$row['id'].'">' . $row['name'] . '</a>';
			$h['title'] = '';
			$h['image'] = '';
			if ($row['picture'] != '') $h['image'] = 'rest/v10/Contacts/' . $row['id'] . '/file/picture?format=sugar-html-json&platform=base&_hash=' . $row['picture'];			
			//$h['x0'] = 0;
			//$h['y0'] = 0;
			$hierarchy[$i++] = $h;
			$GLOBALS['log']->debug('get_contact_children: '.$h['name']);
			$h['children'] = self::get_contact_children1($h['ida'], $h['children']);
			array_push($children, $h);
		}
		return($children);
    }
    
}


?>
