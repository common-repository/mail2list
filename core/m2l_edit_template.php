<div class="wrap">
<div id="icon-edit-pages" class="icon32"><br /></div>
<?php

if($_GET['id']) {
	$action = 'Edit';
	$mails = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."m2l_templates WHERE id = ".$_GET['id']);
	foreach($mails as $mail) {
		$id = $mail->id;
		$template = $mail->template;
		$label = $mail->label;	
	}
} else {
	$action = 'New';
}

?>
<h2><?php echo $action ?> Template</h2>

<script src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/mail2list/js/nicEdit.js" type="text/javascript"></script>

<form method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_template">
<?php if($_GET['id']) { ?>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
<?php } ?>
<table class="form-table">
<tr valign="top">
<th scope="row">Name</th>
<td><input type="text" name="label" value="<?php echo stripslashes($label); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Template</th>
<td><textarea class="large-text code" cols="50" rows="10" name="template" id="m2l_body"><?php echo stripslashes($template); ?></textarea></td>
</tr>
<tr valign="top">
<th scope="row">Help</th>
<td>You can use these tag in your template:
<ul>
	<li>[FIRSTNAME] - recipient's firstname (if present)</li>
	<li>[LASTNAME] - recipient's lastname (if present)</li>
	<li>[SITENAME] - website name</li>
	<li>[SITEURL] - website address</li>
</ul>
</td>
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