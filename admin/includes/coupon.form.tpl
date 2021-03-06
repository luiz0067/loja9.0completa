
	<form enctype="multipart/form-data" action="index.php?ToDo=%%GLOBAL_FormAction%%" onsubmit="return ValidateForm(CheckCouponForm)" id="frmNews" method="post">
	<input type="hidden" id="couponId" name="couponId" value="%%GLOBAL_CouponId%%">
	<input type="hidden" id="couponexpires" name="couponexpires" value="">
	<input type="hidden" id="couponCode" name="couponcode" value="%%GLOBAL_CouponCode%%">
	<div class="BodyContainer">
	<table class="OuterPanel">
	  <tr>
		<td class="Heading1" id="tdHeading">%%GLOBAL_Title%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%GLOBAL_Intro%%</p>
			%%GLOBAL_Message%%
			<p><input type="submit" name="SubmitButton1" value="%%LNG_Save%%" class="FormButton">&nbsp; <input type="button" name="CancelButton1" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()"></p>
		</td>
	  </tr>
		<tr>
			<td>
			  <table class="Panel">
				<tr>
				  <td class="Heading2" colspan=2>%%LNG_NewCouponDetails%%</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_CouponCode%%:
					</td>
					<td>
						<input type="text" id="couponcode" name="couponcode" class="Field250" value="%%GLOBAL_CouponCode%%" />
						<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '%%LNG_CouponCode%%', '%%LNG_CouponCodeHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d1"></div>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_CouponName%%:
					</td>
					<td>
						<input type="text" id="couponname" name="couponname" class="Field250" value="%%GLOBAL_CouponName%%">
						<img onmouseout="HideHelp('d6');" onmouseover="ShowHelp('d6', '%%LNG_CouponName%%', '%%LNG_CouponNameHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d6"></div>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_DiscountAmount%%:
					</td>
					<td>
						<input type="text" id="couponamount" name="couponamount" class="Field50" value="%%GLOBAL_DiscountAmount%%">
						<select type="text" id="coupontype" name="coupontype" class="Field100" style="width:50px">
							<option %%GLOBAL_SelDiscount1%% value="1">%</option>
							<option %%GLOBAL_SelDiscount2%% value="0">%%GLOBAL_CurrencyToken%%</option>
						</select>
						<img onmouseout="HideHelp('d2');" onmouseover="ShowHelp('d2', '%%LNG_DiscountAmount%%', '%%LNG_DiscountAmountHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d2"></div>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_ExpiryDate%%:
					</td>
					<td>
						<input class="plain" id="dc1" value="%%GLOBAL_ExpiryDate%%" size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.getElementById('dc1'),document.getElementById('dc2'));return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="images/calbtn.gif" width="34" height="22" border="0" alt=""></a>
						&nbsp;<img onmouseout="HideHelp('d4');" onmouseover="ShowHelp('d4', '%%LNG_ExpiryDate%%', '%%LNG_ExpiryDateHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d4"></div>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_MinimumPurchase%%:
					</td>
					<td>
						%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="couponminpurchase" name="couponminpurchase" class="Field50" value="%%GLOBAL_MinPurchase%%"> %%GLOBAL_CurrencyTokenRight%%
						<img onmouseout="HideHelp('d3');" onmouseover="ShowHelp('d3', '%%LNG_MinimumPurchase%%', '%%LNG_MinimumPurchaseHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d3"></div>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_Enabled%%:
					</td>
					<td>
						<input type="checkbox" id="couponenabled" name="couponenabled" value="ON" %%GLOBAL_Enabled%%> <label for="couponenabled">%%LNG_YesCouponEnabled%%</label>
					</td>
				</tr>

				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_CouponMaxUses%%:
					</td>
					<td>
						<input type="text" id="couponmaxuses" name="couponmaxuses" class="Field50" value="%%GLOBAL_MaxUses%%" />
						<img onmouseout="HideHelp('d5');" onmouseover="ShowHelp('d5', '%%LNG_CouponMaxUses%%', '%%LNG_CouponMaxUsesHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d5"></div>
					</td>
				</tr>
			</table>
			<table class="Panel">
				<tr>
				  <td class="Heading2" colspan=2>%%LNG_CouponAppliesTo%%</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_AppliesTo%%:
					</td>
					<td style="padding-bottom: 3px;">
						<input onclick="ToggleUsedFor(0)" type="radio" id="usedforcat" name="usedfor" value="categories" %%GLOBAL_UsedForCat%%> <label for="usedforcat">%%LNG_CouponAppliesToCategories%%</label><br />
						<div id="usedforcatdiv" style="padding-left:25px">
							<select multiple="multiple" size="12" name="catids[]" id="catids" class="Field250 ISSelectReplacement">
								<option value="0" %%GLOBAL_AllCategoriesSelected%%>%%LNG_AllCategories%%</option>
								%%GLOBAL_CategoryList%%
							</select>
							<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '%%LNG_ChooseCategories%%', '%%LNG_ChooseCategoriesHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d1"></div>
						</div>
													<div style="clear: left;" />
						<input onclick="ToggleUsedFor(1)" type="radio" id="usedforprod" name="usedfor" value="products"> <label for="usedforprod">%%LNG_CouponAppliesToProducts%%</label><br />
						<div id="usedforproddiv" style="padding-left:25px">
							<select size="12" name="products" id="ProductSelect" class="Field250" onchange="$('#ProductRemoveButton').attr('disabled', false);">
								%%GLOBAL_SelectedProducts%%
							</select>
							<div class="Field250" style="text-align: left;">
								<div style="float: right;">
									<input type="button" value="%%LNG_CouponRemoveSelected%%" id="ProductRemoveButton" disabled="disabled" class="FormButton" style="width: 125px;" onclick="removeFromProductSelect('ProductSelect', 'prodids');" />
								</div>
								<input type="button" value="%%LNG_CouponAddProduct%%" class="FormButton" style="width: 125px;" onclick="openProductSelect('coupon', 'ProductSelect', 'prodids');" />
							<input type="hidden" name="prodids" id="prodids" class="Field250" value="%%GLOBAL_ProductIds%%" />
						</div>
					</td>
				</tr>
			</table>


			<table border="0" cellspacing="0" cellpadding="2" width="100%" class="PanelPlain">
				<tr>
					<td width="200" class="FieldLabel">
						&nbsp;
					</td>
					<td>
						<input type="submit" value="%%LNG_Save%%" class="FormButton" />
						<input type="reset" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()" />
					</td>
				</tr>
			</table>

			</td>
		</tr>
	</table>

	</div>
	</form>

	<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="calendar/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>
	<input type="text" id="dc2" name="dc2" style="display:none">

	<script type="text/javascript">

		function ConfirmCancel()
		{
			if(confirm("%%LNG_ConfirmCancelCoupon%%"))
				document.location.href = "index.php?ToDo=viewCoupons";
		}

		function CheckCouponForm()
		{
			var couponname = document.getElementById("couponname");
			var usedforcatdiv = document.getElementById("usedforcatdiv");
			var usedforproddiv = document.getElementById("usedforproddiv");
			var catids = document.getElementById("catids");
			var prodids = document.getElementById("prodids");
			var da = document.getElementById("couponamount");
			var mp = document.getElementById("couponminpurchase");
			var dc1 = document.getElementById("dc1");
			var ce = document.getElementById("couponexpires");

			ce.value = dc1.value;

			if($('#couponcode').val() == '') {
				alert('%%LNG_EnterCouponCode%%');
				$('#couponcode').focus();
				return false;
			}

			if(couponname.value == "") {
				alert("%%LNG_EnterCouponName%%");
				couponname.focus();
				return false;
			}

			if(usedforcatdiv.style.display == "") {
				if(catids.selectedIndex == -1) {
					alert("%%LNG_ChooseCouponCategory%%");
					catids.focus();
					return false;
				}
			}

			if(usedforproddiv.style.display == "") {
				if(prodids.value == "") {
					alert("%%LNG_EnterCouponProductId%%");
					prodids.focus();
					return false;
				}
			}

			if(isNaN(parseInt(da.value)))
			{
				alert("%%LNG_EnterValidAmount%%");
				da.focus();
				da.select();
				return false;
			}

			m = mp.value.replace("%%GLOBAL_CurrencyToken%%", "");

			if(isNaN(m) && m != "")
			{
				alert("%%LNG_EnterValidMinPrice%%");
				mp.focus();
				mp.select();
				return false;
			}

			// Everything is OK
			return true;
		}

		function ToggleUsedFor(Which) {
			var usedforcatdiv = document.getElementById("usedforcatdiv");
			var usedforproddiv = document.getElementById("usedforproddiv");
			var usedforcat = document.getElementById("usedforcat");
			var usedforprod = document.getElementById("usedforprod");

			if(Which == 0) {
				usedforcat.checked = true;
				usedforcatdiv.style.display = "";
				usedforproddiv.style.display = "none";
			}
			else {
				usedforprod.checked = true;
				usedforcatdiv.style.display = "none";
				usedforproddiv.style.display = "";
			}
		}

		%%GLOBAL_ToggleUsedFor%%

	</script>
