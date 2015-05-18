<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Admin Model
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Addons_model extends CI_Model {


	/**
	 * Get Plugin Formatting
	 *
	 * Used in various locations to list formatting options
	 *
	 * @access	public
	 * @param	bool	whether or not to include a "None" option
	 * @return	array
	 */
	function get_plugin_formatting($include_none = FALSE)
	{
		static $filelist = array();
		static $plugins  = array();

		if (empty($plugins))
		{
			$plugins = ee('Model')->get('Plugin')
				->filter('is_typography_related', 'y')
				->all();
		}

		$default = array('br' => lang('auto_br'), 'xhtml' => lang('xhtml'));

		if ($include_none === TRUE)
		{
			$default['none'] = lang('none');
		}

		foreach ($plugins as $plugin)
		{
			$filelist[$plugin->plugin_package] = $plugin->plugin_name;
		}

		$return = $default + $filelist;

		ksort($return);
		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Plugins
	 *
	 * @access	public
	 * @param	str	$plugin_name	(optional) Limit the return to this add-on
	 * @return	array
	 */
	function get_plugins($plugin_name = NULL)
	{
		$this->load->helper('directory');

		$info = array();

		$ext_len = strlen('.php');
		$plugins = array();

		// first party plugins
		if (($map = directory_map(PATH_PI, TRUE)) !== FALSE)
		{
			foreach ($map as $file)
			{
				if (strncasecmp($file, 'pi.', 3) == 0 && substr($file, -$ext_len) == '.php' && strlen($file) > strlen('pi..php'))
				{
					$name = substr($file, 3, -$ext_len);

					if ($plugin_name && $name != $plugin_name)
					{
						continue;
					}

					$plugins[] = array(
						'name' => $name,
						'path' => PATH_PI.$file
					);
				}
			}
		}

		// now third party add-ons, which are arranged in "packages"
		// only catch files that match the package name, as other files are merely assets
		if (($map = directory_map(PATH_ADDONS, 2)) !== FALSE)
		{
			foreach ($map as $pkg_name => $files)
			{
				if ( ! is_array($files))
				{
					$files = array($files);
				}

				foreach ($files as $file)
				{
					if (is_array($file))
					{
						// we're only interested in the top level files for the addon
						continue;
					}

					elseif (strncasecmp($file, 'pi.', 3) == 0 &&
							substr($file, -$ext_len) == '.php' &&
							strlen($file) > strlen('pi..php'))
					{
						if ( ! class_exists(ucfirst($pkg_name)))
						{
							if ($plugin_name && $pkg_name != $plugin_name)
							{
								continue;
							}

							$plugins[] = array(
								'name' => $pkg_name,
								'path' => PATH_ADDONS.$pkg_name.'/'.$file
							);
						}
					}
				}
			}
		}

		foreach ($plugins as $plugin)
		{
			$class_name = ucfirst($plugin['name']);

			if ( ! class_exists($class_name))
			{
				include($plugin['path']);

				if ( ! class_exists($class_name))
				{
					trigger_error(str_replace(array('%c', '%f'), array(htmlentities($class_name), htmlentities($plugin['path'])), lang('plugin_class_does_not_exist')));
					continue;
				}
			}

			$properties = array('name', 'version', 'author', 'author_url', 'description', 'typography');
			$error = FALSE;
			$missing_properties = array();

			foreach ($properties as $property)
			{
				if ( ! property_exists($class_name, $property))
				{
					$missing_properties[] = $property;
				}
			}

			if ( ! empty($missing_properties))
			{
				ee()->logger->developer('Error: the plugin "' . $plugin["name"] . '" is missing the following static properties: ' . implode(', ', $missing_properties) . '.');
				$error = TRUE;
			}

			if ( ! method_exists($class_name, 'usage'))
			{
				ee()->logger->developer('Error: the plugin "' . $plugin["name"] . '" is missing the usage() static method.');
				$error = TRUE;
			}

			if ($error)
			{
				continue;
			}

			$plugin_info = array(
				'installed_path' => $plugin['path'],
				'pi_name'        => $class_name::$name,
				'pi_version'     => $class_name::$version,
				'pi_author'      => $class_name::$author,
				'pi_author_url'  => $class_name::$author_url,
				'pi_description' => $class_name::$description,
				'pi_usage'       => $class_name::usage(),
				'pi_typography'  => $class_name::$typography
			);

			$info[$plugin['name']] = $plugin_info;
		}

		return $info;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Installed Modules
	 *
	 * @access	public
	 * @return	array
	 */
	function get_installed_modules($has_cp = FALSE, $has_tab = FALSE)
	{
		$this->db->select('LOWER(module_name) AS module_name, module_version, has_cp_backend, module_id', FALSE);

		if ($has_cp === TRUE)
		{
			$this->db->where('has_cp_backend', 'y');
		}

		if ($has_tab === TRUE)
		{
			$this->db->where('has_publish_fields', 'y');
		}

		return $this->db->get('modules');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Installed Extensions
	 *
	 * @access	public
	 * @return	array
	 */
	function get_installed_extensions($enabled = TRUE)
	{
		$this->db->select('class, version');

		if ($enabled)
		{
			$this->db->where('enabled', 'y');
		}
		else
		{
			$this->db->select('enabled');
		}

		return $this->db->get('extensions');
	}

	// --------------------------------------------------------------------

	/**
	 * Module installed
	 *
	 * Returns true if a module is installed, false if not
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	function module_installed($module_name)
	{
		static $_installed = array();

		if ( ! isset($_installed[$module_name]))
		{
			$this->db->from("modules");
			$this->db->where("module_name", ucfirst(strtolower($module_name)));
			$_installed[$module_name] = ($this->db->count_all_results() > 0) ? TRUE : FALSE;
		}

		return $_installed[$module_name];
	}

	// --------------------------------------------------------------------

	/**
	 * Extension installed
	 *
	 * Returns true if an extension is installed, false if not
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	function extension_installed($ext_name)
	{
		static $_installed = array();

		if ( ! isset($_installed[$ext_name]))
		{
			$this->db->from("extensions");
			$this->db->where("class", ucfirst(strtolower($ext_name.'_ext')));
			$_installed[$ext_name] = ($this->db->count_all_results() > 0) ? TRUE : FALSE;
		}

		return $_installed[$ext_name];
	}

	// --------------------------------------------------------------------

	/**
	 * Fieldtype installed
	 *
	 * Returns true if a fieldtype is installed, false if not
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	function fieldtype_installed($ft_name)
	{
		static $_installed = array();

		if ( ! isset($_installed[$ft_name]))
		{
			$this->db->from("fieldtypes");
			$this->db->where("name", strtolower($ft_name));
			$_installed[$ft_name] = ($this->db->count_all_results() > 0) ? TRUE : FALSE;
		}

		return $_installed[$ft_name];
	}

	// --------------------------------------------------------------------

	/**
	 * RTE Tool installed
	 *
	 * Returns true if a RTE tool is installed, false if not
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	function rte_tool_installed($tool_name)
	{
		// Is the module even installed?
		if ( ! $this->db->table_exists('rte_tools'))
		{
			return FALSE;
		}

		static $_installed = array();

		if ( ! isset($_installed[$tool_name]))
		{
			$this->db->from("rte_tools");
			$this->db->where("name", ucfirst(strtolower(str_replace(' ', '_', $tool_name))));
			$_installed[$tool_name] = ($this->db->count_all_results() > 0) ? TRUE : FALSE;
		}

		return $_installed[$tool_name];
	}

	// --------------------------------------------------------------------

	/**
	 * Update an Extension
	 *
	 * @access	public
	 * @return	void
	 */
	function update_extension($class, $data)
	{
		$this->db->set($data);
		$this->db->where('class', $class);
		$this->db->update('extensions');
	}
}

/* End of file addons_model.php */
/* Location: ./system/expressionengine/models/addons_model.php */
