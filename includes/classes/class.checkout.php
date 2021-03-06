<?php
/**
 * The checkout process class.
 *
 * Handles everything to do with the checkout process.
 */
class ISC_CHECKOUT
{
	/**
	 * Handle the incoming page request.
	 */
	public function HandlePage()
	{
		$action = "";
		if (isset($_REQUEST['action'])) {
			$action = isc_strtolower($_REQUEST['action']);
		}

		if($action == 'gateway_ping') {
			$this->GatewayPing();
			exit;
		}
		else if($action == 'set_external_checkout') {
			$this->SetExternalCheckout();
			exit;
		}

		// Check that there is items in the cart and store purchasing is allowed. If cart is empty or purchasing is disabled, redirect back to the cart page.
		if(empty($_SESSION['CART']['ITEMS']) || !(bool)GetConfig('AllowPurchasing')) {
			if(!isset($_GET['optimizer'])) {
				header('Location: '.GetConfig('AppPath').'/compras.php');
				exit;
			}
		}

		switch($action) {
			case "process_gateway_callback":
				$this->ProcessGatewayCallBack();
				break;
			case "process_payment":
				$this->ProcessOrderPayment();
				break;
			case "pay_for_order": {
				$this->PayForOrder();
				break;
			}
			case "save_biller": {
				$this->SaveBillingAddress();
				break;
			}
			case "choose_billing_address": {
				$this->ChooseBillingAddress();
				break;
			}
			case "confirm_order": {
				$this->ConfirmOrder();
				break;
			}
			case "save_shipper": {
				$this->SaveShippingProvider();
				break;
			}
			case 'save_multiple_shipping_addresses':
				$this->SaveMultipleShippingAddresses();
				break;
			case "choose_shipping_address":
				$this->ChooseShippingAddress();
				break;
			case "choose_shipper": {
				$this->ChooseShippingProvider();
				break;
			}
			case "removegiftcertificate": {
				$this->RemoveGiftCertificate();
				break;
			}
			case 'multiple':
				$this->BeginMultipleAddressCheckout();
				break;
			case "checkout":
				$this->Checkout();
				break;
			default: {
				// If we're performing an express checkout, show that
				if(GetConfig('CheckoutType') == 'single' && $this->SinglePageCheckoutSupported()) {
					$this->ExpressCheckout();
				}
				else {
					$this->Checkout();
				}
			}
		}
	}

	/**
	 * Begin the multiple shipping address checkout process.
	 */
	private function BeginMultipleAddressCheckout()
	{
		$_SESSION['CHECKOUT']['IS_SPLIT_SHIPPING'] = true;
		$this->Checkout();
	}

	/**
	 * Checks if the current visitor's browser supports the single page checkout.
	 *
	 * @return boolean True if supported, false if not.
	 */
	public function SinglePageCheckoutSupported()
	{
		$agent = '';
		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		}

		// iPhone
		if(strpos($agent, 'safari') !== false && strpos($agent, 'mobile') !== false) {
			return false;
		}

		// Windows Mobile
		else if(strpos($agent, 'windows ce') !== false) {
			return true;
		}

		// Opera Mini & Opera Mobile
		else if(strpos($agent, 'opera mini') !== false || strpos($agent, 'opera mobile') !== false) {
			return true;
		}

