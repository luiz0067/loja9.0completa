<script type="text/javascript" src="../javascript/formfield.js?%%GLOBAL_JSCacheToken%%"></script>
<form action="index.php?ToDo=%%GLOBAL_FormAction%%" onsubmit="return ValidateForm(checkAddCustomerForm)" id="frmCustomer" method="post">
<input type="hidden" name="customerId" id="customerId" value="%%GLOBAL_CustomerId%%">
<input type="hidden" name="newCustomer" id="newCustomer" value="%%GLOBAL_NewCustomer%%">
<input id="currentTab" name="currentTab" value="0" type="hidden">
<div class="BodyContainer">
	<table cellSpacing="0" cellPadding="0" width="100%" style="margin-left: 4px; margin-top: 8px;">
	<tr>
		<td class="Heading1">%%GLOBAL_Title%%</td>
	</tr>
	<tr>
		<td class="Intro">
			<p>%%GLOBAL_Intro%%</p>
			<div id="MessageTmpBlock">%%GLOBAL_Message%%</div>
			<p>
				<input type="submit" value="%%LNG_SaveAndExit%%" class="FormButton" name="SaveButton1" />
				<input type="submit" value="%%GLOBAL_SaveAndAddAnother%%" name="SaveContinueButton1"  onclick="saveAndAddAnother();" class="FormButton Field150" />
				<input type="reset" value="%%LNG_Cancel%%" class="FormButton" name="CancelButton1" onclick="confirmCancel()" />
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<ul id="tabnav">
				<li><a href="#" id="tab0" onclick="ShowTab(0)" class="active">%%LNG_CustomerDetails%%</a></li>
				<li><a href="#" id="tab1" onclick="ShowTab(1)">%%LNG_CustomerAddressBook%%</a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td>
			<div id="div0" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_CustomerDetailsIntro%%</div>
				<table width="100%" class="Panel">
					<tr>
						<td class="Heading2" colspan="2">%%LNG_CustomerDetails%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_CustomerFirstName%%:
						</td>
						<td>
							<input type="text" id="custFirstName" name="custFirstName" class="Field300" value="%%GLOBAL_CustomerFirstName%%">
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_CustomerLastName%%:
						</td>
						<td>
							<input type="text" id="custLastName" name="custLastName" class="Field300" value="%%GLOBAL_CustomerLastName%%">
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_CustomerCompany%%:
						</td>
						<td>
							<input type="text" id="custCompany" name="custCompany" class="Field300" value="%%GLOBAL_CustomerCompany%%">
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_CustomerEmail%%:
						</td>
						<td>
							<input type="text" id="custEmail" name="custEmail" class="Field300" value="%%GLOBAL_CustomerEmail%%">
							<input type="button" onclick="checkEmailUniqueRequest(); return false;" value="%%LNG_CustomerEmailUniqueCheckLink%%" class="FormButton Field120"/>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_CustomerGroup%%:
						</td>
						<td>
							<select id="custGroupId" name="custGroupId" class="Field300">
								<option value="0">%%LNG_CustomerGroupNotAssoc%%</option>
								%%GLOBAL_CustomerGroupOptions%%
							</select>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_CustomerPhone%%:
						</td>
						<td>
							<input type="text" id="custPhone" name="custPhone" class="Field80" value="%%GLOBAL_CustomerPhone%%">
						</td>
					</tr>
					<tr style="display: %%GLOBAL_HideStoreCredit%%;">
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_CustomerStoreCredit%%:
						</td>
						<td>
							%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="custStoreCredit" name="custStoreCredit" class="Field50" value="%%GLOBAL_CustomerStoreCredit%%"> %%GLOBAL_CurrencyTokenRight%%
							<img onmouseout="HideHelp('dcuststorecredit');" onmouseover="ShowHelp('dcuststorecredit', '%%LNG_CustomerStoreCredit%%', '%%LNG_CustomerStoreCreditHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="dcuststorecredit"></div>
						</td>
					</tr>
					<tr style="display: %%GLOBAL_HideCustomFields%%;">
						<td colspan="2" style="padding: 0px; margin:0px;">
							<dl class="FormFieldBackend">
								%%GLOBAL_CustomFields%%
							</dl>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="Heading2" colspan="2">%%LNG_CustomerPasswordDetails%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							%%GLOBAL_PasswordRequired%%&nbsp;%%GLOBAL_PasswordLabel%%:
						</td>
						<td>
							<input type="password" id="custPassword" name="custPassword" class="Field250" value="%%GLOBAL_CustomerPassword%%" AUTOCOMPLETE = "OFF">
							<img onmouseout="HideHelp('dcustpassword');" onmouseover="ShowHelp('dcustpassword', '%%GLOBAL_PasswordLabel%%', '%%GLOBAL_PasswordHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="dcustpassword"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							%%GLOBAL_PasswordConfirmRequired%%&nbsp;%%LNG_CustomerPasswordConfirm%%:
						</td>
						<td>
							<input type="password" id="custPasswordConfirm" name="custPasswordConfirm" class="Field250" value="%%GLOBAL_CustomerPasswordConfirm%%" AUTOCOMPLETE = "OFF">
							<img onmouseout="HideHelp('dcustpasswordconfirm');" onmouseover="ShowHelp('dcustpasswordconfirm', '%%LNG_CustomerPasswordConfirm%%', '%%GLOBAL_PasswordConfirmHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="dcustpasswordconfirm"></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="div1" style="padding-top: 10px;">
			<div style="padding-bottom:5px">%%LNG_CustomerDetailsIntro%%</div>
			<div class="MessageBox MessageBoxInfo" style="display: %%GLOBAL_CustomerAddressEmptyShow%%">%%GLOBAL_CustomerAddressListWarning%%</div>
			<p class="Intro" style="display: %%GLOBAL_HideCustomerAddressButtons%%;">
				<input type="button" value="%%LNG_CustomerAddShippingAddress%%" onclick="addShippingAddress();" class="SmallButton Field150" %%GLOBAL_CustomerAddressAddDisabled%% />
				<input type="button" value="%%LNG_DeleteSelected%%" name="DeleteAddressesButton" onclick="confirmDeleteAddressBoxes();" class="SmallButton Field150" %%GLOBAL_CustomerAddressDeleteDisabled%% />
			</p>
			<div class="GridContainer" style="display: %%GLOBAL_CustomerAddressEmptyHide%%">
				%%GLOBAL_CustomerShippingAddressGrid%%
			</div><br />
			<br />
		</div>

		<table border="0" cellspacing="0" cellpadding="2" width="100%" class="PanelPlain" id="SaveButtons">
			<tr>
				<td>
					<input type="submit" value="%%LNG_SaveAndExit%%" class="FormButton" name="SaveButton2" />
					<input type="submit" value="%%GLOBAL_SaveAndAddAnother%%" name="SaveContinueButton2" onclick="saveAndAddAnother();" class="FormButton Field150" />
					<input type="reset" value="%%LNG_Cancel%%" class="FormButton" name="CancelButton2" onclick="confirmCancel()" />
				</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>
