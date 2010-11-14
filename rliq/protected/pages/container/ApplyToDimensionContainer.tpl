<com:TActivePanel ScrollBars="Auto"  CssClass="windowcontent">

<table width="100%">

<tr><td colspan="2" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/reload.png" OnCallback="SyncWithDimension"/><com:TActiveButton OnCallback="SyncWithDimension" Text=<%[ abgleichen ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td class="FormLabel" style="width:70px;">Ziel Dimension:</td>
<td><com:TActiveDropDownList id="Tedidta_stammdaten_group" CssClass="mandantory" /></td>
</tr>

</table>

<com:TActiveTextBox id="Tedsend_tabelle" Text="tm_organisation" visible="true" />
<com:TActiveTextBox id="Tedsend_id" visible="true" />
<com:TActiveTextBox id="Tedsend_field" visible="true" />

</com:TActivePanel>