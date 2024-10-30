<?php

if(isset($_GET['id'])) {
	include('m2l_view_mail.php');
} else if($_GET['delete']=='true') {
	$wpdb->show_errors();
	$wpdb->query("delete from ".$wpdb->prefix."m2l_mails where id=".$_GET['id']);	
?>
		<div id="message" class="updated fade"><p><strong>Mail #<?=$_GET['id']?> deleted.</strong></p></div>	
<?php }

$per_page = 20;

$mails_current_page = ($_GET['pagenum']) ? $_GET['pagenum'] : 1;

$mails = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".$wpdb->prefix."m2l_mails ORDER BY id DESC LIMIT ".(($mails_current_page-1)*$per_page).",".$per_page);

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
		if(confirm('Do you really want to delete this mail?')) {
			location.href = "<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list/mail2list.php&id="+id+"&delete=true";
		}
	}
</script>
<div class="wrap"> 
<div id="icon-edit-pages" class="icon32"><br /></div> 
<h2>Mailing Lists</h2> 

<form id="posts-filter" action="" method="get"> 
<!--
<ul class="subsubsub"> 
	<li><a href='edit-pages.php' class="current">Totale <span class="count">(27)</span></a> |</li> 
	<li><a href='edit-pages.php?post_status=publish'>Pubblicate <span class="count">(20)</span></a> |</li> 
	<li><a href='edit-pages.php?post_status=draft'>Bozze <span class="count">(7)</span></a></li>
</ul> 
-->
<div class="tablenav">
  <div class="alignleft actions">
	<input type="button" class="button-secondary" value="New Mail" id="post-query-submit" onclick="location.href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list_new_mail'"/>
 </div>
<div class="tablenav-pages"><span class="displaying-num">Viewing <?=$mails_page_start?>&#8211;<?=$mails_page_end?> of <?=$mails_tot?></span>
<?php if($mails_tot>$per_page) { ?>
<a class='prev page-numbers' href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list/mail2list.php&pagenum=<?=($mails_current_page-1)?>'>&laquo;</a>
<?php for($i=1; $i<=$mails_pages; $i++) { ?>
<?php if($mails_current_page == $i) { ?>
	<span class='page-numbers current'><?=$i?></span> 
<?php } else { ?>
<a class='page-numbers' href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list/mail2list.php&pagenum=<?=$i?>'><?=$i?></a>
<?php } ?>
<?php } ?>
<a class='next page-numbers' href='<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mail2list/mail2list.php&pagenum=<?=($mails_current_page+1)?>'>&raquo;</a>
<?php } ?>
</div> 
 
</div> 

<div class="clear"></div> 

<table class="widefat page fixed" cellspacing="0"> 
  <thead> 
  <tr> 
	<th scope="col" id="title" class="manage-column column-title" style="">Mailing List</th> 
	<th scope="col" id="author" class="manage-column column-author" style="">Status</th> 
	<th scope="col" id="date" class="manage-column column-date" style="">Created on</th> 
	<th scope="col" id="date" class="manage-column column-date" style="">Sent on</th> 
	<th scope="col" id="date" class="manage-column column-date" style=""></th> 
  </tr> 
  </thead> 
 
  <tfoot> 
  <tr> 
	<th scope="col" id="title" class="manage-column column-title" style="">Mailing List</th> 
	<th scope="col" id="author" class="manage-column column-author" style="">Status</th> 
	<th scope="col" id="date" class="manage-column column-date" style="">Created on</th> 
	<th scope="col" id="date" class="manage-column column-date" style="">Sent on</th> 
	<th scope="col" id="date" class="manage-column column-date" style=""></th>
  </tr> 
  </tfoot>

  <tbody> 
<?php

foreach($mails as $mail) {

?>
  <tr class="alternate"> 
	<td><a href="<?php echo $_SERVER['REQUEST_URI']?>&id=<?=$mail->id?>"><?=$mail->subject?></a></td> 
	<td><?=$mail->status?></td> 
	<td><?=$mail->created_on?></td> 
	<td><?=$mail->sent_on?></td> 
	<td><a href="javascript:abs_delete_confirm(<?=$mail->id?>)">Delete</a></td> 
  </tr> 
<?php } ?>
  </tbody> 
 </table>  
 
</div>
<?php } ?>