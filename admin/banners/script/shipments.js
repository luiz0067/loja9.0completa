var Shipments = {
	Expand: function(shipmentId)
	{
		if(!$('#trQ'+shipmentId).hasClass('Expanded')) {
			$('#expand'+shipmentId).attr('src', $('#expand'+shipmentId).attr('src').replace('plus.gif', 'minus.gif'));
			$('#trQ'+shipmentId).find('.QuickView').load('remote.php?remoteSection=orders&w=getShipmentQuickView&shipmentId='+shipmentId, {}, function() {
				$('#trQ'+shipmentId).show().addClass('Expanded');
				$('#tr'+shipmentId).addClass('QuickViewExpanded');
			});
		}
		else {
			$('#expand'+shipmentId).attr('src', $('#expand'+shipmentId).attr('src').replace('minus.gif', 'plus.gif'));
			$('#trQ'+shipmentId).hide().removeClass('Expanded');
			$('#tr'+shipmentId).removeClass('QuickViewExpanded');
		}
	},

	Export: function()
	{
		$.iModal({
			data: $('#exportBox').html(),
			width: 320
		});
	},

	DeleteView: function(viewId)
	{
		if(confirm(lang.ConfirmDeleteCustomSearch)) {
			window.location = "index.php?ToDo=deleteCustomShipmentSearch&searchId="+viewId;
		}

		return false;
	},

	DeleteSelected: function()
	{
		if(!$('.GridContainer input[type=checkbox]:checked').length) {
			alert(lang.SelectOneMoreShipmentsDelete);
			return false;
		}
		if(confirm(lang.ConfirmDeleteShipments)) {
			$('#shipmentsForm').submit();
			return true;
		}
		else {
			return false;
		}
	},

	PrintPackingSlip: function(shipmentId, orderId)
	{
		var l = screen.availWidth / 2 - 450;
		var t = screen.availHeight / 2 - 320;
		var win = window.open('index.php?ToDo=printShipmentPackingSlips&orderId='+orderId+'&shipmentId='+shipmentId, 'packingSlip', 'width=900,height=650,left='+l+',top='+t+',scrollbars=1');
		return false;
	}
};