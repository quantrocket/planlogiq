<com:TActiveRepeater
    ID="RessourceStundenRepeater">

    <prop:HeaderTemplate>
        <tr>
            <td colspan="16" class="taskheader">
                <com:TActiveLabel Text=<%#$this->parent->parent->data->res_name%> />
            </td>
            <td colspan="80">&nbsp;</td>
        </tr>
        <tr>
    </prop:HeaderTemplate>

    <prop:EmptyTemplate>
            keine Daten!
    </prop:EmptyTemplate>

    <prop:FooterTemplate>
        </tr>
    </prop:FooterTemplate>

    <prop:ItemTemplate>
        <td colspan="4" class="<%# $this->ItemIndex%2?'alternating':'nonealternating' %>"> <%#$this->data+1%>:00 </td>
    </prop:ItemTemplate>

</com:TActiveRepeater>


<com:TActiveRepeater
    ID="RessourceViertelRepeater">

    <prop:HeaderTemplate>
        <tr>
    </prop:HeaderTemplate>

    <prop:EmptyTemplate>
            keine Daten!
    </prop:EmptyTemplate>

    <prop:FooterTemplate>
        </tr>
        <tr>
            <td colspan="96">
                &nbsp;
            </td>
        </tr>
    </prop:FooterTemplate>

    <prop:ItemTemplate>
        <com:TActiveTableCell CssClass="<%#$this->page->checkRessourceStatus(($this->data+1)*4+1,$this->parent->parent->data->idtm_ressource)%>">&nbsp;</com:TActiveTableCell>
        <com:TActiveTableCell CssClass="<%#$this->page->checkRessourceStatus(($this->data+1)*4+2,$this->parent->parent->data->idtm_ressource)%>">&nbsp;</com:TActiveTableCell>
        <com:TActiveTableCell CssClass="<%#$this->page->checkRessourceStatus(($this->data+1)*4+3,$this->parent->parent->data->idtm_ressource)%>">&nbsp;</com:TActiveTableCell>
        <com:TActiveTableCell CssClass="<%#$this->page->checkRessourceStatus(($this->data+1)*4+4,$this->parent->parent->data->idtm_ressource)%>">&nbsp;</com:TActiveTableCell>
    </prop:ItemTemplate>

</com:TActiveRepeater>
