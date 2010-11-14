<!---com:TActiveButton OnCallback="toggleGrid" Text="toggle grids" /--->
<com:TActiveImageButton OnCallback="toggleGrid" ImageUrl="start.png" />

<com:TActivePanel>

    <com:XActiveDataGrid ID="grid1" Visible="true" AllowPaging="false" DataKeyField="id" AutoGenerateColumns="false" >

        <com:XActiveTemplateColumn ID="col1" >
            <prop:ItemTemplate>
                <com:TLabel Text="enter some data: " /><com:TTextBox ID="id1"/>
            </prop:ItemTemplate>
        </com:XActiveTemplateColumn>

    </com:XActiveDataGrid> 

    <!--- result/edit datagrid --->       
    <com:XActiveDataGrid ID="grid2" Visible="false" AllowPaging="false" AutoGenerateColumns="false" DataKeyField="id"  >
                            
        <com:XActiveTemplateColumn ID="col2" >
            <prop:ItemTemplate>             
                <com:TLabel Text="enter some other data: " /><com:TTextBox ID="id2" />
            </prop:ItemTemplate>
        </com:XActiveTemplateColumn>

    </com:XActiveDataGrid> 

</com:TActivePanel>