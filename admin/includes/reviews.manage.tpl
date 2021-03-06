
	<script language=JavaScript>

		function MassActionReviews(action) {
			var sortURL = g('ReviewSortURL').href;
			sortURL = sortURL.substring(sortURL.indexOf('?')+1, sortURL.length);
			$.post('remote.php?'+sortURL+'&w='+action, $('#frmReviews').serialize(), ReviewsMassActioned);
		}

		function ReviewsMassActioned(response) {
			var status = $('status', response).text();
			BindAjaxGridSorting();
			var message = $('message', response).text();
			if(status == 0) {
				display_error('ReviewsStatus', message);
			}
			else {
				display_success('ReviewsStatus', message);
				var grid = $('grid', response).text();
				if(!grid) {
					$('#ReviewGridView').hide();
				}
				else {
					$('.GridContainer').html(grid);
				}
			}
		}
	</script>

	<div class="BodyContainer">
	<table id="Table13" cellSpacing="0" cellPadding="0" width="100%">
		<tr>
			<td class="Heading1">%%LNG_ManageReviews%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%GLOBAL_ReviewIntro%%</p>
			<div id="ReviewsStatus">%%GLOBAL_Message%%</div>
			<table id="IntroTable" cellspacing="0" cellpadding="0" width="100%">
			<td class="Intro" valign="top">
				<input type="button" name="IndexDeleteButton" value="%%LNG_DeleteReviews1%%" id="IndexDeleteButton" class="SmallButton" onclick="ConfirmDeleteSelected()" %%GLOBAL_DisableDelete%% /> &nbsp;<input type="button" name="IndexApproveButton" value="%%LNG_ApproveReviews%%" id="IndexApproveButton" class="SmallButton" onclick="ApproveSelected()" %%GLOBAL_DisableApproved%% /> &nbsp;<input type="button" name="IndexDisapproveButton" value="%%LNG_DisapproveReviews%%" id="IndexDisapproveButton" class="SmallButton" onclick="DisapproveSelected()" %%GLOBAL_DisableDisapproved%% />
			</td>
			<td class="SmallSearch" align="right">
				<table id="Table16" style="display:%%GLOBAL_DisplaySearch%%">
				<tr>
					<form action="index.php?ToDo=viewReviews%%GLOBAL_SortURL%%" method="get" onsubmit="return ValidateForm(CheckSearchForm)">
					<input type="hidden" name="ToDo" value="viewReviews">
					<td nowrap>
						<input name="searchQuery" id="searchQuery" type="text" value="%%GLOBAL_Query%%" id="SearchQuery" class="Button" size="20" />&nbsp;
						<input type="image" name="SearchButton" id="SearchButton" src="images/searchicon.gif" border="0" />
					</td>
					</form>
				</tr>
				<tr>
					<td align="right" style="padding-right:55pt">
						<a id="SearchClearButton" href="index.php?ToDo=viewReviews">%%LNG_ClearResults%%</a>
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
		<td style="display: %%GLOBAL_DisplayGrid%%" id="ReviewGridView">
			<form name="frmReviews" id="frmReviews" method="post" action="index.php?ToDo=deleteReviews">
				<div class="GridContainer">
					%%GLOBAL_ReviewDataGrid%%
				</div>
			</form>
		</td></tr>
	</table>
	</div>

	<script type="text/javascript">

		function PreviewReview(ReviewId)
		{
			var l = screen.availWidth / 2 - 250;
			var t = screen.availHeight / 2 - 300;
			var win = window.open('index.php?ToDo=previewReview&reviewId='+ReviewId, 'previewReview', 'width=500,height=600,left='+l+',top='+t+',scrollbars=1');
		}

		function CheckSearchForm()
		{
			var query = document.getElementById("searchQuery");

			if(query.value == "")
			{
				alert("%%LNG_EnterSearchTerm%%");
				query.focus();
				return false;
			}

			return true;
		}

		function ConfirmDeleteSelected()
		{
			var fp = document.getElementById("frmReviews").elements;
			var c = 0;

			for(i = 0; i < fp.length; i++)
			{
				if(fp[i].type == "checkbox" && fp[i].checked)
					c++;
			}

			if(c > 0)
			{
				if(confirm("%%LNG_ConfirmDeleteReviews%%"))
					MassActionReviews('deleteReviews');
			}
			else
			{
				alert("%%LNG_ChooseReview1%%");
			}
		}

		function ApproveSelected()
		{
			var frm = document.getElementById("frmReviews");
			var fp = frm.elements;
			var c = 0;

			for(i = 0; i < fp.length; i++)
			{
				if(fp[i].type == "checkbox" && fp[i].checked)
					c++;
			}

			if(c > 0)
			{
				MassActionReviews('approveReviews');
			}
			else
			{
				alert("%%LNG_ChooseReview2%%");
			}
		}

		function DisapproveSelected()
		{
			var frm = document.getElementById("frmReviews");
			var fp = frm.elements;
			var c = 0;

			for(i = 0; i < fp.length; i++)
			{
				if(fp[i].type == "checkbox" && fp[i].checked)
					c++;
			}

			if(c > 0)
			{
				MassActionReviews('disapproveReviews');
			}
			else
			{
				alert("%%LNG_ChooseReview3%%");
			}
		}

		function ToggleDeleteBoxes(Status)
		{
			var fp = document.getElementById("frmReviews").elements;

			for(i = 0; i < fp.length; i++)
				fp[i].checked = Status;
		}

	</script>
