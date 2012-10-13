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

function QuipForumBuildRoute(&$query)
{

	$segments = array();
	
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if(isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if(isset($query['page']))
	{
		$segments[] = $query['page'];
		unset($query['page']);	
	}
	if(isset($query['sort']))
	{
		$segments[] = $query['sort'];
		unset($query['sort']);	
	}
	
	return $segments;

}


function QuipForumParseRoute($segments)
{

	$vars = array();
	
	if(isset($segments[0]))
		$vars['view'] = $segments[0];
	
	if(isset($segments[1]))
		$vars['id'] = $segments[1];
		
	if(isset($segments[2]))
		$vars['page'] = $segments[2];
	
	if(isset($segments[3]))
		$vars['sort'] = $segments[3];
		
	return $vars;

}