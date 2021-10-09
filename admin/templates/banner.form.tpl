
	<form action="index.php?ToDo=%%GLOBAL_FormAction%%" id="frmBanner" method="post">
	<input type="hidden" name="bannerId" value="%%GLOBAL_BannerId%%">
	<div class="BodyContainer">
	<table class="OuterPanel">
	  <tr>
		<td class="Heading1" id="tdHeading">%%GLOBAL_Title%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%LNG_BannerIntro%%</p>
			%%GLOBAL_Message%%
			<p><input type="submit" name="SubmitButton1" value="%%LNG_Save%%" class="FormButton">&nbsp; <input type="button" name="CancelButton1" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()"></p>
		</td>
	  </tr>
		<tr>
			<td>
			  <table class="Panel">
				<tr>
				  <td class="Heading2" colspan=2>%%LNG_NewBannerDetails%%</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_BannerName%%:
					</td>
					<td>
						<input type="text" id="bannername" name="bannername" class="Field400" value="%%GLOBAL_BannerName%%">
						<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '%%LNG_BannerName%%', '%%LNG_BannerNameHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d1"></div>
					</td>
				</tr>
					<td class="FieldLabel" colspan="2">
						<script>
						function showFoto(src,title){
						//chamando a fun��o do lightbox respons�vel pelo in�cio das a��es
						tb_show(title, src, false);
						} 
						</script>
						<div id="normal" name="normal" style="display:none">
						<span class="Required">*</span>&nbsp;%%LNG_BannerContent%%:
						<div style="padding-left:180px;">%%GLOBAL_WYSIWYG%%</div></div>
						<div id="fresh" name="fresh" style="display:none">
								<div id="flashcontent" style="clear: both; width: 100%;">
								</div>
								<script type="text/javascript" src="includes/amcharts/swfobject.js?%%GLOBAL_JSCacheToken%%"></script>
								<script type="text/javascript">
									$(document).ready(function() {
										var so = new SWFObject("%%GLOBAL_ShopPath%%/admin/banners/campo.swf", "ampie", "600", "200", "8", "#FFFFFF");
										so.addVariable("conteudoTxt", "%%GLOBAL_Texto%%");
										so.addVariable("bannerId", "%%GLOBAL_BannerId%%");
										so.addVariable("ToDo", "%%GLOBAL_FormAction%%");
										so.write("flashcontent");
									});
								</script>
						</div
						<!--
						<img onmouseout="HideHelp('d2');" onmouseover="ShowHelp('d2', '%%LNG_BannerContent%%', '%%LNG_BannerContentHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d2"></div>-->
					</td>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_BannerPage%%:
					</td>
					<td>
						<input type="radio" name="bannerpage" id="bannerpage1" value="home_page" %%GLOBAL_IsHomePage%% /> <label for="bannerpage1">%%LNG_BannerHomePage%%</label>
						<img onmouseout="HideHelp('d3');" onmouseover="ShowHelp('d3', '%%LNG_BannerPage%%', '%%LNG_BannerPageHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d3"></div>
						<br />
						<input type="radio" name="bannerpage" id="bannerpage2" value="category_page" %%GLOBAL_IsCategory%% /> <label for="bannerpage2">%%LNG_BannerCategoryPage%%</label><br />
							<div id="page_category" style="padding-left:25px">
								<select name="bannercat" id="bannercat" class="Field200">
									<option value="">%%LNG_ChooseACategory%%</option>
									%%GLOBAL_CategoryOptions%%
								</select>
							</div>
						<input type="radio" name="bannerpage" id="bannerpage3" value="brand_page" %%GLOBAL_IsBrand%% /> <label for="bannerpage3">%%LNG_BannerBrandPage%%</label><br />
							<div id="page_brand" style="padding-left:25px">
								<select name="bannerbrand" id="bannerbrand" class="Field200">
									<option value="">%%LNG_ChooseABrand%%</option>
									%%GLOBAL_BrandOptions%%
								</select>
							</div>
						<input type="radio" name="bannerpage" id="bannerpage4" value="search_page" %%GLOBAL_IsSearch%% /> <label for="bannerpage4">%%LNG_BannerSearchPage%%</label><br />
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_BannerDateRange%%:
					</td>
					<td>
						<input type="radio" id="bannerdate1" name="bannerdate" value="always" %%GLOBAL_IsAlwaysDate%%> <label for="bannerdate1">%%LNG_BannerDisplayAlways%%</label>
						<img onmouseout="HideHelp('d4');" onmouseover="ShowHelp('d4', '%%LNG_BannerDateRange%%', '%%LNG_BannerDateRangeHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d4"></div>
						<br />
						<input type="radio" id="bannerdate2" name="bannerdate" value="custom" %%GLOBAL_IsCustomDate%%> <label for="bannerdate2">%%LNG_BannerDisplayBetween%%</label>
					</td>
				</tr>
				<tr id="trCustomDate" style="display:none">
					<td class="FieldLabel">
						&nbsp;
					</td>
					<td style="padding-left:25px">
						<table border="0">
							<tr>
								<td>
									%%LNG_BannerFrom%%:
								</td>
								<td>
									<select name="from_day" id="from_day" class="Field70">
										%%GLOBAL_FromDayOptions%%
									</select>
									<select name="from_month" id="from_month" class="Field70">
										%%GLOBAL_FromMonthOptions%%
									</select>
									<select name="from_year" id="from_year" class="Field70">
										%%GLOBAL_FromYearOptions%%
									</select>
								</td>
							</tr>
							<tr>
								<td align="right">
									%%LNG_BannerTo%%:
								</td>
								<td>
									<select name="to_day" id="to_day" class="Field70">
										%%GLOBAL_ToDayOptions%%
									</select>
									<select name="to_month" id="to_month" class="Field70">
										%%GLOBAL_ToMonthOptions%%
									</select>
									<select name="to_year" id="to_year" class="Field70">
										%%GLOBAL_ToYearOptions%%
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_Visible%%:
					</td>
					<td>
						<input type="checkbox" id="bannerstatus" name="bannerstatus" value="ON" %%GLOBAL_Visible%%> <label for="bannerstatus">%%LNG_YesBannerVisible%%</label>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_BannerLocation%%:
					</td>
					<td>
				<select name="bannerloc" id="bannerloc" class="Field150">
							<option value="">%%LNG_ChooseALocation%%</option>
							<option value="top" %%GLOBAL_IsLocationTop%%>%%LNG_TopOfPage%%</option>
							<option value="bottom" %%GLOBAL_IsLocationBottom%%>%%LNG_BottomOfPage%%</option>
							<option value="direito" %%GLOBAL_IsLocationDireito%%>Lado Direito</option>
							<option value="esquerdo" %%GLOBAL_IsLocationEsquerdo%%>Lado Esquerdo</option>

						</select>	
						<img onmouseout="HideHelp('d5');" onmouseover="ShowHelp('d5', '%%LNG_BannerLocation%%', '%%LNG_BannerLocationHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d5"></div>
					</td>
				<script type="text/javascript">
				var selectmenu=document.getElementById("bannerloc")
				selectmenu.onchange=function(){ //run some code when "onchange" event fires
					var chosenoption=this.options[this.selectedIndex] //this refers to "selectmenu"
					if(chosenoption.value == "top") {
						//$("#fresh").css("display", "");
						//$("#normal").css("display", "none");
						$("#fresh").show(1000);
						$("#normal").hide(1000);
					}
					else {
						//$("#fresh").css("display", "none");
						//$("#normal").css("display", "");
						$("#fresh").hide(1000);
						$("#normal").show(1000);
					}
				}
				</script>
				</tr>
				<tr>
					<td class="Gap">&nbsp;</td>
					<td class="Gap"><input type="submit" name="SubmitButton1" value="%%LNG_Save%%" class="FormButton">&nbsp; <input type="button" name="CancelButton1" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()">
					</td>
				</tr>
				<tr><td class="Gap"></td></tr>
				<tr><td class="Gap"></td></tr>
				<tr><td class="Sep" colspan="2"></td></tr>
			 </table>
			</td>
		</tr>
	</table>

	</div>
	</form>

	<script type="text/javascript">

		var selected_page = '';

		function ConfirmCancel() {
			if(confirm("%%LNG_ConfirmCancelBanner%%"))
				document.location.href = "index.php?ToDo=viewBanners";
		}

		function CheckBannerForm() {
			return false;
		}

		function ToggleDate(DateType) {
			if(DateType == "custom") {
				$("#trCustomDate").css("display", "");
			}
			else {
				$("#trCustomDate").css("display", "none");
			}
		}

		// Hide the location options on page load
		$(document).ready(function() {
			$('#page_category').css('display', 'none');
			$('#page_brand').css('display', 'none');

			// Do we need to show the custom date range?
			%%GLOBAL_ShowCustomDate%%

			// Do we need to show the category dropdown?
			%%GLOBAL_ShowCategory%%

			// Do we need to show the brand dropdown?
			%%GLOBAL_ShowBrand%%

			%%GLOBAL_SelectedJS%%
			//
			%%GLOBAL_MostraNormal%%
			%%GLOBAL_MostraFlash%%
		});

		$('#bannerpage1').click(function() {
			$('#page_category').css('display', 'none');
			$('#page_brand').css('display', 'none');
		});

		$('#bannerpage2').click(function() {
			$('#page_category').css('display', '');
			$('#bannercat').focus();
			$('#page_brand').css('display', 'none');
		});

		$('#bannerpage3').click(function() {
			$('#page_brand').css('display', '');
			$('#bannerbrand').focus();
			$('#page_category').css('display', 'none');
		});

		$('#bannerpage4').click(function() {
			$('#page_category').css('display', 'none');
			$('#page_brand').css('display', 'none');
		});

		$('#bannerdate1').click(function() {
			$('#trCustomDate').css('display', 'none');
		});

		$('#bannerdate2').click(function() {
			$('#trCustomDate').css('display', '');
		});

		// Save the page type when it's changed
		$('#bannerpage1').click(function() {
			selected_page = $('#bannerpage1').val();
		});

		$('#bannerpage2').click(function() {
			selected_page = $('#bannerpage2').val();
		});

		$('#bannerpage3').click(function() {
			selected_page = $('#bannerpage3').val();
		});

		$('#bannerpage4').click(function() {
			selected_page = $('#bannerpage4').val();
		});

		// Check the form when it's submitted
		$('#frmBanner').submit(function() {
			if($('#bannername').val() == '') {
				alert('%%LNG_EnterBannerName%%');
				$('#bannername').focus();
				$('#bannername').select();
				return false;
			}

			switch(selected_page) {
				case 'category_page': {
					if($('#bannercat :selected').val() == '') {
						alert('%%LNG_BannerChooseCat%%');
						$('#bannercat').focus();
						return false;
					}
					break;
				}
				case 'brand_page': {
					if($('#bannerbrand :selected').val() == '') {
						alert('%%LNG_BannerChooseBrand%%');
						$('#bannerbrand').focus();
						return false;
					}
					break;
				}
				case '': {
					alert('%%LNG_ChooseBannerShow%%');
					return false;

				}
			}

			if($('#bannerloc :selected').val() == '') {
				alert('%%LNG_ChooseBannerLocation%%');
				$('#bannerloc').focus();
				return false;
			}
		});

	</script>
