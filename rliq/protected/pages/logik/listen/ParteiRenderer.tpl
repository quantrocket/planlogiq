<tr style="background-color:#ffffff">
	<td><com:TLiteral Text="<%#$this->data->partei_name%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->partei_name2%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->partei_name3%>" /></td>
	<td><com:TLiteral Text="<%#$this->data->partei_vorname%>" /></td>
	<td>
	<com:TImage ImageUrl="/gsprocurement/themes/basic/gfx/16x16/actions/edit_add.png" />
	<com:THyperLink Text="neue Adresse" NavigateUrl="<%# $this->getRequest()->constructUrl('page','logik.adresse',array('idta_partei'=>$this->Data->idta_partei,'modus'=>'0')) %>" />
	</td>
</tr>
<tr><td colspan="5">
<com:TRepeater ID="Repeater2">

	<prop:HeaderTemplate>
    <table cellspacing="1" width="100%" id="subrepeater" border="0">
    </prop:HeaderTemplate>
 
 <!--default-->
    
    <prop:ItemTemplate>
    <tr>
    	<td class="subspace">&nbsp;
    	</td><td><%#$this->data->adresse_street%>
    	</td><td><%#$this->data->adresse_zip%>
    	</td><td><%#$this->data->adresse_town%>
    	</td><td>
    	<com:TImage ImageUrl="/gsprocurement/themes/basic/gfx/16x16/actions/pencil.png" />
    	<com:THyperLink Text="edit" NavigateUrl="<%# $this->getRequest()->constructUrl('page','logik.adresse',array('idta_adresse'=>$this->Data->idta_adresse,'modus'=>'1')) %>" />
    </td></tr>
    </prop:ItemTemplate>
 
 <!--alternate-->
 
 	<prop:AlternatingItemTemplate>
    <tr class="alternating">
    	<td class="subspace">&nbsp;
    	</td><td><%#$this->data->adresse_street%>
    	</td><td><%#$this->data->adresse_zip%>
    	</td><td><%#$this->data->adresse_town%>
    	</td><td>
    	<com:TImage ImageUrl="/gsprocurement/themes/basic/gfx/16x16/actions/pencil.png" />
    	<com:THyperLink Text="edit" NavigateUrl="<%# $this->getRequest()->constructUrl('page','logik.adresse',array('idta_adresse'=>$this->Data->idta_adresse,'modus'=>'1')) %>" />
    </td></tr>
    </prop:AlternatingItemTemplate>
 
 
    <prop:FooterTemplate>
    </table>
    </prop:FooterTemplate>
 
</com:TRepeater>


</td></tr>