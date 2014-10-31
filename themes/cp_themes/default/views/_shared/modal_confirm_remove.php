<h1><?=lang('confirm_removal')?></h1>
<?=form_open($form_url, 'class="settings"', (isset($hidden)) ? $hidden : array())?>
	<div class="alert inline issue">
		<p><?=lang('confirm_removal_desc')?></p>
	</div>
	<div class="txt-wrap">
		<ul class="checklist">
			<?php $end = end($checklist); ?>
			<?php foreach ($checklist as $item): ?>
			<li<?php if ($item == $end) echo ' class="last"'; ?>><?=$item['kind']?>: <b><?=$item['desc']?></b></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<fieldset class="form-ctrls">
		<?=cp_form_submit('btn_confirm_and_remove', 'btn_confirm_and_remove_working')?>
	</fieldset>
</form>