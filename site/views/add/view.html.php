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


jimport('joomla.application.component.view');

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'boardinfo'.'.php');


class QuipForumViewAdd extends JView 
{
    

        public $boardData 	= null;
        public $boardId		= 0;
        public $userData	= null;
        public $userOptions;
        public $postData;
        public $userAccessLevel	= 0;
        

        public function display($tpl = null) 
        {
			
			$session =& JFactory::getSession();
			   
			$this->userData =& JFactory::getUser();     
			$this->boardId = $session->get('quipforum_board_id','1'); 
			
			$this->userAccessLevel = comQuipForumHelper::getUserAccessLevel($this->boardId);
			
			
			if(!$this->userAccessLevel)
			{
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
			
		
			if(JRequest::getVar("id"))
			{
				$this->postData	= $this->get('PostData');
				
				if(!$this->userData->id)
				{
					return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				}
				
				if(($this->postData->user_id != $this->userData->id) && !$this->userData->authorise('core.manage', 'com_quipforum') && $this->userAccessLevel < 4 )
				{
					return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				}
				
			}
			else
			{

				$this->postData =& JTable::getInstance('posts', 'Table');
				$this->postData->load();
			}
			
			
			$this->boardData = $this->get('BoardData');   

            parent::display($tpl);
            
        }
}
