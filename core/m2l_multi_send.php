<script src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/mail2list/js/prototype-1.6.0.3.js" type="text/javascript"></script>
<div class="wrap"> 
	<div id="icon-edit-pages" class="icon32"><br /></div> 
<h2>Invio Mail...</h2>

<?

$recipients = array();

switch($_POST['recipients']) {
	case 'all':
		$q_recipients = $wpdb->get_results("SELECT ID, user_email, 'new' as status FROM ".$wpdb->prefix."users WHERE user_email<>''",ARRAY_A);
		$i = 0;
		foreach($q_recipients as $q_recipient) {
			$recipients[$i] = $q_recipient;
			$i++;
		}
		break;
	case 'TEST':
		$recipients[0] = array('ID'=>1,'user_email'=>get_option('admin_email'),'status'=>'new');
		break;
	case 'Administrator':
	case 'Editor':
	case 'Author':
	case 'Contributor':
	case 'Subscriber':
		$q_recipients = $wpdb->get_results("SELECT ".$wpdb->prefix."users.ID as ID, ".$wpdb->prefix."users.user_email as user_email, 'new' as status FROM ".$wpdb->prefix."users left join ".$wpdb->prefix."usermeta on ".$wpdb->prefix."usermeta.user_id = ".$wpdb->prefix."users.ID where ".$wpdb->prefix."usermeta.meta_key = 'wp_capabilities' and ".$wpdb->prefix."usermeta.meta_value LIKE '%".$_POST['recipients']."%' and ".$wpdb->prefix."users.user_email<>''",ARRAY_A);
		$i = 0;
		foreach($q_recipients as $q_recipient) {
			$recipients[$i] = $q_recipient;
			$i++;
		}
		break;
	default:
		$filters = unserialize(stripslashes($wpdb->get_var("SELECT filters FROM ".$wpdb->prefix."m2l_lists WHERE id=".$_POST['recipients'])));
		$sql_filter = '';
		foreach($filters as $filter) {
			$filter['condition'] = (($filter['condition']!='where') ? 'or' : 'where');
			$sql_filter .= $filter['condition'].' (meta_key = \''.$filter['filter'].'\' AND meta_value LIKE \''.$filter['value'].'\') ';
		}
	
		$sql = "SELECT ".$wpdb->prefix."usermeta.user_id, ".$wpdb->prefix."usermeta.meta_key, ".$wpdb->prefix."usermeta.meta_value, ".$wpdb->prefix."users.user_nicename, ".$wpdb->prefix."users.user_email FROM ".$wpdb->prefix."usermeta LEFT JOIN ".$wpdb->prefix."users ON ".$wpdb->prefix."usermeta.user_id = ".$wpdb->prefix."users.ID ".$sql_filter." AND meta_key NOT LIKE 'wp_%' AND meta_key NOT IN ('admin_color','comment_shortcuts','rich_editing')";
	
		$recipients_fromdb = $wpdb->get_results($sql);
	
		foreach($recipients_fromdb as $recipient_fromdb) {
			$tmp_recipients[$recipient_fromdb->user_id]['user_email'] = $recipient_fromdb->user_email;
			$tmp_recipients[$recipient_fromdb->user_id][$recipient_fromdb->meta_key] = $recipient_fromdb->meta_value;
		}
	
		foreach($tmp_recipients as $id=>$recipient) {
	
			$php_filter = '';
			
			foreach($filters as $filter) {
				$filter['condition'] = (($filter['condition']!='where') ? $filter['condition'] : '');
				$php_filter .= $filter['condition'].' "'.$recipient[$filter['filter']].'" == "'.$filter['value'].'" ';			
			}
			
			eval('$ok = (bool)('.$php_filter.');');
			if($ok) {
				if(!isset($recipient['first_name'])) {
					$recipient['first_name'] = get_usermeta($id,'first_name');
				}
				if(!isset($recipient['last_name'])) {
					$recipient['last_name'] = get_usermeta($id,'last_name');
				}
				$recipients[] = array('ID'=>$id,'user_email'=>$recipient['user_email'],'first_name'=>$recipient['first_name'],'last_name'=>$recipient['last_name'],'status'=>'new');
			}
		}
		break;
}

$wpdb->query("INSERT INTO ".$wpdb->prefix."m2l_mails SET subject='".mysql_real_escape_string($_POST['subject'])."', recipients='".mysql_real_escape_string(serialize($recipients))."', body='".mysql_real_escape_string($_POST['body'])."', created_on=NOW(), status='new'");
$mail_id = $wpdb->insert_id;

?>
<div class="metabox-holder" id="poststuff" style="width:800px">
<div id="post-body">
<div id="post-body-content">
<div class="postbox" id="epagepostcustom" style="display: block;">
	<h3 class="hndle"><span>Recipients</span></h3>
	<div class="inside">
<? foreach($recipients as $current_recipient) { ?>
		<div style="height: 20px; line-height:20px;"><span style="float:right;" id="recipient_<?=$current_recipient['ID']?>"></span><span style="width:650px;"><strong><?=$current_recipient['first_name']?> <?=$current_recipient['last_name']?> (<?=$current_recipient['user_email']?>)</strong></span></div>
<? } ?>
    </div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
var AjaxQueue = {
	batchSize: 1, //No.of simultaneous AJAX requests allowed, Default : 1
	urlQueue: [], //Request URLs will be pushed into this array
	elementsQueue: [], //Element IDs of elements to be updated on completion of a request ( as in Ajax.Updater )
	optionsQueue: [], //Request options will be pushed into this array
	setBatchSize: function(bSize){ //Method to set a different batch size. Recommended: Set batchSize before making requests
		this.batchSize = bSize;
	},
	push: function(url, options, elementID){ //Push the request in the queue. elementID is optional and required only for Ajax.Updater calls
		this.urlQueue.push(url);
		this.optionsQueue.push(options);
		if(elementID!=null){
			this.elementsQueue.push(elementID);
		} else {
			this.elementsQueue.push("NOTSPECIFIED");
		}

		this._processNext();
	},
	_processNext: function() { // Method for processing the requests in the queue. Private method. Don't call it explicitly
		if(Ajax.activeRequestCount < AjaxQueue.batchSize) // Check if the currently processing request count is less than batch size
		{
			if(AjaxQueue.elementsQueue.first()=="NOTSPECIFIED") { //Check if an elementID was specified
				// Call Ajax.Request if no ElementID specified
				//Call Ajax.Request on the first item in the queue and remove it from the queue
				new Ajax.Request(AjaxQueue.urlQueue.shift(), AjaxQueue.optionsQueue.shift()); 

				var junk = AjaxQueue.elementsQueue.shift();
			} else {
				// Call Ajax.Updater if an ElementID was specified.
				//Call Ajax.Updater on the first item in the queue and remove it from the queue
				new Ajax.Updater(AjaxQueue.elementsQueue.shift(), AjaxQueue.urlQueue.shift(), AjaxQueue.optionsQueue.shift());
			}
		}
	}
};
Ajax.Responders.register({
  //Call AjaxQueue._processNext on completion ( success / failure) of any AJAX call.
  onComplete: AjaxQueue._processNext
});

AjaxQueue.setBatchSize(1);
<? foreach($recipients as $key=>$mailto_recipient) { ?>
params = 'mail2list_ajax_send_mail=true&mail2list_recipient_id=<?=$key?>&mail2list_mailID=<?=$mail_id?>';
AjaxQueue.push("<?php echo $_SERVER['REQUEST_URI']; ?>",{method: 'post', parameters: params}, "recipient_<?=$mailto_recipient['ID']?>");
<? } ?>
</script>