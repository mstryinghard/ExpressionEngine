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

		<?=form_open('C=admin_content'.AMP.'M=update_custom_category_fields', '', $form_hidden)?>
		<?php
			$this->table->set_template($cp_table_template);
			$this->table->set_heading(
										array('data' => lang('preference'), 'style' => 'width:50%;'),
										lang('setting')
									);			

			$this->table->add_row(array(
					'<strong>'.required().lang('field_name', 'field_name').'</strong>'.
					'<div class="subtext">'.lang('field_name_cont').'</div>',
					form_input(array('id'=>'field_name','name'=>'field_name','class'=>'fullfield','value'=>$field_name))
				)
			);

			$this->table->add_row(array(
					'<strong>'.required().lang('field_label', 'field_label').'</strong>'.
					'<div class="subtext">'.lang('cat_field_label_info').'</div>',
					form_input(array('id'=>'field_label','name'=>'field_label','class'=>'fullfield','value'=>$field_label))
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('field_type', 'field_type').'</strong>',
					form_dropdown('field_type', $field_type_options, $field_type, 'id="field_type"  style="width:150px"')
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('field_max_length', 'field_max1').'</strong>',
					form_input(array('id'=>'field_maxl','name'=>'field_maxl', 'size'=>4,'value'=>$field_maxl))
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('textarea_rows', 'field_ta_rows').'</strong>',
					form_input(array('id'=>'field_ta_rows','name'=>'field_ta_rows', 'size'=>4,'value'=>$field_ta_rows))
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('field_list_items', 'field_list_items').'</strong>'.
					'<div class="subtext">'.lang('field_list_instructions').'</div>',
					form_textarea(array('id'=>'field_list_items','name'=>'field_list_items','class'=>'fullfield', 'rows'=>10, 'cols'=>50, 'value'=>$field_list_items))
				)
			);

			$this->table->add_row(array(
					'<strong>'.form_label(lang('deft_field_formatting'), 'field_default_fmt').'</strong>',
					form_dropdown('field_default_fmt', $field_default_fmt_options, $field_default_fmt, 'id="field_default_fmt"  style="width:150px"')
				)
			);

			if ($update_formatting)
			{
				$this->table->add_row(array(
						'<strong>'.lang('update_existing_cat_fields', 'update_formatting').'</strong>',
						form_checkbox('update_formatting', 'y', FALSE, 'id="update_formatting"')
					)
				);
			}

			$this->table->add_row(array(
					'<strong>'.lang('show_formatting_buttons', 'show_formatting_buttons').'</strong>',
					form_radio('field_show_fmt', 'y', $field_show_fmt_y, 'id="field_show_fmt_y"').NBS.NBS.
					lang('yes', 'field_show_fmt_y').NBS.NBS.NBS.NBS.
					form_radio('field_show_fmt', 'n', $field_show_fmt_n, 'id="field_show_fmt_n"').NBS.NBS.
					lang('no', 'field_show_fmt_n')
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('text_direction', 'text_direction').'</strong>',
					form_radio('field_text_direction', 'ltr', $field_text_direction_ltr, 'id="field_text_direction_ltr"').NBS.NBS.
					lang('ltr', 'field_text_direction_ltr').NBS.NBS.NBS.NBS.
					form_radio('field_text_direction', 'rtl', $field_text_direction_rtl, 'id="field_text_direction_rtl"').NBS.NBS.
					lang('rtl', 'field_text_direction_rtl')
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('is_field_required', 'is_field_required').'</strong>',
					form_radio('field_required', 'y', $field_required_y, 'id="field_required_y"').NBS.NBS.
					lang('yes', 'field_required_y').NBS.NBS.NBS.NBS.
					form_radio('field_required', 'n', $field_required_n, 'id="field_required_n"').NBS.NBS.
					lang('no', 'field_required_n')
				)
			);

			$this->table->add_row(array(
					'<strong>'.lang('field_order', 'field_order').'</strong>',
					form_input(array('id'=>'field_order','name'=>'field_order', 'size'=>4,'value'=>$field_order))
				)
			);

			echo $this->table->generate();
		?>

		<p class="centerSubmit"><?=form_submit('custom_field_edit', lang($submit_lang_key), 'class="submit"')?></p>

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

/* End of file category_edit.php */
/* Location: ./themes/cp_themes/corporate/admin/category_edit.php */