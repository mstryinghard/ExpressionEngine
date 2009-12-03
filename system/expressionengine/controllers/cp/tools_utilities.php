<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2009, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Home Page Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
class Tools_utilities extends Controller {

	var $errors = array();
	var $members = array();
	var $members_custom = array();
	var $default_fields	= array();
	var $default_custom_fields	= array();
	var $taken = array();
	var $invalid_names = array();
	//var $new_custom_fields = array();

	function Tools_utilities()
	{
		// Call the Controller constructor.  
		// Without this, the world as we know it will end!
		parent::Controller();

		// Does the "core" class exist?  Normally it's initialized
		// automatically via the autoload.php file.  If it doesn't
		// exist it means there's a problem.
		if ( ! isset($this->core) OR ! is_object($this->core))
		{
			show_error('The ExpressionEngine Core was not initialized.  Please make sure your autoloader is correctly set up.');
		}

		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('tools');
		$this->load->vars(array('cp_page_id'=>'tools'));
		
		$this->javascript->compile();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Index function
	 * 
	 * Every controller must have an index function, which gets called
	 * automatically by CodeIgniter when the URI does not contain a call to
	 * a specific method call
	 *
	 * @access	public
	 * @return	mixed
	 */	
	function index()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->cp->set_variable('cp_page_title', $this->lang->line('tools_utilities'));

		$this->javascript->compile();

		$this->load->vars(array('controller'=>'tools/tools_utilities'));

		$this->load->view('_shared/overview');
	}

	// --------------------------------------------------------------------

	/**
	 * Import Utilities
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function import_utilities()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->cp->set_variable('cp_page_title', $this->lang->line('import_utilities'));

		$this->javascript->compile();

		$this->load->view('tools/import_utilities');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Member Import
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function member_import()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');

		$this->cp->set_variable('cp_page_title', $this->lang->line('member_import'));

		$this->javascript->compile();

		$this->load->view('tools/member_import');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Import Member Data from XML
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function import_from_xml()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');

		$this->_import_xml_form();
	}
	
	function _import_xml_form()
	{
		$this->load->helper(array('form', 'date'));
		$this->lang->loadfile('member_import');
		$this->load->model('member_model');

		$this->cp->set_variable('cp_page_title', $this->lang->line('import_from_xml'));

		$this->javascript->compile();
		
		$get_groups = $this->member_model->get_member_groups(); 
		
		foreach ($get_groups->result() as $member_group)
		{
			$member_groups[$member_group->group_id] = $member_group->group_title;
		}

		$vars['language_options'] = array('None' => 'None', 'English' => 'English'); //$todo
		$vars['member_groups'] = $member_groups;
		$vars['dst_enabled'] = $this->config->item('daylight_savings');  
		$vars['auto_custom_field_enabled'] = TRUE;
		
		$this->load->view('tools/import_from_xml', $vars);
		
	}


	function _import_xml_validate()
	{
		$this->load->library('form_validation');
		
		$config = array(
					   array(
							 'field'   => 'xml_file',
							 'label'   => 'lang:xml_file_loc',
							 'rules'   => 'required|callback__file_exists'
						  ),
					   array(
							 'field'   => 'group_id',
							 'label'   => 'lang:default_group_id',
							 'rules'   => ''
						  ),
					   array(
							 'field'   => 'language',
							 'label'   => 'lang:language',
							 'rules'   => ''
						  ),
					   array(
							 'field'   => 'timezones',
							 'label'   => 'lang:timezone',
							 'rules'   => ''
						  ),
					   array(
							 'field'   => 'time_format',
							 'label'   => 'lang:time_format',
							 'rules'   => ''
						  ),
					   array(
							 'field'   => 'daylight_savings',
							 'label'   => 'lang:daylight_savings',
							 'rules'   => ''
						  ),
					   array(
							 'field'   => 'auto_custom_field',
							 'label'   => 'lang:auto_custom_field',
							 'rules'   => ''
						  )
						
					);
		
		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<span class="notice">', '</span>');
	}


	// --------------------------------------------------------------------

	/**
	 * ConfirmImport Member Data from XML
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function confirm_xml_form()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');

		$this->load->library('table');
		$this->load->helper(array('form', 'date'));
		$this->lang->loadfile('member_import');
		$this->load->model('member_model');
		$this->load->library('javascript');

		
		$this->db->select('group_title');
		$this->db->where('group_id', $this->input->post('group_id'));
		$this->db->distinct();
		$query = $this->db->get('member_groups');
		
		$group_title = '';
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$row['group_title'];
			
		}

		$this->javascript->compile();

		$data = array(
						'xml_file'   		=> $this->input->post('xml_file'),
						'group_id' 			=> $this->input->post('group_id'),
						'language' 			=> ($this->input->post('language') == $this->lang->line('none')) ? '' : $this->input->post('language'),
						'timezone' 			=> $this->input->post('timezones'),
						'time_format' 		=> $this->input->post('time_format'),
						'daylight_savings' 	=> ($this->input->post('daylight_savings') == 'y') ? 'y' : 'n',
						'auto_custom_field' => ($this->input->post('auto_custom_field') == 'y') ? 'y' : 'n'
					);
					

		$vars['data_display'] = array(
						'xml_file'   		=> $data['xml_file'],
						'group_id' 			=> $group_title,
						'language' 			=> ($data['language'] == $this->lang->line('none')) ? $this->lang->line('none') : ucfirst($data['language']),
						'timezone' 			=> $this->lang->line($data['timezone']),
						'time_format' 		=> ($data['time_format'] == 'us') ? $this->lang->line('united_states') : $this->lang->line('european'),
						'daylight_savings' 	=> ($data['daylight_savings'] == 'y') ? $this->lang->line('yes') : $this->lang->line('no'),
						'auto_custom_field' => ($data['auto_custom_field'] == 'y') ? $this->lang->line('yes') : $this->lang->line('no')
					 );

				
		$vars['form_hidden'] = $data;
		$vars['added_fields'] = array();
	
		// Branch off here if we need to create a new custom field
		if ($data['auto_custom_field'] == 'y')
		{
			$new_custom_fields = $this->custom_field_check($data['xml_file']);
			
			if ($new_custom_fields != FALSE && count($new_custom_fields) > 0)
			{
				return $this->_new_custom_fields_form($data, $vars, $new_custom_fields);
			}
			else
			{
				$vars['message'] = $this->lang->line('unable_to_parse_custom_fields');
			}
		}

		$this->_confirm_custom_field_form($vars);
	}
	
	function _confirm_custom_field_form($vars)
	{
		$this->_import_xml_validate();
		
		if ($this->form_validation->run() === FALSE)
		{
			return $this->_import_xml_form();
		}

		$this->load->library('table');
		$this->load->helper(array('form', 'date'));

		$this->cp->set_variable('cp_page_title', $this->lang->line('confirm_details'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=import_from_xml', $this->lang->line('import_from_xml'));
	
		$vars['post_url'] = 'C=tools_utilities'.AMP.'M=process_xml';

		$this->load->view('tools/confirm_import_xml', $vars);		
	}
	
	function final_confirm_xml_form()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');
		$this->load->library('table');
		$this->load->helper(array('form', 'date'));
		$this->lang->loadfile('member_import');
		$this->load->model('member_model');
		$this->load->library('javascript');

		
		$this->db->select('group_title');
		$this->db->where('group_id', $this->input->post('group_id'));
		$this->db->distinct();
		$query = $this->db->get('member_groups');
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();
			
		}
		$this->javascript->compile();

		$data = array(
						'xml_file'   		=> $this->input->post('xml_file'),
						'group_id' 			=> $this->input->post('group_id'),
						'language' 			=> ($this->input->post('language') == $this->lang->line('none')) ? '' : $this->input->post('language'),
						'timezone' 			=> $this->input->post('timezones'),
						'time_format' 		=> $this->input->post('time_format'),
						'daylight_savings' 	=> ($this->input->post('daylight_savings') == 'y') ? 'y' : 'n',
						'auto_custom_field' => ($this->input->post('auto_custom_field') == 'y') ? 'y' : 'n'
					);
					

		$vars['data_display'] = array(
						'xml_file'   		=> $data['xml_file'],
						'group_id' 			=> $row['group_title'],
						'language' 			=> ($data['language'] == $this->lang->line('none')) ? $this->lang->line('none') : ucfirst($data['language']),
						'timezone' 			=> $this->lang->line($data['timezone']),
						'time_format' 		=> ($data['time_format'] == 'us') ? $this->lang->line('united_states') : $this->lang->line('european'),
						'daylight_savings' 	=> ($data['daylight_savings'] == 'y') ? $this->lang->line('yes') : $this->lang->line('no'),
						'auto_custom_field' => ($data['auto_custom_field'] == 'y') ? $this->lang->line('yes') : $this->lang->line('no')
					 );

				
		$vars['form_hidden'] = $data;
		
		$this->load->library('table');
		$this->load->helper(array('form', 'date'));

		$this->cp->set_variable('cp_page_title', $this->lang->line('confirm_details'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=import_from_xml', $this->lang->line('import_from_xml'));
	
		$vars['post_url'] = 'C=tools_utilities'.AMP.'M=process_xml';
		
		$vars['added_fields'] = $this->input->post('added_fields');

		$this->load->view('tools/confirm_import_xml', $vars);		
		
	
	}
	
		
	function _new_custom_fields_form($data, $vars, $new_custom_fields)
	{
		$this->load->library('table');
		
		$this->load->helper(array('form', 'date'));
		$this->lang->loadfile('member_import');
		
		$this->javascript->output(array(
				'$(".toggle_all").toggle(
					function(){
						$("input[class=toggle]").each(function() {
							this.checked = true;
						});
					}, function (){
						var checked_status = this.checked;
						$("input[class=toggle]").each(function() {
							this.checked = false;
						});
					}
				);')
			);	

		$this->javascript->compile();			
				
		$vars['form_hidden']['new'] = $new_custom_fields;
		$vars['new_fields'] = $new_custom_fields;
				
		$query = $this->member_model->count_records('member_fields');
			
		$vars['order_start'] = $query + 1;

		$vars['new_fields'] = $new_custom_fields;
		
		/**  Create the pull-down menu **/
		
