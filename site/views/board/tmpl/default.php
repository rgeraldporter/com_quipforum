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


<h1><? echo $this->board->data[$this->boardId]->topic; ?></h1>

<? echo $this->userPanel; ?>

<div class='qforum-sub-description'><? echo $this->board->data[$this->boardId]->description; ?></div>

<div class='qforum-index-container'>

<div class='qforum-nav'>

<?php echo $this->postOptions; ?>

<?php echo $this->settingsOptions; ?>

<?php echo $this->postReadClearOptions; ?>

<div class='qforum-index-refresh'>
<a href=''>Refresh</a>
</div>

<div class='qforum-small-nav'>	

<div class='qforum-index-textline'>

</div>

<div class='qforum-index-boards-list'>
<a href='<? echo JRoute::_('index.php?option=com_quipforum&view=list'); ?>'>Boards</a>
</div>


<div class='qforum-index-boards-pagination'>
<? echo $this->pageNav; ?>
</div>

<?php echo $this->adminOptions; ?>	
		

<!--pm-alert-->
	
</div></div>

<div class='qforum-index-threads'>


<div id='qforum-index-tree-container'>
<ul class='thread' id='qforum-threads'>
<?

echo $this->preBoardMarkup;

echo $this->threadMarkup;


?></ul>
<div class='qforum-index-boards-pagination'>
<? echo $this->pageNav; ?>
</div>
<textarea><?php echo $this->jsonLog; ?></textarea>
</div>


</div>		
</div>
