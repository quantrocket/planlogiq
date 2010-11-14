<com:TAutoComplete Id="XXXsuggest_idtm_organisation"
                    OnSuggest="XXXsuggestOrganisation"
                    MinChars="2"
                    OnSuggestionSelected="XXXsuggestionSelectedOne"
                    Suggestions.DataKeyField="idtm_organisation"
                    ResultPanel.CssClass="acomplete"
                    ResultPanel.Style="position:relative"
                    CssClass="mandantorylarge"
                    Text=""
                    width="175px">
<prop:Suggestions.ItemTemplate>
    <li><b><%# $this->Data['org_vorname'] %> <%# $this->Data['org_name'] %></b></li>
</prop:Suggestions.ItemTemplate>
</com:TAutoComplete>
<com:TActiveImageButton onCallBack="clearSuggestBox" ImageUrl="/rliq/themes/basic/gfx/16x16/actions/redo.png"/>