<?php

$mail = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."m2l_lists where id=".$_GET['id']);

?>
<div class="wrap"> 
	<div id="icon-edit-pages" class="icon32"><br /></div> 
<h2>View List: "<?=stripslashes($mail->label)?>"</h2>

<div class="metabox-holder" id="poststuff" style="width:800px">
<div id="post-body">
<div id="post-body-content">
<div class="postbox" id="epagepostcustom">
	<h3 class="hndle"><span>Recipients</span></h3>
	<div class="inside">
<?php

	$filters = unserialize(stripslashes($mail->filters));
	$sql_filter = '';
	foreach($filters as $filter) {
		$filter['condition'] = (($filter['condition']!='where') ? 'or' : 'where');
		$sql_filter .= $filter['condition'].' (meta_key = \''.$filter['filter'].'\' AND meta_value LIKE \''.$filter['value'].'\') ';
	}

	$sql = "SELECT wp_usermeta.user_id, wp_usermeta.meta_key, wp_usermeta.meta_value, wp_users.user_nicename, wp_users.user_email FROM wp_usermeta LEFT JOIN wp_users ON wp_usermeta.user_id = wp_users.ID ".$sql_filter." AND meta_key NOT LIKE 'wp_%' AND meta_key NOT IN ('admin_color','comment_shortcuts','rich_editing')";

	$recipients_fromdb = $wpdb->get_results($sql);

	foreach($recipients_fromdb as $recipient_fromdb) {
		$recipients[$recipient_fromdb->user_id]['user_email'] = $recipient_fromdb->user_email;
		$recipients[$recipient_fromdb->user_id][$recipient_fromdb->meta_key] = $recipient_fromdb->meta_value;
	}

	foreach($recipients as $id=>$recipient) {

		$php_filter = '';
		
		foreach($filters as $filter) {
			$filter['condition'] = (($filter['condition']!='where') ? $filter['condition'] : '');
			$php_filter .= $filter['condition'].' "'.$recipient[$filter['filter']].'" == "'.$filter['value'].'" ';			
		}
		
		eval('$ok = (bool)('.$php_filter.');');
		if(!$ok) {
			unset($recipients[$id]);
		} else {
			if(!isset($recipient['first_name'])) {
				$recipients[$id]['first_name'] = get_usermeta($id,'first_name');
			}
			if(!isset($recipient['last_name'])) {
				$recipients[$id]['last_name'] = get_usermeta($id,'last_name');
			}
		}
	}

?>
<?php foreach($recipients as $id=>$recipient) { ?>
		<div style="height: 20px; line-height:20px;"><span style="width:650px;"><strong><?=$recipient['first_name']?> <?=$recipient['last_name']?> (<?=$recipient['user_email']?>)</strong></span></div>
<?php } ?>
    </div>
</div>
</div>
</div>
</div>
</div>