var ExpressCheckout = {
	completedSteps: new Array(),
	currentStep: 'AccountDetails',
	signedIn: 0,
	digitalOrder: 0,
	createAccount: 0,
	anonymousCheckout: 0,
	checkoutLogin: 0,

	init: function()
	{
		if($('#CheckoutStepAccountDetails').css('display') == 'none') {
			ExpressCheckout.currentStep = 'BillingAddress';
		}
		else {
			$('#BillingDetailsLabel').html(lang.ExpressCheckoutStepBillingAccountDetails);
		}

		$('.ExpressCheckoutBlock').hover(function() {
			if($(this).hasClass('ExpressCheckoutBlockCompleted')) {
				$(this).css('cursor', 'pointer');
			}
		},
		function() {
			$(this).css('cursor', 'default');
		});

		$('.ExpressCheckoutTitle').click(function() {
			if($(this).hasClass('ExpressCheckoutBlockCompleted')) {
				$(this).find('.ChangeLink').click();
			}
		});


	},

	Login: function()
	{
		$('#CheckoutLoginError').hide();
		ExpressCheckout.anonymousCheckout = 0;
		ExpressCheckout.createAccount = 0;

		if($('#login_email').val().indexOf('@') == -1 || $('#login_email').val().indexOf('.') == -1) {
			alert(lang.LoginEnterValidEmail);
			$('#login_email').focus();
			$('#login_email').select();
			return false;
		}

		if($('#login_pass').val() == '') {
			alert(lang.LoginEnterPassword);
			$('#login_pass').focus();
			return false;
		}

		ExpressCheckout.ShowLoadingIndicator('#LoginForm');

		$.ajax({
			url: 'remote.php',
			type: 'post',
			dataType: 'xml',
			data: 'w=expressCheckoutLogin&'+$('#LoginForm').serialize(),
			success: ExpressCheckout.LoginResult
		});

		return false;
	},

	LoginResult: function(xml)
	{
		ExpressCheckout.HideLoadingIndicators();

		$('#BillingDetailsLabel').html(lang.ExpressCheckoutStepBillingAddress);
		ExpressCheckout.DisplayAccountRequiredFields(false);

		// Login was successful
		if($('status', xml).text() == 1) {
			ExpressCheckout.checkoutLogin = 1;
			ExpressCheckout.ResetNextSteps();
			var value = document.createTextNode(lang.CheckingOutAs+' '+$('#login_email').val());
			$('#CheckoutStepAccountDetails .ExpressCheckoutCompletedContent').html(value);
			$('#CheckoutStepBillingAddress .ExpressCheckoutContent').html($('billingContents', xml).text());
			$('#CheckoutStepShippingAddress .ExpressCheckoutContent').html($('shippingContents', xml).text());
			$('#CheckoutStepBillingAddress .ExpressCheckoutContent .FormField.JSHidden').show();
			$('#CheckoutStepShippingAddress .ExpressCheckoutContent .FormField.JSHidden').show();
			ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'AccountDetails';
			ExpressCheckout.ChangeStep();
		}
		else {
			var message = $('message', xml).text();
			if($('errorContainer', xml).text()) {
				$($('errorContainer', xml).text()).html(message).show();
				$('#LoginIntro').hide();
			}
			$('#login_email').focus();
			$('#login_email').select();
		}
	},

	GuestCheckout: function()
	{
		$('#CreateAccountForm').show();
		$('#CheckoutLoginError').hide();

		if($('#CheckoutGuestForm').css('display') != 'none' && !$('#checkout_type_register:checked').val()) {
			$('#CheckoutStepAccountDetails .ExpressCheckoutCompletedContent').html(lang.ExpressCheckoutCheckingOutAsGuest);
			ExpressCheckout.DisplayAccountRequiredFields(false);
			ExpressCheckout.anonymousCheckout = 1;
			ExpressCheckout.createAccount = 0;
		}
		else {
			$('#CheckoutStepAccountDetails .ExpressCheckoutCompletedContent').html(lang.ExpressCheckoutCreatingAnAccount);
			ExpressCheckout.DisplayAccountRequiredFields(true);
			ExpressCheckout.createAccount = 1;
			ExpressCheckout.anonymousCheckout = 0;
		}

		// We were previously logged in so we need to refetch the address fields because we're now a guest
		if(ExpressCheckout.checkoutLogin == 1) {
			ExpressCheckout.checkoutLogin = 0;
			ExpressCheckout.ShowLoadingIndicator('#CheckoutGuestForm');
			$.ajax({
				url: 'remote.php',
				type: 'post',
				dataType: 'xml',
				data: 'w=expressCheckoutGetAddressFields',
				success: ExpressCheckout.GuestCheckoutLoaded
			});
		}
		else {
			ExpressCheckout.GuestCheckoutLoaded();
		}
	},

	GuestCheckoutLoaded: function(xml)
	{
		if(typeof(xml) != 'undefined') {
			ExpressCheckout.HideLoadingIndicators();
			$('#CheckoutStepBillingAddress .ExpressCheckoutContent').html($('billingContents', xml).text());
			$('#CheckoutStepShippingAddress .ExpressCheckoutContent').html($('shippingContents', xml).text());
		}
		$('#CheckoutStepBillingAddress .ExpressCheckoutContent .FormField.JSHidden').show();
		$('#CheckoutStepShippingAddress .ExpressCheckoutContent .FormField.JSHidden').show();
		ExpressCheckout.ResetNextSteps();
		ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'AccountDetails';
		ExpressCheckout.ChangeStep();
	},

	ResetNextSteps:function()
	{
		steps = ExpressCheckout.GetSteps();
		var beginReset = false;
		var newCompleted = Array();
		$.each(steps, function(i, step) {
			if(step == ExpressCheckout.currentStep) {
				newCompleted[newCompleted.length] = step;
				beginReset = true;
			}
			else if(beginReset == true) {
				$('#CheckoutStep'+step).removeClass('ExpressCheckoutBlockCompleted');
				$('#CheckoutStep'+step+' .ExpressCheckoutCompletedContent').html('');
			}
		});

		ExpressCheckout.completedSteps = newCompleted;
	},

	ChangeStep: function(step)
	{
		if(typeof(step) == 'undefined') {
			step = ExpressCheckout.CalculateNextStep(ExpressCheckout.currentStep);
		}

		if(step == ExpressCheckout.currentStep) {
			return false;
		}

		$('#CheckoutStep'+ExpressCheckout.currentStep+' .ExpressCheckoutContent').slideUp('slow');
		$('#CheckoutStep'+ExpressCheckout.currentStep).addClass('ExpressCheckoutBlockCollapsed');
		if($.inArray(ExpressCheckout.currentStep, ExpressCheckout.completedSteps) != -1) {
			$('#CheckoutStep'+ExpressCheckout.currentStep).addClass('ExpressCheckoutBlockCompleted');
		}
		$('#CheckoutStep'+step+' .ExpressCheckoutContent').slideDown('slow');
		$('#CheckoutStep'+step).removeClass('ExpressCheckoutBlockCollapsed');
		ExpressCheckout.currentStep = step;
		return false;
	},

	GetSteps: function()
	{
		var steps = Array();
		if(ExpressCheckout.signedIn == 0) {
			steps[steps.length] = 'AccountDetails';
		}
		steps[steps.length] = 'BillingAddress';
		if(!ExpressCheckout.digitalOrder) {
			steps[steps.length] = 'ShippingAddress';
			steps[steps.length] = 'ShippingProvider';
		}
		steps[steps.length] = 'Confirmation';
		steps[steps.length] = 'PaymentDetails';
		return steps;
	},

	CalculateNextStep: function(currentStep) {
		steps = ExpressCheckout.GetSteps();
		var nextStep = '';
		$.each(steps, function(i, step) {
			if(step == currentStep) {
				nextStep = steps[i + 1];
			}
		});

		if(nextStep) {
			return nextStep;
		}
	},

	ChooseBillingAddress: function()
	{
		// Chosen to use a new address?
		if(!$('#BillingAddressTypeExisting:checked').val() || $('#ChooseBillingAddress').css('display') == 'none') {
			ExpressCheckout.UseNewBillingAddress();
			return false;
		}

		// An address hasn't been selected
		if($('.SelectBillingAddress select option:selected').val() == -1) {
			alert(lang.ExpressCheckoutChooseBilling);
			$('.SelectBillingAddress select').focus();
			return false;
		}

		var addressValue = $('.SelectBillingAddress select option:selected').text();
		if(addressValue.length > 60) {
			addressValue = addressValue.substring(0, 57)+'...';
		}
		$('#CheckoutStepBillingAddress .ExpressCheckoutCompletedContent').html(addressValue);

		ExpressCheckout.ResetNextSteps();
		ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'BillingAddress';
		if(!ExpressCheckout.digitalOrder) {
			// We're shipping to this address to so do that as well
			if($('#ship_to_billing_existing:checked').val()) {
				ExpressCheckout.ChooseShippingAddress(true);
			}
			else {
				ExpressCheckout.ChangeStep();
			}
		}
		else {
			ExpressCheckout.LoadOrderConfirmation();
		}
		return false;
	},

	ChooseShippingAddress: function(copyBilling)
	{
		if(typeof(copyBilling) != 'undefined') {
			$('#ShippingAddressTypeExisting').attr('checked', true);
			var billingAddress = $('.SelectBillingAddress select option:selected').val();
			$('.SelectShippingAddress select').val(billingAddress);
		}

		// Chosen to use a new address?
		if(!$('#ShippingAddressTypeExisting:checked').val() || $('#ChooseShippingAddress').css('display') == 'none') {
			ExpressCheckout.UseNewShippingAddress();
			return false;
		}

		// An address hasn't been selected
		if($('.SelectShippingAddress select option:selected').val() == -1) {
			alert(lang.ExpressCheckoutChooseShipping);
			$('.SelectShippingAddress select').focus();
			return false;
		}

		var addressValue = $('.SelectShippingAddress select option:selected').text();
		if(addressValue.length > 60) {
			addressValue = addressValue.substring(0, 57)+'...';
		}

		$('#CheckoutStepShippingAddress .ExpressCheckoutCompletedContent').html(addressValue);
		ExpressCheckout.LoadShippingProviders();
		return false;
	},

	ChooseShippingProvider: function()
	{
		// A shipping provider hasn't been selected
		var shippingValid = true;
		$('#CheckoutStepShippingProvider .ShippingProviderList').each(function() {
			if(!$(this).find('input[type=radio]:checked').val()) {
				alert(lang.ExpressCheckoutChooseShipper);
				$(this).find('input[type=radio]').get(0).focus();
				shippingValid = false;
				return false;
			}
		});

		if(shippingValid == false) {
			return false;
		}

		var numShippers = $('#CheckoutStepShippingProvider .ShippingProviderList').length;
		if(numShippers == 1) {
			var shippingCheck = $('#CheckoutStepShippingProvider .ShippingProviderList input[type=radio]:checked')
			var shippingMethod = shippingCheck.attr('id');
			shippingMethod = shippingMethod.replace('shippingCheck_', '')+'_'+shippingCheck.val();
			var shipperName = $('#shippingMethod_'+shippingMethod+' .ShipperName').html();
			var shipperPrice = $('#shippingMethod_'+shippingMethod+' .ShipperPrice').html();
			$('#CheckoutStepShippingProvider .ExpressCheckoutCompletedContent').html(shipperName + ' '+lang.ExpressCheckoutFor+' '+shipperPrice);
		}
		else {
			$('#CheckoutStepShippingProvider .ExpressCheckoutCompletedContent').html(lang.ShippingMethodCombined);
		}

		ExpressCheckout.ResetNextSteps();
		ExpressCheckout.LoadOrderConfirmation();
	},

	ShowLoadingIndicator: function(step) {
		if(typeof(step) == 'undefined') {
			step = 'body';
		}
		$(step).find('.ExpressCheckoutBlock input[type=submit]').each(function() {
			$(this).attr('oldValue', $(this).val());
			$(this).val(lang.ExpressCheckoutLoading);
			$(this).attr('disabled', true);
		});
		$(step).find('.LoadingIndicator').show();
		$('body').css('cursor', 'wait');
	},

	HideLoadingIndicators: function()
	{
		$('.ExpressCheckoutBlock input[type=submit]').each(function() {
			if($(this).attr('oldValue') && $(this).attr('disabled') == true) {
				$(this).val($(this).attr('oldValue'));
				$(this).attr('disabled', false);
			}
		});
		$('.LoadingIndicator').hide();
		$('body').css('cursor', 'default');
	},

	LoadOrderConfirmation: function()
	{
		var postVars = {};

		ExpressCheckout.ShowLoadingIndicator();

		if(ExpressCheckout.anonymousCheckout == 1) {
			postVars.anonymousCheckout = 1;
		}

		if(ExpressCheckout.createAccount == 1) {
			postVars.createAccount = 1;
		}

		if($('#BillingAddressTypeExisting:checked').val() && $('#ChooseBillingAddress').css('display') != 'none') {
			postVars.billingType = 'existing';
			postVars.billingAddressId = $('.SelectBillingAddress select').val();
		}
		else {
			var billingDetails = ExpressCheckout.GetCustomPostVars('billing');
			for (var i in billingDetails) {
				postVars[i] = billingDetails[i];
			}
			postVars.billingType = 'new';
		}

		// If this is a physical order, we have to pass across the shipping details too
		if(!ExpressCheckout.digitalOrder) {
			// Pass along the shipping provider too
			var serialize = $('#CheckoutStepShippingProvider form').serializeArray();
			for (var i in serialize) {
				postVars[serialize[i].name] = serialize[i].value;
			}
		}

		var serialize = $('#OrderConfirmationForm').serializeArray();
		for (var i in serialize) {
			postVars[serialize[i].name] = serialize[i].value;
		}

		postVars.w = 'expressCheckoutShowConfirmation';

		$.ajax({
			url: 'remote.php',
			type: 'post',
			dataType: 'xml',
			data: postVars,
			success: ExpressCheckout.OrderConfirmationLoaded
		});
	},

	OrderConfirmationLoaded: function(xml)
	{
		ExpressCheckout.HideLoadingIndicators();

		if($('status', xml).text() == 0) {
			$('#CheckoutStepShippingAddress .ExpressCheckoutCompletedContent').html('');
			alert($('message', xml).text().replace('\n', "\n"));
			if($('step', xml).text()) {
				ExpressCheckout.ChangeStep($('step', xml).text());
			}
			return false;
		}
		$('#CheckoutStepConfirmation .ExpressCheckoutContent').html($('confirmationContents', xml).text());
		if(!ExpressCheckout.digitalOrder) {
			ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'ShippingProvider';
		}
		else {
			ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'BillingAddress';
		}

		$('#provider_list input[type=radio], #credit_provider_list input[type=radio]').click(function() {
			if(!$(this).hasClass('ProviderHasPaymentForm')) {
				ExpressCheckout.HidePaymentForm();
			}
			else {
				$('#CheckoutStepPaymentDetails').show();
			}
		});
		ExpressCheckout.ChangeStep('Confirmation');
	},

	HidePaymentForm: function()
	{
		$('#CheckoutStepPaymentDetails').hide();
		$('#CheckoutStepPaymentDetails .ExpressCheckoutContent').html('');
	},

	LoadPaymentForm: function(provider)
	{
		$.ajax({
			url: 'remote.php',
			data: 'w=expressCheckoutLoadPaymentForm&'+$('#CheckoutStepConfirmation form').serialize(),
			type: 'post',
			success: ExpressCheckout.PaymentFormLoaded
		});
	},

	ShowSingleMethodPaymentForm: function()
	{
		$('#CheckoutStepPaymentDetails').show();
		ShowContinueButton();
	},

	PaymentFormLoaded: function(xml)
	{
		if($('status', xml).text() == 0) {
			alert($('message', xml).text().replace('\n', "\n"));
			if($('step', xml).text()) {
				ExpressCheckout.ChangeStep($('step', xml).text());
			}
			return false;
		}

		$('#CheckoutStepPaymentDetails .ExpressCheckoutContent').html($('paymentContents', xml).text());
		$('#CheckoutStepPaymentDetails').show();
		ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'Confirmation';
		ExpressCheckout.ChangeStep('PaymentDetails');
	},

	ValidateNewAccount: function(validateOnly)
	{
		if (validateOnly !== true) {
			validateOnly = false;
		}

		if(ExpressCheckout.createAccount == 1) {
			var password, confirmPassword, formfield = FormField.GetValues(CustomCheckoutFormNewAccount);

			for (var i=0; i<formfield.length; i++) {

				// Check email
				if (formfield[i].privateId == 'EmailAddress') {
					if(formfield[i].value.indexOf('@') == -1 || formfield[i].value.indexOf('.') == -1) {
						alert(lang.LoginEnterValidEmail);
						FormField.Focus(formfield[i].field);
						return false;
					}
				}

				// Ignore these if we are not creating an account, else save the values for later
				if (formfield[i].privateId == 'Password' || formfield[i].privateId == 'ConfirmPassword') {
					if (!ExpressCheckout.createAccount) {
						continue;
					} else if (formfield[i].privateId == 'Password') {
						password = formfield[i];
					} else {
						confirmPassword = formfield[i];
					}
				}

				var rtn = FormField.Validate(formfield[i].field);

				if (!rtn.status) {
					alert(rtn.msg);
					FormField.Focus(formfield[i].field);
					return false;
				}
			}

			// Compare the passwords
			if (ExpressCheckout.createAccount && password.value !== confirmPassword.value) {
				alert(lang.AccountPasswordsDontMatch);
				FormField.Focus(confirmPassword.field);
				return false;
			}
		}

		if (validateOnly) {
			return true;
		}

		ExpressCheckout.ShowLoadingIndicator();

		var data = ExpressCheckout.GetCustomPostVars('billing', false);
		data['w'] = 'expressCheckoutRegister';

		$.ajax({
			url: 'remote.php',
			type: 'post',
			dataType: 'xml',
			data: data,
			success: function(xml) {
				ExpressCheckout.HideLoadingIndicators();
				if($('status', xml).text() == 0) {
					alert($('message', xml).text().replace('\n', "\n"));
					if($('step', xml).text()) {
						ExpressCheckout.ChangeStep($('step', xml).text());
					}
					if($('focus', xml).text()) {
						try {
							$($('focus', xml).text()).focus();
							$($('focus', xml).text()).select();
						}
						catch(e) { }
					}
					return false;
				}
				else {
					// Call the new address form again now that we're done here
					ExpressCheckout.UseNewBillingAddress(true);
				}
			}
		});
	},

	UseNewBillingAddress: function(inValidate)
	{
		if(typeof(inValidate) == 'undefined') {
			if ((ExpressCheckout.anonymousCheckout == 1 || ExpressCheckout.createAccount == 1) && !ExpressCheckout.ValidateNewAccount()) {
				return false;
			}
		}

		if(!ExpressCheckout.ValidateNewAddress('billing')) {
			return false;
		}

		var addressValue = ExpressCheckout.BuildAddressLine('billing');
		if(addressValue.length > 60) {
			addressValue = addressValue.substring(0, 57)+'...';
		}
		addressValue = document.createTextNode(addressValue);
		$('#CheckoutStepBillingAddress .ExpressCheckoutCompletedContent').html(addressValue);

		ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'BillingAddress';
		ExpressCheckout.ResetNextSteps();
		if(!ExpressCheckout.digitalOrder) {
			// We're shipping to this address to so do that as well
			var shipField = FormField.GetFieldByPrivateId(CustomCheckoutFormBillingAddress, 'ShipToAddress');
			if(shipField && FormField.GetValue(shipField).length > 0) {
				ExpressCheckout.UseNewShippingAddress(true);
			}
			else {
				ExpressCheckout.ChangeStep();
			}
		}
		else {
			ExpressCheckout.LoadOrderConfirmation();
		}
		return false;
	},

	UseNewShippingAddress: function(copyBilling)
	{
		if (typeof(copyBilling) == 'undefined') {
			copyBilling = false;
		} else {
			copyBilling = true;
		}

		if(copyBilling) {
			var billingFields = FormField.GetValues(CustomCheckoutFormBillingAddress);
			var shippingFields = FormField.GetValues(CustomCheckoutFormShippingAddress);
			var matching;

			for (var i=0; i<billingFields.length; i++) {

				if (billingFields[i].privateId.toLowerCase == 'shiptoaddress') {
					continue;
				}

				matching = false;

				for (var j=0; j<shippingFields.length; j++) {
					if (billingFields[i].label == shippingFields[j].label) {
						matching = shippingFields[j].field;
						continue;
					}
				}

				if (!matching) {
					continue;
				}

				if (billingFields[i].privateId.toLowerCase() == 'state') {
					var options = {
						'options': FormField.GetOptions(billingFields[i].field, true),
						'display': 'select'
					};

					if (options.options.length == 0) {
						options.display = 'option';
					}

					FormField.SetValue(matching, billingFields[i].value, options);

				} else {
					FormField.SetValue(matching, billingFields[i].value);
				}
			}

			$('#ShippingAddressTypeNew').attr('checked', true);
			$('#ShippingAddressTypeNew').trigger('click');
		}

		if(!ExpressCheckout.ValidateNewAddress('shipping', copyBilling)) {
			if (copyBilling) {
				ExpressCheckout.ChangeStep();
			}
			return false;
		}

		var addressValue = ExpressCheckout.BuildAddressLine('shipping');

		if(addressValue.length > 60) {
			addressValue = addressValue.substring(0, 57)+'...';
		}
		ExpressCheckout.ResetNextSteps();
		addressValue = document.createTextNode(addressValue);
		$('#CheckoutStepShippingAddress .ExpressCheckoutCompletedContent').html(addressValue);
		ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'ShippingAddress';
		ExpressCheckout.LoadShippingProviders();
		return false;
	},

	LoadShippingProviders: function()
	{
		ExpressCheckout.ShowLoadingIndicator();
		var shippingProvidersLoaded = function(xml) {
			ExpressCheckout.HideLoadingIndicators();
			if($('status', xml).text() == 0) {
				alert($('message', xml).text().replace('\n', "\n"));
				$('#CheckoutStepShippingAddress .ExpressCheckoutCompletedContent').html('');
				if($('step', xml).text()) {
					ExpressCheckout.ChangeStep($('step', xml).text());
				}
				return false;
			}

			$('#CheckoutStepShippingAddress').addClass('ExpressCheckoutBlockCompleted');
			$('#CheckoutStepShippingProvider .ExpressCheckoutContent').html($('providerContents', xml).text());
			ExpressCheckout.completedSteps[ExpressCheckout.completedSteps.length] = 'ShippingAddress';
			ExpressCheckout.ChangeStep('ShippingProvider');
		};

		if($('#ShippingAddressTypeExisting:checked').val() && $('#ChooseShippingAddress').css('display') != 'none') {
			postVars = '&shippingType=existing&shippingAddressId='+$('.SelectShippingAddress select').val();
		}
		else {
			postVars = ExpressCheckout.GetCustomPostVars('shipping');
			postVars.shippingType = 'new';
		}

		if($('#ShippingAddressTypeExisting:checked').val() && $('#ChooseShippingAddress').css('display') != 'none') {
			$.ajax({
				url: 'remote.php?w=expressCheckoutGetShippers',
				type: 'post',
				data: postVars,
				success: shippingProvidersLoaded
			});
		}
		else {
			$.ajax({
				url: 'remote.php?w=expressCheckoutGetShippers',
				type: 'post',
				data: postVars,
				success: shippingProvidersLoaded
			});
		}
	},

	BuildAddressLine: function(type)
	{
		if(type == 'billing') {
			var fieldList = {
				'FirstName' : '',
				'LastName' : '',
				'Company' : '',
				'AddressLine1' : '',
				'City' : '',
				'State' : '',
				'Zip' : '',
				'Country' : ''
			};

			var formId = CustomCheckoutFormBillingAddress;
		}
		else {
			var fieldList = {
				'FirstName' : '',
				'LastName' : '',
				'Company' : '',
				'AddressLine1' : '',
				'City' : '',
				'State' : '',
				'Zip' : '',
				'Country' : ''
			};

			var formId = CustomCheckoutFormShippingAddress;
		}

		var formfields = FormField.GetValues(formId);
		var addressLine = '';

		for (var i=0; i<formfields.length; i++) {
			fieldList[formfields[i].privateId] = formfields[i].value;
		}

		for (var i in fieldList) {
			var val = fieldList[i];
			if (val !== '') {
				if(addressLine != '' && i != 'LastName') {
					addressLine += ', ';
				} else if(i == 'LastName') {
					addressLine += ' ';
				}

				addressLine += val;
			}
		};

		return addressLine;
	},

	ValidateNewAddress: function(lowerType, resultOnly)
	{
		if (resultOnly !== true) {
			resultOnly = false;
		}

		if(lowerType == 'billing') {
			var formId = CustomCheckoutFormBillingAddress;
		} else {
			var formId = CustomCheckoutFormShippingAddress;
		}

		var formfields = FormField.GetValues(formId);
		var hasErrors = false;

		for (var i=0; i<formfields.length; i++) {

			var rtn = FormField.Validate(formfields[i].field);

			if (!rtn.status) {
				if (!resultOnly) {
					alert(rtn.msg);
				}

				FormField.Focus(formfields[i].field);
				hasErrors = true;
				return false;
			}
		}

		if(hasErrors == true) {
			return false;
		}
		else {
			return true;
		}
	},

	ToggleAddressType: function(address, type)
	{
		if(type == 'Select') {
			$('.Select'+address+'Address').show();
			$('.Add'+address+'Address').hide();
		}
		else {
			$('.Add'+address+'Address').show();
			$('.Select'+address+'Address').hide();
		}
	},

	ConfirmPaymentProvider: function()
	{
		//if terms and conditions is enabled and the customer didn't tick agree terms and conditions
		if($('.CheckoutHideOrderTermsAndConditions').css('display') != "none" && $('#AgreeTermsAndConditions').attr('checked') != true){
			alert(lang.TickArgeeTermsAndConditions);
			return false;
		}

		if(!confirm_payment_provider()) {
			return false;
		}

		var paymentProvider = '';

		// Get the ID of the selected payment provider
		if($('#use_store_credit').css('display') != "none") {
			if($('#store_credit:checked').val()) {
				if($('#credit_provider_list').css('display') != "none") {
					paymentProvider = $('#credit_provider_list input:checked');
				}
			}
			else {
				paymentProvider = $('#provider_list input:checked');
			}
		}
		else {
			paymentProvider = $('#provider_list input:checked');
		}

		if(paymentProvider != '' && $(paymentProvider).hasClass('ProviderHasPaymentForm')) {
			var providerName = $('.ProviderName'+paymentProvider.val()).html();
			$('#CheckoutStepConfirmation .ExpressCheckoutCompletedContent').html(providerName);
			ExpressCheckout.LoadPaymentForm($(paymentProvider).val());
			return false;
		}
		else {
			ExpressCheckout.HidePaymentForm();
			return true;
		}
	},

	ApplyCouponCode: function()
	{
		if($('#couponcode').val() == '') {
			alert(lang.EnterCouponCode);
			$('#couponcode').focus();
			return false;
		}

		// Reload the order confirmation
		ExpressCheckout.LoadOrderConfirmation();
		return false;
	},

	UncheckPaymentProvider: function()
	{
		$('#provider_list input').each(function() {
			$(this).attr('checked', '');
		});
	},

	DisplayAccountRequiredFields: function(display)
	{
		if (display !== true) {
			display = false;
		}

		var formIdx = [CustomCheckoutFormNewAccount,CustomCheckoutFormBillingAddress,CustomCheckoutFormShippingAddress];
		var formfields = FormField.GetValues(formIdx, true);
		var password, confirmpass, savebilling, saveshipping;

		for (var i=0; i<formfields.length; i++) {
			if (formfields[i].privateId == 'Password') {
				password = formfields[i];
			} else if (formfields[i].privateId == 'ConfirmPassword') {
				confirmpass = formfields[i];
			} else if (formfields[i].privateId == 'SaveThisAddress' && formfields[i].formId == CustomCheckoutFormBillingAddress) {
				savebilling = formfields[i];
			} else if (formfields[i].privateId == 'SaveThisAddress' && formfields[i].formId == CustomCheckoutFormShippingAddress) {
				saveshipping = formfields[i];
			}
		}

		if (typeof(password) !== 'undefined') {
			FormField.SetRequired(password.field, display);
			FormField.SetRequired(confirmpass.field, display);
		}

		if (display) {
			if (typeof(password) !== 'undefined') {
				FormField.Show(password.field);
				FormField.Show(confirmpass.field);
			}
			FormField.Show(savebilling.field);
			FormField.Show(saveshipping.field);
		} else {
			if (typeof(password) !== 'undefined') {
				FormField.Hide(password.field);
				FormField.Hide(confirmpass.field);
			}
			FormField.Hide(savebilling.field);
			FormField.Hide(saveshipping.field);
		}
	},

	GetCustomPostVars: function(type, customOnly)
	{
		if (type.toLowerCase() == 'shipping') {
			var formIdx = [CustomCheckoutFormShippingAddress];
		} else {
			var formIdx = [CustomCheckoutFormNewAccount,CustomCheckoutFormBillingAddress];
		}

		if (customOnly == undefined) {
			customOnly = false;
		}

		var label, formfields, data = {};

		for (var i=0; i<formIdx.length; i++) {

			formfields = FormField.GetValues(formIdx[i]);

			for (var j=0; j<formfields.length; j++) {
				if (customOnly && formfields[j].privateId !== '') {
					continue;
				}

				if (formfields[j].privateId !== '') {
					label = type + '_' + formfields[j].privateId;
				} else {
					label = 'custom[' + formfields[j].fieldId + ']';
				}

				/**
				 * JQuery can't handle if the value is an array (this will not handle recursive)
				 */
				if (typeof(formfields[j].value) !== 'string' && typeof(formfields[j].value.pop) !== 'undefined') {
					for (var n=0; n<formfields[j].value.length; n++) {
						data[label + '[' + n + ']'] = formfields[j].value[n];
					}
				} else {
					data[label] = formfields[j].value;
				}
			}
		}

		return data;
	}
};