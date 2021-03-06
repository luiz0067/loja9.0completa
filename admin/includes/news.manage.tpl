
	<div class="BodyContainer">
	<table id="Table13" cellSpacing="0" cellPadding="0" width="100%">
		<tr>
			<td class="Heading1">%%LNG_ViewNews%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%GLOBAL_NewsIntro%%</p>
			%%GLOBAL_Message%%
			<table id="IntroTable" cellspacing="0" cellpadding="0" width="100%">
			<tr>
			<td class="Intro" valign="top">
				<input type="button" name="IndexAddButton" value="%%LNG_AddNews%%..." id="IndexCreateButton" class="SmallButton" onclick="document.location.href='index.php?ToDo=addNews'" /> &nbsp;<input type="button" name="IndexDeleteButton" value="%%LNG_DeleteSelected%%" id="IndexDeleteButton" class="SmallButton" onclick="ConfirmDeleteSelected()" %%GLOBAL_DisableDelete%% />
			</td>
			<td class="SmallSearch" align="right">
				<table id="Table16" style="display:%%GLOBAL_DisplaySearch%%">
				<tr>
					<form action="index.php?ToDo=viewNews%%GLOBAL_SortURL%%" method="get" onsubmit="return ValidateForm(CheckSearchForm)">
					<input type="hidden" name="ToDo" value="viewNews">
					<td nowrap>
						<input name="searchQuery" id="searchQuery" type="text" value="%%GLOBAL_Query%%" id="SearchQuery" class="Button" size="20" />&nbsp;
						<input type="image" name="SearchButton" id="SearchButton" style="padding-left: 10px; vertical-align: top;" src="images/searchicon.gif" border="0" />
					</td>
					</form>
				</tr>
				<tr>
					<td align="right" style="padding-right:55pt">
						<a id="SearchClearButton" href="index.php?ToDo=viewNews">%%LNG_ClearResults%%</a>
					</td>
				</tr>
				<tr>
					<td></td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
		<td style="display: %%GLOBAL_DisplayGrid%%">
			<form name="frmNews" id="frmNews" method="post" action="index.php?ToDo=deleteNews">
				<div class="GridContainer">
					%%GLOBAL_NewsDataGrid%%
				</div>
			</form>
		</td></tr>
	</table>
	</div>

	<script type="text/javascript">

		function CheckSearchForm()
		{
			var query = document.getElementById("searchQuery");

			if(query.value == "")
			{
				alert("%%LNG_EnterSearchTerm%%");
				return false;
			}

			return true;
		}

		function ConfirmDeleteSelected()
		{
			var fp = document.getElementById("frmNews").elements;
			var c = 0;

			for(i = 0; i < fp.length; i++)
			{
				if(fp[i].type == "checkbox" && fp[i].checked)
					c++;
			}

			if(c > 0)
			{
				if(confirm("%%LNG_ConfirmDeleteNews%%"))
					document.getElementById("frmNews").submit();
			}
			else
			{
				alert("%%LNG_ChooseNews%%");
			}
		}

		function ToggleDeleteBoxes(Status)
		{
			var fp = document.getElementById("frmNews").elements;

			for(i = 0; i < fp.length; i++)
				fp[i].checked = Status;
		}

		function PreviewNews(NewsId)
		{
			var l = screen.availWidth / 2 - 300;
			var t = screen.availHeight / 2 - 300;
			var win = window.open('index.php?ToDo=prevNews&newsId='+NewsId, 'newsPreview', 'width=600,height=600,left='+l+',top='+t+',scrollbars=1');
		}

	</script>
