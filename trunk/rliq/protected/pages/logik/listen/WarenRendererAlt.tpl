<tr class="alternating">
	<td><com:TLiteral Text="<%#$this->data->idta_waren%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->waren_bezeichnung%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->waren_artikelnummer%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->waren_menge%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->waren_preis%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->waren_typ%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->waren_dat_ea%>" /></td>
	<td>
	<com:TImage ImageUrl="/gsprocurement/themes/basic/gfx/16x16/actions/pencil.png" />
	<com:THyperLink Text="edit" NavigateUrl="<%# $this->getRequest()->constructUrl('page','logik.waren',array('idta_waren'=>$this->Data->idta_waren,'modus'=>'1')) %>" />
	</td>
</tr>