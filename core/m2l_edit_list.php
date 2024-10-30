<div class="wrap">
<div id="icon-edit-pages" class="icon32"><br /></div>
<?php

if($_GET['id']) {
	$action = 'Edit';
	$mails = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."m2l_lists WHERE id = ".$_GET['id']);
	foreach($mails as $mail) {
		$id = $mail->id;
		$label = $mail->label;
	}
} else {
	$action = 'New';
}

?>
<h2><?php echo $action ?> List</h2>

<script src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/mail2list/js/nicEdit.js" type="text/javascript"></script>

<form method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_lists">
<?php if($_GET['id']) { ?>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
<?php } ?>
<table class="form-table" id="m2l_filter_table">
<tr valign="top">
<th scope="row">Name</th>
<td><input type="text" name="label" value="<?php echo stripslashes($label); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Filter</th>
<td><a href="javascript:mail2list_add_filter('m2l_filter_row');">Add a filter...</a></td>
</tr>
<tr valign="top" id="m2l_filter_row">
<td><input type="hidden" value="where" name="filters_andor[]" />where</td>
<td id="m2l_filter_filters_td">
	<select name="filters_filter[]">
		<optgroup label="Usermeta">
<?php
	$filters = $wpdb->get_results("SELECT DISTINCT meta_key FROM wp_usermeta WHERE meta_key NOT LIKE 'wp_%' AND meta_key NOT IN ('admin_color','comment_shortcuts','rich_editing')");
	foreach($filters as $filter) {?>
			<option value="<?=$filter->meta_key?>"><?=$filter->meta_key?></option>
	<?php } ?>
		</optgroup>
	</select> = <input type="text" name="filters_value[]" value="" /></td>
</tr>
</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<script type="text/javascript">
	new nicEditor({iconsPath : '<?php bloginfo('wpurl'); ?>/wp-content/plugins/mail2list/images/nicEditorIcons.gif'}).panelInstance('m2l_body');
</script>