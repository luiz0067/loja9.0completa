<?php

	CLASS ISC_CARTCONTENT_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{

			$count = 0;
			$subtotal = 0;

			$_SESSION['CHECKOUT'] = array();

			// Get a list of all products in the cart
			$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
			$product_array = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();

			$GLOBALS['AdditionalCheckoutButtons'] = '';


			// Go through all the checkout modules looking for one with a GetSidePanelCheckoutButton function defined
			$ShowCheckoutButton = false;
			if (!empty($product_array)) {
				foreach (GetAvailableModules('checkout', true, true) as $module) {
					if(isset($module['object']->_showBothButtons) && $module['object']->_showBothButtons) {
						$ShowCheckoutButton = true;
						$GLOBALS['AdditionalCheckoutButtons'] .= $module['object']->GetCheckoutButton();
					} elseif (method_exists($module['object'], 'GetCheckoutButton')) {
						$GLOBALS['AdditionalCheckoutButtons'] .= $module['object']->GetCheckoutButton();
					} else {
						$ShowCheckoutButton = true;
					}
				}
			}

			$GLOBALS['HideMultipleAddressShipping'] = 'display: none';
			if(gzte11(ISC_MEDIUMPRINT) && $GLOBALS['ISC_CLASS_CART']->api->GetNumPhysicalProducts() > 1 && $ShowCheckoutButton && GetConfig("MultipleShippingAddresses")) {
				$GLOBALS['HideMultipleAddressShipping'] = '';
			}

			$GLOBALS['HideCheckoutButton'] = '';

			if (!$ShowCheckoutButton) {
				$GLOBALS['HideCheckoutButton'] = 'display: none';
				$GLOBALS['HideMultipleAddressShippingOr'] = 'display: none';
			}

			$wrappingOptions = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('GiftWrapping');
			if(empty($wrappingOptions)) {
				$publicWrappingOptions = false;
			}
			else {
				$publicWrappingOptions = true;
			}

			if(!GetConfig('ShowThumbsInCart')) {
				$GLOBALS['HideThumbColumn'] = 'display: none';
				$GLOBALS['ProductNameSpan'] = 2;
			}
			else {
				$GLOBALS['HideThumbColumn'] = '';
				$GLOBALS['ProductNameSpan'] = 1;
			}

			$wrappingAdjustment = 0;
			$itemTotal = 0;

			$GLOBALS['SNIPPETS']['CartItems'] = "";

			foreach ($product_array as $k => $product) {
				$GLOBALS['CartItemId'] = (int) $product['cartitemid'];

				// If the item in the cart is a gift certificate, we need to show a special type of row
				if (isset($product['type']) && $product['type'] == "giftcertificate") {
					$GLOBALS['GiftCertificateName'] = isc_html_escape($product['data']['prodname']);
					$GLOBALS['GiftCertificateAmount'] = CurrencyConvertFormatPrice($product['giftamount']);

					$GLOBALS['GiftCertificateTo'] = isc_html_escape($product['certificate']['to_name']);

					$GLOBALS["Quantity" . $product['quantity']] = 'selected="selected"';

					$GLOBALS['ProductPrice'] = CurrencyConvertFormatPrice($product['giftamount']);
					$GLOBALS['ProductTotal'] = CurrencyConvertFormatPrice($product['giftamount'] * $product['quantity']);

					$itemTotal += $product['giftamount']*$product['quantity'];

					$GLOBALS['SNIPPETS']['CartItems'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartItemGiftCertificate");
				}
				// Normal product in the cart - show a product row
				else {
					$GLOBALS['ProductLink'] = ProdLink($product['data']['prodname']);
					$GLOBALS['ProductAvailability'] = isc_html_escape($product['data']['prodavailability']);
					$GLOBALS['ItemId'] = (int) $product['data']['productid'];
					$GLOBALS['VariationId'] = (int) $product['variation_id'];
					$GLOBALS['ProductQuantity'] = (int) $product['quantity'];

					// Should we show thumbnails in the cart?
					if(!empty($product['data']['vcimagethumb'])) {
						$productImage = getConfig('ShopPath') . '/' . getConfig('ImageDirectory') . '/' . $product['data']['vcimagethumb'];
					}
					else {
						$productImage = $product['data'];
					}

					if (GetConfig('ShowThumbsInCart')) {
						$GLOBALS['ProductImage'] = ImageThumb($productImage, ProdLink($product['data']['prodname']));
					}

					$GLOBALS['UpdateCartQtyJs'] = "Cart.UpdateQuantity(this.options[this.selectedIndex].value);";

					$GLOBALS['HideCartProductFields'] = 'display:none;';
					$GLOBALS['CartProductFields'] = '';
					$this->GetProductFieldDetails($product['product_fields'], $k);

					$GLOBALS['EventDate'] = '';
					if (isset($product['event_date'])) {
						$GLOBALS['EventDate'] = '<div style="font-style: italic; font-size:10px; color:gray">(' . $product['event_name'] . ': ' . isc_date('M jS Y', $product['event_date']) . ')</div>';
					}

					// Can this product be wrapped?
					$GLOBALS['GiftWrappingName'] = '';
					$GLOBALS['HideGiftWrappingAdd'] = '';
					$GLOBALS['HideGiftWrappingEdit'] = 'display: none';
					$GLOBALS['HideGiftWrappingPrice'] = 'display: none';
					$GLOBALS['GiftWrappingPrice'] = '';
					$GLOBALS['GiftMessagePreview'] = '';
					$GLOBALS['HideGiftMessagePreview'] = 'display: none';
					$GLOBALS['HideWrappingOptions'] = 'display: none';

					if($product['data']['prodtype'] == PT_PHYSICAL && $product['data']['prodwrapoptions'] != -1 && $publicWrappingOptions == true) {
						$GLOBALS['HideWrappingOptions'] = '';

						if(isset($product['wrapping'])) {
							$GLOBALS['GiftWrappingName'] = isc_html_escape($product['wrapping']['wrapname']);
							$GLOBALS['HideGiftWrappingAdd'] = 'display: none';
							$GLOBALS['HideGiftWrappingEdit'] = '';
							$GLOBALS['HideGiftWrappingPrice'] = '';
							$wrappingAdjustment += $product['wrapping']['wrapprice']*$product['quantity'];
							$GLOBALS['GiftWrappingPrice'] = CurrencyConvertFormatPrice($product['wrapping']['wrapprice']);
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
					}

					$subtotalPrice = 0;
					if (isset($product['discount_price'])) {
						$subtotalPrice = $product['discount_price'];
					} else {
						$subtotalPrice = $product['product_price'];
					}

					if (isset($product['discount_price']) && $product['discount_price'] != $product['original_price']) {
						$GLOBALS['ProductPrice'] = sprintf("<s class='CartStrike'>%s</s> %s", CurrencyConvertFormatPrice($product['original_price']), CurrencyConvertFormatPrice($subtotalPrice));
					} else {
						$GLOBALS['ProductPrice'] = CurrencyConvertFormatPrice($subtotalPrice);
					}

					$GLOBALS['ProductTotal'] = CurrencyConvertFormatPrice(($subtotalPrice * $product['quantity']));

					$itemTotal += $subtotalPrice * $product['quantity'];

					// If we're using a cart quantity drop down, load that
					if (GetConfig('TagCartQuantityBoxes') == 'dropdown') {
						$GLOBALS["Quantity" . $product['quantity']] = "selected=\"selected\"";
						if(isset($GLOBALS["Quantity0"])) {
							$GLOBALS['QtyOptionZero'] = "<option ".$GLOBALS["Quantity0"]." value='0'>0</option>";
						}
						else {
							$GLOBALS['QtyOptionZero'] = "<option value='0'>0</option>";
						}

						// Fixes products being displayed with '0' quantity when the quantity is greater than 30 (hard coded limit in snippet)
						if ($product['quantity'] > 30) {
							$GLOBALS["QtyOptionSelected"] = "<option ".$GLOBALS["Quantity" . $product['quantity']]." value='" . $product['quantity'] . "'>" . $product['quantity'] . "</option>";
						}

						$GLOBALS['CartItemQty'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartItemQtySelect");
					}
					// Otherwise, load the textbox
					else {
						$GLOBALS['CartItemQty'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartItemQtyText");
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

					$GLOBALS['ProductName'] = isc_html_escape($product['data']['prodname']);
					$GLOBALS['SNIPPETS']['CartItems'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartItem");
				}
				$GLOBALS["Quantity" . $product['quantity']] = "";
			}

			if($wrappingAdjustment > 0) {
				$GLOBALS['GiftWrappingTotal'] = CurrencyConvertFormatPrice($wrappingAdjustment);
			}
			else {
				$GLOBALS['HideGiftWrappingTotal'] = 'display: none';
			}

			$GLOBALS['HideAdjustedTotal'] = "none";

			$GLOBALS['AdjustedCartSubTotal'] = $GLOBALS['CartSubTotal'] - $GLOBALS['CartSubTotalDiscount'];

			$GLOBALS['CartItemTotal'] = CurrencyConvertFormatPrice($itemTotal);


			$GLOBALS['SNIPPETS']['Coupons'] = '';

			$coupons = $GLOBALS['ISC_CLASS_CART']->api->GetAppliedCouponCodes();
			if (count($coupons)) {
				foreach ($coupons as $coupon) {
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

					$GLOBALS['SNIPPETS']['Coupons'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartCoupon");
				}
			}

			$GLOBALS['SNIPPETS']['GiftCertificates'] = '';

			// Has the customer chosen one or more gift certificates to apply to this order? We need to show them
			if (isset($_SESSION['CART']['GIFTCERTIFICATES']) && is_array($_SESSION['CART']['GIFTCERTIFICATES'])) {
				$certificates = $_SESSION['CART']['GIFTCERTIFICATES'];

				uasort($certificates, "GiftCertificateSort");

				foreach ($certificates as $certificate) {
					$GLOBALS['GiftCertificateCode'] = isc_html_escape($certificate['giftcertcode']);
					$GLOBALS['GiftCertificateId'] = $certificate['giftcertid'];
					$GLOBALS['GiftCertificateBalance'] = $certificate['giftcertbalance'];

					if ($GLOBALS['GiftCertificateBalance'] > $GLOBALS['AdjustedCartSubTotal']) {
						$GLOBALS['GiftCertificateRemaining'] = $certificate['giftcertbalance'] - $GLOBALS['AdjustedCartSubTotal'];
						$GLOBALS['CertificateAmountUsed'] = $certificate['giftcertbalance'] - $GLOBALS['GiftCertificateRemaining'];
					} else {
						$GLOBALS['CertificateAmountUsed'] = $certificate['giftcertbalance'];
						$GLOBALS['GiftCertificateRemaining'] = 0;
					}

					// Subtract this amount from the adjusted total
					$GLOBALS['AdjustedCartSubTotal'] -= $GLOBALS['GiftCertificateBalance'];
					if ($GLOBALS['AdjustedCartSubTotal'] <= 0) {
						$GLOBALS['AdjustedCartSubTotal'] = 0;
					}

					$GLOBALS['GiftCertificateBalance'] = CurrencyConvertFormatPrice($GLOBALS['GiftCertificateBalance']);
					$GLOBALS['GiftCertificateRemaining'] = CurrencyConvertFormatPrice($GLOBALS['GiftCertificateRemaining']);
					$GLOBALS['CertificateAmountUsed'] = CurrencyConvertFormatPrice($GLOBALS['CertificateAmountUsed']);

					$GLOBALS['SNIPPETS']['GiftCertificates'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartGiftCertificate");
				}

				if ($GLOBALS['SNIPPETS']['GiftCertificates']) {
					$GLOBALS['HideAdjustedTotal'] = '';
					if ($GLOBALS['AdjustedCartSubTotal'] == 0) {
						$GLOBALS['HidePanels'][] = "SideGiftCertificateCodeBox";
					}
				}
			}

			if ($GLOBALS['AdjustedCartSubTotal'] != $GLOBALS['CartSubTotal']) {
				$GLOBALS['HideAdjustedTotal'] = "";

				$GLOBALS['AdjustedCartSubTotal'] = CurrencyConvertFormatPrice($GLOBALS['AdjustedCartSubTotal']);
			}
			$GLOBALS['CartSubTotal'] = CurrencyConvertFormatPrice($GLOBALS['CartSubTotal']);

			if (!gzte11(ISC_LARGEPRINT)) {
				$GLOBALS['HidePanels'][] = "SideGiftCertificateCodeBox";
			}

			// Are there any products in the cart?
			if ($GLOBALS['ISC_CLASS_CART']->api->GetNumProductsInCart() == 0) {
				$GLOBALS['HideShoppingCartGrid'] = "none";
			} else {
				$GLOBALS['HideShoppingCartEmptyMessage'] = "none";
			}
		}

		public function GetProductFieldDetails($productFields, $cartItemId)
		{
			// custom product fields on cart page
			$GLOBALS['HideCartProductFields'] = 'display:none;';
			$GLOBALS['CartProductFields'] = '';
			if(isset($productFields) && !empty($productFields) && is_array($productFields)) {
				$GLOBALS['HideCartProductFields'] = '';
				foreach($productFields as $filedId => $field) {

					switch ($field['fieldType']) {
						//field is a file
						case 'file': {

							//file is an image, display the image
							$fieldValue = '<a target="_Blank" href="'.$GLOBALS['ShopPath'].'/arquivos.php?cartitem='.$cartItemId.'&prodfield='.$filedId.'">'.isc_html_escape($field['fileOriginName']).'</a>';
							break;
						}
						//field is a checkbox
						case 'checkbox': {
							$fieldValue = GetLang('Checked');
							break;
						}
						//if field is a text area or short text display first
						default: {
							if(isc_strlen($field['fieldValue'])>50) {
								$fieldValue = isc_substr(isc_html_escape($field['fieldValue']), 0, 50)." ..";
							} else {
								$fieldValue = isc_html_escape($field['fieldValue']);
							}
						}
					}

					if(trim($fieldValue) != '') {
						$GLOBALS['CustomFieldName'] = isc_html_escape($field['fieldName']);
						$GLOBALS['CustomFieldValue'] = $fieldValue;
						$GLOBALS['CartProductFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CartProductFields");
					}
				}
			}
		}
	}
