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


class QuipForumViewList extends JView
{

	public $layout = null;

	public function display($tpl = null)
	{
	
		$component = JComponentHelper::getComponent( 'com_quipforum' );
		$params = new JParameter( $component->params );
		
		$rows =& $this->get('BoardList');
		$this->assignRef('rows', $rows);

       /* Call the state object */
       $state =& $this->get( 'state' );

       /* Get the values from the state object that were inserted in the model's construct function */
       $lists['order_Dir'] = $state->get( 'filter_order_Dir' );
       $lists['order']     = $state->get( 'filter_order' );

       $this->assignRef( 'lists', $lists );
       

		
		// MUST change to different method -- this is here for template adjustments, not layout adjustments
		
		JSubMenuHelper::addEntry(JText::_('QFORUM_CONFIGURATION'), 'index.php?option=com_quipforum&view=config&task=config', false);
		JSubMenuHelper::addEntry(JText::_('QFORUM_BOARDS'), 'index.php?option=com_quipforum', @$active['tabs']);

		
		parent::display($tpl);
	
	}

}