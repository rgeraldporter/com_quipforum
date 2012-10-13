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


switch(JRequest::getWord('task'))
{

	case "edit":
	case "add":
	
		JToolBarHelper::title( JText::_('Quip Forum'), 'quip_forum_title');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		
		break;

	default: 
		JToolBarHelper::title( JText::_('Quip Forum'), 'quip_forum_title');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::preferences('com_weever');
		JToolBarHelper::editList();
		JToolBarHelper::deleteList('Are you sure you want to delete these tabs?');
		JToolBarHelper::addNew();
		
		break;
	
}

$controller = JController::getInstance('QuipForum');
$controller->registerTask('unpublish', 'publish');
$controller->execute(JRequest::getWord('task'));
$controller->redirect();
