/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

"use strict";

(function ($) {
	$(document).ready(function () {

		$('li.remove a').click(function (e) {
			$(this).closest('figure').find('input[type="hidden"]').val('');
			$(this).closest('fieldset').hide();
			e.preventDefault();
		});

		$('.avatarPicker').FilePicker({
			ajax: false,
			filters: false,
			callback: function(data, picker) {
				if (data instanceof jQuery)
				{
					data = data.find('img');
					picker.modal.find('.m-close').click();
					picker.input_value.val(data.attr('alt'));
					picker.input_img.html("<img src='" + data.attr('src') + "' />");
					picker.input_img.parents('fieldset').show();
				}
				else
				{
					picker.modal.find('.m-close').click();
					picker.input_value.val(data.file_id);
					picker.input_name.html(data.file_name);
					picker.input_img.html("<img src='" + data.path + "' />");
					picker.input_img.parents('fieldset').show();
				}
			}
		});

	});
})(jQuery);