<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/header');
	$this->load->view('_shared/main_menu');
	$this->load->view('_shared/sidebar');
	$this->load->view('_shared/breadcrumbs');
}
?>

<div id="mainContent"<?=$maincontent_state?>>
	<?php $this->load->view('_shared/right_nav')?>
	<div class="contents">

			<div class="heading"><h2><?=$cp_page_title?></h2></div>
			
		    <div class="pageContents">
		
				<?=form_open($form_action, '', $form_hidden)?>

				<p class="notice"><?=$message?></p>

				<p class="notice"><?=lang('data_will_be_lost')?></p>

				<p><?=form_submit('submit', lang('delete_module'), 'class="delete"')?></p>
	
				<?=form_close()?>

		</div> <!-- pageContents -->
	</div> <!-- contents -->
</div> <!-- mainContent -->

<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/accessories');
	$this->load->view('_shared/footer');
}

/* End of file delete_confirm.php */
/* Location: ./themes/cp_themes/corporate/addons/delete_confirm.php */