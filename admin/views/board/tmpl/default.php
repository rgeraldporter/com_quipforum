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
$option = JRequest::getCmd('option');
$access_array = "";

?>

<form action='index.php' method='post' name='adminForm' id='adminForm'>
	<fieldset class='adminForm'><legend><?php echo JText::_('QFORUM_BOARD_EDIT'); ?></legend>

	<div class='admintable'>
	<fieldset><legend>Board Info</legend>

	<table>
	<tr>
	<td>
	<label for="qforum-topic">Discussion Topic</label>
	</td>
	<td>
	<input type="text" name="topic" maxlength="35" id="qforum-topic" value="<?php echo $this->row->topic; ?>" />
	</td></tr>
	
	<tr>
	<td>
	<label for="qforum-hash-tag">Hash Tag #</label>
	</td>
	<td>
	<input type="text" name="tag" maxlength="12" id="qforum-hash-tag" value="<?php echo $this->row->tag; ?>" />
	</td>
	</tr>
	<tr>
	<td>
	<label for="qforum-description">Description</label>
	</td><td>
	<textarea id="qforum-description" name="description"><?php echo $this->row->description; ?></textarea>
	</td></tr>
	<tr>
	<td>
	<label for="qforum-order">Order</label>
	</td>
	<td>
	<input type="text" name="ordering" id="qforum-order" value="<?php echo $this->row->ordering; ?>" />
	</td></tr>
	<tr><td>
		<label for="qforum-published">Published</label>
	</td>
	<td>
	<?php echo $this->published; ?>
	</td></tr></table>
	</fieldset>
	
	<fieldset><legend>Access Control Levels</legend>
	
	<table><tr><th>Joomla Access Level</th><th>Permission</th></tr>
	
	<?php foreach((array)$this->jAccessLevels as $k=>$v): ?>
	
		<?php
			$optcheck0 = "";
			$optcheck1 = "";
			$optcheck2 = "";
			$optcheck3 = "";
			$optcheck4 = "";
			$access_array .= $v->id . ",";
		?>
	
		<?php if(@$this->qAccessLevels[$v->id]): ?>
		<?php $varvar = "optcheck".$this->qAccessLevels[$v->id]; ?>
		<?php $$varvar = " selected=\"selected\""; ?>
		<?php endif; ?>
	
		<tr>
			<td><?php echo $v->title; ?></td>
			<td><select name="jaccess<?php echo $v->id; ?>">
			<option value="0"<?php echo $optcheck0; ?>>No access*</option>
			<option value="1"<?php echo $optcheck1; ?>>Read only</option>
			<option value="2"<?php echo $optcheck2; ?>>Reply only</option>
			<option value="3"<?php echo $optcheck3; ?>>Read &amp; Write</option>
			<option value="4"<?php echo $optcheck4; ?>>Moderator</option>
			</select>
			</td>
		</tr>
		
	
	<?php endforeach; ?>

	<?php $access_array = rtrim($access_array,","); ?>
	
	</table>

	<p>*Consult the Access List Manager and User Groups to confirm what permissions will be inherited. Users on multiple access lists are given a level of access to each seperate board equivalent to their highest possible access level for that board.</p>
		<p>Note that site managers and administrators will have some extra priviliges on boards they have access to.</p>
	
	</fieldset>
	
	<div>
	<div>ID</div>
	<div><?php echo $this->row->id; ?></div>
	</div>

	</div></fieldset>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="access_array" value="<?php echo $access_array; ?>" />
	<?php echo JHTML::_('form.token'); ?>
	 
</form>