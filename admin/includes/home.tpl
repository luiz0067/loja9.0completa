<script type="text/javascript" src="script/dashboard.js?%%GLOBAL_JSCacheToken%%"></script>
<div class="BodyContainer" style="margin-top: 0;">
	<div style="%%GLOBAL_HideGettingStarted%%" class="DashboardGettingStarted">
		%%GLOBAL_GettingStarted%%
	</div>
	<div style="%%GLOBAL_HideOverview%%" class="DashboardCommonTasks">
		<div class="Heading2">%%GLOBAL_CurrentlyLoggedInAs%%, <a href="index.php?ToDo=logOut" class="Logout">Sair?</a></div>
		%%GLOBAL_Messages%%
		<div class="DashboardRightColumn">

			<div class="DashboardPanel DashboardPanelCurrentNotifications" style="%%GLOBAL_HideNotificationsList%%">
				<div class="DashboardPanelContent">
					<h3>%%LNG_PendingItems%%</h3>
					<ul>
						%%GLOBAL_NotificationsList%%
					</ul>
				</div>
			</div>

			<div class="DashboardPanel DashboardPanelPerformanceIndicators" id="DashboardPanelPerformanceIndicators">
				<div class="DashboardPanelContent">
					<div class="DashboardPillMenu DashboardPerformanceIndicatorsPeriodButton">
						<div class="DashboardPillMenuStart"></div>
						<div class="DashboardPillMenuEnd"></div>
						<span class="Label">
							%%LNG_View%%:
						</span>
						<span class="Buttons">
							<a href="#" class="%%GLOBAL_PerformanceIndicatorsActiveDay%%" rel="period=day">%%LNG_Day%%</a>
							<a href="#" class="%%GLOBAL_PerformanceIndicatorsActiveWeek%%" rel="period=week">%%LNG_Week%%</a>
							<a href="#" class="%%GLOBAL_PerformanceIndicatorsActiveMonth%%" rel="period=month">%%LNG_Month%%</a>
							<a href="#" class="%%GLOBAL_PerformanceIndicatorsActiveYear%% Last" rel="period=year">%%LNG_Year%%</a>
						</span>
					</div>
					<h2>%%LNG_StoreSnapshot%%</h2>
					<div id="DashboardPerformanceIndicators">
						%%GLOBAL_PerformanceIndicatorsTable%%
					</div>
				</div>
			</div>

			<div class="DashboardPanel DashboardPanelOrderBreakdown" style="%%GLOBAL_HideDashboardBreakdownGraph%%">
				<div class="DashboardPanelContent">
					<span class="DashboardActionButton DashboardOrderBreakdownAllStatsButton">
						<a href="index.php?ToDo=viewStats">
							<span class="ButtonArrow"></span>
							<span class="ButtonText ButtonTextWithArrow">%%LNG_ViewAllStatistics%%</span>
						</a>
					</span>
				    <h2>Ultimos 7 Dias</h2>
					<ul class="OrderBreakdownChart">
						%%GLOBAL_DashboardBreakdownGraph%%
					</ul>
					<div class="OrderBreakdownChartKey">
						<div class="First">%%GLOBAL_GraphSeriesLabel0%%</div>
						<div>%%GLOBAL_GraphSeriesLabel1%%</div>
						<div>%%GLOBAL_GraphSeriesLabel2%%</div>
						<div>%%GLOBAL_GraphSeriesLabel3%%</div>
						<div class="Last">%%GLOBAL_GraphSeriesLabel4%%</div>
					</div>
				</div>
			</div>
			<div class="DashboardPanel DashboardPanelHelpArticles" style="">
				<div class="DashboardPanelContent" style="overflow: auto">
					<h2>Twitter Box</h2>
                    <table width="360"><tr><td>
