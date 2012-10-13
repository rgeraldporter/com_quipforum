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

<h1><? echo $this->postData->subject; ?> </h1>



<h4>from <?php echo $this->boardData->topic; ?></h4>

<div class='qforum-post-author'>Posted by <? echo $this->postData->user_alt_name; ?> on <? echo $this->postData->post_date; ?></div>

<div class='qforum-post-body'><? 

// this differentiation should happen elsewhere in the future
if($this->postData->compressed)
	echo gzinflate($this->postData->bodyblob); 
else
	echo $this->postData->body;	
?>
</div>



<h3>Options</h3>
<div><ul>
<li>
<a href='<? echo JRoute::_('index.php?option=com_quipforum&view=board&id='.$this->boardData->id); ?>'> Return to <?php echo $this->boardData->topic; ?></a></li>
<?php echo $this->adminOptions; ?>
</ul></div>

<?php echo $this->adminLog; ?>

<a name="thread-tree"></a>
<h3>Thread Tree</h3>
<div id='qforum-index-tree-container'>
<ul class='thread' id='qforum-threads'>
<?

echo $this->threadMarkup;


?></ul>
</div>
<?php if($this->replyAllowed): ?>
<a name="reply"></a>

<h3>Reply to <? echo $this->postData->subject; ?></h3>


<form action='<? echo JRoute::_('index.php'); ?>' class="form-validate" method='post' name='addpost' id='addpost'>
	<div id='forum-post-options-bar'>
	
	<div id='forum-post-username'>
	<fieldset>
	<legend>Name</legend>
	<input type='text' name='user_alt_name' value='<?php echo $this->userData->name; ?>' id='qforum-post-name' class='validate-name' style='' />
	</fieldset>
	</div>
	
	</div>
	<div id='qforum-post-area'>
	<fieldset>
	<legend>Subject &amp; Body</legend>
	<input type='text' name='subject' id='qforum-post-subject' placeholder='Subject' class='validate-subject' maxlength='120' /><br />
<?php
		$editor = "";
		$editor =& JFactory::getEditor();
		echo $editor->display('body', '', '550', '400', '60', '20', false);

?>
	
	
	<div><button class="button validate" type="submit">Send</button></div>
	<input type='hidden' name='user_id' value='<?php echo $this->userData->id; ?>' />
	<input type='hidden' name='parent_id' value='<?php echo $this->postData->id; ?>' />
	<input type='hidden' name='thread_id' value='<?php echo $this->postData->thread_id; ?>' />
	<input type='hidden' name='view' value='post' />
	<input type='hidden' name='task' value='save' />
	<input type='hidden' name='option' value='com_quipforum' />
	<input type='hidden' name='board_id' value='<?php echo $this->postData->board_id; ?>' />
	<?php echo JHTML::_('form.token'); ?>
	</fieldset>
	</div>
</form>
<? endif;


