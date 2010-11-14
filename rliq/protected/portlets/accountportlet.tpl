<com:TCallbackOptions ID="options">
	<prop:ClientSide.RequestTimeOut>720000</prop:ClientSide.RequestTimeOut>
</com:TCallbackOptions>

<div class="portlet">

<h2 class="portlet-title"><com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/gaim.png" Id="ApplicationTools"/><%[ Account ]%></h2>

<com:CActiveContextMenu ForControl="ApplicationTools" CssClass="menu desktop" OnMenuItemSelected="generateStructureOrganisation" ActiveControl.CallbackOptions="options">
	<com:CContextMenuItem id="GenerateOrganisationStructure" Text=<%[ Generate Organisation Structure ]%> CommandName="createOrganisationStructure" />
</com:CActiveContextMenu>


<div class="portlet-link"><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kuser.png" />
Welcome, <b><%= $this->User->Name %></b>!</div>

<com:AxListMenu CssClass="portlet-content" ActCss="portlet-link" IActCss="portlet-link" Colapse="false">
    <com:AxListMenuItem PagePath="user.user" Text=<%[ Users ]%> Visible=<%= $this->User->isInRole('Administrator') %> />
    <com:AxListMenuItem PagePath="Home" Text=<%[ Home ]%> />
    <com:AxListMenuItem PagePath="user.myaccount" Text=<%[ My Account ]%> />
 </com:AxListMenu>

<table width="100%">
    <tr><td class="portlet-link"><com:TLinkButton Text="Logout" OnClick="logout" /></td></tr>
</table>

</div><!-- end of portlet -->