		$vars['m_field_type_options'] = array(
									'text'=>$this->lang->line('text_input'),
									'textarea'=>$this->lang->line('textarea')
									);
		$vars['m_field_type'] = '';

		/**  Field formatting **/
		
		$vars['m_field_fmt_options'] = array(
									'none'=>$this->lang->line('none'),
									'br'=>$this->lang->line('auto_br'),
									'xhtml'=>$this->lang->line('xhtml')
									);											
		$vars['m_field_fmt'] = '';

				
		return $this->load->view('tools/custom_field_form', $vars);				
	}
	
	function process_xml()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');
		
		$xml_file   = ( ! $this->input->post('xml_file'))  ? '' : $this->input->post('xml_file');

		//  Read XML file contents
		$contents = read_file($xml_file);
		
		if ($contents === FALSE)
		{
			return $this->view_xml_errors($this->lang->line('unable_to_read_file'));
		}		
		
		$this->load->library('xmlparser');

		// parse XML data
		$xml = $this->xmlparser->parse_xml($contents);

		if ($xml === FALSE)
		{
			return $this->view_xml_errors($this->lang->line('unable_to_parse_xml'));
		}		

		$this->validate_xml($xml);

		//  Show Errors
		if (count($this->errors) > 0)
		{
			$out = array();
			
			foreach($this->errors as $error)
			{
				foreach($error as $val)
				{
					$out[] = $val;
				}
			}
			
			return $this->view_xml_errors($out);
		}

		/** -------------------------------------
		/**  Ok! Cross Fingers and do it!
		/** -------------------------------------*/
		
		$imports = $this->do_import();
		
		$msg = $this->lang->line('import_success_blurb').'<br>'.str_replace('%x', $imports, $this->lang->line('total_members_imported'));
		$this->session->set_flashdata('message_success', $msg);

		$this->functions->redirect(BASE.AMP.'C=tools_utilities'.AMP.'M=import_from_xml');

	}
	/* END */


	function custom_field_check($xml_file)
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		//  Read XML file contents
		$contents = read_file($xml_file);
		$new_custom_fields = array();
		
		if ($contents === FALSE)
		{
			return;
		}		
		
		$this->load->library('xmlparser');

		// parse XML data
		$xml = $this->xmlparser->parse_xml($contents);
		
		if ($xml == FALSE)
		{
			return FALSE;
		}
		
		//  Retreive Valid fields from database
		$query = $this->db->query("SHOW COLUMNS FROM exp_members");
		$existing_fields['birthday'] = '';

		foreach ($query->result_array() as $row)
		{
			$existing_fields[$row['Field']] = '';
		}

		$this->db->select('m_field_name');
		$m_custom_fields = $this->db->get('member_fields');
		
		if ($m_custom_fields->num_rows() > 0)
		{
   			foreach ($m_custom_fields->result() as $row)
   			{
				$existing_c_fields[$row->m_field_name] = '';
			}
		}
		
		// note- might could just go through first iteration?
		if (is_array($xml->children[0]->children))
		{
			foreach($xml->children as $member)
			{
				if ($member->tag == "member")
				{
					foreach($member->children as $tag)
					{
						$i = 0;
						
						// Is the XML tag an allowed database field
						if (! isset($existing_fields[$tag->tag]) && ! isset($existing_c_fields[$tag->tag]))
						{
							$new_custom_fields[] = $tag->tag;						
						}
						elseif (isset($existing_c_fields[$tag->tag]))
						{
							while($i < 100)
							{
								$i++;
								
								if ( ! isset($existing_c_fields[$tag->tag.'_'.$i]))
								{
									$new_custom_fields[] = $tag->tag.'_'.$i;
									break;
								}
							}
						}
					}
				}
			}
		}
		
		return array_unique($new_custom_fields);
	}

	// --------------------------------------------------------------------

	/**
	 * Validate XML for Member Import
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function validate_xml($xml)
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}
			
		$this->lang->loadfile('member_import');
		$this->load->library('validate');
		$this->load->helper('security');
		
		$this->validate->member_id			= '';
		$this->validate->val_type			= 'new';
		$this->validate->fetch_lang			= TRUE;
		$this->validate->require_cpw		= FALSE;
		$this->validate->enable_log			= FALSE;
		$this->validate->cur_username		= '';
		$this->validate->cur_screen_name	= '';
		$this->validate->cur_password		= '';
		$this->validate->cur_email			= '';

		
		$i = 0;
		
		//  Retreive Valid fields from database
		$query = $this->db->query("SHOW COLUMNS FROM exp_members");

		foreach ($query->result_array() as $row)
		{
			$this->default_fields[$row['Field']] = '';
		}

		$this->db->select('m_field_name');
		$m_custom_fields = $this->db->get('member_fields');
		
		if ($m_custom_fields->num_rows() > 0)
		{
   			foreach ($m_custom_fields->result() as $row)
   			{
				$this->default_custom_fields[$row->m_field_name] = '';
			}
		}
		
		// we don't allow <unique_id>
		unset($this->default_fields['unique_id']);
		
		$u = array(); // username garbage array
		$s = array(); // screen_name garbage array
		$e = array(); // email garbage array
		$m = array(); // member_id garbage array
		
		if (is_array($xml->children[0]->children))
		{
			foreach($xml->children as $member)
			{
				if ($member->tag == "member")
				{
					foreach($member->children as $tag)
					{
						// Is the XML tag an allowed database field
						if (isset($this->default_fields[$tag->tag]))
						{
							$this->members[$i][$tag->tag] = $tag->value;						
						}
						elseif ($tag->tag == 'birthday')
						{
							// We have a special XML format for birthdays that doesn't match the database fields
							foreach($tag->children as $birthday)
							{
									switch ($birthday->tag)
									{
										case 'day':
											$this->members[$i]['bday_d'] = $birthday->value;
											break;
										case 'month':
											$this->members[$i]['bday_m'] = $birthday->value;
											break;
										case 'year':
											$this->members[$i]['bday_y'] = $birthday->value;
											break;
										default:
											$this->errors[] = array($this->lang->line('invalid_tag')." '&lt;".$birthday->tag."&gt;'");
											break;
									}
							}

								
							if ( ! isset($this->members[$i]['bday_d']) || ! isset($this->members[$i]['bday_m']) || ! isset($this->members[$i]['bday_y']))
							{
								$this->errors[] = array($this->lang->line('missing_birthday_child'));
							}
								
							$this->members[$i][$tag->tag] = $tag->value;
						}
						elseif (isset($this->default_custom_fields[$tag->tag]))
						{
							$this->members_custom[$i][$tag->tag] = $tag->value;
						}							
						else
						{
							// not a database field and not a <birthday> so club it like a baby seal!
							//$this->errors[] = array($this->lang->line('invalid_tag')." '&lt;".$tag->tag."&gt;'");							
						}
				
						/* -------------------------------------
						/*  username, screen_name, and email
						/*  must be validated and unique
						/* -------------------------------------*/
						
						switch ($tag->tag)
						{
							case 'username':
								$this->validate->username = $tag->value;
								if ( ! in_array($tag->value, $u))
								{
									$u[] = $tag->value;
								}
								else
								{
									$this->errors[] = array($this->lang->line('duplicate_username').$tag->value);
								}
								break;
							case 'screen_name':
								$this->validate->screen_name = $tag->value;
								if ( ! in_array($tag->value, $s))
								{
									$s[] = $tag->value;
								}
								else
								{
									$this->errors[] = array($this->lang->line('duplicate_screen_name').$tag->value);
								}
								break;
							case 'email':
								if ( ! in_array($tag->value, $e) OR $this->config->item('allow_multi_emails') == 'y')
								{
									$e[] = $tag->value;
								}
								else
								{
									$this->errors[] = array($this->lang->line('duplicate_email').$tag->value);
								}
								$this->validate->email = $tag->value;
								break;
							case 'member_id':
								if ( ! in_array($tag->value, $m))
								{
									$m[] = $tag->value;
								}
								else
								{
									$this->errors[] = array(str_replace("%x", $tag->value, $this->lang->line('duplicate_member_id')));
								}
								break;
							case 'password':
								// encode password if it is type="text"
								$this->members[$i][$tag->tag] = ($tag->attributes['type'] == 'text') ? do_hash($tag->value) : $tag->value;
								break;
						}
					}
			
					$username 		= (isset($this->members[$i]['username'])) ? $this->members[$i]['username'] : '';
					$screen_name 	= (isset($this->members[$i]['screen_name'])) ? $this->members[$i]['screen_name'] : '';
					$email 			= (isset($this->members[$i]['email'])) ? $this->members[$i]['email'] : '';
			
					/* -------------------------------------
					/*  Validate separately to display
					/*  exact problem
					/* -------------------------------------*/
		
					$this->validate->validate_username();

					if ( ! empty($this->validate->errors))
					{
						foreach($this->validate->errors as $key => $val)
						{
							$this->validate->errors[$key] = $val." (Username: '".$username."' - ".$this->lang->line('within_user_record')." '".$username."')";
						}
						$this->errors[] = $this->validate->errors;
						unset($this->validate->errors);
					}
			
					$this->validate->validate_screen_name();
			
					if ( ! empty($this->validate->errors))
					{
						foreach($this->validate->errors as $key => $val)
						{
							$this->validate->errors[$key] = $val." (Screen Name: '".$screen_name."' - ".$this->lang->line('within_user_record')." '".$username."')";
						}
						$this->errors[] = $this->validate->errors;
						unset($this->validate->errors);
					}
			
					$this->validate->validate_email();
			
					if ( ! empty($this->validate->errors))
					{
						foreach($this->validate->errors as $key => $val)
						{
							$this->validate->errors[$key] = $val." (Email: '".$email."' - ".$this->lang->line('within_user_record')." '".$username."')";
						}
						$this->errors[] = $this->validate->errors;
						unset($this->validate->errors);
					}
					
					/** -------------------------------------
					/**  Add a random hash if no password is defined
					/** -------------------------------------*/
					
					if ( ! isset($this->members[$i]['password']))
					{
						$this->members[$i]['password'] = do_hash(mt_rand());
					}
					$i++;
				}
				else
				{
					/** -------------------------------------
					/**  Element isn't <member>
					/** -------------------------------------*/
					
					$this->errors[] = array($this->lang->line('invalid_element'));
				}
			}
		}
		else
		{
			/** -------------------------------------
			/**  No children of the root element
			/** -------------------------------------*/
			
			$this->errors[] = array($this->lang->line('invalid_xml'));
		}
	}
	/* END */
	
	/** -------------------------------------
	/**  Perform XML Member Import
	/** -------------------------------------*/
	
	function do_import()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}		
		
		//  Set our optional default values
		$this->default_fields['group_id']			= $this->input->post('group_id');
		$this->default_fields['language']			= ($this->input->post('language') == $this->lang->line('none') OR $this->input->post('language') == '') ? 'english' : strtolower($this->input->post('language'));
		$this->default_fields['timezone']			= ($this->input->post('timezone') && $this->input->post('timezone') != '') ? $this->input->post('timezone') : $this->config->item('server_timezone');
		$this->default_fields['time_format']		= $this->input->post('time_format');
		$this->default_fields['daylight_savings']	= ($this->input->post('daylight_savings') == 'y') ? 'y' : 'n';
		$this->default_fields['ip_address']			= '0.0.0.0';
		$this->default_fields['join_date']			= $this->localize->now;
		
		//  Rev it up, no turning back!
		$new_ids = array();
		$counter = 0;
		
		$custom_fields = (count($this->default_custom_fields) > 0) ? TRUE : FALSE;
		
		
		foreach ($this->members as $count => $member)
		{
			$data = array();
			$dupe = FALSE;

			foreach ($this->default_fields as $key => $val)
			{
				if (isset($member[$key]))
				{
					$data[$key] = $member[$key];						
				}
				elseif ($val != '')
				{
					$data[$key] = $val;
				}
			}
			
			if ($custom_fields == TRUE)
			{
				foreach ($this->default_custom_fields as $key => $val)
				{
					if (isset($member_custom[$key]))
					{
						$cdata[$key] = $member_custom[$key];						
					}
					elseif ($val != '')
					{
						$cdata[$key] = $val;
					}
				}	
			}			
				
				

			//  Add a unique_id for each member
			$data['unique_id'] = random_string();  //$this->functions->random('encrypt');
			
			/* -------------------------------------
			/*  See if we've already imported a member with this member_id -
			/*  could possibly occur if an auto_increment value is used
			/*  before a specified member_id.
			/* -------------------------------------*/
			
			if (isset($data['member_id']))
			{
				if (isset($new_ids[$data['member_id']]))
				{
					/* -------------------------------------
					/*  Grab the member so we can re-insert it after we
					/*  take care of this nonsense
					/* -------------------------------------*/
					$dupe = TRUE;
					
					$tempdata = $this->db->get_where('members', array('member_id' => $data['member_id']));
					
					if ($custom_fields == TRUE)
					{
						$tempcdata = $this->db->get_where('member_data', array('member_id' => $data['member_id']));
					}
				}
			}
			/* -------------------------------------
			/*  Shove it in!
			/*  We are using REPLACE as we want to overwrite existing members if a member id is specified
			/* -------------------------------------*/
			
			$this->db->replace('members', $data); 

			//  Add the member id to the array of imported member id's
			$new_ids[$this->db->insert_id()] = '';
			
			if ($custom_fields == TRUE)
			{
				$cdata['member_id'] = $this->db->insert_id();
			}

			//  Insert the old auto_incremented member, if necessary
			if ($dupe === TRUE)
			{
				unset($tempdata->row['member_id']); // dump the member_id so it can auto_increment a new one
				$this->db->insert('members', $tempdata->row);
				$new_ids[$this->db->insert_id()] = '';
			
				if ($custom_fields == TRUE)
				{
					$tempcdata->row['member_id'] = $this->db->insert_id();
					$this->db->insert('member_data', $tempcdata->row);
				}				
				
			}
			
			if ($custom_fields == TRUE)
			{
				$this->db->replace('member_data', $cdata); 
			}			
	
			$counter++;			
		}
		
		/** -------------------------------------
		/**  Add records to exp_member_data and exp_member_homepage tables for all imported members
		/** -------------------------------------*/
		
		$values = '';
		
		foreach ($new_ids as $key => $val)
		{
			$values .= "('$key'),";
		}
		
		$values = substr($values, 0, -1);
		
		if ($custom_fields == FALSE)
		{
			$this->db->query("INSERT INTO exp_member_data (member_id) VALUES ".$values);
		}
		
		$this->db->query("INSERT INTO exp_member_homepage (member_id) VALUES ".$values);
		
		//  Update Statistics
		$this->stats->update_member_stats();
		
		return $counter;
	}
	/* END */
	
	function _create_custom_validation()
	{
		$this->load->library('form_validation');
		
		$this->invalid_names = $this->cp->invalid_custom_field_names();
		
		// Gather existing field names
		$this->db->select('m_field_name');
		$m_custom_fields = $this->db->get('member_fields');
		
		if ($m_custom_fields->num_rows() > 0)
		{
   			foreach ($m_custom_fields->result() as $row)
   			{
				$this->taken[] = $row->m_field_name;
			}
		}

		foreach($_POST['create_ids'] as $key => $val)
		{
			$this->form_validation->set_rules("m_field_name[".$key."]", '', 'required|callback__valid_name');	
			$this->form_validation->set_rules("m_field_label[".$key."]", '', 'required');
			$this->form_validation->set_rules("required[".$key."]", '', '');
			$this->form_validation->set_rules("public[".$key."]", '', '');
			$this->form_validation->set_rules("reg_form[".$key."]", '', '');			
		}
		
		$this->form_validation->set_message('required', $this->lang->line('s_required'));
		$this->form_validation->set_error_delimiters('<span class="notice">', '</span>');
		
	}
	
	function _valid_name($str)
	{
		$error = array();
		
		// Does field name have invalid characters?
		if (preg_match('/[^a-z0-9\_\-]/i', $str))
		{
				$error[] = $this->lang->line('invalid_characters');
		}				

		// Is the field one of the reserved words?
		if (in_array($str, $this->invalid_names))
		{
				$error[] = $this->lang->line('reserved_word');
		}
				
		// Is the field name taken?
		if (in_array($str, $this->taken))
		{
			$error[] = $this->lang->line('duplicate_field_name');
		}
				
		$this->taken[] = $str;
		
		if (count($error) > 0)
		{

			$out = implode(',', $error);	
			$this->form_validation->set_message('_valid_name', $out);
			return FALSE;				

		}
		
		return TRUE;
	}


	function create_custom_fields()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}		
		
		$this->load->helper('form');
		$this->lang->loadfile('admin_content');
		$this->lang->loadfile('members');	
		$this->lang->loadfile('member_import');
				
		$this->_create_custom_validation();
		
		if ($this->form_validation->run() === FALSE)
		{
			return $this->confirm_xml_form();
		}		

		$error = array();
		$taken = array();

		$total_fields = count($this->input->post('create_ids'));

		foreach ($_POST['create_ids'] as $k => $v)
		{
			$data['m_field_name'] 		= $_POST['m_field_name'][$k];
			$data['m_field_label'] 		= $_POST['m_field_label'][$k];
			$data['m_field_description']= (isset($_POST['m_field_description'][$k])) ? $_POST['m_field_description'][$k]	: '';
			$data['m_field_type'] 		= (isset($_POST['m_field_type'][$k])) ? $_POST['m_field_type'][$k] : 'text';
			$data['m_field_list_items'] = (isset($_POST['m_field_list_items'][$k])) ? $_POST['m_field_list_items'][$k] : '';			
			$data['m_field_ta_rows'] 	= (isset($_POST['m_field_ta_rows'][$k])) ? $_POST['m_field_ta_rows'][$k] : '100';
			$data['m_field_maxl'] 		= (isset($_POST['m_field_maxl'][$k])) ? $_POST['m_field_maxl'][$k] : '100';
			$data['m_field_width'] 		= (isset($_POST['m_field_width'][$k])) ? $_POST['m_field_width'][$k] : '100%';
			$data['m_field_search'] 	= 'y';
			$data['m_field_required'] 	= (isset($_POST['required'][$k])) ? 'y' : 'n';
			$data['m_field_public'] 	= (isset($_POST['public'][$k])) ? 'y' : 'n';
			$data['m_field_reg'] 		= (isset($_POST['reg_form'][$k])) ? 'y' : 'n';
			$data['m_field_fmt'] 		= (isset($_POST['m_field_fmt'][$k])) ? $_POST['m_field_fmt'][$k] : 'xhtml';
			$data['m_field_order'] 		= (isset($_POST['m_field_order'][$k])) ? $_POST['m_field_order'][$k] : '';
							
			$this->db->insert('member_fields', $data); 
			$this->db->query('ALTER table exp_member_data add column m_field_id_'.$this->db->insert_id().' text NULL DEFAULT NULL');	

			$_POST['added_fields'][$_POST['m_field_name'][$k]] = $_POST['m_field_label'][$k];

		}

		
