var Cart = {
	ToggleShippingEstimation: function()
	{
		$('.EstimatedShippingMethods').hide();
		$('.EstimateShipping').toggle();
		$('.EstimateShippingLink').toggle();
		$('.EstimateShipping select:eq(0)').focus();
	},

	EstimateShipping: function()
	{
		$('.EstimatedShippingMethods').hide();
		$('.EstimateShipping .EstimateShippingButtons span').hide();
		$('.EstimateShipping .EstimateShippingButtons input').val(lang.Calculating);
		$('.EstimateShipping .EstimateShippingButtons input').attr('disabled', true);
		$.ajax({
			url: 'remote.php',
			type: 'post',
			data: {
				w: 'getShippingQuotes',
				countryId: $('#shippingZoneCountry').val(),
				stateId: $('#shippingZoneState').val(),
				stateName: escape($('#shippingZoneStateName').val()),
				zipCode: $('#shippingZoneZip1').val()+$('#shippingZoneZip2').val()
			},
			success: function(data)
			{
				$('.EstimatedShippingMethods .ShippingMethodList').html(data);
				$('.EstimatedShippingMethods').show();
				$('.EstimateShipping .EstimateShippingButtons span').show();
				$('.EstimateShipping .EstimateShippingButtons input').val(lang.CalculateShipping);
				$('.EstimateShipping .EstimateShippingButtons input').attr('disabled', false);
			}
		});
	},

	ToggleShippingEstimateCountry: function()
	{
		var countryId = $('#shippingZoneCountry').val();
		$.ajax({
			url: 'remote.php',
			type: 'post',
			data: 'w=countryStates&c='+countryId,
			success: function(data)
			{
				$('#shippingZoneState option:gt(0)').remove();
				var states = data.split('~');
				var numStates = 0;
				for(var i =0; i < states.length; ++i) {
					vals = states[i].split('|');
					if(!vals[0]) {
						continue;
					}
					$('#shippingZoneState').append('<option value="'+vals[1]+'">'+vals[0]+'</option>');
					++numStates;
				}

				if(numStates == 0) {
					$('#shippingZoneState').hide();
					$('#shippingZoneStateName').show();
				}
				else {
					$('#shippingZoneState').show();
					$('#shippingZoneStateName').hide();
				}
				$('#shippingZoneState').val('0');
			}
		});
	},

	UpdateShippingCost: function()
	{
		var returnVal = true;
		var method = $('.EstimatedShippingMethods table').each(function() {
			var method = $('input[type=radio]:checked', this).val();
			if(typeof(method) == 'undefined' || method == '') {
				alert(lang.ChooseShippingMethod);
				$('input[type=radio]:eq(0)', this).focus();
				returnVal = false;
				return returnVal;
			}
		});

		if(returnVal == false) {
			return returnVal;
		}

		$('#cartForm').submit();
	},

	RemoveItem: function(itemId)
	{
		if(confirm(lang.CartRemoveConfirm)) {
			document.location.href = "compras.php?action=remove&item="+itemId;
		}
	},

	UpdateQuantity: function(qty)
	{
		if(qty == 0) {
			if(confirm(lang.CartRemoveConfirm)) {
				$('#cartForm').submit();
			}
			else {
				return false;
			}
		}
		else {
			$('#cartForm').submit();
		}
	},

	ValidateQuantityForm: function(form)
	{
		var valid = true;
		var qtyInputs = $(form).find('input.qtyInput');
		qtyInputs.each(function() {
			if(isNaN($(this).val()) || $(this).val() < 0) {
				alert(lang.InvalidQuantity);
				this.focus();
				this.select();
				valid = false;
				return false;
			}
		});
		if(valid == false) {
			return false;
		}

		return true;
	},

	CheckCouponCode: function()
	{
		if($('#couponcode').val() == '') {
			alert(lang.EnterCouponCode);
			$('#couponcode').focus();
			return false;
		}
	},

	CheckGiftCertificateCode: function()
	{
		if($('#giftcertificatecode').val() == '') {
			alert(lang.EnterGiftCertificateCode);
			$('#giftcertificatecode').focus();
			return false;
		}
	},

	ManageGiftWrapping: function(itemId)
	{
		$.iModal({
			type: 'ajax',
			url: 'remote.php?w=selectGiftWrapping&itemId='+itemId
		});
	},

	ToggleGiftWrappingType: function(option)
	{
		if($(option).hasClass('HasPreview')) {
			$('.GiftWrappingPreviewLinks').hide();
			$('#GiftWrappingPreviewLink'+$(option).val()).show();
		}
		else {
			$('.GiftWrappingPreviewLinks').hide();
		}

		if($(option).hasClass('AllowComments')) {
			$(option).parents('.WrappingOption').find('.WrapComments').show();
		}
		else {
			$(option).parents('.WrappingOption').find('.WrapComments').hide();
		}
	},

	ToggleMultiWrapping: function(value)
	{
		if(value == 'same') {
			$('.WrappingOptionsSingle').show();
			$('.WrappingOptionsMultiple').hide();
		}
		else {
			$('.WrappingOptionsSingle').hide();
			$('.WrappingOptionsMultiple').show();
		}
	},

	RemoveGiftWrapping: function(itemId)
	{
		if(confirm(lang.ConfirmRemoveGiftWrapping)) {
			return true;
		}
		else {
			return false;
		}
	},

	ShowEditOptionsInCartForm: function(itemId)
	{
		$.iModal({
			type: 'ajax',
			url: 'remote.php?w=editconfigurablefieldsincart&itemid='+itemId
		});
	},

	DeleteUploadedFile: function(fieldid, itemid)
	{
		if(confirm(lang.DeleteProductFieldFileConfirmation)) {
			$.ajax({
				url: 'remote.php',
				type: 'post',
				data: 'w=deleteuploadedfileincart&field='+fieldid+'&item='+itemid,
				success: function(data) {
					document.getElementById('CurrentProductFile_'+fieldid).value = '';
					$('#CartFileName_'+fieldid).hide();
				}
			});
		}
		return;
	},

	ReloadCart: function()
	{
		window.location = "compras.php";
	}

};