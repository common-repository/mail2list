<?php

$mail = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."m2l_mails where id=".$_GET['id']);

?>

<div class="wrap"> 
	<div id="icon-edit-pages" class="icon32"><br /></div> 
<h2>View Mail: "<?=stripslashes($mail->subject)?>"</h2>

<div class="metabox-holder" id="poststuff" style="width:800px">
<div id="post-body">
<div id="post-body-content">
<div class="postbox" id="epagepostcustom" style="display: block;">
	<h3 class="hndle"><span><?=stripslashes($mail->subject)?></span></h3>
	<div class="inside">
		<?=stripslashes($mail->body)?>
    </div>
</div>
<div class="postbox" id="epagepostcustom" style="display: block;">
	<h3 class="hndle"><span>Recipients</span></h3>
	<div class="inside">
<?php $recipients = unserialize(stripslashes($mail->recipients)); ?>
<?php foreach($recipients as $recipient) {
		if(!isset($recipient['first_name'])) {
			$recipient['first_name'] = get_usermeta($recipient['ID'],'first_name');
		}
		if(!isset($recipient['last_name'])) {
			$recipient['last_name'] = get_usermeta($recipient['ID'],'last_name');
		}
?>
		<div style="height: 20px; line-height:20px;"><span style="float:right;" id="recipient_<?=$recipient['ID']?>"><?=$recipient['status']?></span><span style="width:650px;"><strong><?=$recipient['first_name']?> <?=$recipient['last_name']?> (<?=$recipient['user_email']?>)</strong></span></div>
<?php } ?>
    </div>
</div>
</div>
</div>
</div>
</div>