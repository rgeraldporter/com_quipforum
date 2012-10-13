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

jimport('joomla.application.component.controller');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_quipforum'.DS.'tables');

class QuipForumController extends JController
{

	
	public function display()
	{
	
		$view = JRequest::getVar('view');
		
		if(!$view)
		{
			JRequest::setVar('view','list');
		}
		
		parent::display();
	
	}
	
	
	public function add()
	{
	
		JRequest::setVar('view', 'board');
		$this->display();
	
	}
	
	public function edit()
	{
	
		JRequest::setVar('view', 'board');
		$this->display();
	
	}
	
	
	public function save()
	{
		
		$db = &JFactory::getDBO();
		$access = array();
		$option = JRequest::getCmd('option');
		JRequest::checkToken() or jexit('Invalid Token');

		$row =& JTable::getInstance('boards','Table');
		
		if(!$row->bind(JRequest::get('post')))
		{
			JError::raiseError(500, $row->getError());
		}

		
		if(!$row->store())
		{
			JError::raiseError(500, $row->getError());
		}
		
		$access_array = JRequest::getVar('access_array');
		$access = explode(",", $access_array);

		foreach((array)$access as $k=>$v)
		{

			$access_level = JRequest::getInt('jaccess'.$v, 0);
			
			$query = "DELETE FROM #__quipforum_board_access WHERE board_id = '".$row->id."' AND viewlevel_id = '".$v."' ";
			
			$db->setQuery($query);
			$db->query();
			
			$query = "INSERT INTO #__quipforum_board_access (board_id, viewlevel_id, access_level) VALUES ('".$row->id."', '".$v."', '".$access_level."') ";
			$db->setQuery($query);
			$db->query();
			unset($rowAccess);
		}
		
		
		
		$this->setRedirect('index.php?option='.$option.'&view=board&task=edit&cid[]='.$row->id,'Board saved.');
	
	}
	
	public function publish()
	{
	
		$option = JRequest::getCmd('option');
		$cid = JRequest::getVar('cid', array());
		$row =& JTable::getInstance('boards', 'Table');
		
		$publish = 1;
		
		if($this->getTask() == 'unpublish')
			$publish = 0;
		

		
		if(!$row->publish($cid, $publish))
		{
			JError::raiseError(500, $row->getError());		
		}
				
		$s = '';
		
		if(count($cid) > 1 )
			$s = 's';
			
		$msg = "Board".$s;
		
		if($this->getTask() == 'unpublish')
			$msg .= " unpublished.";
		else
			$msg .= " published.";
			
		$this->setRedirect('index.php?option='.$option, $msg);	
	
	}
	
	
	

}