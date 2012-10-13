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
?>

<h1><?php echo "Forum List"; ?></h1>

<div class='forum-index-container'>
<div class='forum-nav'>

<!--div class='forum-add-post'></div>
<div class='forum-index-settings'><a href='post'>Settings</a></div-->

<div class='forum-small-nav'>	

<div class='forum-index-textline'>
</div>

<div class='forum-index-boards-list'>
</div>

<div class='forum-index-multiboard-add'>
</div>


<!--div class='forum-index-search'>
	<form name='search' action='index.php' method='post' style='margin:0;white-space:nowrap;'>
		<input type='text' name='i' value='search board...'  onclick=\"document.search.i.value=''\" />
		<input type='hidden' name='app' value='forum' />
		<input type='hidden' name='m' value='search_forum' />
	</form>		
</div-->
	
	
</div></div>

<?
//echo $this->forumObjectList;
$link = "";

		
foreach((array) $this->forumObjectList as $k=>$v)
{

			
		//$this->lastmarked = strtotime($this->get_board_marked());
		
		//if($this->user->id())
		//	$this->get_post_read_array();
			
		// $this->count_new_posts();
		
		//if($this->board_id == -1 && !$this->user->id())
	//		continue;
			
		//if($this->user->id())
		//	$new_count_html = "<div class='forum-list-board-newcount'>".icon::png("new")." ".$this->new_posts." new posts</div>";
		//else
		//	$new_count_html = "";
		
	$link = JRoute::_('index.php?option=com_quipforum&view=board&id='.$v->id);	

?>
	<div class='forum-list-board-container'>

	<div class='forum-list-board-desc'><div class='forum-list-board-name'>
	<a href='<? echo $link ?>'>
	<? echo $v->topic; ?></a>
	</div>
	<div class='forum-list-board-url'>
	<a href='<? echo $link ?>'><? echo $link ?></a></div>
	
	<div class='forum-list-board-long-desc'>
	<? echo $v->description; ?>
	</div>
	</div>
	<div class='forum-list-board-stats'>
	<div class='forum-list-board-threadcount'>
	<? echo $v->thread_count; ?> discussion threads
	</div>
	</div>
	</div>
	<br />
			
<?
		
}

		
?>
<div class='forum-thread-count'>
</div>
</div>