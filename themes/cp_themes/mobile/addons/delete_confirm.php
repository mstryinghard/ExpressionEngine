<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/header');
}
?>
<div id="home" class="current">
    <div class="toolbar">
        <h1><?=$cp_page_title?></h1>
        <a href="<?=BASE.AMP?>C=addons" class="back"><?=lang('back')?></a>
        <a class="button" id="infoButton" href="<?=BASE.AMP.'C=login'.AMP.'M=logout'?>"><?=lang('logout')?></a>
    </div>
	<?php $this->load->view('_shared/right_nav')?>
	<?php $this->load->view('_shared/message');?>
	
	<?=form_open($form_action, '', $form_hidden)?>

	<div class="container pad">

		<p><strong><?=$message?></strong></p>

		<p class="notice"><?=lang('data_will_be_lost')?></p>

		<p><?=form_submit('delete', lang('delete_module'), 'class="whiteButton"')?></p>
	
	</div>

	<?=form_close()?>


</div>
<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/accessories');
	$this->load->view('_shared/footer');
}

/* End of file delete_confirm.php */
/* Location: ./themes/cp_themes/mobile/addons/delete_confirm.php */