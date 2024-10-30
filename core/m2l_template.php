<?php

if($_POST['template']) {
	if($_POST['id']!='') {
		$wpdb->query("update ".$wpdb->prefix."m2l_templates set template='".mysql_real_escape_string($_POST['template'])."', label='".mysql_real_escape_string($_POST['label'])."' where id = ".$_POST['id']);		
?>
		<div id="message" class="updated fade"><p><strong>Template updated.</strong></p></div>	
<?php
	} else {
		$wpdb->query("insert into ".$wpdb->prefix."m2l_templates set template='".mysql_real_escape_string($_POST['template'])."', label='".mysql_real_escape_string($_POST['label'])."'");	
?>
		<div id="message" class="updated fade"><p><strong>Template saved.</strong></p></div>	
<?php
	}
}

if($_GET['delete']=='true') {
	$wpdb->show_errors();
	$wpdb->query("delete from ".$wpdb->prefix."m2l_templates where id=".$_GET['id']);	
?>
		<div id="message" class="updated fade"><p><strong>Template #<?=$_GET['id']?> deleted.</strong></p></div>	
<?php }

$per_page = 20;

$mails_current_page = ($_GET['pagenum']) ? $_GET['pagenum'] : 1;

$mails = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".$wpdb->prefix."m2l_templates ORDER BY id DESC LIMIT ".(($mails_current_page-1)*$per_page).",".$per_page);

$mails_tot = $wpdb->get_var("SELECT FOUND_ROWS()");

if($mails_tot>$per_page) {
	$mails_pages = ceil($mails_tot/$per_page);
	$mails_page_start = 1;
	$mails_page_end = $per_page;
} else {
	$mails_page_start = ($mails_current_page-1)*$per_page;
	$mails_page_end = $mails_tot;
}

?>
<script>
	function abs_delete_confirm(id) {
		if(confirm('Do you really want to delete this template?')) {
			location.href = "<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_template&id="+id+"&delete=true";
		}
	}
</script>
<div class="wrap"> 
<div id="icon-edit-pages" class="icon32"><br /></div> 
<h2>Templates</h2> 

<!--
<ul class="subsubsub"> 
	<li><a href='edit-pages.php' class="current">Totale <span class="count">(27)</span></a> |</li> 
	<li><a href='edit-pages.php?post_status=publish'>Pubblicate <span class="count">(20)</span></a> |</li> 
	<li><a href='edit-pages.php?post_status=draft'>Bozze <span class="count">(7)</span></a></li>
</ul> 
-->
<div class="tablenav"> 
  <div class="alignleft actions">
	<input type="button" class="button-secondary" value="New Template" id="post-query-submit" onclick="location.href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_new_template'"/>
 </div>
<div class="tablenav-pages"><span class="displaying-num">Viewing <?=$mails_page_start?>&#8211;<?=$mails_page_end?> of <?=$mails_tot?></span>
<?php if($mails_tot>$per_page) { ?>
<a class='prev page-numbers' href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_template&pagenum=<?=($mails_current_page-1)?>'>&laquo;</a>
<?php for($i=1; $i<=$mails_pages; $i++) { ?>
<?php if($mails_current_page == $i) { ?>
	<span class='page-numbers current'><?=$i?></span> 
<?php } else { ?>
<a class='page-numbers' href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_template&pagenum=<?=$i?>'><?=$i?></a>
<?php } ?>
<?php } ?>
<a class='next page-numbers' href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_template&pagenum=<?=($mails_current_page+1)?>'>&raquo;</a>
<?php } ?>
</div> 
 
</div> 

<div class="clear"></div> 
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<table class="widefat page fixed" cellspacing="0"> 
  <thead> 
  <tr>
 	<th scope="col" id="title" class="manage-column column-author" style=""></th>
	<th scope="col" id="title" class="manage-column column-title" style="">Template Name</th>
	<th scope="col" id="date" class="manage-column column-date" style=""></th> 
  </tr> 
  </thead> 
 
  <tfoot> 
  <tr> 
 	<th scope="col" id="title" class="manage-column column-author" style=""></th>
	<th scope="col" id="title" class="manage-column column-title" style="">Template Name</th>
	<th scope="col" id="date" class="manage-column column-date" style=""></th> 
  </tr> 
  </tfoot>

  <tbody> 
<?php
$default_template = get_option('m2l_default_template');
foreach($mails as $mail) {

?>
  <tr class="alternate">
	<td><input type="radio" name="m2l_default_template" <?php if($default_template==$mail->id) { echo 'checked="checked"'; } ?> value="<?=$mail->id?>" /></td> 
	<td><a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list/core/m2l_edit_template.php&id=<?=$mail->id?>"><?=$mail->label?></a></td>
	<td><a href="javascript:abs_delete_confirm(<?=$mail->id?>)">Delete</a></td>
  </tr> 
<?php } ?>
  </tbody> 
 </table>  
 <div class="tablenav">
<div class="alignleft actions">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="m2l_default_template" />
<input type="submit" class="button-secondary action" value="Imposta come Modello predefinito"/>
</div>
</div>
</form>
</div>