<script language="JavaScript" type="text/javascript" src=""></script>
                    </td></tr></table>
				</div>
			</div>
		</div>
		<div class="DashboardLeftColumn">

			%%GLOBAL_TrialExpiryMessage%%

			<div class="DashboardPanel DashboardPanelFeatured DashboardPanelOverview">
				<div class="DashboardPanelContent">
					<div class="DashboardAtAGlance" style="%%GLOBAL_HideAtAGlance%%">
						<ul>
							<b>Numeros: &nbsp;</b> %%GLOBAL_AtGlanceItems%%
						</ul>
						<br class="ClearLeft" />
					</div>
				</div>
			</div>

			
		<div class="DashboardPanel DashboardPanelFeatured DashboardPanelOverview">
				<div class="DashboardPanelContent2">
<table width="100%" border="0">
<tr>
<td width="50%" valign="top">
<h2>&nbsp;Receita por Produto</h2>
<div id="flashcontent">
</div>
<script type="text/javascript" src="includes/amcharts/swfobject.js?1"></script>
<script type="text/javascript">
$(document).ready(function() {
var so = new SWFObject("includes/amcharts/ampie.swf", "ampie", "98%", "250", "8", "#FFFFFF");
so.addVariable("path", "includes/amcharts/");
so.addVariable("settings_file", escape("includes/amcharts/ordersbyrevenue.xml"));
so.addVariable("data_file", escape("index.php?ToDo=ordStatsByRevenueData&from=%%GLOBAL_Antes%%&to=%%GLOBAL_Agora%%&vendorId="));
so.addVariable("preloader_color", "#000000");
so.write("flashcontent");
});
</script>


</td>
<td width="50%" valign="top">
<h2>&nbsp;Produtos Mais Vendidos</h2>
<div id="flashcontent2">
</div>
<script type="text/javascript" src="includes/amcharts/swfobject.js?1"></script>
<script type="text/javascript">
$(document).ready(function() {
var so = new SWFObject("includes/amcharts/ampie.swf", "ampie", "98%", "250", "8", "#FFFFFF");
so.addVariable("path", "includes/amcharts/");
so.addVariable("settings_file", escape("includes/amcharts/top20customers.xml"))
so.addVariable("data_file", escape("index.php?ToDo=overviewStatsTop20Prods&from=%%GLOBAL_Antes%%&to=%%GLOBAL_Agora%%"));
so.addVariable("preloader_color", "#999999");
so.write("flashcontent2");
});
</script>


</td>
</tr>
</table>
				</div>
			</div>
			
			
			<div class="DashboardPanel DashboardPanelRecentOrders" style="%%GLOBAL_HideRecentOrders%%">
				<div class="DashboardPanelContent">
					<span class="DashboardActionButton DashboardRecentOrdersAllButton">
						<a href="index.php?ToDo=viewOrders">
							<span class="ButtonArrow"></span>
							<span class="ButtonText ButtonTextWithArrow">%%LNG_ViewAllOrders%%</span>
						</a>
					</span>
					<h2>Novos Pedidos na Loja</h2>
					<div class="DashboardFilterOptions" style="margin-top: 18px;">
						<div>
							Marcador:
						</div>
						<ul class="DashboardRecentOrdersToggle">
							<li class="%%GLOBAL_RecentOrdersActiveRecentClass%%"><a href="#" rel="status=recent">%%LNG_RecentOrders%%</a></li>
							<li class="%%GLOBAL_RecentOrdersActivePendingClass%%"><a href="#" rel="status=pending">%%LNG_PendingOrders%%</a></li>
							<li class="%%GLOBAL_RecentOrdersActiveCompletedClass%%"><a href="#" rel="status=completed">%%LNG_CompletedOrders%%</a></li>
							<li class="%%GLOBAL_RecentOrdersActiveRefundedClass%%"><a href="#" rel="status=refunded">%%LNG_RefundedOrders%%</a></li>
						</ul>
						<br style="clear: left;" />
					</div>
					<ul style="clear: left" class="DashboardRecentOrderList">
						%%GLOBAL_RecentOrdersList%%
					</ul>
				</div>
			</div>
		</div>
	</div>
	<br class="Clear" />
</div>