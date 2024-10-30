<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2>Options</h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Mail From</th>
<td><input type="text" name="m2l_from_email" value="<?php echo get_option('m2l_from_email'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Name From</th>
<td><input type="text" name="m2l_from_name" value="<?php echo get_option('m2l_from_name'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Host</th>
<td><input type="text" name="m2l_smtp_host" value="<?php echo get_option('m2l_smtp_host'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">SMTP?</th>
<td><input type="hidden" name="m2l_use_smtp" value="false"><input value="true" type="checkbox" name="m2l_use_smtp" <?php if(get_option('m2l_use_smtp')=='true') { echo 'checked="checked"'; } ?> /></td>
</tr>
<tr valign="top">
<th scope="row">SMTP Username</th>
<td><input type="text" name="m2l_smtp_username" value="<?php echo get_option('m2l_smtp_username'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">SMTP Password</th>
<td><input type="password" name="m2l_smtp_password" value="<?php echo get_option('m2l_smtp_password'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Address Verification</th>
<?php

	$olderr = error_reporting(0);
	set_error_handler("ignoreerrhandler");
	$fp = fsockopen(get_option('siteurl'),80);
	restore_error_handler();
	error_reporting($olderr);
	if ($fp) {
		$can_connect = true;
	} else {
		$can_connect = false;
	}

	function ignoreerrhandler($errno, $errstr, $errfile, $errline) {
		return;
	}

?>
<?php if($can_connect) { ?>
<td><input type="hidden" name="m2l_check_email" value="false"><input type="checkbox" name="m2l_check_email" <?php if(get_option('m2l_check_email')=='true') { echo 'checked="checked"'; } ?> /></td>
<?php } else { ?>
<td><input type="hidden" name="m2l_check_email" value="false"><span style="color: red; font-weight: bold;">You cannot use email validations checks on this webserver.<br/>Check if your webserver have internet access.</span></td>
<?php } ?>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="m2l_from_email,m2l_from_name,m2l_use_smtp,m2l_smtp_host,m2l_smtp_username,m2l_smtp_password,m2l_check_email" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>