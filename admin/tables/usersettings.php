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

class TableUserSettings extends JTable
{

	public $user_id				= 0;
	public $post_prefix			= null;
	public $post_sig			= null;
	public $post_template		= null;
	public $ignore_list			= null;
	public $icon				= null;
	public $colours				= null;
	public $flags 				= null;
	

	public function __construct(&$db)
	{
	
		parent::__construct('#__quipforum_user_settings', 'user_id', $db);
			
	}
	


}