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

	<?php $this->load->view('_shared/message')?>
		
        <div class="lightHeading"><h2><?=lang('edit_template')?>: <?=$template_group?>/<?=$template_name?></h2></div>
        
        <div class="templatePageContents">
        	
			<div id="templateEditor" class="formArea">
				
				
		<?php if ($message):?>
			<span class="notice"><?=$message?></span>
		<?php endif;?>

		<div class="clear_left" id="template_details" style="margin-bottom: 0">
<span class="button">

			<?=form_open('C=design'.AMP.'M=template_revision_history'.AMP.'tgpref='.$group_id, array('id' => 'revisions', 'name' => 'revisions', 'template_id' => $template_id, 'target' => 'Revisions'))?>	
			
			<?=form_dropdown('revision_id', $revision_options, '', 'id="revision_id"')?>
			<?=form_submit('submit', lang('submit'), 'class="submit" id="revision_button"')?>
			<?=form_close()?>
</span>			
			<p>
			<?php if ($file_synced === FALSE):?>
			<?=lang('from_file')?> [<?=$last_file_edit?>] (<?=lang('save_to_sync')?>)
			<?php else:?>
			<?=lang('from_db')?> [<?=$edit_date?>] <?=lang('by').NBS.$last_author?>
			<?php endif;?>
			</p>

		</div>

		<div id="template_create">
			<?=form_open('C=design'.AMP.'M=update_template'.AMP.'tgpref='.$group_id, '', array('template_id' => $template_id, 'group_id' => $group_id))?>
			

			<?=form_textarea(array(
									'name'	=> 'template_data',
									'id'	=> 'template_data',
									'cols'	=> '100',
									'rows'	=> $prefs['template_size'],
									'value' => $template_data,
									'style' => 'border-left: 1px solid #ced7de;'
							));?>


		<?php if(is_array($warnings) && count($warnings)): ?>
			<?=form_hidden('warnings', 'yes')?>
			<div class="editAccordion open first">
				<h3><?=lang('template_warnings')?></h3>
				<div>
				<table class="templateTable templateEditorTable" id="templateWarningsList" border="0" cellspacing="0" cellpadding="0" style="margin: 0;">
					<tr>
						<th><?=lang('template_warnings_blurb')?></th>
						<th><?=lang('template_warnings_actions')?></th>
					</tr>

					<?php foreach($warnings as $tag_name => $info): ?>
						<tr>
							<td>
								{exp:<?=$tag_name?> &hellip;<br />
								<p style="font-weight: bold;">
									<?php foreach(array_unique($info['errors']) as $error): ?>
									<?=lang($error)?><br />
									<?php endforeach; ?>
								</p>
							</td>
							<td style="padding: 5px; font-weight: bold;">
								<p>
									<?php if (in_array('tag_install_error', $info['errors'])): ?>
									<a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=module_installer'.AMP.'module='.ucfirst($tag_name)?>" rel="external" id="install_<?=$tag_name?>" class="submit install_module">Install Module</a>
									<?php endif;?>
									<a href="#" id="replace_<?=$tag_name?>" class="submit find_and_replace">Find and Replace</a>
								</p>
							</td>
						</tr>
					<?php endforeach;?>
				</table>
				<script type="text/javascript" charset="utf-8">
					EE.manager = EE.manager || {};
					EE.manager.warnings = <?=$this->javascript->generate_json($warnings, TRUE)?>;
				</script>
			
				</div>
			</div>
		<?php endif; ?>

		<?php if ($can_admin_templates): ?>
			<div class="editAccordion">
				<h3><?=lang('preferences')?></h3>
				<div>
					<table class="templateTable templateEditorTable" id="templatePreferences" border="0" cellspacing="0" cellpadding="0" style="margin: 0;">
						<tr>
							<th><?=lang('name_of_template')?></th>
							<th><?=lang('type')?></th>
							<th><?=lang('cache_enable')?></th>
							<th><?=lang('refresh_interval')?></th>
							<th><?=lang('enable_php')?></th>
							<th><?=lang('parse_stage')?></th>
							<th><?=lang('hit_counter')?></th>
							<th><?=lang('template_size')?></th>
						</tr>
						<tr>
							<td><input name="group_name" class="group_name" type="text" size="15" value="<?=$template_name?>" /></td>
							<td><select class="template_type" name="template_type" id="template_type">
								<option value="css" <?=($prefs['template_type'] == 'css') ? 'selected="selected"':''?>><?=lang('css_stylesheet')?></option>
								<option value="js" <?=($prefs['template_type'] == 'js') ? 'selected="selected"':''?>><?=lang('js')?></option>
								<option value="rss" <?=($prefs['template_type'] == 'rss') ? 'selected="selected"':''?>><?=lang('rss')?></option>
								<option value="static" <?=($prefs['template_type'] == 'static') ? 'selected="selected"':''?>><?=lang('static')?></option>
								<option value="webpage" <?=($prefs['template_type'] == 'webpage') ? 'selected="selected"':''?>><?=lang('webpage')?></option>
								<option value="xml" <?=($prefs['template_type'] == 'xml') ? 'selected="selected"':''?>><?=lang('xml')?></option>
							</select></td>
							<td>
								<?=form_dropdown('cache', array('y' => lang('yes'), 'n' => lang('no')), $prefs['cache'])?>
							</td>
							<td>
								<table><tr><td style="text-align:left;"><input class="refresh" name="refresh" type="text" size="4" value="<?=$prefs['refresh']?>" /></td></tr><tr>
									<td style="text-align:left;"><?=lang('refresh_in_minutes')?></td>
								</tr></table>
							</td>
							<td>
								<?=form_dropdown('allow_php', array('y' => lang('yes'), 'n' => lang('no')), $prefs['allow_php'])?>
							</td>
							<td>
								<?=form_dropdown('php_parse_location', array('i' => lang('input'), 'o' => lang('output')), $prefs['php_parse_location'])?>
							</td>
							<td><input name="hits" class="hits" type="text" size="8" value="<?=$prefs['hits']?>" /></td>
							<td><input name="template_size" class="template_size" type="text" size="4" value="<?=$prefs['template_size']?>" /></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="editAccordion">
				<h3><?=lang('access')?></h3>
				<div>
				<table class="templateTable templateEditorTable" id="templateAccess" border="0" cellspacing="0" cellpadding="0" style="margin: 0;">
					<tr>
						<th><?=lang('member_group')?></th>
						<th><?=lang('can_view_template')?></th>
					</tr>
					<tr>
						<td><?=lang('select_all')?></td>
						<td><?=lang('yes')?> <input type="radio" name="select_all_top" id="select_all_top_y" class="ignore_radio" value="y" /> &nbsp; <?=lang('no')?> <input type="radio" name="select_all_top" id="select_all_top_n" class="ignore_radio" value="n" /></td>
					</tr>
					<?php foreach($member_groups as $id => $group):?>
					<tr>
						<td><?=$group->group_title?></td>
						<td><?=lang('yes')?> <input type="radio" name="access_<?=$id?>" id="access_<?=$id?>_y" value="y" <?=$access[$id] ? 'checked="checked"' : ''?> /> &nbsp; <?=lang('no')?> <input type="radio" name="access_<?=$id?>" id="access_<?=$id?>_n" value="n" <?=$access[$id] ? '' : 'checked="checked"'?> /></td>
					</tr>
					<?php endforeach; ?>
					<tr>
						<td>Select All</td>
						<td><?=lang('yes')?> <input type="radio" name="select_all_bottom" id="select_all_bottom_y" class="ignore_radio" value="y" /> &nbsp; <?=lang('no')?> <input type="radio" name="select_all_bottom" id="select_all_bottom_n" class="ignore_radio" value="n" /></td>
					</tr>
				</table>
				</div>
			</div>

		<?php endif; ?>

			<div class="editAccordion shun">
				<h3><?=lang('template_notes')?></h3>
				<div>
					<table class="templateTable templateEditorTable" border="0" cellspacing="0" cellpadding="0" style="margin: 0;">
					<tr>
						<th><?=lang('template_notes_desc')?></th>
					</tr>
					<tr>
						<td><textarea class="field" rows="10" name="template_notes" id="template_notes"><?=$template_notes?></textarea></td>
					</tr>
					</table>
				</div>
			</div>
			
			<?php if ($save_template_revision): ?>
			<p><?=form_checkbox('save_template_revision', 'y', $save_template_revision, 'id="save_template_revision"')?> &nbsp;
			<?=form_label(lang('save_template_revision'), 'save_template_revision')?></p>
			<?php endif; ?>
			
			<!-- @todo put columns back in -->
			<input type="hidden" name="columns" id="columns" value = "" />

			<?php if ($can_save_file): ?>
			<p><?=form_checkbox('save_template_file', 'y', $save_template_file, 'id="save_template_file"')?> &nbsp;
			<?=form_label(lang('save_template_file'), 'save_template_file')?></p>
			<?php endif; ?>

			<p><?=form_submit('update', lang('update'), 'class="submit"')?> <?=form_submit('update_and_return', lang('update_and_return'), 'class="submit"')?></p>
			<?=form_close()?>

				</div>
			</div>
		</div>
	</div> <!-- contents -->
</div> <!-- mainContent -->

<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/accessories');
	$this->load->view('_shared/footer');
}

/* End of file edit_template.php */
/* Location: ./themes/cp_themes/default/design/edit_template.php */