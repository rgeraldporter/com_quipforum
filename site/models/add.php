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

jimport('joomla.application.component.modelitem');

class QuipForumModelAdd extends JModelItem
{

    protected 	$boardData; 		

    
    public function getBoardData()
    {
    	
    	$session =& JFactory::getSession();
    	
  		$board_id = $session->get('quipforum_board_id','1'); # will need to change to first available published forum.
    	
    	return $this->getBoardDataDB($board_id);
    
    }
    
	        
    protected function getBoardDataDB($id = 1)
    {
    		
		$start = "";
		$limit = "";
		$where = "#__quipforum_boards.id = '".$id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_boards.id, ".
			"#__quipforum_boards.topic, ".
			"#__quipforum_boards.tag, ".
			"#__quipforum_boards.description ".
			"FROM #__quipforum_boards ",
			$start, $limit, $where, $order
		);

		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardData = $db->loadObject();
		
    	return $this->boardData;
    }
    
    
        
    public function getUserAccessLevel()
    {
    	$session =& JFactory::getSession();
    	$board_id = $session->get('quipforum_board_id','1');
    	$userData = JFactory::getUser();
    	$userGroups = JAccess::getGroupsByUser($userData->id);
    	$boardAccessList = comQuipForumHelper::getboardAccessListDB($userGroups);
    	$highestAccessLevel = 0;
    	
    	foreach((array)$userGroups as $kgroup=>$vgroup)
    	{
    		if(@$boardAccessList[$board_id][$vgroup] > $highestAccessLevel)
    		{
    			$highestAccessLevel = $boardAccessList[$board_id][$vgroup];
    		}
    	}

    	return $highestAccessLevel;
    
    }
    

    public function getPostData()
    {
    	
    	$id = JRequest::getInt('id');
    	
    	return $this->getPostDataDB($id);
    
    }

	        
    protected function getPostDataDB($id = null)
    {
    		
    	if(!$id)
    		JError::raiseError('404', JText::_('Post not found'));
    			
		$start = "";
		$limit = "";
		$where = "#__quipforum_posts.id = '".$id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_posts.* ".
			"FROM #__quipforum_posts ",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->postData = $db->loadObject();
		
		// Now for the board ID code(s) #plural after singular works..
		$start = "";
		$limit = "";
		$where = "#__quipforum_post_references.id = '".$id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_post_references.board_id ".
			"FROM #__quipforum_post_references ",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->postData->board_id = $db->loadObject()->board_id;		

    	return $this->postData;
    }
    

}