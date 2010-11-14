<com:TCallbackOptions ID="options">
	<prop:ClientSide.RequestTimeOut>720000</prop:ClientSide.RequestTimeOut>
</com:TCallbackOptions>

<com:PWCWindow ID="mpnlVariantenContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MyVariantenContainer->ClientID%>"
               Mode="Existing"
               Width="700px"
               Left="100"
               Top="100"
               Title="Varianten">
</com:PWCWindow>


<com:TActivePanel ID="MyVariantenContainer" Display="None">
    <com:Application.pages.container.VariantenContainer ID="VariantenContainer"  />
</com:TActivePanel>
<com:TActivePanel Display="None">
        <table>
        <tr>
            <td></td>
            <td>Bericht:</td><td><com:TActiveDropDownList id="DWH_idta_struktur_bericht" CssClass="mandantorylarge" AutoPostback="true" OnCallBack="StrukturBerichtChanged"/>
            <com:TActiveTextBox Id="DWH_idtm_struktur" Visible="false" Text="0" /></td>
            <td>
                <com:TActivePanel Id="DWH_PERIODAREA" Visible="true">
                <table width="100%">
                    <tr><td>
                        <com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kalarm.png" />Zeit:</td><td><com:TActiveTextBox id="DWH_idta_perioden"/></td>
                    </td><td>
                        </td><td><com:TActiveCheckBox id="DWH_per_single" CssClass="mandantory" AutoPostback="true" OnCallBack="PeriodenSingle" />Single
                    </td></tr>
                </table>
                </com:TActivePanel>
            </td>            
            <td><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kdisknav.png" />
            </td>            
            </tr>
        </table>
</com:TActivePanel>

<com:TCallback ID="MyCallback" OnCallback="callback_MyCallback" />
<com:TCallback ID="MyCallbackTime" OnCallback="callback_MyCallbackTime" />
<com:TCallback ID="MyCallbackReport" OnCallback="callback_MyCallbackReport" />
<com:TCallback ID="MyCallbackDouble" OnCallback="callback_MyCallbackDouble" />
<com:TCallback ID="MyCallbackDrop" OnCallback="callback_MyCallbackDrop" />

<com:PFDHTMLXTree ID="TreeViewDHTMLX" />

<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/ktouch.png" ID="StructureTools" />
<com:CActiveContextMenu ForControl="StructureTools" CssClass="menu desktop" OnMenuItemSelected="generateStructure" ActiveControl.CallbackOptions="options">
	<com:CContextMenuItem id="GenerateStructure" Text=<%[ Struktur aufbauen ]%> CommandName="Structure"/>
	<com:CContextMenuItem id="CleanStructure" Text=<%[ Struktur bereinigen ]%> CommandName="CleanStructure"/>
	<com:CContextMenuItemSeparator/>
	<com:CContextMenuItem id="GenerateNested" Text=<%[ Mengeninfo bauen ]%> CommandName="Nested"/>
        <com:CContextMenuItem id="InitValues" Text=<%[ Werte initialisieren ]%> CommandName="InitValues"/>
        <com:CContextMenuItemSeparator/>
	<com:CContextMenuItem id="GenerateLinks" Text=<%[ Treiber initialisieren ]%> CommandName="InitLinks"/>
</com:CActiveContextMenu>
Planungssicht:</td><td><com:TActiveDropDownList id="DWH_idta_stammdatensicht" CssClass="mandantory" AutoPostback="true" OnCallBack="ParameterChanged" />
Variante:</td><td><com:TActiveDropDownList id="DWH_idta_variante" CssClass="mandantory" AutoPostback="true" OnCallBack="ParameterChanged" /> <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filefind.png" OnCallBack="OpenVariantenContainer"/>
 || 