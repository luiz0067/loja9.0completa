
	<tr id="tr%%GLOBAL_CustomerId%%" class="GridRow" onmouseover="this.className='GridRowOver'" onmouseout="this.className='GridRow'">
		<td align="center" style="width:25px">
			<input type="checkbox" name="customers[]" value="%%GLOBAL_CustomerId%%">
		</td>
		<td align="center" style="width:15px">
			<a href="#" onclick="OrderView('%%GLOBAL_CustomerId%%');" style="display:%%GLOBAL_HideExpand%%"><img id="expand%%GLOBAL_CustomerId%%" class="ExpandLink"  src="images/plus.gif" align="left" width="19" height="16" title="%%LNG_ExpandCustQuickView%%" style="vertical-align: middle;" border="0"></a>
		</td>
		<td align="center" style="width:18px">
			<img src="images/customer.gif" width="16" height="16">
		</td>
		<td class="%%GLOBAL_SortedFieldNameClass%%">
			%%GLOBAL_Name%%
		</td>
		<td class="%%GLOBAL_SortedFieldEmailClass%%">
			%%GLOBAL_Email%%
		</td>
		<td class="%%GLOBAL_SortedFieldPhoneClass%%">
			%%GLOBAL_Phone%%
		</td>
		<td class="%%GLOBAL_SortedFieldGroup%%" style="display: %%GLOBAL_HideGroup%%">
			%%GLOBAL_Group%%
		</td>
		<td nowrap="nowrap" class="%%GLOBAL_SortedFieldStoreCreditClass%%" style="display: %%GLOBAL_HideStoreCredit%%">
			%%GLOBAL_StoreCredit%%
		</td>
		<td class="%%GLOBAL_SortedFieldDateClass%%">
			%%GLOBAL_Date%%
		</td>
		<td class="%%GLOBAL_SortedFieldNumOrdersClass%%">
			%%GLOBAL_NumOrders%%
		</td>
		<td>
			%%GLOBAL_LoginLink%%
			%%GLOBAL_ViewNotesLink%%
			%%GLOBAL_EditCustomerLink%%
		</td>
	</tr>
	<tr id="trQ%%GLOBAL_CustomerId%%" style="display:none">
		<td colspan="2"></td>
		<td colspan="3" id="tdQ%%GLOBAL_CustomerId%%" class="QuickView"></td>
		<td colspan="5"></td>
	</tr>
