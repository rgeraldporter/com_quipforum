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


class QuipForumViewSettings extends JView 
{
    

        public 	$boardId;
        public 	$userData;
        public 	$userSettings;
        public 	$checks;

        public function display($tpl = null) 
        {
			
			$session =& JFactory::getSession();
			
			if(!$this->boardId = JRequest::getInt('id'))
				$this->boardId = $session->get('quipforum_board_id','1'); # will need to change to first available published forum.
				
			$this->userData = JFactory::getUser();
			
			if(!$this->userData->id)
			{
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
			
			$this->userSettings =& JTable::getInstance('usersettings', 'Table');
			$this->userSettings->load($this->userData->id);
			//$this->userSettings = $this->get('UserSettings');
			
			if(!$this->userSettings->flags)
				$flags = new blankUserSettingsFlags;
			else
				$flags = json_decode($this->userSettings->flags);
				
			$checked = "checked='checked'";
			$unchecked = "";
			
			if($flags->no_smileys): $this->checks->no_smileys = $checked; else: $this->checks->no_smileys = $unchecked; endif;
			if($flags->no_icons): $this->checks->no_icons = $checked; else: $this->checks->no_icons = $unchecked; endif;
			if($flags->no_colours): $this->checks->no_colours = $checked; else: $this->checks->no_colours = $unchecked; endif;
			if($flags->flag_unread): $this->checks->flag_unread = $checked; else: $this->checks->flag_unread = $unchecked;  endif;
			if($flags->email_me): $this->checks->email_me = $checked; else: $this->checks->email_me = $unchecked; endif;
			if($flags->line): $this->checks->line = $checked; else: $this->checks->line = $unchecked;  endif;
				
			$document =& JFactory::getDocument();
			
			$document->addStyleSheet("components/com_quipforum/assets/css/settings.css",'text/css',"screen");
          	
            parent::display($tpl);
            
        }
}

