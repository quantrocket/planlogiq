<com:TActivePanel>
<com:TAutoComplete Id="XXXsuggest_idtm_activity"
                    OnSuggest="XXXsuggestOrganisation"
                    MinChars="1"
                    OnSuggestionSelected="XXXsuggestionSelectedOne"
                    Suggestions.DataKeyField="idtm_activity"
                    ResultPanel.CssClass="acomplete"
                    ResultPanel.Style="position:relative"
                    CssClass="mandantorylarge"
                    width="190px"
                    Text="">
<prop:Suggestions.ItemTemplate>
    <li><b><%# $this->Data['act_name'] %></b></li>
</prop:Suggestions.ItemTemplate>
</com:TAutoComplete>

<com:TActiveImageButton onCallBack="viewTreeBox" ImageUrl="/rliq/themes/basic/gfx/16x16/actions/build.png"/>

<com:TActivePanel Display="None" id="myTreeBox" Style="z-index:3;overflow:auto;width:235px;height:170px;" />
<com:TCallback ID="MyACTCallback" OnCallback="callback_MyACTCallback" />
</com:TActivePanel>