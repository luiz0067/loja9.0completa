<script type="text/javascript">
	$(document).ready(function() {
		// disable all but the first variation box
		$(".VariationSelect:gt(0)").attr('disabled', 'disabled');
	});

	$(".VariationSelect").change(function() {
		// get the index of this select
		var index = $('.VariationSelect').index($(this));

		// deselected an option, disable all select's below this one and remove their options
		if (this.selectedIndex == 0) {
			$('.VariationSelect:gt(' + index + ')').each(function() {
				$(this).attr('disabled', 'disabled');
				//$(this).find('option:gt(0)').remove();
			});

			updateSelectedVariation($('body'));

			return;
		}
		else {
			// disable selects greater than the next
			$('.VariationSelect:gt(' + (index + 1) + ')').attr('disabled', 'disabled');
		}

		//serialize the options of the variation selects
		var optionIds = '';
		$('.VariationSelect:lt(' + (index + 1) + ')').each(function() {
			if (optionIds != '') {
				optionIds += ',';
			}

			optionIds += $(this).val();
		});

		// request values for this option
		$.getJSON(
			'%%GLOBAL_ShopPath%%/remote.php?w=GetVariationOptions&productId=%%GLOBAL_ProductId%%&options=' + optionIds,
			function(data) {
				// were options returned?
				if (data.hasOptions) {
					// load options into the next select, disable and focus it
					$('.VariationSelect:eq(' + (index + 1) + ') option:gt(0)').remove();
					$('.VariationSelect:eq(' + (index + 1) + ')').append(data.options).attr('disabled', '').focus();
				}
				else if (data.comboFound) { // was a combination found instead?
					// display price, image etc
					updateSelectedVariation($('body'), data, data.combinationid);
				}
			}
		);
	});
</script>