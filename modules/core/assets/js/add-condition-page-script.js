//Main variables

//Div that contains or groups with their respective conditions
var groups_container_id = "#wp-cpg-or-groups";
var row_template = "";

jQuery(document).ready(function ($) {

	jQuery('.page-title-action').after('<a href="' + wp_cpg_gateway_conditions_obj.settings_page_url + '" class="page-title-action">' + wp_cpg_gateway_conditions_obj.text.go_to_settings + '</a>');

	//Initializing payment gateways select multiple
	$("#payment_gateways").select2();

	init_selects_2($(".condition"));
	change_input_place_holder();

	//Initializing events
	$(".btn-add-group").click(function () {
		$groups_container = $("#wp-cpg-or-groups");
		$groups_container.append($("#hidden-conditions-group-template").html());
		rename_groups($groups_container.children());
		init_selects_2($(".condition"));
	});

	$("body").on("click", ".btn-remove-group", function () {

		$groups_container = $("#wp-cpg-or-groups");
		$group = $(this).parents(".or_group_table");
		number_of_groups = $groups_container.children().length;

		if (number_of_groups == 1)
			return false;

		$group.remove();
		rename_groups($groups_container.children());

	});

	$("body").on("click", ".btn-add-condition", function () {

		$group_index = $(this).parents("table").index();

		$condition_row_template = $($("#hidden-condition-row-template").html());

		$condition_row_template.insertAfter($(this).parent().parent());
		$group_conditions_container = $(this).parents(".conditions");
		rename_conditions($group_conditions_container.children(), $group_index);

		var countAndConditions = $group_conditions_container.children().length;
		if (countAndConditions > 3 && !jQuery('.many-and-conditions-warning').length) {
			$group_conditions_container.append('<tr class="condition many-and-conditions-warning"><td colspan="6">' + wp_cpg_gateway_conditions_obj.text.too_many_and_conditions + '</td></tr>');
		}

		init_selects_2($(".condition"));

	});

	$("body").on("click", ".btn-remove-condition", function () {

		$group_index = $(this).parents("table").index();
		condition_index = $(this).parents("tr").index();
		$conditions_container = $(this).parents(".conditions");
		number_of_conditions = $conditions_container.children().length;

		if (number_of_conditions == 1) {
			return false;
		}

		$(this).parent().parent().remove();

		rename_conditions($conditions_container.children(), $group_index);

	});

	$("body").on("change", ".condition-input-modifier", function () {

		var condition_select = $(this);
		var condition_key = condition_select.val();
		var $condition_row = condition_select.parents("tr");
		$condition_row.find(".condition-operators-selection").empty().append(wp_cpg_gateway_conditions_obj.conditions[condition_key]["html_operators"]);

		var $input = $(wp_cpg_gateway_conditions_obj.conditions[condition_key]["html_input"]);

		$condition_row.find(".input-column").empty().append($input);

		rename_groups($(groups_container_id).children());
		init_selects_2($(".condition"));


	});

	$("body").on("change", ".condition-operators-selection", function () {

		change_input_place_holder();

	});

	function change_input_place_holder() {

		$(".input-column").each(function () {

			var $input_column = $(this);
			var $input = $input_column.find("input,select");
			var inputName = $input.attr('name');
			var $condition_row = $input_column.parents("tr");

			var selected_operator = $condition_row.find(".condition-operators-selection").val();

			if (selected_operator == "appears_in_this_list" || selected_operator == "not_appears_in_this_list") {
				$input.data('list-placeholder-added', 1);
				$input.attr("placeholder", "Value 1; Value 2; etc");

			} else if ($input.data('list-placeholder-added')) {
				$input.attr('placeholder', '');
			}
			if (selected_operator == "equal_to_field" || selected_operator == "not_equal_to_field") {
				var $fieldsSelect = $input_column.parent().find('.type-selection select').clone();
				$fieldsSelect.attr('name', inputName);
				$fieldsSelect.attr('class', 'cpg-fields-value');
				$fieldsSelect.val($input_column.data('saved-value'));

				if (!$input_column.data('original-input-html') || $input[0].outerHTML.indexOf('cpg-fields-value') < 0) {
					$input_column.data('original-input-html', $input[0].outerHTML);
				}
				$input_column.empty();
				$input_column.append($fieldsSelect);
			} else if ($input_column.data('original-input-html')) {
				$input_column.empty();
				$input_column.append($input_column.data('original-input-html'));

				if ($input_column.find('select.wpcpg-select-2').length) {
					init_selects_2($input_column.parent());
				}

			}

		});

	}

	function rename_conditions($conditions, group_index) {
		$conditions.each(function (i) {

			$current_conditon_row = $(this);
			$current_conditon_row.find(".condition-name").empty().append("condition " + (i + 1));

			current_condition_name_and_id_default_text = "groups[" + group_index + "][conditions][" + i + "]";

			type_selection_id_and_name = current_condition_name_and_id_default_text + "[type]";
			$current_conditon_row.find(".type-selection select").attr("name", type_selection_id_and_name);

			operator_selection_id_and_name = current_condition_name_and_id_default_text + "[operator]";
			$current_conditon_row.find(".operator-selection select").attr("name", operator_selection_id_and_name);

			user_data_id_and_name = current_condition_name_and_id_default_text + "[data]";
			$current_conditon_row.find(".input-column :first-child").attr("name", user_data_id_and_name);

		});
	}

	function rename_groups($groups) {

		$groups.each(function (i) {

			$current_group = $(this);
			$current_group.find(".group-text").empty().append("Group " + (i + 1));
			rename_conditions($current_group.find(".conditions").children(), $current_group.index());

		});
	}

	function get_condition_select_2_ajax_object($condition_row) {

		var condition_key = $condition_row.find(".condition-input-modifier").val();
		var $condition_select = $condition_row.find(".wpcpg-select-2");

		if (!wp_cpg_gateway_conditions_obj.conditions[condition_key].request_options_with_ajax) {

			return {dropdownAutoWidth: true}
		}

		var select2_obj = {

			ajax: {
				url: wp_cpg_gateway_conditions_obj.ajax.url,
				dataType: "json",
				method: "POST",
				delay: 1000,
				data: function (params) {
					return{
						q: params.term,
						condition_search_args: wp_cpg_gateway_conditions_obj.conditions[condition_key].condition_search_args,
						search_type: wp_cpg_gateway_conditions_obj.conditions[condition_key].search_type,
						ajax_request_nonce: wp_cpg_gateway_conditions_obj.ajax.ajax_request_nonce,
						selected_option: $condition_select.val(),
						action: wp_cpg_gateway_conditions_obj.conditions[condition_key].ajax_method
					}
				},
				processResults: function (response) {
					// Tranforms the top-level key of the response object from 'items' to 'results'                        					
					resultsArray = [];

					response.data.posts.forEach(function (currentPost) {
						resultsArray.push({id: currentPost.value, text: currentPost.text});
					});

					return {results: resultsArray};

				}
			},
			minimumInputLength: 1,
			dropdownAutoWidth: true,
			height: '22px'

		}

		var select2_obj = jQuery.extend({}, select2_obj);
		return select2_obj;

	}

	function init_selects_2($conditions_rows) {

		$conditions_rows.each(function () {

			var $current_row = $(this);
			var $select = $current_row.find(".input-column select");
			if (!$select.length) {
				return true;
			}
			var condition_key = $current_row.find(".condition-input-modifier").val();
			var condition = wp_cpg_gateway_conditions_obj.conditions[condition_key];

			if (!condition.is_select_2) {
				return true;
			}

			if (!$select.data("wpcpg-already-init")) {
				var select_2_ajax_obj = get_condition_select_2_ajax_object($current_row)
				$select.select2(select_2_ajax_obj);
				$select.data("wpcpg-already-init", "1");
			}

		});

	}

});


jQuery(document).ready(function () {
	jQuery('.custom_gateways').hide();
	jQuery('.use_custom_gateways').change(function (e) {
		jQuery('.custom_gateways').slideToggle();
	});
});