//need to do something about missing order variables

	
		$sql = "SELECT exp_members.member_id
				FROM exp_members
				LEFT JOIN exp_member_data ON exp_members.member_id = exp_member_data.member_id
				WHERE exp_member_data.member_id IS NULL
				ORDER BY exp_members.member_id";
			
		$query = $this->db->query($sql);
			
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$this->db->query("INSERT INTO exp_member_data (member_id) values ('{$row['member_id']}')");
			}
		}
		

		$_POST['auto_custom_field'] = 'n';
		unset($_POST['new']);
		unset($_POST['m_field_name']);
		unset($_POST['m_field_label']);
		unset($_POST['create_ids']);
		
		return $this->final_confirm_xml_form();
	}	

	// --------------------------------------------------------------------

	/**
	 * Convert Member Data from Delimited File
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function convert_from_delimited()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');

		$this->_convert_from_delimited_form();
	}
	
	// --------------------------------------------------------------------
	
	function _convert_from_delimited_validation()
	{
		$this->load->library('form_validation');
					
		$this->form_validation->set_rules('member_file',		'File',				'required|callback__file_exists');	
		$this->form_validation->set_rules('delimiter',			'lang:delimiter',	'required|enum[tab,other,comma]');			
		$this->form_validation->set_rules('delimiter_special',	'lang:other',		'trim|callback__not_alphanu');			
		$this->form_validation->set_rules('enclosure',			'lang:enclosure',	'callback__prep_enclosure');			

		$this->form_validation->set_error_delimiters('<span class="notice">', '</span>');
	}
	
	// --------------------------------------------------------------------
	
	function _convert_from_delimited_form()
	{
		$this->load->helper('form');
		
		$this->cp->set_variable('cp_page_title', $this->lang->line('convert_from_delimited'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));

		$this->javascript->output('
		$("#delimiter_special").focus(function() {
						$("#other").attr("checked", true);
					});
		$("#comma,#tab").focus(function() {
				$("#delimiter_special").val("");
			});			
					
		');

		$this->javascript->compile();
		$this->load->view('tools/convert_from_delimited');
	}

	
	// --------------------------------------------------------------------
	
	function _pair_fields_form()
	{
		$this->load->library('table');
		
		//  Snag form POST data
		switch ($this->input->post('delimiter'))
		{
			case 'tab'	:	$this->delimiter = "\t";
				break;
			case 'other':	$this->delimiter = $this->input->post('delimiter_special');
				break;
			case 'comma':
			default:		$this->delimiter = ",";
		}

		$member_file = $this->input->post('member_file');

		$this->enclosure = ($this->input->post('enclosure') === FALSE) ? '' : $this->input->post('enclosure');


		//  Read data file into an array
		$fields = $this->datafile_to_array($member_file);
		
		if ( ! isset($fields[0]) OR count($fields[0]) < 3)
		{
			// No point going further if there aren't even the minimum required
			//show_error($this->lang->line('not_enough_fields'));
			return $this->view_xml_errors($this->lang->line('not_enough_fields'));
		}
		
		//  Retreive Valid fields from database
		$query = $this->db->query("SHOW COLUMNS FROM exp_members");


		foreach ($query->result_array() as $row)
		{
			$this->default_fields[$row['Field']] = '';
		}

		//  Retreive custom member fields from database
		$this->db->select('m_field_name');
		$this->db->from('member_fields');
		$this->db->order_by('m_field_name');

		$query = $this->db->get();
		

		$vars['custom_select_options'][''] = $this->lang->line('select');
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$this->custom_fields[$row['m_field_name']] = '';
				$vars['custom_select_options'][$row['m_field_name']] = $row['m_field_name'];
			}
		}
		
		// we do not allow <unique_id> in our XML format
		unset($this->default_fields['unique_id']);
		
		ksort($this->default_fields);

		$vars['select_options'][''] = $this->lang->line('select');
		
		foreach ($this->default_fields as $key => $val)
		{
			$vars['select_options'][$key] = $key;
		}
		
		$vars['fields'] = $fields;
		
		$vars['form_hidden'] = array(
				'member_file'		=> $this->input->post('member_file'),
				'delimiter'			=> $this->input->post('delimiter'),
				'enclosure'			=> $this->enclosure,
				'delimiter_special'	=> $this->delimiter
				);
				
		$vars['encrypt'] = '';
		
		$this->cp->set_variable('cp_page_title', $this->lang->line('assign_fields'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));

		
		$this->load->view('tools/convert_xml_pairs', $vars);

	}


	// --------------------------------------------------------------------

	/**
	 * Pair Fields
	 *
	 * Pair delimited data with Member fields
	 *
	 * @access	private
	 * @return	boolean
	 */
	function pair_fields()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');
		$this->load->helper('form');
		
		$this->_convert_from_delimited_validation();
		
		if ($this->form_validation->run() === FALSE)
		{
			return $this->_convert_from_delimited_form();
		}
		
		return $this->_pair_fields_form();
	}
		
	// --------------------------------------------------------------------

	/**
	 * Not Alpha or Numeric
	 *
	 * Validation callback that makes sure that no alphanumeric chars are submitted
	 *
	 * @access	private
	 * @param	string
	 * @return	boolean
	 */
	function _not_alphanu($str = '')
	{
		if ($this->input->post('delimiter') == 'other')
		{
			if ($str == '')
			{
				$this->form_validation->set_message('_not_alphanu', str_replace('%x', $this->lang->line('other'), $this->lang->line('no_delimiter')));
				return FALSE;
			}
			
			preg_match("/[\w\d]*/", $str, $matches);

			if ($matches[0] != '')
			{		
				$this->form_validation->set_message('_not_alphanu', $this->lang->line('alphanumeric_not_allowed'));
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * File exists
	 *
	 * Validation callback that checks if a file exits
	 *
	 * @access	private
	 * @param	string
	 * @return	boolean
	 */
	function _file_exists($file)
	{
		if ( ! file_exists($file))
		{
			$this->form_validation->set_message('_file_exists', $this->lang->line('invalid_path').$file);
			return FALSE;
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	function _prep_enclosure($enclosure)
	{
		// undo changes made by form prep as we need the literal characters
		// and htmlspecialchars_decode() doesn't exist until PHP 5, so...
		$enclosure = str_replace('&#39;', "'", $enclosure);
		$enclosure = str_replace('&amp;', "&", $enclosure);
		$enclosure = str_replace('&lt;', "<", $enclosure);
		$enclosure = str_replace('&gt;', ">", $enclosure);
		$enclosure = str_replace('&quot;', '"', $enclosure);
		$enclosure = stripslashes($enclosure);
		
		return $enclosure;
	}

	
	// --------------------------------------------------------------------

	//  Confirm Data to XML Form
	
	function _pair_fields_validation()
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('unique_check', 'lang:other',	'callback__unique_required');			
		$this->form_validation->set_rules('encrypt', '', '');
		
		$this->form_validation->set_error_delimiters('<p class="notice">', '</p>');

	}
	
	// --------------------------------------------------------------------

	//  Check for uniqueness and required values
	
	function _unique_required ($selected_fields)
	{
		//  Get field pairings
		$paired = array();
		$mssg = array();
	
		if (is_array($selected_fields))
		{
			foreach ($selected_fields as $val)
			{

				if ($val != '' && in_array($val, $paired))
				{
					$mssg[] = str_replace("%x", $val, $this->lang->line('duplicate_field_assignment'));
				}

				$paired[] = $val;	 			
			}
		}
		
		if (! in_array('username', $paired))
		{
			$mssg[] = $this->lang->line('missing_username_field');
		}
		
		if (! in_array('screen_name', $paired))
		{
			$mssg[] = $this->lang->line('missing_screen_name_field');
		}
		
		if (! in_array('email', $paired))
		{
			$mssg[] = $this->lang->line('missing_email_field');
		}

		if (count($mssg) > 0)
		{	
			$out = implode('<br>', $mssg);	
			$this->form_validation->set_message('_unique_required', $out);
			return FALSE;
		}
		
		return TRUE;		
	}
	

	function confirm_data_form()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');
		$this->load->helper('form');
		$this->load->library('table');

		//  Snag POST data
		$member_file = ( ! $this->input->post('member_file'))  ? '' : $this->input->post('member_file');
		
		switch ($this->input->post('delimiter'))
		{
			case 'tab'	:	$this->delimiter = "\t"; break;
			case 'comma'	:	$this->delimiter = ","; break;
			case 'other':	$this->delimiter = $this->input->post('delimiter_special');
		}

		$this->enclosure 	= ($this->input->post('enclosure') === FALSE) ? '' : $this->input->post('enclosure');
		$encrypt			= ($this->input->post('encrypt') == 'y') ? TRUE : FALSE;

		
		//  Get field pairings
		$paired = array();
		$cpaired = array();
		
		foreach ($_POST as $key => $val)
		{
			if (substr($key, 0, 5) == 'field')
			{
				$_POST['unique_check'][$key] = $val;
				$paired[$key] = $val;	 			
			}
			elseif (substr($key, 0, 7) == 'c_field')
			{
				$_POST['unique_check'][$key] = $val;
				$cpaired[$key] = $val;				
			}
		}
		
		$this->_pair_fields_validation();
		
		if ($this->form_validation->run() === FALSE)
		{
			return $this->_pair_fields_form();
		}


		//  Read the data file
		
		$fields = $this->datafile_to_array($member_file);
		
		$vars['form_hidden'] = 	array('member_file'		=> $this->input->post('member_file'),
							'delimiter'			=> $this->input->post('delimiter'),
							'delimiter_special'	=> $this->delimiter,
							'enclosure'			=> $this->enclosure,
							'encrypt'			=> $this->input->post('encrypt')
							);

		foreach ($paired as $key => $val)
		{
			$vars['form_hidden'][$key] = $val;
			
			if (isset($cpaired['c_'.$key]) && $cpaired['c_'.$key] != '')
			{
				$vars['form_hidden'][$key] = $cpaired['c_'.$key];
			}
						
		}

		$vars['fields'] = $fields;
		$vars['paired'] = $paired;		
		$vars['cpaired'] = $cpaired;
		$vars['custom_fields'] = (count($cpaired) > 0) ? TRUE : FALSE;	
		$vars['type_view'] = FALSE;
		$vars['type_download'] = TRUE;		

		$this->cp->set_variable('cp_page_title', $this->lang->line('confirm_field_assignment'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));
		
		$this->load->view('tools/confirm_convert_xml', $vars);
	}
	/* END */
	
	
	/** -------------------------------------
	/**  Create XML File
	/** -------------------------------------*/
	
	function create_xml()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->lang->loadfile('member_import');
		$this->load->helper(array('file', 'xml'));
		
		//  Snag POST data
		$member_file = ( ! $this->input->post('member_file'))  ? '' : $this->input->post('member_file');
		
		switch ($this->input->post('delimiter'))
		{
			case 'tab'	:	$this->delimiter = "\t"; break;
			case 'comma'	:	$this->delimiter = ","; break;
			case 'other':	$this->delimiter = $this->input->post('delimiter_special');
		}
		
		$this->enclosure 	= ($this->input->post('enclosure') === FALSE) ? '' : $this->input->post('enclosure');
		$encrypt			= ($this->input->post('encrypt') == 'y') ? TRUE : FALSE;
		$type				= $this->input->post('type');
		
		//  Read file contents
		$contents = read_file($member_file);
		
		if ($contents === FALSE)
		{
			return;
		}		
		
		//  Get structure
		$structure = array();
		
		foreach ($_POST as $key => $val)
		{
			if (substr($key, 0, 5) == 'field')
			{
				$structure[] = $val;				
			}
		}
		
		$this->load->library('xmlparser');

		// parse XML data
		$xml = $this->xmlparser->parse_xml($contents);
		

		$params = array(
							'data'			=> $contents,
							'structure'		=> $structure,
							'root'			=> 'members',
							'element'		=> 'member',
							'delimiter'		=> $this->delimiter,
							'enclosure'		=> $this->enclosure
						);
		
		$xml = $this->xmlparser->delimited_to_xml($params, 1);
		
		//  Add type="text" parameter for plaintext passwords
		if ($encrypt === TRUE)
		{
			$xml = str_replace('<password>', '<password type="text">', $xml);
		}
		
		if ( ! empty($this->xmlparser->errors))
		{
			return $this->view_xml_errors($this->xmlparser->errors);
		}

		//  Output to browser or download
		switch ($type)
		{
			case 'view'		: return $this->view_xml($xml); break;
			case 'download' : $this->download_xml($xml); break;
		}
	}
	/* END */
	
	
	/** -------------------------------------
	/**  View XML in browser
	/** -------------------------------------*/
	
	function view_xml($xml)
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->load->helper('string');
		
		$this->cp->set_variable('cp_page_title', $this->lang->line('view_xml'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));

		
		$xml = str_replace("\n", BR, htmlentities($xml));
		$xml = str_replace("\t", repeater(NBS, 4), $xml);
		$vars['output'] = $xml;
		$vars['heading'] = $this->lang->line('view_xml');

		$this->load->view('tools/view_xml', $vars);
	}
	/* END */
	
		
	/** -------------------------------------
	/**  View XML errors
	/** -------------------------------------*/
	
	function view_xml_errors($errors, $message = '')
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->load->helper('string');
			
		$this->cp->set_variable('cp_page_title', $this->lang->line('parse_error'));
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=member_import', $this->lang->line('member_import_utility'));

		$out = '<ul>';
		
		if (is_array($errors))
		{
			foreach ($errors as $error)
			{
				$out .= '<li>'.$error.'</li>';
			}
			
		}
		else
		{
			$out .= '<li>'.$errors.'</li>';
		}
		 
		$out .= '</ul>';
	
		$vars['output'] = $out;
		$vars['heading'] = $this->lang->line('parse_error');

		$vars['message'] = ($message == '') ? NULL : $message;

		$this->load->view('tools/view_xml', $vars);

	}
	/* END */
	
	
	/** -------------------------------------
	/**  Download XML file
	/** -------------------------------------*/
	
	function download_xml($xml)
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}
		
		$this->load->helper('download');
		$now = $this->localize->set_localized_time();
        
        $filename = 'member_'.date('y', $now).date('m', $now).date('d', $now).'.xml';
		
		force_download($filename, $xml);
	}
	/* END */

	// --------------------------------------------------------------------

	/**
	 * Translation Tool
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function translation_tool($message = '')
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}
		
		if ( ! is_really_writable(APPPATH.'translations/'))
		{
			$vars['not_writeable'] = lang('translation_dir_unwritable');
		}
		
		$vars['message'] = $message;

		$this->load->model('tools_model');

		$this->cp->set_variable('cp_page_title', $this->lang->line('translation_tool'));

		$this->javascript->compile();

		$vars['language_files'] = $this->tools_model->get_language_filelist();
		
		$this->load->view('tools/translation_tool', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Translate
	 *
	 * @todo: copy function desc
	 * 
	 * @access	public
	 * @return	mixed
	 */	
	function translate()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->load->helper('form');
		$this->load->library('table');
		$this->load->model('tools_model');
		$language_file = $this->input->get_post('language_file');

		// @confirm- 1.6 shows filename in breadcrumb
		//$this->cp->set_variable('cp_page_title', $this->lang->line('translation'));
		
		$this->cp->set_variable('cp_page_title', $language_file);
		$this->cp->set_breadcrumb(BASE.AMP.'C=tools_utilities'.AMP.'M=translation_tool', $this->lang->line('translation_tool'));

		$this->cp->add_js_script(array('plugin' => 'tablesorter'));

		$this->jquery->tablesorter('.mainTable', '{
			headers: {1: {sorter: false}},
			widgets: ["zebra"]
		}');

		$this->javascript->compile();

		// awkward name to prevent collision with potential language vars
		$vars['form_hidden']['trans_ee_language_file'] = $language_file;

		$lang_list = $this->tools_model->get_language_list($language_file);
		
		if (count($lang_list) == 0)
		{
			$vars['language_list'] = FALSE;
		}
		else
		{
			$vars['language_list'] = $lang_list;
		}

		$this->load->view('tools/translate', $vars);
	}

	// --------------------------------------------------------------------

	function translation_save()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$this->load->helper('security');

		$filename = $this->security->sanitize_filename($this->input->post('trans_ee_language_file'));
		$dest_loc = APPPATH.'translations/'.$filename;

		unset($_POST['trans_ee_language_file']);

		$str = '<?php'."\n".'$lang = array('."\n\n\n";

		foreach ($_POST as $key => $val)
		{
			$val = str_replace("<",  "&lt;",	$val);
			$val = str_replace(">",  "&gt;",	$val);
			$val = str_replace("'",  "&#39;",  $val);
			$val = str_replace("\"", "&quot;", $val);
			$val = stripslashes($val);

			$str .= "\"$key\" =>\n\"$val\",\n\n";
		}

		$str .= "''=>''\n);\n?".">";

		// Make sure any existing file is writeable
		if (file_exists($dest_loc))
		{
			@chmod($dest_loc, FILE_WRITE_MODE);
		}

		$this->load->helper('file');

		if (write_file($dest_loc, $str))
		{
			$this->session->set_flashdata('message_success', $this->lang->line('file_saved').$filename);
			$this->functions->redirect(BASE.AMP.'C=tools_utilities'.AMP.'M=translate'.AMP.'language_file='.$filename);
		}
		else
		{
			$this->translation_tool($this->lang->line('invalid_path'));
		}
	}

	/** -------------------------------------
	/**  Read delimited data file into array
	/** -------------------------------------*/
	
	function datafile_to_array($file)
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		$contents = file($file);
		$fields = array();


		//  Parse file into array
		if ($this->enclosure == '')
		{
			foreach ($contents as $line)
			{
				$fields[] = explode($this->delimiter, $line);
			}			
		}
		else
		{
			foreach ($contents as $line)
			{
				preg_match_all("/".preg_quote($this->enclosure)."(.*?)".preg_quote($this->enclosure)."/si", $line, $matches);
				$fields[] = $matches[1];
			}
		}

		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * PHP Info
	 *
	 * @todo: copy function desc
	 *
	 * @access	public
	 * @return	mixed
	 */
	function php_info()
	{
		if (! $this->cp->allowed_group('can_access_tools') OR ! $this->cp->allowed_group('can_access_utilities'))
		{
			show_error($this->lang->line('unauthorized_access'));
		}

		// the second conditional is for hosted demos to prevent users from viewing the PHP environment details
		if ($this->config->item('demo_date') != FALSE)
		{
			show_error($this->lang->line('unauthorized_access'));
		}
		
		$this->cp->set_variable('cp_page_title', $this->lang->line('php_info'));
		$this->javascript->compile();
		// a bit of a breadcrumb override is needed
		$this->cp->set_variable('cp_breadcrumbs', array(
			BASE.AMP.'C=tools' => $this->lang->line('tools'),
			BASE.AMP.'C=tools_utilities'=> $this->lang->line('tools_utilities')
		));

		ob_start();

		phpinfo();

		$buffer = ob_get_contents();

		ob_end_clean();

		$output = (preg_match("/<body.*?".">(.*)<\/body>/is", $buffer, $match)) ? $match['1'] : $buffer;
		$output = preg_replace("/width\=\".*?\"/", "width=\"100%\"", $output);
		$output = preg_replace("/<hr.*?>/", "<br />", $output); // <?
		$output = preg_replace("/<a href=\"http:\/\/www.php.net\/\">.*?<\/a>/", "", $output);
		$output = preg_replace("/<a href=\"http:\/\/www.zend.com\/\">.*?<\/a>/", "", $output);
		$output = preg_replace("/<a.*?<\/a>/", "", $output);// <?
		$output = preg_replace("/<th(.*?)>/", "<th \\1 >", $output);
		$output = preg_replace("/<tr(.*?).*?".">/", "<tr \\1>\n", $output);
		$output = preg_replace("/<td.*?".">/", "<td valign=\"top\">", $output);
		$output = preg_replace("/<h2 align=\"center\">PHP License<\/h2>.*?<\/table>/si", "", $output);
		$output = preg_replace("/ align=\"center\"/", "", $output);
		$output = preg_replace("/<table(.*?)bgcolor=\".*?\">/", "\n\n<table\\1>", $output);
		$output = preg_replace("/<table(.*?)>/", "\n\n<table\\1 class=\"mainTable\" cellspacing=\"0\">", $output);
		$output = preg_replace("/<h2>PHP License.*?<\/table>/is", "", $output);
		$output = preg_replace("/<br \/>\n*<br \/>/is", "", $output);
		$output = preg_replace('/<h(1|2)\s*(class="p")?/i', '<h\\1', $output);
		$output = str_replace("<h1></h1>", "", $output);
		$output = str_replace("<h2></h2>", "", $output);

		$vars['php_info'] = $output;

		$this->load->view('tools/php_info', $vars);
	}

	/* END */
}

/* End of file tools_utilities.php */
/* Location: ./system/expressionengine/controllers/cp/tools_utilities.php */