		return true;
	}

	/**
	 * Begin the checkout process.
	 */
	private function Checkout()
	{
		// ensure products are in stock
		$this->CheckStockLevels();

		// If the customer is signed in, then the first step of the checkout is actually the choose billing address page so show that
		if(CustomerIsSignedIn()) {
			$this->ChooseBillingAddress();
			return;
		}

		$_SESSION['CHECKOUT']['CHECKOUT_TYPE'] = 'normal';

		if(isset($_REQUEST['bad_login']) && $_REQUEST['bad_login'] == 1) {

		}
		else {
			$GLOBALS['HideLoginMessage'] = 'none';
		}

		// Otherwise, we need to show the login page for checking out
		if(GetConfig('GuestCheckoutEnabled') && (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'multiple')) {
			$GLOBALS['HideCheckoutRegistrationRequired'] = 'display: none';
		}
		else {
			$GLOBALS['HideCheckoutGuest'] = 'display: none';
		}

		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName').' - '.GetLang('Checkout'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('checkout');
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	 * Start the express checkout process.
	  */
	private function ExpressCheckout()
	{
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');

		// ensure products are in stock
		$this->CheckStockLevels();

		$_SESSION['CHECKOUT']['CHECKOUT_TYPE'] = 'express';

		// set up some precheckout cart values
		$GLOBALS['ISC_CLASS_CART']->SetCartValues();

		// Begin by setting some defaults
		$GLOBALS['ExpressCheckoutSignedIn']		= 0;
		$GLOBALS['ExpressCheckoutDigitalOrder'] = 0;

		$GLOBALS['ExpressCheckoutHideAccountDetails'] 	 = 'display: none';
		$GLOBALS['ExpressCheckoutHideShippingAddress'] 	 = 'display: none';
		$GLOBALS['ExpressCheckoutHideShippingProviders'] = 'display: none';
		$GLOBALS['ExpressCheckoutHidePaymentDetails']	 = 'display: none';

		$checkoutSteps = array(
			'AccountDetails' => 1,
			'BillingAddress' => 2,
			'ShippingAddress' => 3,
			'ShippingProvider' => 4,
			'Confirmation' => 5,
			'PaymentDetails' => 6
		);

		// If the customer is signed in there are significantly fewer steps we need to complete
		if(CustomerIsSignedIn()) {
			$GLOBALS['ExpressCheckoutSignedIn'] = 1;
			// Remove the account details step
			unset($checkoutSteps['AccountDetails']);
		}
		// Customer isn't signed in - we need to show the account details section
		else {
			$GLOBALS['ExpressCheckoutHideAccountDetails'] = '';
		}

		// If guest checkout isn't enabled, we need to hide that section
		if(!GetConfig('GuestCheckoutEnabled')) {
			$GLOBALS['HideGuestCheckoutOptions'] = 'display: none';
		}
		else {
			$GLOBALS['HideRegisteredCheckoutOptions'] = 'display: none';
		}

		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$_SESSION['CART']['HASH'] = $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash();

		// Get a list of this customers billing/shipping addresses as straight up, the billing form will be shown
		$GLOBALS['SNIPPETS']['BillingAddressStepContents'] = $this->ExpressCheckoutChooseAddress('billing');
		$GLOBALS['SNIPPETS']['ShippingAddressStepContents'] = $this->ExpressCheckoutChooseAddress('shipping');

		if ($GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
			// this is a digital order
			$GLOBALS['ExpressCheckoutDigitalOrder'] = 1;
			// Remove the shipping address & providers section?
			unset($checkoutSteps['ShippingAddress']);
			unset($checkoutSteps['ShippingProvider']);
		} else {
			// this is a physical order - we need to ask for the shipping address
			$GLOBALS['ExpressCheckoutHideShippingAddress'] = '';
			$GLOBALS['ExpressCheckoutHideShippingProviders'] = '';

			/*
			if ($GLOBALS['ISC_CLASS_CART']->api->GetCartFreeShipping()) {
				// this physical order has free shipping, remove the provider checkout step
				unset($checkoutSteps['ShippingProvider']);
			} else {
				// this physical order does not have free shipping - ask for the shipping provider
				$GLOBALS['ExpressCheckoutHideShippingProviders'] = '';
			}
			*/
		}

		// Now calculate the number for each of the steps
		$step = 1;
		foreach($checkoutSteps as $name => $oldStep) {
			$GLOBALS['ExpressCheckoutStep'.$name] = $step;

			// If this is the first step then we need to show it by default
			if($step == 1) {
				$GLOBALS['CollapsedStepClass'.$name] = '';
			}
			else {
				$GLOBALS['CollapsedStepClass'.$name] = 'ExpressCheckoutBlockCollapsed';
			}
			++$step;
		}

		$GLOBALS['GoToStep'] = '';
		if(isset($_SESSION['CHECKOUT']['GoToCheckoutStep'])) {
			if(!CustomerIsSignedIn()) {
					$GLOBALS['GoToStep'] = "
						$('#checkout_type_guest').attr('checked', true);
						ExpressCheckout.ChangeStep('AccountDetails');
						ExpressCheckout.GuestCheckout();
					";
			}
			switch($_SESSION['CHECKOUT']['GoToCheckoutStep']) {

					case "BillingAddress":
						$GLOBALS['GoToStep'] .= "$('#ship_to_billing').attr('checked', false);";
						break;
					 case "ShippingProvider":
							$GLOBALS['GoToStep'] .= "
								ExpressCheckout.ChangeStep('BillingAddress');
								ExpressCheckout.ChooseBillingAddress();
							";
							break;
					default:
							$GLOBALS['GoToStep'] = "";
							break;
			}
		}

		/**
		 * ID's for the custom checkout field forms
		 */
		$GLOBALS['CustomCheckoutFormNewAccount'] = FORMFIELDS_FORM_ACCOUNT;
		$GLOBALS['CustomCheckoutFormBillingAddress'] = FORMFIELDS_FORM_BILLING;
		$GLOBALS['CustomCheckoutFormShippingAddress'] = FORMFIELDS_FORM_SHIPPING;

		/**
		 * Load up any form field JS event data and any validation lang variables
		 */
		$GLOBALS['FormFieldRequiredJS'] = $GLOBALS['ISC_CLASS_FORM']->buildRequiredJS();

		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName').' - '.GetLang('ExpressCheckout'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('checkout_express');
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		exit;
	}

	/**
	 * Generate the choose an address form for the express checkout for either a billing or shipping address.
	 *
	 * @param string The type of address fields to generate (either billing or shipping)
	 * @return string The generated address form.
	 */
	public function ExpressCheckoutChooseAddress($addressType, $buildRequiredJS=false)
	{
		$templateAddressType = ucfirst($addressType);

		$GLOBALS['AddressList'] = '';

		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');

		$GLOBALS['AddressType'] = $addressType;
		$GLOBALS['UpperAddressType'] = $templateAddressType;
		$GLOBALS['HideCreateAddress'] = 'display: none';
		$GLOBALS['HideChooseAddress'] = 'display: none';

		$GLOBALS['CreateAccountForm'] = '';
		$country_id = GetCountryIdByName(GetConfig('CompanyCountry'));

		$selectedCountry = GetConfig('CompanyCountry');
		$selectedState = 0;

		if ($addressType == 'shipping') {
			$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_SHIPPING);
			$formId = FORMFIELDS_FORM_SHIPPING;
		} else if (!CustomerIsSignedIn()) {
			$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_ACCOUNT);
			$fields += $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_BILLING);
			$formId = FORMFIELDS_FORM_ACCOUNT;
		} else {
			$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_BILLING);
			$formId = FORMFIELDS_FORM_BILLING;
		}

		// If the customer isn't signed in, then by default we show the create form
		if(!CustomerIsSignedIn() ) {
			$GLOBALS['HideCreateAddress'] = '';

			$address = array();
			if($addressType=='billing' && isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
				$address = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
			} else if($addressType=='shipping') {
				if(isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
					$address = $_SESSION['CHECKOUT']['SHIPPING_ADDRESS'];
				} else if(isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
					$address = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
				}
			}

			// if billing address is saved in session, use the session billing address to prefill the address form
			if(!empty($address)) {

				if(isset($address['shipcountry'])) {
					$selectedCountry = $address['shipcountry'];
				}
				if(isset($address['shipstateid'])) {
					$selectedState = $address['shipstateid'];
				}
				if(isset($address['shipcountryid'])) {
					$country_id = $address['shipcountryid'];
				}

				$addressMap = array(
					'EmailAddress' => 'shipemail',
					'FirstName' => 'shipfirstname',
					'LastName' => 'shiplastname',
					'CompanyName' => 'shipcompany',
					'AddressLine1' => 'shipaddress1',
					'AddressLine2' => 'shipaddress2',
					'City' => 'shipcity',
					'State' => 'shipstate',
					'Zip' => 'shipzip',
					'Phone' => 'shipphone'
				);

				$fieldMap = array();

				foreach (array_keys($fields) as $fieldId) {
					if ($fields[$fieldId]->record['formfieldprivateid'] == '') {
						continue;
					}

					$fieldMap[$fields[$fieldId]->record['formfieldprivateid']] = $fieldId;
				}

				foreach ($addressMap as $formField => $addressField) {
					if (!isset($address[$addressField]) || !isset($fieldMap[$formField])) {
						continue;
					}

					$fields[$fieldMap[$formField]]->SetValue($address[$addressField]);
				}
			}

			/**
			 * Turn off the 'leave password blank' label for the passwords if we are not
			 * logged in (creating a new account)
			 */
			foreach (array_keys($fields) as $fieldId) {
				if ($fields[$fieldId]->getFieldType() == 'password') {
					$fields[$fieldId]->setLeaveBlankLabel(false);
				}
			}
		}

		// If the customer is logged in, load up their existing addresses
		else {
			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
			$shippingAddresses = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerShippingAddresses();

			// If the customer doesn't have any addresses, show the creation form
			if(empty($shippingAddresses)) {
				$GLOBALS['HideChooseAddress'] = 'display: none';
				$GLOBALS['HideCreateAddress'] = '';
			}
			else {
				$GLOBALS['HideChooseAddress'] = '';
				$addressMap = array(
					'shipfullname',
					'shipcompany',
					'shipaddress1',
					'shipaddress2',
					'shipcity',
					'shipstate',
					'shipzip',
					'shipcountry'
				);

				foreach($shippingAddresses as $address) {
					$formattedAddress = '';
					foreach($addressMap as $field) {
						if(!$address[$field]) {
							continue;
						}
						$formattedAddress .= $address[$field] .', ';
					}
					$GLOBALS['AddressSelected'] = '';

					if(isset($_SESSION['CHECKOUT']['SelectAddress'])) {
						if($_SESSION['CHECKOUT']['SelectAddress'] == $address['shipid']) {
							$GLOBALS['AddressSelected'] = ' selected="selected"';
						}
					} else if(!$GLOBALS['AddressList']) {
						$GLOBALS['AddressSelected'] = ' selected="selected"';
					}

					$GLOBALS['AddressId'] = $address['shipid'];
					$GLOBALS['AddressLine'] = isc_html_escape(trim($formattedAddress, ', '));
					$GLOBALS['AddressList'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ExpressCheckoutAddress');
				}
			}
		}

		if($addressType == 'billing') {
			if(!CustomerIsSignedIn()) {
				$GLOBALS['CreateAccountForm'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ExpressCheckoutCreateAccount');
			}

			$GLOBALS['BillToAddressButton'] = GetLang('BillToThisAddress');
			if($GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
				$GLOBALS['UseAddressTitle'] = isc_html_escape(GetLang('BillToThisAddress'));
				$GLOBALS['HideShippingOptions'] = 'display: none';
			}
			else {
				$GLOBALS['UseAddressTitle'] = isc_html_escape(GetLang('BillAndShipToAddress'));
			}
			$GLOBALS['UseExistingAddress'] = GetLang('UseExistingBillingAddress');
			$GLOBALS['AddNewAddress'] = GetLang('UseNewBillingAddress');
			$GLOBALS['ShipToBillingName'] = 'ship_to_billing';
		}
		else {
			$GLOBALS['BillToAddressButton'] = GetLang('ShipToThisAddress');
			$GLOBALS['UseAddressTitle'] = isc_html_escape(GetLang('ShipToThisAddress'));
			$GLOBALS['UseExistingAddress'] = GetLang('UseExistingShippingAddress');
			$GLOBALS['AddNewAddress'] = GetLang('UseNewShippingAddress');
			$GLOBALS['ShipToBillingName'] = 'bill_to_shipping';
			$GLOBALS['HideShippingOptions'] = 'display: none';
		}

		// We need to loop here so we can get the field Id for the state
		$countryId = GetCountryIdByName($selectedCountry);
		$stateFieldId = 0;
		$countryFieldId = 0;
		foreach (array_keys($fields) as $fieldId) {
			if (strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'state') {
				$stateFieldId = $fieldId;
			} else if (strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'country') {
				$countryFieldId = $fieldId;
			}
		}

		// Compile the fields. Also set the country and state dropdowns while we are here
		$GLOBALS['CompiledFormFields'] = '';
		foreach (array_keys($fields) as $fieldId) {
			//	lowercase the formfieldprivateid for conditional matching below
			$formFieldPrivateId = strtolower($fields[$fieldId]->record['formfieldprivateid']);

			if ($formFieldPrivateId == 'country') {
				$fields[$fieldId]->setOptions(GetCountryListAsIdValuePairs());

				if ($selectedCountry !== '') {
					$fields[$fieldId]->setValue($selectedCountry);
				}

				/**
				 * This is the event handler for changing the states where a country is selected
				 */
				$fields[$fieldId]->addEventHandler('change', 'FormFieldEvent.SingleSelectPopulateStates', array('countryId' => $countryFieldId, 'stateId' => $stateFieldId));

			} else if ($formFieldPrivateId == 'state' && isId($countryId)) {
				$stateOptions = GetStateListAsIdValuePairs($countryId);
				if (is_array($stateOptions) && !empty($stateOptions)) {
					$fields[$fieldId]->setOptions($stateOptions);
				}
				else {
					// no states for our country, we need to mark this as not required
					$fields[$fieldId]->setRequired(false);
				}

			/**
			 * Put an event handler on the 'ship to this address' so we can change the button value if
			 * this is the billing form, else remove it as it is the shipping form
			 */
			} else if ($formFieldPrivateId == 'shiptoaddress') {

				if ($addressType == 'shipping' || $GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
					//	don't add the field to the form if the address type being added is a shipping address, or if the current cart has no shippable items
					continue;
				} else {
					$fields[$fieldId]->addEventHandler('change', 'FormFieldEvent.CheckBoxShipToAddress');
				}
			}

			/**
			 * Set the 'save address' and 'ship to' checkboxes to checked
			 */
			if ($formFieldPrivateId == 'savethisaddress' || $formFieldPrivateId == 'shiptoaddress') {
				$fields[$fieldId]->setValueByIndex(array(0));
			}

			$GLOBALS['CompiledFormFields'] .= $fields[$fieldId]->loadForFrontend() . "\n";
		}

		$GLOBALS['CompiledFormFieldJavascript'] = "\n\n" . $GLOBALS['ISC_CLASS_FORM']->buildRequiredJS(true);
		return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ExpressCheckoutChooseAddress');
	}

	/**
	 * Generate the order confirmation for the express checkout.
	 */
	public function GenerateExpressCheckoutConfirmation()
	{
		$GLOBALS['ISC_CLASS_CHECKOUT'] = GetClass('ISC_CHECKOUT');
		$GLOBALS['ISC_CLASS_CHECKOUT']->BuildOrderConfirmation();
		$confirmation = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ExpressCheckoutConfirmation');
		return $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true, $confirmation);
	}

	/**
	 * Process a ping back from a particular payment gateway.
	 */
	private function GatewayPing()
	{
		if(!isset($_REQUEST['provider'])) {
			exit;
		}

		// Invalid checkout provider passed
		if(!GetModuleById('checkout', $provider, $_REQUEST['provider'])) {
			exit;
		}

		// This gateway doesn't support a ping back/notification
		if(!method_exists($provider, 'ProcessGatewayPing')) {
			exit;
		}

		// Call the process method
		$provider->ProcessGatewayPing();
		exit;
	}

	/**
	*	Process details for a particular payment gateway inline.
	*/
	private function ProcessOrderPayment()
	{
		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedInThroughOrder()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');

		// The contents of the cart have changed since we last started the checkout process, need to go back to the first step of the checkout
		if(isset($_SESSION['CART']['HASH']) && $_SESSION['CART']['HASH'] != $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash()) {
			unset($_SESSION['CHECKOUT']);
			$_SESSION['CART_CHANGED'] = true;
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// ensure products are in stock
		$this->CheckStockLevels();

		$order_token = "";

		if(isset($_COOKIE['SHOP_ORDER_TOKEN'])) {
			$order_token = $_COOKIE['SHOP_ORDER_TOKEN'];
		}

		// If the order token is empty then something has gone wrong.
		if($order_token == '') {
			@ob_end_clean();
			header("Location: ".$GLOBALS['ShopPathSSL']."/concluir.php?action=confirm_order");
			die();
		}

		// Load the pending order
		$orders = LoadPendingOrdersByToken($order_token);

		if(!is_array($orders)) {
			@ob_end_clean();
			header("Location: ".$GLOBALS['ShopPathSSL']."/concluir.php?action=confirm_order");
			die();
		}

		if ($orders['status'] != ORDER_STATUS_INCOMPLETE) {
			// has this order already been completed? redirect to finish order
			@ob_end_clean();
			header("Location: ".$GLOBALS['ShopPathSSL']."/formadepagamento.php");
			die();
		}

		// Get the payment module
		if(!GetModuleById('checkout', $provider, $orders['paymentmodule'])) {
			@ob_end_clean();
			header("Location: ".$GLOBALS['ShopPathSSL']."/concluir.php?action=confirm_order");
			die();
		}

		$provider->SetOrderData($orders);

		if(isset($_SESSION['CHECKOUT']['ProviderListHTML']) && method_exists($provider, 'DoExpressCheckoutPayment')) {
			$provider->DoExpressCheckoutPayment();
			die();
		}


		// Does this method have it's own processing method?
		if(method_exists($provider, "ProcessPaymentForm")) {
			$result = $provider->ProcessPaymentForm();
			if($result) {
				$paymentStatus = $provider->GetPaymentStatus();
				$orderStatus = GetOrderStatusFromPaymentStatus($paymentStatus);
				if(CompletePendingOrder($order_token, $orderStatus)) {
					// Everything is fine, send the customer to the thank you page.
					@ob_end_clean();
					header("Location: ".$GLOBALS['ShopPathSSL']."/formadepagamento.php");
					die();
				}
			}

			// Otherwise there was an error
			$this->ShowPaymentForm($provider);
		}

		// If we're still here then something from the above has gone wrong. Show the confirm page again
		@ob_end_clean();
		header("Location: ".$GLOBALS['ShopPathSSL']."/concluir.php?action=confirm_order");
		die();
	}

	/**
	*	Show the order confirmation page before redirecting to the payment provider
	*/
	private function ConfirmOrder()
	{
		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');

		// Do we have a billing address?
		if(!isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
				@ob_end_clean();
				header(sprintf("location:%s/concluir.php?action=choose_billing_address", $GLOBALS['ShopPath']));
				die();
		}

		// The contents of the cart have changed since we last started the checkout process, need to go back to the first step of the checkout
		if(isset($_SESSION['CART']['HASH']) && $_SESSION['CART']['HASH'] != $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash()) {
			unset($_SESSION['CHECKOUT']);
			$_SESSION['CART_CHANGED'] = true;
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// ensure products are in stock
		$this->CheckStockLevels();

		// If this order is not a digital order and doesn't have a shipping address, we need to send them back for that
		if (!$GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
			if($this->GetOrderShippingAddresses() === false) {
				@ob_end_clean();
				header(sprintf("location:%s/concluir.php?action=choose_shipping_address", $GLOBALS['ShopPath']));
				die();
			}

			// Shipping also needs to be configured here. If it hasn't been, we call the shipping quote page
			if(!isset($_SESSION['CHECKOUT']['SHIPPING'])) {
				@ob_end_clean();
				header(sprintf("location:%s/concluir.php?action=choose_shipper", $GLOBALS['ShopPath']));
				die();
			}
		}

		$GLOBALS['EnterCouponCode'] = isc_html_escape(GetLang('EnterCouponCode'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName') . " - " . GetLang('ConfirmYourOrder'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("checkout_confirm");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	* Remove an applied gift certificate from this order.
	*/
	private function RemoveGiftCertificate()
	{
		if (isset($_SESSION['CART']['GIFTCERTIFICATES']) && isset($_SESSION['CART']['GIFTCERTIFICATES'][$_REQUEST['giftcertificateid']])) {
			unset($_SESSION['CART']['GIFTCERTIFICATES'][$_REQUEST['giftcertificateid']]);
		}

		$GLOBALS['CheckoutSuccessMsg'] = GetLang('GiftCertificateRemovedFromCart');
		$this->ConfirmOrder();
	}


	/**
	*	Save the selected shipping provider's details as a cookie
	*/
	private function SaveBillingAddress()
	{
		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// If the customer isn't signed in then they've just entered an address that we need to validate
		if(!CustomerIsSignedIn()) {
			$errors = array();
			// An invalid address was entered, show the form again
			$addressDetails = $this->ValidateGuestCheckoutAddress('billing', $errors);
			if(!$addressDetails) {
				$this->ChooseBillingAddress($errors);
				return;
			}
		}
		else {
			// We've just selected an address
			if(isset($_GET['address_id'])) {
				$addressDetails = (int)$_GET['address_id'];
			}
		}

		// There was a problem saving the selected billing address
		if(!$this->SetOrderBillingAddress($addressDetails)) {
			$this->ChooseBillingAddress();
			return;
		}

		if(!CustomerIsSignedIn()) {
			$emailField = $GLOBALS['ISC_CLASS_FORM']->getFormField(FORMFIELDS_FORM_ACCOUNT, '1', '', true);
			$_SESSION['ACCOUNT_EMAIL'] = $emailField->getValue();
			$_SESSION['ACCOUNT_FIRSTNAME'] = $addressDetails['shipfirstname'];
			$_SESSION['ACCOUNT_LASTNAME'] = $addressDetails['shiplastname'];
		}

		// If we're automatically creating accounts for customers then we need to save those details too
		if(!CustomerIsSignedIn() && GetConfig('GuestCheckoutCreateAccounts')) {
			$password = substr(md5(uniqid(true)), 0, 8);
			$autoAccount = 1;
			$_SESSION['CHECKOUT']['CREATE_ACCOUNT'] = 1;
			$_SESSION['CHECKOUT']['ACCOUNT_DETAILS'] = array(
				'email' => $emailField->getValue(),
				'password' => $password,
				'firstname' => $addressDetails['shipfirstname'],
				'lastname' => $addressDetails['shiplastname'],
				'company' => '',
				'phone' => $addressDetails['shipphone'],
				'autoAccount' => $autoAccount
			);
		}

		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		if ($GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
			@ob_end_clean();
			header(sprintf("location:%s/concluir.php?action=confirm_order", $GLOBALS['ShopPath']));
		}
		else {
			// Are we shipping to the same address?
			if(isset($_POST['ship_to_billing'])) {
				if(!$this->SetOrderShippingAddress($addressDetails)) {
					$this->ChooseShippingAddress();
					return;
				}

				// Now they need to choose the shipping provider for their order
				@ob_end_clean();
				header("Location: ".GetConfig('ShopPath')."/concluir.php?action=choose_shipper");
				exit;
			}

			// Otherwise, we just move to the next step
			@ob_end_clean();
			header(sprintf("location:%s/concluir.php?action=choose_shipping_address", $GLOBALS['ShopPath']));
		}
		exit;
	}

	/**
	 * Get the billing address of an order.
	 *
	 * @param boolean Set to true to extract the full address for a customer that's using an existing address.
	 * @return array The associated address.
	 */
	public function GetBillingAddress($getFullAddress=true)
	{
		if(!isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
			return false;
		}

		$address = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
		if (isId($address) && $getFullAddress) {
			$GLOBALS['ISC_CLASS_ACCOUNT'] = GetClass('ISC_ACCOUNT');
			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
			$address = $GLOBALS['ISC_CLASS_ACCOUNT']->GetShippingAddress((int)$address, $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId());
		}
		return $address;
	}

	/**
	 * Set the billing address of an order either based on a passed ID or an array of billing details.
	 *
	 * @param mixed Either a billing address ID or an array containing the billing address.
	 * @return boolean True if successful, false if there was an error.
	 */
	public function SetOrderBillingAddress($address)
	{
		$GLOBALS['ISC_CLASS_ACCOUNT'] = GetClass('ISC_ACCOUNT');
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');

		if(is_array($address)) {
			$_SESSION['CHECKOUT']['BILLING_ADDRESS'] = $address;
		}
		// Otherwise, we were passed the ID of an address so let's validate it
		else {
			$billingAddress = $GLOBALS['ISC_CLASS_ACCOUNT']->GetShippingAddress((int)$address, $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId());
			if($billingAddress) {
				$_SESSION['CHECKOUT']['BILLING_ADDRESS'] = (int)$address;

				if (isId($billingAddress['shipformsessionid'])) {
					$prevSessData = $GLOBALS['ISC_CLASS_FORM']->getSavedSessionData($billingAddress['shipformsessionid'], array(), FORMFIELDS_FORM_ADDRESS);
					$this->SetCustomFieldData('billing', $prevSessData);
				}
			}
			// An invalid address was passed
			else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Set the custom fields data for this order
	 *
	 * Method will save any of the custom field data into the session to be stored later on
	 *
	 * @access public
	 * @param string $type The type of custom data (customer, billing or shipping)
	 * @param array $data The custom field data array. The data array keys are the form field IDs
	 * @return bool TRUE if the $data array is correct and saved in the session, FALSE if the $data array is invalid
	 */
	public function SetCustomFieldData($type, $data)
	{
		if ($type == '' || !is_array($data)) {
			return false;
		}

		if (!isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS'])) {
			$_SESSION['CHECKOUT']['CUSTOM_FIELDS'] = array();
		}

		if (!isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS'][$type])) {
			$_SESSION['CHECKOUT']['CUSTOM_FIELDS'][$type] = array();
		}

		/**
		 * Make sure each array key is an ID
		 */
		foreach ($data as $key => $val) {
			if (!isId($key)) {
				continue;
			}

			$_SESSION['CHECKOUT']['CUSTOM_FIELDS'][$type][$key] = $val;
		}

		return true;
	}

	/**
	 * Set the shipping address of an order either based on a passed ID or an array of shipping details.
	 *
	 * @param mixed Either a shipping address ID or an array containing the shipping address.
	 * @return boolean True if successful, false if there was an error.
	 */
	public function SetOrderShippingAddress($address)
	{
		$GLOBALS['ISC_CLASS_ACCOUNT'] = GetClass('ISC_ACCOUNT');
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');

		unset($_SESSION['CHECKOUT']['SPLIT_SHIPPING']);
		unset($_SESSION['CHECKOUT']['IS_SPLIT_SHIPPING']);
		unset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS']);

		if(is_array($address)) {
			$_SESSION['CHECKOUT']['SHIPPING_ADDRESS'] = $address;
			$shippingAddress = $address;
		}
		// Otherwise, we were passed the ID of an address so let's validate it
		else {
			if($GLOBALS['ISC_CLASS_ACCOUNT']->GetShippingAddress((int)$address, $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId())) {
				$_SESSION['CHECKOUT']['SHIPPING_ADDRESS'] = (int)$address;
				$shippingAddress = $GLOBALS['ISC_CLASS_ACCOUNT']->GetShippingAddress((int)$address, $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId());

				if (isId($shippingAddress['shipformsessionid'])) {
					$prevSessData = $GLOBALS['ISC_CLASS_FORM']->getSavedSessionData($shippingAddress['shipformsessionid'], array(), FORMFIELDS_FORM_ADDRESS);
					$this->SetCustomFieldData('shipping', $prevSessData);
				}
			}
			// An invalid address was passed
			else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Set the shipping provider/method/cost of an order based on the passed shipping provider ID.
	 *
	 * @param int The vendor ID of the items to set the shipping provider for.
	 * @param int The address ID of the items to set the shipping provider for.
	 * @param int The shipping provider ID.
	 * @return boolean True if successful, false if there was an error.
	 */
	public function SetOrderShippingProvider($vendorId, $addressId, $providerId)
	{
		if(isset($_SESSION['CHECKOUT']['SHIPPING_QUOTES'][$vendorId][$addressId][$providerId])) {
			$providerData = $_SESSION['CHECKOUT']['SHIPPING_QUOTES'][$vendorId][$addressId][$providerId];

			$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId] = array(
				'COST' => $providerData['price'],
				'MODULE' => $providerData['module'],
				'PROVIDER' => $providerData['description']
			);
			if(isset($providerData['handling'])) {
				$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId]['HANDLING'] = $providerData['handling'];
			}
			else {
				$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId]['HANDLING'] = 0;
			}

			return true;
		}

		// Obviously things didn't go to plan - so let's return false.
		return false;
	}

	/**
	*	Save the selected shipping provider's details as a cookie
	*/
	private function SaveShippingProvider()
	{
		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// The contents of the cart have changed since we last started the checkout process, need to go back to the first step of the checkout
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		if(isset($_SESSION['CART']['HASH']) && $_SESSION['CART']['HASH'] != $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash()) {
			unset($_SESSION['CHECKOUT']);
			$_SESSION['CART_CHANGED'] = true;
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// ensure products are in stock
		$this->CheckStockLevels();

		$cartContent = $this->BreakdownCartByAddressVendor();
		foreach($cartContent as $vendorId => $addresses) {
			foreach(array_keys($addresses) as $addressId) {
				if(!isset($_POST['shipping_provider'][$vendorId][$addressId]) || !$this->SetOrderShippingProvider($vendorId, $addressId, $_POST['shipping_provider'][$vendorId][$addressId])) {
					// Bad provider data
					@ob_end_clean();
					if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
						header("Location: ".GetConfig('ShopPath').'/concluir.php');
					}
					else {
						header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
					}
					die();
				}
			}
		}

		// We've saved the shipping provider - to the next step we go!
		@ob_end_clean();
		header(sprintf("location: %s/concluir.php?action=confirm_order", $GLOBALS['ShopPath']));
		die();
	}

	/**
	 * Validate an incoming shipping/billing address.
	 *
	 * @param string The type of address to validate (billing or shipping)
	 * @param array An array of errors, passed by reference - if there are any
	 * @return array An array of information about the address if valid.
	 */
	public function ValidateGuestCheckoutAddress($type, &$errors)
	{
		$errors = array();

		// for the billing address we need to validate the email address
		if($type == 'billing') {
			$emailField = $GLOBALS['ISC_CLASS_FORM']->getFormField(FORMFIELDS_FORM_ACCOUNT, '1', '', true);
			$email = $emailField->getValue();

			if(!isset($email) || !is_email_address($email)) {
				$errors[] = GetLang('AccountEnterValidEmail');
				return false;
			}

			// Check that this email address isn't already in use by a customer
			$customer = GetClass('ISC_CUSTOMER');
			if($customer->AccountWithEmailAlreadyExists($email)) {
				$errors[] = sprintf(GetLang('CheckoutEmailAddressInUse'), GetConfig('ShopPath').'/login.php');
				return false;
			}
		}

		require_once(ISC_BASE_PATH . '/lib/addressvalidation.php');

		// parse the form fields and validate them
		$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_ADDRESS, true);

		/**
		 * Validate the field input
		 */
		$errmsg = '';
		if (!validateFieldData($fields, $errmsg)) {
			$errors[] = $errmsg;
			return false;
		}

		$ShippingAddress = parseFieldData($fields);

		if (isset($ShippingAddress['shipfirstname']) && isset($ShippingAddress['shipaddress1'])) {
			return $ShippingAddress;
		}
		else {
			if ($type == "billing") {
				$errors[] = GetLang('UnableSaveOrderBillingAddress');
			}
			else {
				$errors[] = GetLang('UnableSaveOrderShippingAddress');
			}
			return false;
		}
	}

	/**
	 * Save one or more selected addresses for split-shipping.
	 */
	private function SaveMultipleShippingAddresses()
	{
		//  Split shipping only works for signed in users
		if(!CustomerIsSignedIn()) {
			@ob_end_clean();
			header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			exit;
		}

		// If split shipping is not available, take the customer back to the shipping address selection page
		if(!gzte11(ISC_MEDIUMPRINT) || !GetConfig('MultipleShippingAddresses') || !isset($_POST['multiaddress'])) {
			@ob_end_clean();
			header("Location: ".GetConfig('ShopPath').'/concluir.php?action=choose_shipping_address&type=single');
			exit;
		}

		$silent = false;
		if(isset($_POST['addAnotherAddress'])) {
			$silent = true;
		}

		// Get a list of all shipping addresses for this customer
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
		$addresses = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerShippingAddresses();

		$_SESSION['CHECKOUT']['SPLIT_SHIPPING'] = array();

		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$cartProducts = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();
		foreach($cartProducts as $cartItemId => $product) {
			// If this isn't a physical item, skip it
			if($product['data']['prodtype'] != PT_PHYSICAL) {
				continue;
			}

			// If we don't have an address for this product, we need to throw back to the address
			// selection page, as they've done something dodgy.
			for($i = 1; $i <= $product['quantity']; ++$i) {
				$id = $cartItemId.'_'.$i;
				if(!isset($_POST['multiaddress'][$id]) || !isset($addresses[$_POST['multiaddress'][$id]]) && $silent == false) {
					@ob_end_clean();
					header("Location: ".GetConfig('ShopPath').'/concluir.php?action=choose_shipping_address&type=single');
					exit;
				}
				// Otherwise, the address is valid, so store it in the array
				$addressId = $addresses[$_POST['multiaddress'][$id]]['shipid'];
				$this->SetOrderSplitShippingAddress($cartItemId, $addressId);
			}
		}

		if(empty($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {
			unset($_SESSION['CHECKOUT']['SPLIT_SHIPPING']);
		}

		// Do we need to go to the add address page?
		if(isset($_POST['addAnotherAddress'])) {
			$this_page = urlencode("concluir.php?action=choose_shipping_address");
			@ob_end_clean();
			header(sprintf("Location: %s/areadoclientewebshop.php?action=add_shipping_address&from=%s", $GLOBALS['ShopPath'], $this_page));
			die();
		}

		// OK, the shipping method has been set, move on to the next step
		@ob_end_clean();
		header("Location: ".GetConfig('ShopPath').'/concluir.php?action=choose_shipper');
		exit;
	}

	/**
	 * Set the address for a specific item in the shopping cart, if we're using
	 * multiple shipping addresses for an order.
	 *
	 * @param int The ID of the item in the cart.
	 * @param int The shipping address we'll be sending this item to.
	 * @return boolean True if successfully set.
	 */
	public function SetOrderSplitShippingAddress($cartItemId, $addressId)
	{
		if(isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'][$addressId][$cartItemId])) {
			++$_SESSION['CHECKOUT']['SPLIT_SHIPPING'][$addressId][$cartItemId];
		}
		else {
			$_SESSION['CHECKOUT']['SPLIT_SHIPPING'][$addressId][$cartItemId] = 1;
		}
		$_SESSION['CHECKOUT']['IS_SPLIT_SHIPPING'] = true;
	//	$_SESSION['CHECKOUT']['SHIPPING_ADDRESS'] = 'split';
		return true;
	}

	/**
	 * Return an array containing all of the different shipping addresses for this order.
	 *
	 * @return array An array of shipping addresses.
	 */
	public function GetOrderShippingAddresses()
	{
		static $addressCache = array();
		if(!empty($addressCache)) {
			return $addressCache;
		}

		$addresses = array();
		if(isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {
			$addressIds = array_map('intval', array_keys($_SESSION['CHECKOUT']['SPLIT_SHIPPING']));
			$query = "
				SELECT *
				FROM [|PREFIX|]shipping_addresses
				WHERE shipid IN (".implode(',', $addressIds).")
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($address = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$addresses[$address['shipid']] = $address;
			}
		}
		// If the saved address is an array, this is something the customer has entered themselves
		else if(isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS']) && is_array($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
			$addresses[0] = $_SESSION['CHECKOUT']['SHIPPING_ADDRESS'];
		}
		// Otherwise, we need to fetch the address
		else if(isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
			$GLOBALS['ISC_CLASS_ACCOUNT'] = GetClass('ISC_ACCOUNT');
			$shippingDetails = $GLOBALS['ISC_CLASS_ACCOUNT']->GetShippingAddress($_SESSION['CHECKOUT']['SHIPPING_ADDRESS']);
			$addresses[$shippingDetails['shipid']] = $shippingDetails;
		}
		$addressCache = $addresses;

		if(empty($addressCache)) {
			return false;
		}

		return $addressCache;
	}

	/**
	 * Breakdown all of the items in the shopping cart by both their shipping address
	 * and the vendor.
	 *
	 * @return array An array of all of the products, broken down by shipping address and vendor.
	 */
	public function BreakdownCartByAddressVendor()
	{
		$productList = array();

		$cart = GetClass('ISC_CART');
		$cartProducts = $cart->api->GetProductsInCart();

		$splitShipping = array();
		if(isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {
			foreach($_SESSION['CHECKOUT']['SPLIT_SHIPPING'] as $address => $products) {
				foreach($products as $productId => $quantity) {
					$splitShipping[$productId][$address] = $quantity;
				}
			}
		}
		// Everything is going to the one address, much easier
		else {
			if(!isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS']) || is_array($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
				$shippingAddress = 0;
			}
			else {
				$shippingAddress = @$_SESSION['CHECKOUT']['SHIPPING_ADDRESS'];
			}
		}
		$vendorIds = $this->GetCheckoutVendorIds();

		foreach($cartProducts as $cartItemId => $product) {
			if(!gzte11(ISC_HUGEPRINT) || !isset($product['data']['prodvendorid'])) {
				$vendorId = 0;
			}
			else {
				$vendorId = $product['data']['prodvendorid'];
			}
			if(isset($splitShipping[$cartItemId])) {
				foreach($splitShipping[$cartItemId] as $address => $quantity) {
					// Override the qantity with the amount that's going to this address
					$product['quantity'] = $quantity;
					$productList[$vendorId][$address][$cartItemId] = $product;
				}
			}
			// Everything is going to the one address, so just add the product in
			else {
				// For digital products
				if(!isset($shippingAddress)) {
					$shippingAddress = 0;
				}
				$productList[$vendorId][$shippingAddress][$cartItemId] = $product;
			}
		}
		return $productList;
	}

	/**
	 * Get all of the available shipping methods for all of the items in the cart.
	 * The returned methods will be broken down by vendor and by shipping address.
	 *
	 * @return array An array of shipping methods for all items in the cart.
	 */
	public function GetCheckoutShippingMethods()
	{
		$shippingQuotes = array();
		$cart = GetClass('ISC_CART');
		$shippingAddresses = $this->GetOrderShippingAddresses();
		$cartContent = $this->BreakdownCartByAddressVendor();
		foreach($cartContent as $vendorId => $addresses) {
			foreach($addresses as $addressId => $products) {
				if(!isset($shippingAddresses[$addressId])) {
					continue;
				}

				$address = $shippingAddresses[$addressId];
				$methods = $cart->GetAvailableShippingMethodsForProducts($address, $products, $vendorId);
				$shippingQuotes[$vendorId][$addressId] = array(
					'quotes' => $methods,
					'items' => $products
				);
			}
		}

		return $shippingQuotes;
	}

	/**
	 *	Show a list of available shipping providers and let the customer choose the one to use
	 */
	private function ChooseShippingProvider()
	{
		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// The contents of the cart have changed since we last started the checkout process, need to go back to the first step of the checkout
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		if(isset($_SESSION['CART']['HASH']) && $_SESSION['CART']['HASH'] != $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash()) {
			unset($_SESSION['CHECKOUT']);
			$_SESSION['CART_CHANGED'] = true;
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// ensure products are in stock
		$this->CheckStockLevels();

		// Setup the default shipping error message
		$GLOBALS['ShippingError'] = GetLang("NoShippingProvidersError");

		$addressDetails = 0;

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			// If the customer isn't signed in then they've just entered an address that we need to validate
			if(!CustomerIsSignedIn()) {
				$errors = array();
				// An invalid address was entered, show the form again
				$addressDetails = $this->ValidateGuestCheckoutAddress('shipping', $errors);
				if(!$addressDetails) {
					$this->ChooseShippingAddress($errors);
					return;
				}
			}
		}

		// We've just selected an address
		if(isset($_GET['address_id'])) {
			$addressDetails = (int)$_GET['address_id'];
		}

		if($addressDetails !== 0) {
			// There was a problem saving the selected shipping address
			if(!$this->SetOrderShippingAddress($addressDetails)) {
				@ob_end_clean();
				if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
					header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
				}
				else {
					header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
				}
				die();
			}
		}

		$shippingAddress = 0;
		if(!isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {
			// If the saved address is an array, this is something the customer has entered themselves
			if(is_array($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
				$shippingDetails = $_SESSION['CHECKOUT']['SHIPPING_ADDRESS'];
			}
			// Otherwise, we need to fetch the address
			else if(isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
				$GLOBALS['ISC_CLASS_ACCOUNT'] = GetClass('ISC_ACCOUNT');
				$shippingDetails = $GLOBALS['ISC_CLASS_ACCOUNT']->GetShippingAddress($_SESSION['CHECKOUT']['SHIPPING_ADDRESS']);
			}

			// Take them to add a shipping address
			if(!$shippingDetails) {
				$this_page = urlencode("concluir.php?action=checkout");
				@ob_end_clean();
				header(sprintf("Location: %s/areadoclientewebshop.php?action=add_shipping_address&from=%s", $GLOBALS['ShopPath'], $this_page));
				die();
			}
		}

		if(!isset($_SESSION['CHECKOUT']['SUBTOTAL_COST'])) {
			$GLOBALS['ISC_CLASS_CART']->SetCartValues();
			// Reload this page
			header('Location: '.GetConfig('AppPath').'/concluir.php?action=choose_shipper');
			exit;
		}

		// Now we have the zone, what available shipping methods do we have?
		$availableMethods = $this->GetCheckoutShippingMethods();

		$hideItemList = false;
		if(count($availableMethods) == 1 && count(current($availableMethods)) == 1) {
			$GLOBALS['HideVendorTitle'] = 'display: none';
			$GLOBALS['HideVendorItems'] = 'display: none';
			$hideItemList = true;
		}

		$GLOBALS['HideNoShippingProviders'] = 'none';
		$GLOBALS['ShippingQuotes'] = '';

		$orderShippingAddresses = $this->GetOrderShippingAddresses();


		$vendors = $GLOBALS['ISC_CLASS_CART']->api->GetCartVendorIds();

		foreach($vendors as $i => $vendorId) {
			$GLOBALS['VendorId'] = $vendorId;
			if($vendorId != 0) {
				$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');
				$vendor = $vendorCache[$vendorId];
				$GLOBALS['VendorName'] = isc_html_escape($vendor['vendorname']);
			}
			else {
				$GLOBALS['VendorName'] = GetConfig('StoreName');
			}

			$shippingDestinations = $availableMethods[$vendorId];
			if(count($shippingDestinations) == 1 && !isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {
				$GLOBALS['HideAddressLine'] = 'display: none';
			}
			else {
				$GLOBALS['HideAddressLine'] = '';
			}

			if(count($vendors) == 1 && $vendorId == 0) {
				$GLOBALS['HideVendorTitle'] = 'display: none';
			}

			$hasTransit = false;

			foreach($shippingDestinations as $addressId => $shippingInfo) {

				if(isset($vendors[$i+1]) || isset($shippingDestinations[$addressId+1])) {
					$GLOBALS['HideHorizontalRule'] = '';
				}
				else {
					$GLOBALS['HideHorizontalRule'] = 'display: none';
				}

				$GLOBALS['AddressId'] = $addressId;
				// If no methods are available, this order can't progress so show an error
				if(empty($shippingInfo['quotes'])) {
					$GLOBALS['HideNoShippingProviders'] = '';
					$GLOBALS['HideShippingProviderList'] = 'none';
					$hideItemList = false;
				}

				$GLOBALS['ItemList'] = '';
				if(!$hideItemList) {
					foreach($shippingInfo['items'] as $product) {
						// Only show physical items
						if($product['data']['prodtype'] != PT_PHYSICAL) {
							continue;
						}
						$GLOBALS['ProductQuantity'] = $product['quantity'];
						$GLOBALS['ProductName'] = isc_html_escape($product['product_name']);

						$GLOBALS['HideGiftWrapping'] = 'display: none';
						$GLOBALS['HideGiftMessagePreview'] = 'display: none';
						$GLOBALS['GiftWrappingName'] = '';
						$GLOBALS['GiftMessagePreview'] = '';
						if(isset($product['wrapping']['wrapname'])) {
							$GLOBALS['HideGiftWrapping'] = '';
							$GLOBALS['GiftWrappingName'] = isc_html_escape($product['wrapping']['wrapname']);
							if(isset($product['wrapping']['wrapmessage'])) {
								if(isc_strlen($product['wrapping']['wrapmessage']) > 30) {
									$product['wrapping']['wrapmessage'] = substr($product['wrapping']['wrapmessage'], 0, 27).'...';
								}
								$GLOBALS['GiftMessagePreview'] = isc_html_escape($product['wrapping']['wrapmessage']);
								if($product['wrapping']['wrapmessage']) {
									$GLOBALS['HideGiftMessagePreview'] = '';
								}
							}
						}


						$GLOBALS['ItemList'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ShippingQuoteProduct');
					}
				}

				// If no methods are available, this order can't progress so show an error
				if(empty($shippingInfo['quotes'])) {
					break;
				}

				if(!$GLOBALS['HideAddressLine']) {
					$address = $orderShippingAddresses[$addressId];
					$addressLine = array(
						$address['shipfirstname'].' '.$address['shiplastname'],
						$address['shipcompany'],
						$address['shipaddress1'],
						$address['shipaddress2'],
						$address['shipcity'],
						$address['shipstate'],
						$address['shipzip'],
						$address['shipcountry']
					);

					// Please see self::GenerateShippingSelect below.
					$addressLine = array_filter($addressLine, array($this, 'FilterAddressFields'));
					$GLOBALS['AddressLine'] = isc_html_escape(implode(', ', $addressLine));
				}
				else {
					$GLOBALS['AddressLine'] = '';
				}

				// Now build a list of the actual available quotes
				$GLOBALS['ShippingProviders'] = '';
				foreach($shippingInfo['quotes'] as $quoteId => $method) {
					$GLOBALS['ShippingProvider'] = isc_html_escape($method['description']);
					$GLOBALS['ShippingPrice'] = CurrencyConvertFormatPrice($method['price']);
					$GLOBALS['ShipperId'] = $quoteId;
					$GLOBALS['ShippingData'] = $GLOBALS['ShipperId'];

					if(isset($method['transit'])) {
							$hasTransit = true;

						$days = $method['transit'];

						if ($days == 0) {
							$transit = GetLang("SameDay");
						}
						else if ($days == 1) {
							$transit = GetLang('NextDay');
						}
						else {
							$transit = sprintf(GetLang('Days'), $days);
						}

						$GLOBALS['TransitTime'] = $transit;
						$GLOBALS['TransitTime'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingTransitTime');
					}
					else {
						$GLOBALS['TransitTime'] = "";
					}
					$GLOBALS['ShippingProviders'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("ShippingProviderItem");
				}

				// Add it to the list
				$GLOBALS['ShippingQuotes'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ShippingQuote');
				$_SESSION['CHECKOUT']['SHIPPING_QUOTES'][$vendorId][$addressId] = $shippingInfo['quotes'];
			}
		}

		if ($hasTransit) {
			$GLOBALS['DeliveryDisclaimer'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingDeliveryDisclaimer');
		}

		// Show the list of available shipping providers
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName') . " - " . GetLang('ChooseShippingProvider'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("checkout_shipper");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	 * Show the page allowing a customer to choose the shipping address for their order.
	 *
	 * @param array Optionally, an array of errors that have occurred and need to be shown.
	 */
	private function ChooseShippingAddress($errors = array())
	{
		// If we're coming here from a post request and we're not logged in then we've just chosen how we're checking out
		if(empty($errors) && $_SERVER['REQUEST_METHOD'] == "POST" && !CustomerIsSignedIn()) {

			// Are we logging in?
			if(isset($_REQUEST['login_email'])) {
				$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
				if(!$GLOBALS['ISC_CLASS_CUSTOMER']->CheckLogin(true)) {
					@ob_end_clean();
					header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout&bad_login=1');
					exit;
				}
			}
			// Perhaps we've chosen to create an account?
			else if(isset($_REQUEST['checkout_type']) && $_REQUEST['checkout_type'] == 'register') {
				@ob_end_clean();
				header("Location: ".GetConfig('ShopPath').'/login.php?action=create_account&checking_out=yes');
				exit;
			}
			// Otherwise, we're trying to checkout as a guest
		}

		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// Set a unique identifier for the items in this cart
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$_SESSION['CART']['HASH'] = $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash();

		$GLOBALS['ISC_CLASS_CART']->SetCartValues();
		// If it's an order with only intangible products we can skip this step
		if ($GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
			// Skip this step because all products are downloadable
			@ob_end_clean();
			header(sprintf("Location: %s/concluir.php?action=confirm_order", $GLOBALS['ShopPath']));
			die();
		}

		$GLOBALS['HideErrors'] = 'display: none';
		if(!empty($errors)) {
			$GLOBALS['ErrorMessage'] = implode('<br />', $errors);
			$GLOBALS['SavedAddress'] = $_POST;
			$GLOBALS['HideIntro'] = 'display: none';
			$GLOBALS['HideErrors'] = '';
		}
		else if(isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS']) && is_array($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
			$GLOBALS['SavedAddress'] = $_SESSION['CHECKOUT']['SHIPPING_ADDRESS'];
		}

		$GLOBALS['FromURL'] = urlencode("concluir.php?action=choose_shipping_address");
		$GLOBALS['ShipAddressButtonText'] = isc_html_escape(GetLang('ShipToThisAddress'));
		$GLOBALS['ShippingFormAction'] = "choose_shipper";

		$products = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();

		// If the cart is empty, take them back to it
		if ($GLOBALS['ISC_CLASS_CART']->api->GetNumProductsInCart() == 0) {
			@ob_end_clean();
			header(sprintf("Location: %s/compras.php", $GLOBALS['ShopPath']));
			die();
		}

		// If the customer isn't signed in then they're performing a guest checkout so they don't see a list of addresses, but actually
		// the shipping address form
		$GLOBALS['HidePanels'][] = 'ChooseBillingAddress';

		if(!CustomerIsSignedIn()) {
			$GLOBALS['HidePanels'][] = 'ChooseShippingAddress';
			$GLOBALS['HideShippingOptions'] = 'display: none';
			$GLOBALS['CreateAccountForm'] = '';

			$GLOBALS['CheckoutShippingTitle'] = GetLang('ShippingAddress');
			$GLOBALS['CheckoutShippingIntro'] = GetLang('EnterShippingAddressBelow');
		}
		else {
			// Hide the address entry panel
			$GLOBALS['HidePanels'][] = 'CheckoutNewAddressForm';

			// Do they have a shipping address stored in the system?
			// If not we will ask them to create one

			if ($this->GetNumShippingAddresses() == 0) {
				// Take them to add a shipping address
				$this_page = urlencode("concluir.php?action=checkout");
				@ob_end_clean();
				header(sprintf("Location: %s/areadoclientewebshop.php?action=add_shipping_address&from=%s", $GLOBALS['ShopPath'], $this_page));
				die();
			}

			$GLOBALS['CheckoutShippingTitle'] = GetLang('ChooseShippingAddress');
			$GLOBALS['CheckoutShippingIntro'] = sprintf("%s <a href='%s/areadoclientewebshop.php?action=add_shipping_address&amp;address_type=" . FORMFIELDS_FORM_SHIPPING . "&amp;from=%s'>%s</a>", GetLang('ChooseShippingAddressIntro1'), $GLOBALS['ShopPath'], $GLOBALS['FromURL'], GetLang('ChooseShippingAddressIntro2'));
			$GLOBALS['CheckoutMultiShippingIntro'] = sprintf("%s <a href='%s/areadoclientewebshop.php?action=add_shipping_address&amp;address_type=" . FORMFIELDS_FORM_SHIPPING . "&amp;from=%s' onclick='Checkout.MultiAddNewAddress(\"shipping\"); return false;'>%s</a>", GetLang('ChooseMultiShippingAddressIntro1'), $GLOBALS['ShopPath'], $GLOBALS['FromURL'], GetLang('ChooseMultiShippingAddressIntro2'));
		}

		$GLOBALS['CustomFieldSelectedAddressType'] = FORMFIELDS_FORM_SHIPPING;

		// Show the list of available shipping addresses
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName') . " - " . GetLang('ChooseShippingAddress'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("checkout_address");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	 * Show the page allowing a customer to choose the billing address for their order.
	 *
	 * @param array Optionally, an array of errors that have occurred and need to be shown.
	 */
	private function ChooseBillingAddress($errors=array())
	{
		// If we're coming here from a post request and we're not logged in then we've just chosen how we're checking out
		if(empty($errors) && $_SERVER['REQUEST_METHOD'] == "POST" && !CustomerIsSignedIn()) {

			// Are we logging in?
			if(isset($_REQUEST['login_email'])) {
				$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
				if(!$GLOBALS['ISC_CLASS_CUSTOMER']->CheckLogin(true)) {
					@ob_end_clean();
					header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout&bad_login=1');
					exit;
				}
			}
			// Perhaps we've chosen to create an account?
			else if(isset($_REQUEST['checkout_type']) && $_REQUEST['checkout_type'] == 'register') {
				@ob_end_clean();
				header("Location: ".GetConfig('ShopPath').'/login.php?action=create_account&checking_out=yes');
				exit;
			}
			// Otherwise, we're trying to checkout as a guest
		}

		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn()) {
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// Set a unique identifier for the items in this cart
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$_SESSION['CART']['HASH'] = $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash();

		$GLOBALS['HideErrors'] = 'display: none';
		if(!empty($errors)) {
			$GLOBALS['ErrorMessage'] = implode('<br />', $errors);
			$GLOBALS['SavedAddress'] = $_POST;
			$GLOBALS['HideIntro'] = 'display: none';
			$GLOBALS['HideErrors'] = '';
		}
		else if(isset($_SESSION['CHECKOUT']['BILLING_ADDRESS']) && is_array($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
			$GLOBALS['SavedAddress'] = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
			if(isset($_SESSION['CHECKOUT']['ACCOUNT_EMAIL'])) {
				$GLOBALS['SavedAddress']['account_email'] = $_SESSION['CHECKOUT']['ACCOUNT_EMAIL'];
			}
		}

		$addressVars = array(
			'account_email' => 'AccountEmail',
		);
		foreach($addressVars as $addressField => $formField) {
			if(isset($GLOBALS['SavedAddress'][$addressField])) {
				$GLOBALS[$formField] = isc_html_escape($GLOBALS['SavedAddress'][$addressField]);
			}
		}


		$GLOBALS['FromURL'] = urlencode("concluir.php?action=choose_billing_address");
		$GLOBALS['ShipAddressButtonText'] = isc_html_escape(GetLang('BillToThisAddress'));
		$GLOBALS['ShippingFormAction'] = "save_biller";

		// If the customer isn't signed in then they're performing a guest checkout so they don't see a list of addresses, but actually
		// the shipping address form
		$GLOBALS['HidePanels'][] = 'ChooseShippingAddress';
		if(!CustomerIsSignedIn()) {
			$GLOBALS['HidePanels'][] = 'ChooseBillingAddress';
			//$GLOBALS['CreateAccountForm'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CheckoutBillingAddressFields');

			$GLOBALS['CheckoutShippingTitle'] = GetLang('BillingAddress');
			$GLOBALS['CheckoutShippingIntro'] = GetLang('EnterBillingAddressBelow');
			$GLOBALS['ShipAddressButtonText'] = isc_html_escape(GetLang('BillAndShipToAddress'));
// 			$GLOBALS['HideFullNameField'] = 'display: none';

		}
		else {
			// Hide the address entry panel
			$GLOBALS['HidePanels'][] = 'CheckoutNewAddressForm';

			// Do they have a shipping address stored in the system?
			// If not we will ask them to create one

			if ($this->GetNumShippingAddresses() == 0) {
				// Take them to add a shipping address
				$this_page = urlencode("concluir.php?action=choose_billing_address");
				@ob_end_clean();
				header(sprintf("Location: %s/areadoclientewebshop.php?action=add_shipping_address&from=%s", $GLOBALS['ShopPath'], $this_page));
				die();
			}

			$GLOBALS['CheckoutShippingTitle'] = GetLang('ChooseBillingAddress');
			$GLOBALS['CheckoutShippingIntro'] = sprintf("%s <a href='%s/areadoclientewebshop.php?action=add_shipping_address&amp;from=%s'>%s</a>", GetLang('ChooseBillingAddressIntro1'), $GLOBALS['ShopPath'], $GLOBALS['FromURL'], GetLang('ChooseBillingAddressIntro2'));

		}

		if(isset($_SESSION['CART_CHANGED'])) {
			$GLOBALS['CheckoutShippingIntro'] = GetLang('CartChangedSinceCheckout');
			unset($_SESSION['CART_CHANGED']);
		}

		// We should not recaclulate anything to do with shipping at this stage!
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$GLOBALS['ISC_CLASS_CART']->SetCartValues();

		if($GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
			$GLOBALS['HideShippingOptions'] = 'display: none';
		}

		$products = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();

		// If the cart is empty, take them back to it
		if ($GLOBALS['ISC_CLASS_CART']->api->GetNumProductsInCart() == 0) {
			@ob_end_clean();
			header(sprintf("Location: %s/compras.php", $GLOBALS['ShopPath']));
			die();
		}

		$GLOBALS['CustomFieldSelectedAddressType'] = FORMFIELDS_FORM_BILLING;

		// Show the list of available shipping addresses
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName') . " - " . GetLang('ChooseBillingAddress'));
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("checkout_address");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	 * Return the number of shipping addresses configured for the current customer.
	 *
	 * @return int The number of shipping addresses belonging to the customer.
	 */
	private function GetNumShippingAddresses()
	{
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
		$query = sprintf("select count(shipid) as num from [|PREFIX|]shipping_addresses where shipcustomerid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId()));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
		return $row['num'];
	}

	/**
	 * Create the pending order in the database with the customers selected payment details, etc.
	 *
	 * @return array An array containing information about what needs to be done next.
	 */
	public function SavePendingOrder()
	{
		$provider = null;
		$verifyPaymentProvider = true;
		$redirectToFinishOrder = false;
		$providerId = '';

		$pendingOrderResult = array();

		if(!isset($_SESSION['CHECKOUT']['PENDING_DATA'])) {
			return false;
		}

		$orderTotal = $_SESSION['CHECKOUT']['PENDING_DATA']['ORDER_TOTAL'];
		$giftCertificateAmount = $_SESSION['CHECKOUT']['PENDING_DATA']['GIFTCERTIFICATE_AMOUNT'];
		$gatewayAmount = $_SESSION['CHECKOUT']['PENDING_DATA']['GATEWAY_AMOUNT'];
		$creditUsed = 0;
		$giftCertificates = array();

		// Find out what currency we are using. We'll need this later to display their previous orders in the currency that they have selected
		$selectedCurrency = GetCurrencyById($GLOBALS['CurrentCurrency']);

		if(isset($_SESSION['CART']['GIFTCERTIFICATES']) && is_array($_SESSION['CART']['GIFTCERTIFICATES'])) {
			$giftCertificates = $_SESSION['CART']['GIFTCERTIFICATES'];

			// Now we check that the gift certificates can actually be applied to the order
			$GLOBALS['ISC_CLASS_GIFT_CERTIFICATES'] = GetClass('ISC_GIFTCERTIFICATES');
			$badCertificates = array();
			$remainingBalance = 0;
			$GLOBALS['ISC_CLASS_GIFT_CERTIFICATES']->GiftCertificatesApplicableToOrder($orderTotal, $giftCertificates, $remainingBalance, $badCertificates);

			// One or more gift certificates were invalid so this order is now invalid
			if(count($badCertificates) > 0) {
				$badCertificatesList = '<strong>'.GetLang('BadGiftCertificates').'</strong><ul>';
				foreach($badCertificates as $code => $reason) {
					if(is_array($reason) && $reason[0] == "expired") {
						$reason = sprintf(GetLang('BadGiftCertificateExpired'), CDate($reason[1]));
					}
					else {
						$reason = GetLang('BadGiftCertificate'.ucfirst($reason));
					}
					$badCertificatesList .= sprintf("<li>%s - %s", isc_html_escape($code), $reason);
				}
				$badCertificatesList .= "</ul>";
				$pendingOrderResult = array(
					'error' => GetLang('OrderContainedInvalidGiftCertificates'),
					'errorDetails' => $badCertificatesList
				);
				return $pendingOrderResult;
			}
			// This order was entirely paid for using gift certificates but the totals don't add up
			else if($orderTotal == $giftCertificateAmount && $remainingBalance > 0) {
				$pendingOrderResult = array(
					'error' => GetLang('OrderTotalStillRemainingCertificates')
				);
				return $pendingOrderResult;
			}
			// Order was entirely paid for using gift certificates
			else if($orderTotal == $giftCertificateAmount) {
				$providerId = 'giftcertificate';
				$verifyPaymentProvider = false;
				$redirectToFinishOrder = true;
			}
		}

		// If the order total is 0, then we just forward the user on to the "Thank You" page and set the payment provider to ''
		if($orderTotal == 0) {
			$providerId = '';
			$verifyPaymentProvider = false;
			$redirectToFinishOrder = true;
		}

		if($verifyPaymentProvider) {
			if (isset($_POST['credit_checkout_provider']) && $_POST['credit_checkout_provider'] != "") {
				$_POST['checkout_provider'] = $_POST['credit_checkout_provider'];
			}

			$selected_provider = "";
			$providers = GetCheckoutModulesThatCustomerHasAccessTo(true);

			// If there's more than one, use the value they've chosen
			if ((count($providers) >1 && isset($_POST['checkout_provider'])) || isset($_SESSION['CHECKOUT']['ProviderListHTML'])) {
				$selected_provider = $_POST['checkout_provider'];
			}
			// If there's only one payment provider, then they're paying via that
			else if(count($providers) == 1) {
				$selected_provider = $providers[0]['object']->GetId();
				$_POST['checkout_provider'] = $selected_provider;
			}
			else {
				$selected_provider = '';
			}

			if (!isset($_POST['checkout_provider'])) {
				$_POST['checkout_provider'] = '';
			}

			// Are we using our store credit?
			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
			$customer = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerDataByToken();
			if (isset($_POST['store_credit']) && $_POST['store_credit'] == 1 && $customer['custstorecredit'] > 0) {
				// User has not chosen a payment provider and can't afford this order using only store credit, throw back as error
				if (!$_POST['checkout_provider'] && $customer['custstorecredit'] < $orderTotal) {
					return false;
				}
				// Otherwise we can use the store credit. Subtract store credit from users account and send them to the finished page
				else {
					$onlyCredit = false;
					$updateExtra = '';
					// If we're only using store credit
					$creditToUse = $orderTotal-$giftCertificateAmount;
					if ($customer['custstorecredit'] >= $creditToUse) {
						// Set the checkout provider
						$providerId = 'storecredit';
						$verifyPaymentProvider = false;
						$redirectToFinishOrder = true;
						$creditUsed = $creditToUse;
						$onlyCredit = true;
					}
					else {
						// Using all of our store credit to pay for this order and we owe more.
						$creditUsed = $customer['custstorecredit'];
						$gatewayAmount -= $creditUsed;
					}
				}
			}
		}

		$orderStatus = ORDER_STATUS_INCOMPLETE;

		// Now with round 2, do we still need to verify the payment provider?
		if($verifyPaymentProvider) {
			// If there's more than one provider and one wasn't selected on the order confirmation screen then there's a problem
			if ((count($providers) == 0 || (count($providers) > 1 && !isset($_POST['checkout_provider']))) && !isset($_SESSION['CHECKOUT']['ProviderListHTML']) ) {
				return false;
			}

			// Is the payment provider selected actually valid?
			if (!GetModuleById('checkout', $provider, $selected_provider)) {
				return false;
			}
			$providerId = $provider->GetId();
		}

		// Load up all of the data for the items in the cart
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$cartItems = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();

		// OK, we're successful down to here - do they want to create an account? If so then assign it to
		// a session so we can create the actual record on a successful order
		if(isset($_SESSION['CHECKOUT']['CREATE_ACCOUNT'])) {
			$accountDetails = $_SESSION['CHECKOUT']['ACCOUNT_DETAILS'];
			$token = GenerateCustomerToken();
			$customerData = array(
				'email' => $accountDetails['email'],
				'password' => $accountDetails['password'],
				'firstname' => $accountDetails['firstname'],
				'lastname' => $accountDetails['lastname'],
				'company' => $accountDetails['company'],
				'phone' => $accountDetails['phone'],
				'token' => $token,
				'autoAccount' => $accountDetails['autoAccount']
			);

			// Do we have any custom fields?
			if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['customer'])) {
				$customerData['custom'] = $_SESSION['CHECKOUT']['CUSTOM_FIELDS']['customer'];
			}

			// Are we also saving the address details to our address book?
			$customerData['addresses'] = array();

			foreach (array('BILLING_ADDRESS', 'SHIPPING_ADDRESS') as $shipkey) {
				if (!isset($_SESSION['CHECKOUT'][$shipkey]) || !isset($_SESSION['CHECKOUT'][$shipkey]['saveAddress']) || !$_SESSION['CHECKOUT'][$shipkey]['saveAddress']) {
					continue;
				}

				$address = $_SESSION['CHECKOUT'][$shipkey];

				// Do we have any custom fields for this one?
				if ($shipkey == 'BILLING_ADDRESS') {
					$customKey = 'billing';
				} else {
					$customKey = 'shipping';
				}

				if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS'][$customKey])) {
					$address['shipformsessionid'] = $_SESSION['CHECKOUT']['CUSTOM_FIELDS'][$customKey];
				}

				$customerData['addresses'][] = $address;
			}

			// Save the customer details temporarily
			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
			$GLOBALS['ISC_CLASS_CUSTOMER']->setCustomerOrderSession($customerData);

			unset($_SESSION['CHECKOUT']['CREATE_ACCOUNT']);
			unset($_SESSION['CHECKOUT']['ACCOUNT_DETAILS']);
		}

		if(isset($_COOKIE['SHOP_TOKEN'])) {
			$customerToken = $_COOKIE['SHOP_TOKEN'];
		}
		else {
			$customerToken = '';
		}

		$orderComments = '';
		if(isset($_REQUEST['ordercomments'])) {
			$orderComments = $_REQUEST['ordercomments'];
		}

		$checkoutSession = $_SESSION['CHECKOUT'];
		$pendingData = $checkoutSession['PENDING_DATA'];

		// Get a list of the vendors for all of the items in the cart, and loop through them
		// to build all of the pending orders
		$cartContent = $this->BreakdownCartByAddressVendor();
		$vendorOrderInfo = array();
		foreach($cartContent as $vendorId => $addresses) {
			foreach($addresses as $addressId => $products) {
				$allDigital = 1;
				$productArray = array();
				foreach($products as $cartItemId => $product) {
					// A physical product, mark as so
					if ($product['data']['prodtype'] == PT_PHYSICAL) {
						$allDigital = 0;
					}
					// Mark the quantity of this item
					$productArray[$cartItemId] = $product['quantity'];
				}
				$vendorInfo = $pendingData['VENDORS'][$vendorId.'_'.$addressId];
				$vendorData = array(
					'ordsubtotal' => $vendorInfo['ITEM_TOTAL'],
					'ordtaxtotal' => $vendorInfo['TAX_COST'],
					'ordtaxname' => $vendorInfo['TAX_NAME'],
					'ordtaxrate' => $vendorInfo['TAX_RATE'],
					'ordtotalamount' => $vendorInfo['ORDER_TOTAL'],
					'ordshipcost' => @$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId]['COST'],
					'ordhandlingcost' => @$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId]['HANDLING'],
					'ordshipmethod' => @$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId]['PROVIDER'],
					'ordershipmodule' => @$_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId]['MODULE'],
					'ordisdigital' => $allDigital,
					'products' => $productArray,
					'orddiscountamount' => $pendingData['SUBTOTAL_DISCOUNT']
				);

				if($addressId == 0) {
					$addresses = $this->GetOrderShippingAddresses();
					$vendorData['shippingaddress'] = $addresses[$addressId];
				}
				else {
					$vendorData['shippingaddressid'] = $addressId;
				}

				// Shipping zones can be configured per vendor, so we need to be sure
				// to pass this along correctly too
				if(isset($vendorInfo['SHIPPING_ZONE'])) {
					$shippingZone = GetShippingZoneById($vendorInfo['SHIPPING_ZONE']);
					if(is_array($shippingZone)) {
						$vendorData['ordshippingzoneid'] = $shippingZone['zoneid'];
						$vendorData['ordshippingzone'] = $shippingZone['zonename'];
					}
				}

				$vendorOrderInfo[$vendorId.'_'.$addressId] = $vendorData;
			}
		}

		// Set some defaults about the rest of the order
		$pendingOrder = array(
			"customertoken" => $customerToken,
			'orderpaymentmodule' => $providerId,
			"ordstorecreditamount" => $creditUsed,
			"ordgiftcertificateamount" => $giftCertificateAmount,
			"giftcertificates" => $giftCertificates,
			"ordgatewayamount" => $gatewayAmount,
			'ordtotalincludestax' => $pendingData['TAX_INCLUDED'],
			"ordcurrencyid" => $selectedCurrency['currencyid'],
			"ordcurrencyexchangerate" => $selectedCurrency['currencyexchangerate'],
			'ordcustmessage' => $orderComments,
			'ordipaddress' => GetIP(),
			'extraInfo' => array(),
			'vendorinfo' => $vendorOrderInfo,
			'ordstatus' => $orderStatus
		);

		if(isset($customerId)) {
			$pendingOrder['ordcustid'] = $customerId;
		}

		// Did they agree to signup to any mailing lists?
		if (isset($_POST['join_mailing_list'])) {
			$pendingOrder['extraInfo']['join_mailing_list'] = true;
		}

		if (isset($_POST['join_order_list'])) {
			$pendingOrder['extraInfo']['join_order_list'] = true;
		}

		// Determine the address ID we're using for billing
		if(is_array($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
			$pendingOrder['billingaddress'] = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
		}
		else {
			$pendingOrder['billingaddressid'] = (int)$_SESSION['CHECKOUT']['BILLING_ADDRESS'];
		}

		if(isset($_POST['ordermessage'])) {
			$pendingOrder['ordermessage'] = $_POST['ordermessage'];
		} else {
			$pendingOrder['ordermessage'] = '';
		}

		/**
		 * OK, now to store the custom address fields. Check here to see if we are not split
		 * shipping (single order)
		 */
		if (!isset($_SESSION['CHECKOUT']['IS_SPLIT_SHIPPING']) || !$_SESSION['CHECKOUT']['IS_SPLIT_SHIPPING']) {
			$pendingOrder['ordformsessionid'] = '';
			if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']) && is_array($_SESSION['CHECKOUT']['CUSTOM_FIELDS'])) {

				/**
				 * Save the billing
				 */
				if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing']) && isset($pendingOrder['billingaddress']['saveAddress']) && $pendingOrder['billingaddress']['saveAddress']) {
					$pendingOrder['billingaddress']['shipformsessionid'] = $GLOBALS['ISC_CLASS_FORM']->saveFormSessionManual($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing']);
				}

				/**
				 * Now for the shipping. Only save this once for all the shipping addresses
				 */
				if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['shipping'])) {
					$shippSessId = $GLOBALS['ISC_CLASS_FORM']->saveFormSessionManual($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['shipping']);

					foreach ($pendingOrder['vendorinfo'] as $vendorId => $vendorData) {
						if (isset($vendorData['shippingaddress']['saveAddress']) && $vendorData['shippingaddress']['saveAddress']) {
							$pendingOrder['vendorinfo'][$vendorId]['shippingaddress']['shipformsessionid'] = $shippSessId;
						}
					}
				}

				/**
				 * Now the orders. This part is tricky because the billing and shipping information
				 * have the same keys (same fields used in the frontend). We need to split them up
				 * into separate billing and shipping information and then save it
				 */
				if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing']) && is_array($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing'])) {

					/**
					 * We create a map first so we can map the shipping information to its proper field
					 * ID
					 */
					$billingKeys = array_keys($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing']);
					$fieldAddressMap = $GLOBALS['ISC_CLASS_FORM']->mapAddressFieldList(FORMFIELDS_FORM_BILLING, $billingKeys);

					/**
					 * OK, we have the map, now to split up the custom fields
					 */
					$orderSessData = array();

					foreach ($fieldAddressMap as $fieldId => $newShippingFieldId) {
						$orderSessData[$fieldId] = $_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing'][$fieldId];

						if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['shipping'][$fieldId])) {
							$orderSessData[$newShippingFieldId] = $_SESSION['CHECKOUT']['CUSTOM_FIELDS']['shipping'][$fieldId];
						}
					}

					$pendingOrder['ordformsessionid'] = $GLOBALS['ISC_CLASS_FORM']->saveFormSessionManual($orderSessData);
				}
			}


		/**
		 * This is for split shipping. Loop through each address to get their default custom
		 * field data, combine it with the billing custom field data, create the form session
		 * record and then save that ID for each address
		 */
		} else {
			$shippingAddresses = $this->GetOrderShippingAddresses();
			$origFormSessionData = array();
			if (isset($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing']) && is_array($_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing'])) {
				$origFormSessionData = $_SESSION['CHECKOUT']['CUSTOM_FIELDS']['billing'];
			}

			foreach ($pendingOrder['vendorinfo'] as $vendorId => $vendorData) {
				$address = null;
				$orderSessData = array();

				if (array_key_exists($vendorData['shippingaddressid'], $shippingAddresses)) {
					$address = $shippingAddresses[$vendorData['shippingaddressid']];
				}

				if (isset($address['shipformsessionid']) && isId($address['shipformsessionid'])) {
					$shippingSessionData = $GLOBALS['ISC_CLASS_FORM']->getSavedSessionData($address['shipformsessionid']);

					if (is_array($shippingSessionData)) {

						/**
						 * Same deal with this save session data because the billing and shipping data
						 * use the same fields and therefore have the same keys
						 */
						$billingKeys = array_keys($origFormSessionData);
						$fieldAddressMap = $GLOBALS['ISC_CLASS_FORM']->mapAddressFieldList(FORMFIELDS_FORM_BILLING, $billingKeys);

						/**
						* OK, we have the map, now to split up the custom fields
						*/
						$orderSessData = array();

						foreach ($fieldAddressMap as $fieldId => $newShippingFieldId) {
							$orderSessData[$fieldId] = $origFormSessionData[$fieldId];
							$orderSessData[$newShippingFieldId] = $shippingSessionData[$fieldId];
						}
					}
				}

				$newFormSessionId = $GLOBALS['ISC_CLASS_FORM']->saveFormSessionManual($orderSessData);

				if (isId($newFormSessionId)) {
					$pendingOrder['vendorinfo'][$vendorId]['ordformsessionid'] = $newFormSessionId;
				}
			}
		}

		$pendingToken = CreateOrder($pendingOrder, $cartItems);

		// Try to add the record and if we can't then take them back to the shopping cart
		if (!$pendingToken) {
			return false;
		}

		// Persist the pending order token as a cookie for 24 hours
		ISC_SetCookie("SHOP_ORDER_TOKEN", $pendingToken, time() + (3600*24), true);
		$_COOKIE['SHOP_ORDER_TOKEN'] = $pendingToken;

		// Redirecting to finish order page?
		if($redirectToFinishOrder) {
			return array(
				'redirectToFinishOrder' => true
			);
		}

		$orderData = LoadPendingOrdersByToken($pendingToken);

		// Otherwise, the gateway want's to do something
		$provider->SetOrderData($orderData);

		// Is this an online payment provider? It would like to do something
		if($provider->GetPaymentType() == PAYMENT_PROVIDER_ONLINE || method_exists($provider, "ShowPaymentForm")) {
			// Call the checkout process for the selected provider
			if(method_exists($provider, "ShowPaymentForm")) {
				return array(
					'provider' => $provider,
					'showPaymentForm' => true
				);
			}
			else {
				return array(
					'provider' => $provider
				);
			}
		}
		// If an offline method, we throw them to the "Thank you for your order" page
		else {
			return array(
				'provider' => $provider
			);
		}
	}

	/**
	 * Redirect to the payment provider if one is chosen - otherwise process the payment for an order.
	 */
	private function PayForOrder()
	{
		// If guest checkout is not enabled and the customer isn't signed in then send the customer
		// back to the beginning of the checkout process.
		if(!GetConfig('GuestCheckoutEnabled') && !CustomerIsSignedIn() && !isset($_SESSION['CHECKOUT']['CREATE_ACCOUNT'])) {
			@ob_end_clean();
			header("Location: ".GetConfig('ShopPath').'/concluir.php');
			exit;
		}

		if (GetConfig('EnableOrderTermsAndConditions')==1  && !isset($_POST['AgreeTermsAndConditions'])) {
			@ob_end_clean();
			$_SESSION['REDIRECT_TO_CONFIRMATION_MSG'] = GetLang('TickArgeeTermsAndConditions');
			header("Location: ".$GLOBALS['ShopPath']."/concluir.php?action=confirm_order");
			exit;
		}

		if (!isset($GLOBALS['ISC_CLASS_CART'])) {
			$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		}

		// The contents of the cart have changed since we last started the checkout process, need to go back to the first step of the checkout
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		if(isset($_SESSION['CART']['HASH']) && $_SESSION['CART']['HASH'] != $GLOBALS['ISC_CLASS_CART']->api->GenerateCartHash()) {
			unset($_SESSION['CHECKOUT']);
			$_SESSION['CART_CHANGED'] = true;
			@ob_end_clean();
			if(isset($_SESSION['CHECKOUT']['CHECKOUT_TYPE']) && $_SESSION['CHECKOUT']['CHECKOUT_TYPE'] == 'express') {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=express');
			}
			else {
				header("Location: ".GetConfig('ShopPath').'/concluir.php?action=checkout');
			}
			exit;
		}

		// ensure products are in stock
		$this->CheckStockLevels();

		// Customer actually chose to apply a gift certificate or coupon code to this order so
		// we actually show the confirm order page again which does all of the magic.
		if (isset($_REQUEST['apply_code'])) {
			$this->ConfirmOrder();
			return;
		}

		// Attempt to create the pending order with the selected details
		$pendingResult = $this->SavePendingOrder();

		// There was a problem creating the pending order
		if(!is_array($pendingResult)) {
			@ob_end_clean();
			header("Location: ".$GLOBALS['ShopPath']."/concluir.php?action=confirm_order&hash=".md5(rand(0,99999999999)));
			exit;
		}

		// There was a problem creating the pending order but we have an actual error message
		if(isset($pendingResult['error'])) {
			if(isset($pendingResult['errorDetails'])) {
				$this->BadOrder('', $pendingResult['error'], $pendingResult['errorDetails']);
			}
			else {
				$this->BadOrder('', $pendingResult['error']);
			}
		}

		// We've been told all we need to do is redirect to the finish order page, so do that
		if(isset($pendingResult['redirectToFinishOrder']) && $pendingResult['redirectToFinishOrder']) {
			@ob_end_clean();
			header("Location: ".$GLOBALS['ShopPath']."/formadepagamento.php?hash=".md5(rand(0,999999999)));
			die();
		}

		// Otherwise, the gateway want's to do something
		if($pendingResult['provider']->GetPaymentType() == PAYMENT_PROVIDER_ONLINE || method_exists($pendingResult['provider'], "ShowPaymentForm")) {
			// ProviderListHTML is stored in the session when the provider requires that it can only be the only payment provider during checkout, disable the other checkout method.
			if(isset($_SESSION['CHECKOUT']['ProviderListHTML']) && method_exists($pendingResult['provider'], 'DoExpressCheckoutPayment')) {
				$pendingResult['provider']->DoExpressCheckoutPayment();
				die();
			}

			// If we have a payment form to show then show that
			if(isset($pendingResult['showPaymentForm']) && $pendingResult['showPaymentForm']) {
				$this->ShowPaymentForm($pendingResult['provider']);
			}
			else {
				$pendingResult['provider']->TransferToProvider();
			}
		}
		else {
			// It's an offline payment method, no need to accept payment now
			@ob_end_clean();
			header(sprintf("Location:%s/formadepagamento.php?provider=%s&hash=".md5(rand(0,9999999)), $GLOBALS['ShopPath'], $pendingResult['provider']->GetId()));
			die();
		}
	}

	/**
	 * Get a list of the vendor IDs for all of the items going through the checkout.
	 *
	 * @return array An array of order IDs.
	 */
	public function GetCheckoutVendorIds()
	{
		$vendorIds = array();
		$cart = GetClass('ISC_CART');
		$cartProducts = $cart->api->GetProductsInCart();
		foreach($cartProducts as $product) {
			if(isset($product['data']['prodvendorid']) && $product['data']['prodvendorid']) {
				$vendorIds[] = $product['data']['prodvendorid'];
			}
			else {
				$vendorIds[] = 0;
			}
		}

		return array_unique($vendorIds);
	}

	/**
	 * Display the payment form for a payment provider.
	 *
	 * @param object The payment provider object with the payment form.
	 */
	public function ShowPaymentForm($provider)
	{
		$GLOBALS['PaymentFormContent'] = $provider->ShowPaymentForm();
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('checkout_payment');
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		exit;
	}

	/**
	 * If they're a first time customer and are checking out we don't need to let them choose
	 * a shipping address because we just entered it, so we'll automatically select it and
	 * take them straight to the shipping quote page
	 */
	private function ChooseShipperAndGoToBillingAddress()
	{
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
		$query = sprintf("select shipid from [|PREFIX|]shipping_addresses where shipcustomerid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId()));
		$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit(0, 1);
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			@ob_end_clean();
			header("Location: " . $GLOBALS['ShopPath'] . "/concluir.php?action=choose_shipper&address_id=" . $row['shipid']);
			die();
		}
		else {
			$this->ChooseShippingAddress();
		}
	}

	/**
	 * Show a "bad order" error message.
	 *
	 * @param string The title of the message to be shown.
	 * @param string The message to be shown.
	 * @param string Any additional/extra information we want to show.
	 */
	public function BadOrder($title="", $message="", $detailed="")
	{
		$GLOBALS['ErrorTitle'] = GetLang('OrderError');
		if($title) {
			$GLOBALS['ISC_LANG']['SomethingWentWrong'] = $title;
		}

		if($message == "") {
			$GLOBALS['ErrorMessage'] = sprintf(GetLang('BadOrderDetailsFromProvider'), GetConfig('OrderEmail'), GetConfig('OrderEmail'));
		}
		else {
			$GLOBALS['ErrorMessage'] = $message;
		}

		if($detailed != "") {
			$GLOBALS['ErrorDetails'] = $detailed;
		}

		$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle($GLOBALS['ErrorTitle']);
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("error");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	 * Build the contents for the order confirmation page. This function sets up everything to be used by
	 * the order confirmation on the express checkout page as well as the ConfirmOrder page when using a
	 * multi step checkout.
	 */
	public function BuildOrderConfirmation()
	{
		if(!GetConfig('ShowMailingListInvite')) {
			$GLOBALS['HideMailingListInvite'] = 'none';
		}

		// Do we need to show the special offers & discounts checkbox and should they
		// either of the newsletter checkboxes be ticked by default?
		if (GetConfig('MailAutomaticallyTickNewsletterBox')) {
			$GLOBALS['NewsletterBoxIsTicked'] = 'checked="checked"';
		}

		// Is Email Marketer integrated?
		if (GetConfig('MailXMLAPIValid') && GetConfig('UseMailerForOrders') && GetConfig('MailOrderList') > 0) {
			// Yes, should we tick the speical offers & discounts checkbox by default?
			if (GetConfig('MailAutomaticallyTickOrderBox')) {
				$GLOBALS['OrderBoxIsTicked'] = 'checked="checked"';
			}
		}
		else {
			$GLOBALS['HideOrderCheckBox'] = "none";
		}

		if(isset($_REQUEST['ordercomments'])) {
			$GLOBALS['OrderComments'] = $_REQUEST['ordercomments'];
		}

		// Now we check if we have an incoming coupon or gift certificate code to apply
		if (isset($_REQUEST['couponcode']) && $_REQUEST['couponcode'] != '') {
			$code = trim($_REQUEST['couponcode']);

			// Were we passed a gift certificate code?
			if (preg_match('#^[A-Z0-9]{3}\-[A-Z0-9]{3}\-[A-Z0-9]{3}\-[A-Z0-9]{3}$#', $code) && gzte11(ISC_LARGEPRINT)) {
				$cart = GetClass('ISC_CART');
				if($cart->api->ApplyGiftCertificate($code)) {
					// If successful show a message
					$GLOBALS['CheckoutSuccessMsg'] = GetLang('GiftCertificateAppliedToCart');
				}
				else {
					$GLOBALS['CheckoutErrorMsg'] = implode('<br />', $cart->api->GetErrors());
				}
			}
			// Otherwise, it must be a coupon code
			else {
				$cart = GetClass('ISC_CART');
				if($cart->api->ApplyCoupon($code)) {
					// Coupon code applied successfully
					$GLOBALS['CheckoutSuccessMsg'] = GetLang('CouponAppliedToCart');
				}
				else {
					$GLOBALS['CheckoutErrorMsg'] = implode('<br />', $cart->api->GetErrors());
				}
			}
		}

		$GLOBALS['ISC_CLASS_ACCOUNT'] = GetClass('ISC_ACCOUNT');

		// Determine what we'll be showing for the redeem gift certificate/coupon code box
		if (gzte11(ISC_LARGEPRINT)) {
			$GLOBALS['RedeemTitle'] = GetLang('RedeemGiftCertificateOrCoupon');
			$GLOBALS['RedeemIntro'] = GetLang('RedeemGiftCertificateorCouponIntro');
		}
		else {
			$GLOBALS['RedeemTitle'] = GetLang('RedeemCouponCode');
			$GLOBALS['RedeemIntro'] = GetLang('RedeemCouponCodeIntro');
		}

		$GLOBALS['HideCheckoutError'] = "none";
		$GLOBALS['HidePaymentOptions'] = "";
		$GLOBALS['HideUseCoupon'] = '';
		$checkoutProviders = array();

		// if the provider list html is set in session then use it as the payment provider options.
		// it's normally set in payment modules when it's required.
		if(isset($_SESSION['CHECKOUT']['ProviderListHTML'])) {
			$GLOBALS['HidePaymentProviderList'] = "";
			$GLOBALS['HidePaymentOptions'] = "";
			$GLOBALS['PaymentProviders'] = $_SESSION['CHECKOUT']['ProviderListHTML'];
			$GLOBALS['StoreCreditPaymentProviders'] = $_SESSION['CHECKOUT']['ProviderListHTML'];
			$GLOBALS['CheckoutWith'] = "";
		} else {

			// Get a list of checkout providers
			$checkoutProviders = GetCheckoutModulesThatCustomerHasAccessTo(true);


			// If no checkout providers are set up, send an email to the store owner and show an error message
			if (empty($checkoutProviders)) {
				$GLOBALS['HideConfirmOrderPage'] = "none";
				$GLOBALS['HideCheckoutError'] = '';
				$GLOBALS['HideTopPaymentButton'] = "none";
				$GLOBALS['HidePaymentProviderList'] = "none";
				$GLOBALS['CheckoutErrorMsg'] = GetLang('NoCheckoutProviders');
				$GLOBALS['NoCheckoutProvidersError'] = sprintf(GetLang("NoCheckoutProvidersErrorLong"), $GLOBALS['ShopPath']);

				$GLOBALS['EmailHeader'] = GetLang("NoCheckoutProvidersSubject");
				$GLOBALS['EmailMessage'] = sprintf(GetLang("NoCheckoutProvidersErrorLong"), $GLOBALS['ShopPath']);

				$emailTemplate = FetchEmailTemplateParser();
				$emailTemplate->SetTemplate("general_email");
				$message = $emailTemplate->ParseTemplate(true);

				require_once(ISC_BASE_PATH . "/lib/email.php");
				$obj_email = GetEmailClass();
				$obj_email->Set('CharSet', GetConfig('CharacterSet'));
				$obj_email->From(GetConfig('OrderEmail'), GetConfig('StoreName'));
				$obj_email->Set("Subject", GetLang("NoCheckoutProvidersSubject"));
				$obj_email->AddBody("html", $message);
				$obj_email->AddRecipient(GetConfig('AdminEmail'), "", "h");
				$email_result = $obj_email->Send();
			}

			// We have more than one payment provider, hide the top button and build a list
			else if (count($checkoutProviders) > 1) {
				$GLOBALS['HideTopPaymentButton'] = "none";
				$GLOBALS['HideCheckoutError'] = "none";
			}

			// There's only one payment provider - hide the list
			else {
				$GLOBALS['HidePaymentProviderList'] = "none";
				$GLOBALS['HideCheckoutError'] = "none";
				$GLOBALS['HidePaymentOptions'] = "none";
				list(,$provider) = each($checkoutProviders);
				if(method_exists($provider['object'], 'ShowPaymentForm') && !isset($_SESSION['CHECKOUT']['ProviderListHTML'])) {
					$GLOBALS['ExpressCheckoutLoadPaymentForm'] = 'ExpressCheckout.ShowSingleMethodPaymentForm();';
				}
				if ($provider['object']->GetPaymentType() == PAYMENT_PROVIDER_OFFLINE) {
					$GLOBALS['PaymentButtonSwitch'] = "ShowContinueButton();";
				}
				$GLOBALS['CheckoutWith'] = $provider['object']->GetDisplayName();
			}

			// Build the list of payment provider options
			$GLOBALS['PaymentProviders'] = $GLOBALS['StoreCreditPaymentProviders'] =  "";
			foreach ($checkoutProviders as $provider) {
				$GLOBALS['ProviderChecked'] = '';
				if(count($checkoutProviders) == 1) {
					$GLOBALS['ProviderChecked'] = 'checked="checked"';
				}
				$GLOBALS['ProviderId'] = $provider['object']->GetId();
				$GLOBALS['ProviderName'] = isc_html_escape($provider['object']->GetDisplayName());
				$GLOBALS['ProviderType'] = $provider['object']->GetPaymentType("text");
				if(method_exists($provider['object'], 'ShowPaymentForm')) {
					$GLOBALS['ProviderPaymentFormClass'] = 'ProviderHasPaymentForm';
				}
				else {
					$GLOBALS['ProviderPaymentFormClass'] = '';
				}
				$GLOBALS['PaymentFieldPrefix'] = '';
				$GLOBALS['PaymentProviders'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutProviderOption");
				$GLOBALS['PaymentFieldPrefix'] = 'credit_';
				$GLOBALS['StoreCreditPaymentProviders'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutProviderOption");
			}

		}

		// Are we coming back to this page for a particular reason?
		if (isset($_SESSION['REDIRECT_TO_CONFIRMATION_MSG'])) {
			$GLOBALS['HideCheckoutError'] = '';
			$GLOBALS['CheckoutErrorMsg'] = $_SESSION['REDIRECT_TO_CONFIRMATION_MSG'];
			unset($_SESSION['REDIRECT_TO_CONFIRMATION_MSG']);
		}

		// Get a summary of the order
		$orderSummary = $this->CalculateOrderSummary();

		// Start building the summary of all of the items in the order
		$GLOBALS['SNIPPETS']['CartItems'] = '';
		foreach ($orderSummary['products'] as $cartKey => $product) {
			$GLOBALS['ProductQuantity'] = $product['quantity'];
			$GLOBALS['ProductPrice'] = CurrencyConvertFormatPrice($product['price']);
			$GLOBALS['ProductTotal'] = CurrencyConvertFormatPrice($product['total']);
			$GLOBALS['ProductImage'] = '';


			// If the item in the cart is a gift certificate, we need to show a special type of row
			if (isset($product['type']) && $product['type'] == "giftcertificate") {
				$GLOBALS['GiftCertificateName'] = isc_html_escape($product['data']['prodname']);
				$GLOBALS['GiftCertificateTo'] = isc_html_escape($product['certificate']['to_name']);

				$GLOBALS['SNIPPETS']['CartItems'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutCartItemGiftCertificate");
			}
			else {
				$GLOBALS['ProductAvailability'] = isc_html_escape($product['data']['prodavailability']);
				$GLOBALS['ItemId'] = $product['data']['productid'];

				// If this is a discounted price (from a coupon) override the product price to the was/now version
				if (isset($product['discount_price']) && $product['discount_price'] != $product['original_price']) {
					$GLOBALS['ProductPrice'] = sprintf("<s class='CartStrike'>%s</s> %s", CurrencyConvertFormatPrice($product['original_price']), CurrencyConvertFormatPrice($product['price']));
				}

				// Should we show thumbnails in the cart?
				if (GetConfig('ShowThumbsInCart')) {
					$GLOBALS['ProductImage'] = ImageThumb($product['data'], ProdLink($product['data']['prodname']));
				}

				// Is this product a variation?
				$GLOBALS['ProductOptions'] = '';
				if(isset($product['options']) && !empty($product['options'])) {
					$GLOBALS['ProductOptions'] .= "<br /><small>(";
					$comma = '';
					foreach($product['options'] as $name => $value) {
						if(!trim($name) || !trim($value)) {
							continue;
						}
						$GLOBALS['ProductOptions'] .= $comma.isc_html_escape($name).": ".isc_html_escape($value);
						$comma = ', ';
					}
					$GLOBALS['ProductOptions'] .= ")</small>";
				}

				$GLOBALS['EventDate'] = '';
				if (isset($product['event_date'])) {
					$GLOBALS['EventDate'] = '<div style="font-style: italic; font-size:11px; padding-left:10px">' . $product['event_name'] . ': ' . isc_date('M jS Y', $product['event_date']) . '</div>';
				}

				$GLOBALS['HideGiftWrapping'] = 'display: none';
				$GLOBALS['HideGiftMessagePreview'] = 'display: none';
				$GLOBALS['GiftWrappingName'] = '';
				$GLOBALS['GiftMessagePreview'] = '';
				if(isset($product['wrapping_name'])) {
					$GLOBALS['HideGiftWrapping'] = '';
					$GLOBALS['GiftWrappingName'] = isc_html_escape($product['wrapping_name']);
					if(isset($product['wrapping_message'])) {
						if(isc_strlen($product['wrapping_message']) > 30) {
							$product['wrapping_message'] = substr($product['wrapping_message'], 0, 27).'...';
						}
						$GLOBALS['GiftMessagePreview'] = isc_html_escape($product['wrapping_message']);
						if($product['wrapping_message']) {
							$GLOBALS['HideGiftMessagePreview'] = '';
						}
					}
				}

				//create configurable product fields on order confirmation page with the data posted from add to cart page
				$GLOBALS['CartProductFields'] = '';
				if (isset($product['productFields'])) {
					require_once ISC_BASE_PATH.'/includes/display/CartContent.php';
					ISC_CARTCONTENT_PANEL::GetProductFieldDetails($product['productFields'],$cartKey);
				}

				$GLOBALS['ProductName'] = isc_html_escape($product['data']['prodname']);
				$GLOBALS['SNIPPETS']['CartItems'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutCartItem");
			}
		}

		// Do we have a shipping price to show?
		if($orderSummary['digitalOrder'] == 0) {
			$GLOBALS['ShippingCost'] = CurrencyConvertFormatPrice($orderSummary['shippingCost']);
			$GLOBALS['ShippingMethod'] = sprintf("%s %s %s", isc_html_escape($orderSummary['shippingProvider']), GetLang('For'), $GLOBALS['ShippingCost']);
			$GLOBALS['HideShoppingCartShippingCost'] = '';
			$GLOBALS['ShippingProvider'] = isc_html_escape($orderSummary['shippingProvider']);

			if(count($orderSummary['shippingAddresses']) == 1) {
				$address = current($orderSummary['shippingAddresses']);
				$GLOBALS['ShippingAddress'] = $GLOBALS['ISC_CLASS_ACCOUNT']->FormatShippingAddress($address);
			}
			else if(count($orderSummary['shippingAddresses']) > 1) {
				$GLOBALS['ShippingAddress'] = '<em>(Order will be shipped to multiple addresses)</em>';
			}
			else {
				$GLOBALS['ShippingAddress'] = GetLang('NA');
			}
			// Show the shipping details
			$GLOBALS['HideShippingDetails'] = '';
		}
		// This is a digital order - no shipping applies
		else {
			$GLOBALS['HideShippingDetails'] = 'display: none';
			$GLOBALS['HideShoppingCartShippingCost'] = 'none';
			$GLOBALS['ShippingAddress'] = GetLang('NotRequiredForDigitalDownloads');
			$GLOBALS['ShippingMethod'] = GetLang('ShippingImmediateDownload');
		}

		if(isset($orderSummary['billingAddressId'])) {
			$GLOBALS['BillingAddress'] = $GLOBALS['ISC_CLASS_ACCOUNT']->GetAndFormatShippingAddressById($orderSummary['billingAddressId']);
		}
		else {
			$GLOBALS['BillingAddress'] = $GLOBALS['ISC_CLASS_ACCOUNT']->FormatShippingAddress($orderSummary['billingAddress']);
		}

		// Do we have a handling cost to show?
		if(isset($orderSummary['handlingCost']) && $orderSummary['handlingCost'] > 0) {
			$GLOBALS['HandlingCost'] = CurrencyConvertFormatPrice($orderSummary['handlingCost']);
		}
		else {
			$GLOBALS['HideShoppingCartHandlingCost'] = 'none';
		}

		// Format the item total
		$GLOBALS['ItemTotal'] = CurrencyConvertFormatPrice($orderSummary['itemTotal']);

		if($orderSummary['wrappingCost'] > 0) {
			$GLOBALS['GiftWrappingTotal'] = CurrencyConvertFormatPrice($orderSummary['wrappingCost']);
		}
		else {
			$GLOBALS['HideGiftWrappingTotal'] = 'display: none';
		}

		// Hide everything related to tax by default
		$GLOBALS['HideShoppingCartTaxCost'] = "none";
		$GLOBALS['HideShoppingCartIncludedTaxCost'] = "none";

		// Do we have any tax we need to show?
		if($orderSummary['taxCost'] > 0) {
			$taxLines = "";
			$taxLang = "";
			if ($orderSummary['taxIncluded']) {
				$taxLang = "Included";
			}

			// get the taxes from the addresses and merge them if they are from the same tax rate
			$taxes = array();
			foreach ($orderSummary['vendors'] as $vendorId => $addresses) {
				foreach ($addresses as $addressId => $addressInfo) {
					$taxId = $addressInfo['taxId'];
					if (isset($taxes[$taxId])) {
						$taxes[$taxId]['taxCost'] += $addressInfo['taxCost'];
					}
					else {
						$taxes[$taxId] = array(
							'taxName' => $addressInfo['taxName'],
							'taxCost' => $addressInfo['taxCost'],
							'taxRate' => $addressInfo['taxRate']
						);
					}
				}
			}

			$GLOBALS['SNIPPETS']['TaxLines'] = "";

			// generate lines for each tax rate
			foreach ($taxes as $taxId => $tax) {
				$GLOBALS['TaxName'] = isc_html_escape(sprintf(GetLang($taxLang . 'TaxLine'), $tax['taxName'], ($tax['taxRate'] / 1)));
				$GLOBALS['TaxCost'] = CurrencyConvertFormatPrice($tax['taxCost']);
				$taxLines .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutTaxLine");
			}

			// if more than one tax rate is used, display a total line
			if (count($taxes) > 1) {
				$GLOBALS['TaxName'] = isc_html_escape(GetLang($taxLang . 'TotalTax'));
				$GLOBALS['TaxCost'] = CurrencyConvertFormatPrice($orderSummary['taxCost']);
				$taxLines .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutTaxLine");
			}

			$GLOBALS['SNIPPETS']['TaxLines'] = "";
			$GLOBALS['SNIPPETS']['IncludedTax'] = "";
			// are we displaying the tax before the total or after as included tax?
			if ($orderSummary['taxIncluded']) {
				$GLOBALS['SNIPPETS']['IncludedTax'] = $taxLines;
			}
			else {
				$GLOBALS['SNIPPETS']['TaxLines'] = $taxLines;
			}
		}

		// Format the grand total of the order
		$GLOBALS['TotalCost'] = CurrencyConvertFormatPrice($orderSummary['total']);

		$GLOBALS['HideAdjustedTotal'] = "none";
		$GLOBALS['SNIPPETS']['GiftCertificates'] = '';
		if($orderSummary['adjustedTotal'] != $orderSummary['total']) {
			$GLOBALS['HideAdjustedTotal'] = '';
			$GLOBALS['AdjustedTotalCost'] = $orderSummary['adjustedTotal'];
		}

		$GLOBALS['SNIPPETS']['Coupons'] = '';
		if (count($orderSummary['coupons'])) {
			foreach ($orderSummary['coupons'] as $coupon) {
				$GLOBALS['CouponId'] = $coupon['couponid'];
				$GLOBALS['CouponCode'] = $coupon['couponcode'];
				// percent coupon
				if ($coupon['coupontype'] == 1) {
					$discount = $coupon['discount'] . "%";
				}
				else {
					$discount = CurrencyConvertFormatPrice($coupon['discount']);
				}
				$GLOBALS['CouponDiscount'] = $discount;

				$GLOBALS['SNIPPETS']['Coupons'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("ConfirmOrderCoupon");
			}
		}

		// If we have any gift certificates, list those too
		if(!empty($orderSummary['giftCertificates'])) {
			foreach($orderSummary['giftCertificates'] as $certificate) {
				$GLOBALS['GiftCertificateCode'] = isc_html_escape($certificate['giftcertcode']);
				$GLOBALS['GiftCertificateId'] = $certificate['giftcertid'];
				$GLOBALS['GiftCertificateBalance'] = CurrencyConvertFormatPrice($certificate['giftcertbalance']);
				$GLOBALS['GiftCertificateRemaining'] = CurrencyConvertFormatPrice($certificate['balanceremaining']);
				$GLOBALS['CertificateAmountUsed'] = CurrencyConvertFormatPrice($certificate['amountused']);
				$GLOBALS['SNIPPETS']['GiftCertificates'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("ConfirmOrderGiftCertificate");
			}
		}

		// If the order total comes to $0.00, then we don't show the payment options and a lot of other things (because they have nothing to pay)
		if($orderSummary['adjustedTotal'] == 0) {
			$GLOBALS['HidePaymentOptions'] = "none";
			$GLOBALS['HideUseCoupon'] = 'none';
			$GLOBALS['HidePaymentProviderList'] = "none";
			$GLOBALS['PaymentButtonSwitch'] = "ShowContinueButton(); ExpressCheckout.UncheckPaymentProvider();";
		}

		// However, it's possible that they got to $0.00 by using a gift-certificate. What if they want to add a coupon now? They should be able to.
		if($orderSummary['adjustedTotal'] == 0 && !empty($orderSummary['giftCertificates']) && $orderSummary['total'] != 0) {
			$GLOBALS['HideUseCoupon'] = '';
		}

		// Does the customer have any store credit they can use?
		$GLOBALS['HideUseStoreCredit'] = "none";
		$GLOBALS['HideRemainingStoreCredit'] = "none";
		$customer = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerDataByToken();
		if ($customer['custstorecredit'] > 0) {
			$GLOBALS['HidePaymentOptions'] = "";
			$GLOBALS['StoreCredit'] = CurrencyConvertFormatPrice($customer['custstorecredit']);
			$GLOBALS['HideUseStoreCredit'] = "";
			$GLOBALS['HidePaymentProviderList'] = "none";
			// The customer has enough store credit to pay for the entirity of this order
			if ($customer['custstorecredit'] >= $orderSummary['adjustedTotal']) {
				$GLOBALS['PaymentButtonSwitch'] = "ShowContinueButton();";
				$GLOBALS['HideLimitedCreditWarning'] = "none";
				$GLOBALS['HideLimitedCreditPaymentOption'] = "none";
				$GLOBALS['HideCreditPaymentMethods'] = "none";
				$GLOBALS['RemainingCredit'] = $customer['custstorecredit'] - $orderSummary['adjustedTotal'];
				if ($GLOBALS['RemainingCredit'] > 0) {
					$GLOBALS['HideRemainingStoreCredit'] = '';
					$GLOBALS['RemainingCredit'] = CurrencyConvertFormatPrice($GLOBALS['RemainingCredit']);
				}
			}
			// Customer doesn't have enough store credit to pay for the order
			else {
				$GLOBALS['Remaining'] = CurrencyConvertFormatPrice($orderSummary['adjustedTotal']-$customer['custstorecredit']);

				if(count($checkoutProviders) == 1) {
					$GLOBALS['CheckoutStoreCreditWarning'] = sprintf(GetLang('CheckoutStoreCreditWarning2'), $GLOBALS['Remaining'], $GLOBALS['CheckoutWith']);
					$GLOBALS['HideLimitedCreditPaymentOption'] = "none";
				}
				else {
					$GLOBALS['CheckoutStoreCreditWarning'] = GetLang('CheckoutStoreCreditWarning');
				}
				$GLOBALS['ISC_LANG']['CreditPaymentMethod'] = sprintf(GetLang('CreditPaymentMethod'), $GLOBALS['Remaining']);
			}

			if (count($checkoutProviders) > 1) {
				$GLOBALS['CreditAlt'] = GetLang('CheckoutCreditAlt');
			}
			else if (count($checkoutProviders) <= 1 && isset($GLOBALS['CheckoutWith'])) {
				$GLOBALS['CreditAlt'] = sprintf(GetLang('CheckoutCreditAltOneMethod'), $GLOBALS['CheckoutWith']);
			}
			else {
				if ($customer['custstorecredit'] >= $orderSummary['adjustedTotal']) {
					$GLOBALS['HideCreditAltOptionList'] = "none";
					$GLOBALS['HideConfirmOrderPage'] = "";
					$GLOBALS['HideTopPaymentButton'] = "none";
					$GLOBALS['HideCheckoutError'] = "none";
					$GLOBALS['CheckoutErrorMsg'] = '';
				}
			}
		}

		// Customer has hit this page before. Delete the existing pending order
		// The reason we do a delete is if they're hitting this page again, something
		// has changed with their order or something has become invalid with it along the way.
		if (isset($_COOKIE['SHOP_ORDER_TOKEN']) && IsValidPendingOrderToken($_COOKIE['SHOP_ORDER_TOKEN'])) {
			$query = "
				SELECT orderid
				FROM [|PREFIX|]orders
				WHERE ordtoken='".$GLOBALS['ISC_CLASS_DB']->Quote($_COOKIE['SHOP_ORDER_TOKEN'])."' AND ordstatus=0
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($order = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$entity = new ISC_ENTITY_ORDER();
				if ($entity->delete($order['orderid'], true)) {
					$GLOBALS['ISC_CLASS_LOG']->LogSystemNotice('general', GetLang('OrderDeletedAutomatically', array('order' => $order['orderid'])));
				}
			}
		}

		// Are we showing an error message?
		if (isset($GLOBALS['CheckoutErrorMsg']) && $GLOBALS['CheckoutErrorMsg'] != '') {
			$GLOBALS['HideCheckoutError'] = '';
		}
		else {
			$GLOBALS['HideCheckoutError'] = "none";
		}

		// Is there a success message to show?
		if (isset($GLOBALS['CheckoutSuccessMsg']) && $GLOBALS['CheckoutSuccessMsg'] != '') {
			$GLOBALS['HideCheckoutSuccess'] = '';
		}
		else {
			$GLOBALS['HideCheckoutSuccess'] = "none";
		}

		// Save the information about the pending order in the checkout session, we'll be using it when they visit the next page
		$_SESSION['CHECKOUT']['PENDING_DATA'] = array(
			"ITEM_TOTAL" => $orderSummary['itemTotal'],
			"TAX_COST" => number_format($orderSummary['taxCost'], GetConfig('DecimalPlaces'), ".", ""),
			"TAX_INCLUDED" => $orderSummary['taxIncluded'],
			"ORDER_TOTAL" => number_format($orderSummary['total']-$GLOBALS['ISC_CLASS_CART']->api->Get('SUBTOTAL_DISCOUNT'), GetConfig('DecimalPlaces'), ".", ""),
			"GATEWAY_AMOUNT" => number_format($orderSummary['adjustedTotal'], GetConfig('DecimalPlaces'), ".", ""),
			"GIFTCERTIFICATE_AMOUNT" => number_format($orderSummary['giftCertificateTotal'], GetConfig('DecimalPlaces'), ".", ""),
			"SUBTOTAL_DISCOUNT" => number_format($GLOBALS['ISC_CLASS_CART']->api->Get('SUBTOTAL_DISCOUNT'), GetConfig('DecimalPlaces'), ".", "")
		);

		// Store information about each vendor in the order
		foreach($orderSummary['vendors'] as $vendorId => $addressInfo) {
			foreach($addressInfo as $addressId => $vendorInfo) {
				$_SESSION['CHECKOUT']['PENDING_DATA']['VENDORS'][$vendorId.'_'.$addressId] = array(
					'ITEM_TOTAL' => $vendorInfo['itemTotal'],
					'TAX_COST' => number_format($vendorInfo['taxCost'], GetConfig('DecimalPlaces'), ".", ""),
					'TAX_RATE' => number_format($vendorInfo['taxRate'], GetConfig('DecimalPlaces'), ".", ""),
					'TAX_NAME' => $vendorInfo['taxName'],
					'ORDER_TOTAL' => number_format($vendorInfo['total']-$GLOBALS['ISC_CLASS_CART']->api->Get('SUBTOTAL_DISCOUNT'), GetConfig('DecimalPlaces'), ".", "")
				);
			}
		}

		// If this is an anonymous checkout, save that
		if(isset($_POST['anonymousCheckout'])) {
			$_SESSION['CHECKOUT']['PENDING_DATA']['GUEST_CHECKOUT'] = 1;
		}
		else {
			$_SESSION['CHECKOUT']['PENDING_DATA']['GUEST_CHECKOUT'] = 0;
		}

		// Checkout out as a new customer and wishing to create an account, we need to save those details
		if(!CustomerIsSignedIn()) {
			if(isset($_POST['createAccount']) || GetConfig('GuestCheckoutCreateAccounts')) {
				// If we're automatically creating accounts, assign the user a random password
				$autoAccount = 0;
				if(isset($_POST['billing_Password'])) {
					$password = $_POST['billing_Password'];
				}
				if(!isset($_POST['createAccount']) && GetConfig('GuestCheckoutCreateAccounts')) {
					$password = substr(md5(uniqid(true)), 0, 8);
					$autoAccount = 1;
				}
				if(!isset($_SESSION['CHECKOUT']['CREATE_ACCOUNT']) && isset($_POST['billing_EmailAddress'])) {
					$_SESSION['CHECKOUT']['CREATE_ACCOUNT'] = 1;
					$_SESSION['CHECKOUT']['ACCOUNT_DETAILS'] = array(
						'email' => $_POST['billing_EmailAddress'],
						'password' => $password,
						'firstname' => $_POST['billing_FirstName'],
						'lastname' => $_POST['billing_LastName'],
						'company' => $_POST['billing_CompanyName'],
						'phone' => $_POST['billing_Phone'],
						'autoAccount' => $autoAccount
					);
				}
			}
			else {
				unset($_SESSION['CHECKOUT']['CREATE_ACCOUNT']);
				unset($_SESSION['CHECKOUT']['ACCOUNT_DETAILS']);
			}
		}
		else {
			unset($_SESSION['CHECKOUT']['CREATE_ACCOUNT']);
			unset($_SESSION['CHECKOUT']['ACCOUNT_DETAILS']);
		}

		if(GetConfig('EnableOrderComments') == 1) {
			$GLOBALS['HideOrderComments'] = "";
		} else {
			$GLOBALS['HideOrderComments'] = "none";
		}

		if ($GLOBALS['ISC_CLASS_CART']->api->Get('SUBTOTAL_DISCOUNT') == 0) {
			$GLOBALS['HideOrderDiscount'] = "display : none";
		} else {
			$GLOBALS['OrderDiscount'] = CurrencyConvertFormatPrice($GLOBALS['ISC_CLASS_CART']->api->Get('SUBTOTAL_DISCOUNT'));
			$GLOBALS['HideOrderDiscount'] = "";
		}

		if(GetConfig('EnableOrderTermsAndConditions') == 1) {

			$GLOBALS['HideOrderTermsAndConditions'] = "";

			if(GetConfig('OrderTermsAndConditionsType') == "link") {
				$GLOBALS['AgreeTermsAndConditions'] = GetLang('YesIAgree');

				$GLOBALS['TermsAndConditionsLink'] = "<a href='".GetConfig('OrderTermsAndConditionsLink')."' target='_BLANK'>".strtolower(GetLang('TermsAndConditions'))."</a>.";

				$GLOBALS['HideTermsAndConditionsTextarea'] = "display:none;";

			} else {
				$GLOBALS['HideTermsAndConditionsTextarea']= '';
				$GLOBALS['OrderTermsAndConditions'] = GetConfig('OrderTermsAndConditions');
				$GLOBALS['AgreeTermsAndConditions'] = GetLang('AgreeTermsAndConditions');
				$GLOBALS['TermsAndConditionsLink'] = '';
			}
		} else {
			$GLOBALS['HideOrderTermsAndConditions'] = "display:none;";
		}


		$GLOBALS['AdjustedTotalCost'] = CurrencyConvertFormatPrice($orderSummary['adjustedTotal']);
	}

	/**
	 * Calculate all of the taxes, shipping costs, item totals, gift certificate totals etc of this order.
	 * This method does all of the logic based calculation for the BuildOrderConfirmation method.
	 *
	 * @return array An array of information (summary) for the order.
	 */
	public function CalculateOrderSummary()
	{
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');

		$orderSummary = array(
			'digitalOrder' => 0,
			'itemTotal' => 0,
			'shippingCost' => 0,
			'handlingCost' => 0,
			'taxName' => '',
			'taxRate' => 0,
			'taxCost' => 0,
			'taxIncluded' => 0,
			'total' => 0,
			'adjustedTotal' => 0,
			'products' => array(),
			'giftCertificates' => array(),
			'giftCertificateTotal' => 0,
			'wrappingCost' => 0,
			'vendors' => array(),
			'coupons' => array()
		);

		$wrappingAdjustment = 0;

		// If this item contains one or more physical items, we need to calculate shipping etc
		if(!$GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {
			$orderSummary['shippingAddresses'] = $this->GetOrderShippingAddresses();
		}
		// Otherwise, this is a digital order only
		else {
			$orderSummary['digitalOrder'] = 1;
		}

		// Determine our billing & shipping addresses, also fetch the tax information
		if(isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
			if(!is_array($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
				$orderSummary['billingAddressId'] = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
			}
			else {
				$orderSummary['billingAddress'] = $_SESSION['CHECKOUT']['BILLING_ADDRESS'];
			}
			// Do we have tax we need to show/calculate?
			if(isset($_SESSION['CHECKOUT']['SHIPPING_ADDRESS'])) {
				$shippingAddress = $_SESSION['CHECKOUT']['SHIPPING_ADDRESS'];
			}
			else {
				$shippingAddress = '';
			}
			$salesTaxData = $GLOBALS['ISC_CLASS_CUSTOMER']->GetSalesTaxRate($_SESSION['CHECKOUT']['BILLING_ADDRESS'], $shippingAddress);

			// Tax needs to be applied
			if($salesTaxData['tax_rate'] > 0) {
				$orderSummary['taxName'] = $salesTaxData['tax_name'];
				$orderSummary['taxRate'] = $salesTaxData['tax_rate'];
			}
		}

		// Fetch a list of all of the products in the cart
		$cartContent = $this->BreakdownCartByAddressVendor();
		foreach($cartContent as $vendorId => $addresses) {
			foreach($addresses as $addressId => $products) {
				$itemTotal = 0;
				$taxableTotal = 0;
				$vendorWrapping = 0;
				$addressInfo = array(
					'itemTotal' => 0,
					'taxCost' => 0,
					'wrappingCost' => 0,
					'shippingCost' => 0,
					'handlingCost' => 0,
					'taxName' => '',
					'taxRate' => 0,
					'taxId' => 0
				);
				$onlyDigitalDownloads = true;
				foreach($products as $cartKey => $product) {
					$orderItem = array();

					$orderItem['data'] = $product['data'];
					$orderItem['quantity'] = $product['quantity'];

					// If the item is a gift certificate, we need to do a few things differently
					if(isset($product['type']) && $product['type'] == 'giftcertificate') {
						$orderItem['type'] = 'giftcertificate';
						$orderItem['price'] = $product['giftamount'];
						$orderItem['total'] = $product['giftamount'] * $product['quantity'];
						$orderItem['certificate'] = $product['certificate'];
						$onlyDigitalDownloads = false;
					}

					// Otherwise, this is just a standard product
					else {
						if($product['data']['prodtype'] == PT_PHYSICAL) {
							$onlyDigitalDownloads = false;
						}
						$orderItem['type'] = 'product';

						// If this product is discounted (coupon code) then use the discounted price
						if(isset($product['discount_price'])) {
							$orderItem['original_price'] = $product['original_price'];
							$orderItem['discount_price'] = $product['discount_price'];
							$orderItem['price'] = $product['discount_price'];
						}
						else {
							$orderItem['price'] = $product['product_price'];
						}

						if(isset($product['wrapping'])) {
							$vendorWrapping += $product['wrapping']['wrapprice'] * $orderItem['quantity'];
							$orderItem['wrapping_name'] = $product['wrapping']['wrapname'];
							if (isset($orderItem['wrapping_message']) && isset($product['wrapping']['wrapmessage'])) {
								$orderItem['wrapping_message'] = $product['wrapping']['wrapmessage'];
							}
						}
						$orderItem['total'] = ($orderItem['price'] * $orderItem['quantity']);
					}

					// This "subtotal" needs to be the sub total BEFORE tax was applied, assuming tax isn't included in our product prices
					$itemPrice = $orderItem['price'];

					if($orderItem['type'] != 'giftcertificate' && $orderItem['data']['prodistaxable'] && GetConfig('TaxTypeSelected') == 2 && !GetConfig('PricesIncludeTax')) {
						// The product price is already 100% + tax rate, so wee need go get it back to what it was before tax
						$itemPrice = ($itemPrice / (100 + GetConfig('DefaultTaxRate'))) * 100;
						$orderItem['price'] = $itemPrice;
						$orderItem['total'] = ($itemPrice * $orderItem['quantity']);
					}

					// Increment the order total
					$addressInfo['itemTotal'] += ($itemPrice * $orderItem['quantity']);
					if($orderItem['type'] != 'giftcertificate' && (!array_key_exists('prodistaxable', $product['data']) || $product['data']['prodistaxable'] == 1)) {
						$taxableTotal += ($itemPrice * $orderItem['quantity']);
					}

					if(isset($product['options'])) {
						$orderItem['options'] = $product['options'];
					}

					//is there any product custom fields
					if (isset($product['product_fields']) && !empty($product['product_fields'])) {
						$orderItem['productFields'] = $product['product_fields'];
					}

					//is there any product custom fields
					if (isset($product['event_date'])) {
						$orderItem['event_name'] = $product['event_name'];
						$orderItem['event_date'] = $product['event_date'];
					}


					$orderSummary['products'][] = $orderItem;
				}

				// Is there a handling cost associated?
				if(isset($_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId])) {
					$costing = $_SESSION['CHECKOUT']['SHIPPING'][$vendorId][$addressId];
					$addressInfo['shippingCost'] = $costing['COST'];
					if(isset($costing['HANDLING']) && $costing['HANDLING'] > 0) {
						$addressInfo['handlingCost'] = $costing['HANDLING'];
					}
				}

				// If this is a digital order, apply the digital handling fee
				if($onlyDigitalDownloads) {
					$addressInfo['handlingCost'] = GetConfig('DigitalOrderHandlingFee');
				}

				// get tax data for this address
				if (isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
					if ($addressId != '0') {
						$address = $addressId;
					}
					else {
						$address = $shippingAddress;
					}
					$salesTaxData = $GLOBALS['ISC_CLASS_CUSTOMER']->GetSalesTaxRate($_SESSION['CHECKOUT']['BILLING_ADDRESS'], $address);


					// Tax needs to be applied, so calculate it
					if($salesTaxData['tax_rate'] > 0) {
						$addressInfo['taxName'] = $salesTaxData['tax_name'];
						$addressInfo['taxRate'] = $salesTaxData['tax_rate'];
						$addressInfo['taxId'] = $salesTaxData['tax_id'];

						$taxableTotal += $vendorWrapping;

						// base taxable total
						$baseTotal = $taxableTotal;

						// Calculating tax based on the subtotal + shipping (and also handling if it's included in the shipping)
						if($salesTaxData['tax_based_on'] == 'subtotal_and_shipping') {
							if ($salesTaxData['tax_shipping'] == 0 || ($salesTaxData['tax_shipping'] == 1 && $taxableTotal > 0)) {
								$baseTotal += $addressInfo['shippingCost'];
							}
						}

						// Calculate the tax
						if(GetConfig('PricesIncludeTax')) {
							$addressInfo['taxCost'] = ($baseTotal / (100 + $salesTaxData['tax_rate'])) * $salesTaxData['tax_rate'];
							$orderSummary['taxIncluded'] = 1;
						}
						else {
							$addressInfo['taxCost'] = ($baseTotal / 100) * $salesTaxData['tax_rate'];
						}
					}
				}

				$addressInfo['wrappingCost'] = $vendorWrapping;

				$addressInfo['total'] = 0;
				$addressInfo['total'] += $addressInfo['itemTotal'];
				$addressInfo['total'] += $addressInfo['shippingCost'];
				$addressInfo['total'] += $addressInfo['handlingCost'];
				$addressInfo['total'] += $addressInfo['wrappingCost'];

				if(!GetConfig('PricesIncludeTax')) {
					$addressInfo['total'] += $addressInfo['taxCost'];
				}

				$orderSummary['vendors'][$vendorId][$addressId] = $addressInfo;
			}
		}

		// Only one shipping cost. The name of the provider is whatever the customer chose
		if(isset($_SESSION['CHECKOUT']['SHIPPING']) && count($_SESSION['CHECKOUT']['SHIPPING']) == 1 && !isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {
			$provider = current($_SESSION['CHECKOUT']['SHIPPING']);
			$provider = current($provider);
			$orderSummary['shippingProvider'] = $provider['PROVIDER'];
		}
		// More than one
		else if(isset($_SESSION['CHECKOUT']['SHIPPING'])) {
			$orderSummary['shippingProvider'] = GetLang('ShippingMethodCombined');
		}

		$orderSummary['wrappingCost'] = $wrappingAdjustment;

		// Work out the grand total for the order
		foreach($orderSummary['vendors'] as $addresses) {
			foreach($addresses as $vendorInfo) {
				$orderSummary['total'] += $vendorInfo['total'];
				$orderSummary['itemTotal'] += $vendorInfo['itemTotal'];
				$orderSummary['shippingCost'] += $vendorInfo['shippingCost'];
				$orderSummary['handlingCost'] += $vendorInfo['handlingCost'];
				$orderSummary['wrappingCost'] += $vendorInfo['wrappingCost'];
				$orderSummary['taxCost'] += $vendorInfo['taxCost'];
			}
		}

		$orderSummary['adjustedTotal'] = $orderSummary['total'];

		// Has the customer chosen one or more gift certificates to apply to this order? We need to adjust the total cost of the order to show what they need to pay.
		if (isset($_SESSION['CART']['GIFTCERTIFICATES']) && is_array($_SESSION['CART']['GIFTCERTIFICATES'])) {
			$certificates = $_SESSION['CART']['GIFTCERTIFICATES'];

			$adjustedTotal = $orderSummary['total'];
			$giftCertificateTotal = 0;

			uasort($certificates, "GiftCertificateSort");
			foreach ($certificates as $certificate) {
				if ($certificate['giftcertbalance'] > $adjustedTotal) {
					$certificate['balanceremaining'] = $certificate['giftcertbalance'] - $adjustedTotal;
					$certificate['amountused'] = $certificate['giftcertbalance'] -$certificate['balanceremaining'];
				}
				else {
					$certificate['amountused'] = $certificate['giftcertbalance'];
					$certificate['balanceremaining'] = 0;
				}
				// Subtract from the adjusted order total
				$giftCertificateTotal += $certificate['amountused'];
				$adjustedTotal -= $certificate['giftcertbalance'];
				if($adjustedTotal < 0) {
					$adjustedTotal = 0;
				}
				$orderSummary['giftCertificates'][] = $certificate;
			}

			// Set the adjusted cost of the order (what needs to be paid)
			$orderSummary['adjustedTotal'] = $adjustedTotal;
			$orderSummary['giftCertificateTotal'] = $giftCertificateTotal;
		}

		$orderSummary['adjustedTotal'] -= $GLOBALS['ISC_CLASS_CART']->api->Get('SUBTOTAL_DISCOUNT');

		$orderSummary['coupons'] = $GLOBALS['ISC_CLASS_CART']->api->GetAppliedCouponCodes();

		// Send the order summary back
		return $orderSummary;
	}

	/**
	 * This function can be used when the checkout button for paticular payment providers are on the cart page,
	 * it calls the coresponding function in the checkout module to set needed data and send to the provider
	 *
	 */
	public function SetExternalCheckout()
	{
		if(!isset($_REQUEST['provider'])) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}
		if(!GetModuleById('checkout', $provider, $_REQUEST['provider'])) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}
		// This gateway doesn't support a ping back/notification
		if(!method_exists($provider, 'SetCheckoutData')) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}
		$provider->SetCheckoutData();
	}

	/**
	 * Filter a field and if it's empty, return false. Used in an array_filter in SetPanelSettings()
	 *
	 * @param string The field value.
	 * @return boolean False if the field is empty.
	 * @see SetPanelSettings
	 */
	public function FilterAddressFields($field)
	{
		if(!$field) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	* Checks the stock levels of each product during various stages of checkout to ensure you can't purchase a product that has become out of stock
	*
	*/
	private function CheckStockLevels()
	{
		// products in cart are out of stock
		$productIds = '';
		$outOfStock = false;
		foreach($_SESSION['CART']['ITEMS'] as $item) {
			$productIds .= $item['product_id'].",";
		}

		$productIds = rtrim($productIds, ",");
		if(!$productIds) {
			return;
		}

		$products = array();
		$query = "SELECT productid, prodcurrentinv, prodinvtrack FROM [|PREFIX|]products WHERE productid IN (".$productIds.")";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			$products[$row['productid']] = $row;
		}

		$OutOfStock = '';
		foreach($_SESSION['CART']['ITEMS'] as $item) {
			if (isset($item['type']) && $item['type'] == "giftcertificate") {
				continue;
			}

			$product = $products[$item['product_id']];

			// Inventory tracking at product level
			if ($product['prodinvtrack'] == 1) {
				if ($product['prodcurrentinv'] < $item['quantity']) {
					$OutOfStock = $item['product_name'];
					break;
				}
			}

			// Inventory tracking at product option level
			if ($product['prodinvtrack'] == 2) {
				$inventory = array();

				// check that the selected combination has stock
				$query = sprintf("SELECT vcstock FROM [|PREFIX|]product_variation_combinations WHERE combinationid = '%d'", $GLOBALS['ISC_CLASS_DB']->Quote($item['variation_id']));
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$VcStock = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);
				if ($VcStock < $item['quantity']) {
					$OutOfStock = $item['product_name'];
					break;
				}
			}
		}
		if($OutOfStock != '') {
			unset($_SESSION['CHECKOUT']);
			$_SESSION['CART_CHANGED'] = true;

			@ob_end_clean();
			$_SESSION['CART']['ERROR'] = sprintf(GetLang('CheckoutInvLevelBelowOrderQty'), $OutOfStock);
			header("Location: ".GetConfig('ShopPath').'/compras.php');
			exit;
		}
	}

	public function ProcessGatewayCallBack()
	{
		if(!isset($_REQUEST['provider'])) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}
		if(!GetModuleById('checkout', $provider, $_REQUEST['provider'])) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}

		if(!isset($_REQUEST['callback'])) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}
		// This gateway doesn't support a ping back/notification
		if(!method_exists($provider, $_REQUEST['callback'])) {
			header("Location: ".$GLOBALS['ShopPath']."/compras.php");
			exit;
		}

		$provider->$_REQUEST['callback']();
	}
}