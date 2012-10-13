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

defined('_JEXEC') or die;defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class QuipForumViewBoard extends JView
{

	public function display($tpl = null)
	{
	
		$component = JComponentHelper::getComponent( 'com_quipforum' );
		$params = new JParameter( $component->params );

		$row =& JTable::getInstance('boards', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');


		$id = $cid[0];
		$row->load($id);

		$this->assignRef('row', $row);
		
		$jAccessLevels = $this->get('jACL');
		$this->assignRef('jAccessLevels',$jAccessLevels);
		
		$qAccessLevels = $this->get('qACL');
		$this->assignRef('qAccessLevels',$qAccessLevels);

		$this->assignRef('published', JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published));
		
		parent::display($tpl);
	
	}
	
}