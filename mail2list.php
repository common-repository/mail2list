<?php
/*
Plugin Name: Mail2List
Plugin URI: http://www.xgear.info/
Description: Mail2List let you send html mails to the users of your blog or to a mailing list.
Version: 1.3
Author: Marco Piccardo
Author URI: http://www.xgear.info/
*/

// Admin Panel
global $wpdb;
add_action('admin_menu', 'mail2list_add_pages');
add_action('init', 'mail2list_ajax_send_mail');
add_action('admin_print_scripts','mail2list_admin_includes');
register_activation_hook(__FILE__,'mail2list_install');

function mail2list_admin_includes() {
	wp_enqueue_script('mail2list_js', '/'.PLUGINDIR.'/mail2list/js/scripts.js');	
}

function mail2list_ajax_send_mail() {
	global $wpdb;

	if($_POST['mail2list_ajax_send_mail']=='true') {
		require_once(ABSPATH."wp-includes/class-phpmailer.php");
		require_once(ABSPATH."wp-includes/class-smtp.php");

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

		$mail_infos = $wpdb->get_row("SELECT subject, body, recipients FROM ".$wpdb->prefix."m2l_mails WHERE id=".$_POST['mail2list_mailID']);
    	$recipients = unserialize(stripslashes($mail_infos->recipients));

		if($can_connect and get_option('m2l_check_email')=='true') {
			$check = new SMTP_validateEmail();
			//$check->debug = true;
			$check_results = $check->validate(array($recipients[$_POST['mail2list_recipient_id']]['user_email']),get_option('m2l_from_email'));

			if(!$check_results[$recipients[$_POST['mail2list_recipient_id']]['user_email']]) {
				$recipients[$_POST['mail2list_recipient_id']]['status'] = 'invalid';
				$wpdb->query("UPDATE ".$wpdb->prefix."m2l_mails SET recipients='".mysql_real_escape_string(serialize($recipients))."', sent_on=NOW(), status='sent'");
				echo 'invalid';
				exit(0);
			}
		}
	
		$mail = new PHPMailer();
		
		$mail->From     = get_option('m2l_from_email');
		$mail->FromName = get_option('m2l_from_name');
		$mail->Host     = get_option('m2l_smtp_host');
		
		if(get_option('twh_wp_code')=='true') {
			$mail->Mailer = "smtp";
			if(get_option('m2l_smtp_username')) {
				$mail->SMTPAuth = true;
				$mail->Username = get_option('m2l_smtp_username');
				$mail->Password = get_option('m2l_smtp_password');
			}
		}

		if(!isset($recipients[$_POST['mail2list_recipient_id']]['first_name'])) {
			$recipients[$_POST['mail2list_recipient_id']]['first_name'] = get_usermeta($recipients[$_POST['mail2list_recipient_id']]['ID'],'first_name');
		}
		if(!isset($recipients[$_POST['mail2list_recipient_id']]['last_name'])) {
			$recipients[$_POST['mail2list_recipient_id']]['last_name'] = get_usermeta($recipients[$_POST['mail2list_recipient_id']]['ID'],'last_name');
		}

		$tags = array('[FIRSTNAME]','[LASTNAME]','[EMAIL]','[SITENAME]','[SITEURL]');
		$replaces = array($recipients[$_POST['mail2list_recipient_id']]['first_name'],$recipients[$_POST['mail2list_recipient_id']]['last_name'],$recipients[$_POST['mail2list_recipient_id']]['user_email'],get_bloginfo('name'),get_bloginfo('siteurl'));
		
		$mail->Subject = stripslashes($mail_infos->subject);
		$mail->MsgHTML(str_replace($tags, $replaces, stripslashes(utf8_decode($mail_infos->body))));
	    $mail->AddAddress($recipients[$_POST['mail2list_recipient_id']]['user_email']);

	    if($mail->Send()) {
			$recipients[$_POST['mail2list_recipient_id']]['status'] = 'sent';
			$wpdb->query("UPDATE ".$wpdb->prefix."m2l_mails SET recipients='".mysql_real_escape_string(serialize($recipients))."', sent_on=NOW(), status='sent' WHERE id=".$_POST['mail2list_mailID']);
			echo 'sent';
		} else {
			$recipients[$_POST['mail2list_recipient_id']]['status'] = 'error';
			$wpdb->query("UPDATE ".$wpdb->prefix."m2l_mails SET recipients='".mysql_real_escape_string(serialize($recipients))."', sent_on=NOW(), status='sent' WHERE id=".$_POST['mail2list_mailID']);
			echo 'error';
		}

		exit(0);
	}
}

function mail2list_add_pages() {
    add_menu_page('Mail2List', 'Mail2List', 7, __FILE__, 'mail2list_main_page');
    add_submenu_page(__FILE__, 'New Mail', 'New Mail', 7, 'mail2list_new_mail', 'mail2list_new_mail_page');
    add_submenu_page(__FILE__, 'Templates', 'Templates', 7, 'mail2list_template', 'mail2list_template_page');
    add_submenu_page(__FILE__, 'New Template', 'New Template', 7, 'mail2list_new_template', 'mail2list_new_template');
    add_submenu_page(__FILE__, 'Options', 'Options', 7, 'mail2list_options', 'mail2list_options_page');
    add_submenu_page(__FILE__, 'Mailing Lists', 'Mailing Lists', 10, 'mail2list_lists', 'mail2list_lists_page');
    add_submenu_page(__FILE__, 'New List', 'New List', 10, 'mail2list_new_list', 'mail2list_new_list');
}

function mail2list_template_page() {
	global $wpdb;
    include('core/m2l_template.php');
}

function mail2list_main_page() {
	global $wpdb;
    include('core/m2l_main.php');
}

function mail2list_new_mail_page() {
	global $wpdb;
    include('core/m2l_new_mail.php');
}

function mail2list_lists_page() {
	global $wpdb;
    include('core/m2l_lists.php');
}

function mail2list_options_page() {
	global $wpdb;
    include('core/m2l_options.php');
}

function mail2list_new_template() {
	global $wpdb;
    include('core/m2l_edit_template.php');
}

function mail2list_new_list() {
	global $wpdb;
    include('core/m2l_edit_list.php');
}

function mail2list_install() {
   global $wpdb;
   require_once(ABSPATH.'wp-admin/includes/upgrade.php');

   $table_name = $wpdb->prefix."m2l_mails";
   
      $sql = "CREATE TABLE ".$table_name." (
		  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  subject varchar(255),
		  body longblob,
		  recipients longblob,
		  status varchar(200),
		  created_on datetime,
		  sent_on datetime,
		  PRIMARY KEY  (id)
	  );";

      dbDelta($sql);

	$table_name = $wpdb->prefix."m2l_lists";

      $sql = "CREATE TABLE ".$table_name." (
	  	id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		label varchar(255),
		filters longblob,
		PRIMARY KEY  (id)
	  );";

      dbDelta($sql);

	$table_name = $wpdb->prefix."m2l_templates";

      $sql = "CREATE TABLE ".$table_name." (
	  	id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		label varchar(255),
		template longblob,
		PRIMARY KEY  (id)
	  );";

      dbDelta($sql);

}

?>