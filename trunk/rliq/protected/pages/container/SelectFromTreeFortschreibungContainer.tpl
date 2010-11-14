<com:TActivePanel style="height:200px;width:500px;overflow:auto">

<com:TActiveTextBox ID="SFTauf_tabelle" Visible="false"/>
<com:TActiveTextBox ID="SFTstart_id" Visible="false"/>

<com:TActiveRepeater id="SelectFromTreeGrid">

    <prop:HeaderTemplate>
        <table width="100%">
    </prop:HeaderTemplate>

    <prop:ItemTemplate>
         <tr onMouseOver="setRowBackground(this,'#c3e3ef')"
            onMouseOut="setRowBackground(this,this.style.backgroundColor)"
            class="<%# $this->ItemIndex%2?'alternating':'nonealternating' %>">
            <td><com:TActiveLinkButton onCallback="page.FortschreibungContainer.SelectFromTreeContainer.ChangeLevel" CommandParameter="<%#count(StrukturRecord::finder()->findByidtm_struktur($this->Data->parent_idtm_struktur))>=1?StrukturRecord::finder()->findByidtm_struktur($this->Data->parent_idtm_struktur)->parent_idtm_struktur:'1'%>" Text="Ebene hoch"/></td>
            <td><com:TActiveImage ImageUrl=<%#'/rliq/themes/basic/imgs/s'.StrukturRecord::finder()->findByidtm_struktur($this->Data->idtm_struktur)->idta_struktur_type.'.gif'%> />
            <com:TLiteral Text=<%#$this->Data->struktur_name%>/></td>
            <td> <com:TActiveLinkButton onCallback="page.FortschreibungContainer.SelectFromTreeContainer.ChooseLevel" CommandParameter="<%#$this->Data->idtm_struktur%>" Text="selektieren"/></td>
            <td> <com:TActiveLinkButton onCallback="page.FortschreibungContainer.SelectFromTreeContainer.ChangeLevel" CommandParameter="<%#$this->Data->idtm_struktur%>" Text="Ebene runter" Visible="<%#$this->parent->parent->parent->check_forChildren($this->Data->idtm_struktur)%>"/></td>
        </tr>
     </prop:ItemTemplate>

     <prop:FooterTemplate>
        </table>
     </prop:FooterTemplate>

</com:TActiveRepeater>
</com:TActivePanel>