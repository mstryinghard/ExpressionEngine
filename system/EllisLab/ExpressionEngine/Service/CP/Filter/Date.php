<?php
namespace EllisLab\ExpressionEngine\Service\CP\Filter;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP\URL;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Perpage Filter Class
 *
 * @package		ExpressionEngine
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Date extends Filter {

	private $timestamp;

	public function __construct()
	{
		$this->name = 'filter_by_date';
		$this->label = 'date';
		$this->placeholder = lang('custom_date');
		$this->options = array(
			'86400'     => ucwords(lang('last').' 24 '.lang('hours')),
			'604800'    => ucwords(lang('last').' 7 '.lang('days')),
			'2592000'   => ucwords(lang('last').' 30 '.lang('days')),
			'15552000'  => ucwords(lang('last').' 180 '.lang('days')),
			'31536000'  => ucwords(lang('last').' 365 '.lang('days')),
		);

		$date_format = ee()->session->userdata('date_format', ee()->config->item('date_format'));

		ee()->javascript->set_global('date.date_format', $date_format);
		ee()->javascript->set_global('lang.date.months.full', array(
			lang('january'),
			lang('february'),
			lang('march'),
			lang('april'),
			lang('may'),
			lang('june'),
			lang('july'),
			lang('august'),
			lang('september'),
			lang('october'),
			lang('november'),
			lang('december')
		));
		ee()->javascript->set_global('lang.date.months.abbreviated', array(
			lang('jan'),
			lang('feb'),
			lang('mar'),
			lang('apr'),
			lang('may'),
			lang('june'),
			lang('july'),
			lang('aug'),
			lang('sept'),
			lang('oct'),
			lang('nov'),
			lang('dec')
		));
		ee()->javascript->set_global('lang.date.days', array(
			lang('su'),
			lang('mo'),
			lang('tu'),
			lang('we'),
			lang('th'),
			lang('fr'),
			lang('sa'),
		));
		ee()->cp->add_js_script(array(
			'file' => array('cp/v3/date_picker'),
		));

		$value = $this->value();
		if ( ! array_key_exists($value, $this->options))
		{
			$date = ee()->localize->string_to_timestamp($value);
			$this->timestamp = $date;
			$this->display_value = ee()->localize->format_date($date_format, $date);
			$this->selected_value = $date;
		}
	}

	public function isValid()
	{
		if (array_key_exists($this->value(), $this->options))
		{
			return TRUE;
		}

		return FALSE;
	}

	public function render(URL $url)
	{
		$value = $this->display_value;
		if (is_null($value))
		{
			$value = (array_key_exists($this->value(), $this->options)) ?
				$this->options[$this->value()] :
				$this->value();
		}

		$filter = array(
			'label'			=> $this->label,
			'name'			=> $this->name,
			'value'			=> $value,
			'custom_value'	=> ee()->input->post($this->name),
			'placeholder'	=> $this->placeholder,
			'options'		=> $this->prepareOptions($url),
			'timestamp'		=> $this->timestamp
		);
		return ee()->load->view('_shared/filters/date', $filter, TRUE);
	}

}
// END CLASS

/* End of file Date.php */
/* Location: ./system/EllisLab/ExpressionEngine/Service/CP/Filter/Date.php */