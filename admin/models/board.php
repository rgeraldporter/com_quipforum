<?php
/*
*
*	Quip Forum for Joomla!
*	(c) 2010-2012 Robert Gerald Porter
*
*	Author: 	Robert Gerald Porter <rob@robporter.ca>
*	Version: 	0.3
*   License: 	GPL v3.0
*
*   This extension is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This extension is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details <http://www.gnu.org/licenses/>.
*	
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport( 'joomla.access.access' );

class QuipForumModelBoard extends JModel
{
	
	public function __construct()
	{
        
        parent::__construct();
 
        
	}
	
	public function getjACL()
	{
		
		### Oh my!
		# This is a core Joomla table. 
		# This means there's probably a method to grab this info more officially. 
		# This means I should ask on the Joomla Dev forum for a way to get his info officially.
		# Later.
	
		$query = "SELECT * FROM #__viewlevels ";
		$data = $this->_getList($query);		

		return $data;		
	
	}


	
	public function getqACL()
	{
		
		$data_array = array();
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = $cid[0];
		
		$query = "SELECT * FROM #__quipforum_board_access WHERE board_id = '".$id."' ";
		$data = $this->_getList($query);		

		foreach((array)$data as $k=>$v)
		{
			$data_array[$v->viewlevel_id] = $v->access_level;
		}

		return $data_array;		
	
	}

}


