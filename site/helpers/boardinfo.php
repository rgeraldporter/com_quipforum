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

jimport('joomla.application.component.helper');

class QuipForumBoardInfo
{

	public	$data		= array();

	public function __construct()
	{
	
		$this->getBoardsDataDB();
	
	}
	
	
	protected function getBoardsDataDB()
	{
	

		$start = "";
		$limit = "";
		$where = "";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_boards.* ".
			"FROM #__quipforum_boards",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->data = $db->loadObjectList('id');
	
	}
	
	

}
