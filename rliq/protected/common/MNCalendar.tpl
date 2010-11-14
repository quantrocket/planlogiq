<prop:calFormat></prop:calFormat>
<com:TActivePanel ID="painelcomponentecalendario" Style="vertical-align: top; border: 0px solid green; text-align:center;" ActiveControl.EnableUpdate="true" >
<!-- Add the Header of CALENDAR -->
<com:TActiveHiddenField ID='actvhiddenURL' Value="<%=$this->hiddenURL%>" ActiveControl.EnableUpdate="true" />
<!-- The Body of CALENDAR -->
<span style="width:95%; border: 1px solid #CCCCCC; font-size:12px; font-weight:bold; color:<%=$this->colorSmallFormatHeaderText%>; text-align:center;">
<com:TActiveLinkButton ID='prevHLink' Text='&laquo;' ToolTip='anterior' CausesValidation='false' OnCallBack="nextORprevClicked" Style="text-decoration: none;" />
&nbsp;<%=$this->actualTextMonth%>&nbsp;<%=$this->actualYear%>&nbsp;
<com:TActiveLinkButton ID='nextHLink' Text='&raquo;' ToolTip='proximo' CausesValidation='false' OnCallBack="nextORprevClicked" Style="text-decoration: none;" />
</span>

<com:TActivePanel ID="painelcorpocalendario" Style="vertical-align: top; border: 0px solid red;" ActiveControl.EnableUpdate="true" >
</com:TActivePanel>

<com:TActiveLinkButton ID='formatHLink' Text='[F]' CausesValidation='false' OnCallBack="changeFormat" Style="display:none; text-align:left; text-decoration: none;" />

</com:TActivePanel>