</div>
</form>

<script type="text/javascript"><!--

	$(document).ready(function() {
		ShowTab(%%GLOBAL_CurrentTab%%);
	});

	function ShowTab(T)
	{
			i = 0;
			while (document.getElementById("tab" + i) != null) {
				document.getElementById("div" + i).style.display = "none";
				document.getElementById("tab" + i).className = "";
				i++;
			}

			if (T == 1) {
				$('#SaveButtons').hide();
			} else {
				$('#SaveButtons').show();
			}

			document.getElementById("div" + T).style.display = "";
			document.getElementById("tab" + T).className = "active";
			document.getElementById("currentTab").value = T;
	}

	function getAddressBoxes()
	{
		return $("#IndexGrid :checkbox[name='addresses[]']");
	}

	function selectedAddressBoxes()
	{
		return getAddressBoxes().not("[checked=false]");
	}

	function toggleAddressBoxes(status)
	{
		getAddressBoxes().each(function() { $(this).attr("checked", status); });
	}

	function confirmDeleteAddressBoxes(addressId)
	{
		if ((!isNaN(addressId) && addressId > 0) || selectedAddressBoxes().length > 0) {
			if (confirm("%%LNG_ConfirmDeleteCustomerAddresses%%")) {
				if (!isNaN(addressId) && addressId > 0) {
					MakeHidden('addresses', addressId, 'frmCustomer');
				}
				document.getElementById("frmCustomer").action = "index.php?ToDo=deleteCustomerAddress";
				document.getElementById("frmCustomer").submit();
			}
		} else {
			alert("%%LNG_ChooseCustomerAddress%%");
		}
	}

	function saveAndAddAnother() {
		MakeHidden('addanother', '1', 'frmCustomer');
	}

	function saveAndAddAddress() {
		ShowTab(0);
		if (checkAddCustomerForm()) {
			MakeHidden('addaddresses', '1', 'frmCustomer');
			document.getElementById("frmCustomer").submit();
		}
	}

	function confirmCancel() {
		if(confirm('%%GLOBAL_CancelMessage%%')) {
			document.location.href='index.php?ToDo=viewCustomers';
		} else {
			return false;
		}
	}

	function checkAddCustomerForm()
	{
		var checkFileds = new Array();

		checkFileds['custFirstName'] = "%%LNG_CustomerFirstNameRequired%%"
		checkFileds['custLastName'] = "%%LNG_CustomerLastNameRequired%%"
		checkFileds['custEmail'] = "%%LNG_CustomerEmailRequired%%";

		if ("%%GLOBAL_PasswordRequiredCheck%%" == "1") {
			checkFileds['custPassword'] = "%%LNG_CustomerPasswordRequired%%";
		}

		for (var i in checkFileds) {
			if ($('#' + i).val() == '') {
				alert(checkFileds[i]);
				$('#' + i).focus();
				return false;
			}
		}

		if($('#custEmail').val().indexOf("@") == -1 || $('#custEmail').val().indexOf(".") == -1) {
			alert("%%LNG_CustomerEmailInvalue%%");
			$('#custEmail').focus();
			return false;
                }

		if ($('#custPassword').val() !== $('#custPasswordConfirm').val()) {
			alert("%%GLOBAL_PasswordConfirmError%%");
			$('#custPassword').focus();
			return false;
		}

		if($('#custStoreCredit').val() && isNaN(priceFormat($('#custStoreCredit').val()))) {
			alert("%%LNG_CustomerStoreCreditError%%");
			$('#custStoreCredit').focus().select();
			return false;
		}

		/**
		 * Now for the custom fields
		 */
		var formfields = FormField.GetValues(%%GLOBAL_CustomFieldsAccountFormId%%);

		for (var i=0; i<formfields.length; i++) {
			var rtn = FormField.Validate(formfields[i].field);

			if (!rtn.status) {
				alert(rtn.msg);
				FormField.Focus(formfields[i].field);
				return false;
			}
		}

		return true;
	}

	function checkEmailUniqueRequest(formCheck)
	{
		if (formCheck !== 1) {
			formCheck = 0;
		}

		var obj = {};

		obj.type    = 'POST';
		obj.url     = 'remote.php';
		obj.data    = {
				'w'            : 'checkemailuniqueness',
				'remoteSection': 'customers',
				'customerId'   : '%%GLOBAL_CustomerId%%',
				'email'        : $('#custEmail').val()
				};
		obj.success = checkEmailUniqueResponse;

		$.ajax(obj);
	}

	function checkEmailUniqueResponse(data)
	{
		var message = $('message', data).text();

		$('#MessageTmpBlock').hide();
		$('#MessageTmpBlock').html(message);
		$('#MessageTmpBlock').show('slow');
	}

	function addShippingAddress()
	{
		document.getElementById('frmCustomer').action = 'index.php?ToDo=addCustomerAddress';
		document.getElementById('frmCustomer').submit();
		return false;
	}

	function editShippingAddress(addressId)
	{
		MakeHidden('addressId', addressId, 'frmCustomer');
		document.getElementById('frmCustomer').action = 'index.php?ToDo=editCustomerAddress';
		document.getElementById('frmCustomer').submit();
		return false;
	}

	%%GLOBAL_FormFieldEventData%%

//--></script>