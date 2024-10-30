<?php

if($_POST) {
	include('m2l_multi_send.php');
} else { ?>
<div class="wrap">
<div id="icon-edit-pages" class="icon32"><br /></div> 
<h2>New Mail</h2>

<script src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/mail2list/js/nicEdit.js" type="text/javascript"></script>

<form method="post">

<table class="form-table">
<tr valign="top">
<th scope="row">Subject</th>
<td><input type="text" name="subject" /></td>
</tr>
<tr valign="top">
<th scope="row">Recipients</th>
<td>
	<select name="recipients">
		<optgroup label="Liste predefinite">
			<option value="all">All users</option>
			<option value="TEST">Test mail (to: <?=get_option('admin_email')?>)</option>
		</optgroup>
		<optgroup label="Liste per Gruppi">
			<option>Administrator</option>
			<option>Editor</option>
			<option>Author</option>
			<option>Contributor</option>
			<option>Subscriber</option>
		</optgroup>
<?php $lists = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."m2l_lists ORDER BY label ASC"); ?>
		<optgroup label="Liste Personalizzate">
<?php foreach($lists as $list) { ?>
		<? if(get_option('m2l_default_list') == $list->id) { ?>
			<option value="<?=$list->id?>" selected="selected"><?=$list->label?></option>
		<? } else { ?>
			<option value="<?=$list->id?>"><?=$list->label?></option>
		<? } ?>
<?php } ?>
		</optgroup>
	</select>
</td>
</tr>
<tr valign="top">
<th scope="row">Template</th>
<td>
	<select name="template" onchange="location.href = '<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_new_mail&template='+this.options[this.selectedIndex].value;">
<?php $lists = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."m2l_templates ORDER BY id DESC");
foreach($lists as $list) {
	if((isset($_GET['template']) and $_GET['template'] == $list->id) or (!isset($_GET['template']) and get_option('m2l_default_template') == $list->id)) {
		$template = $list->template;
?>
		<option value="<?=$list->id?>" selected="selected"><?=$list->label?></option>
<?php } else { ?>
		<option value="<?=$list->id?>"><?=$list->label?></option>
<?php } ?>
<?php } ?>
	</select>
</td>
</tr>
<tr valign="top">
<th scope="row">Mail</th>
<td><textarea class="large-text code" cols="50" rows="10" name="body" id="m2l_body"><?php echo stripslashes($template); ?></textarea></td>
</tr>
</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Invia Mail') ?>" />
</p>

</form>
</div>

<script type="text/javascript">
	new nicEditor({iconsPath : '<?php bloginfo('wpurl'); ?>/wp-content/plugins/mail2list/images/nicEditorIcons.gif'}).panelInstance('m2l_body');
</script>
<?php } ?>