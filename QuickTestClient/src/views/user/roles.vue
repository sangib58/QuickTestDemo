<template>
  <v-container class="px-0">
    <Message/>
    <!-- data export options start -->
    <v-btn @click="generatePdf" small outlined>PDF</v-btn>
    <vue-excel-xlsx class="btnExcel" :data="itemsUserRole" :columns="excelColumnsRole" :filename="'role-table'" :sheetname="'roles'">EXCEL</vue-excel-xlsx>
    <v-btn small outlined><download-csv :data="itemsUserRole" name= "role-table.csv">CSV</download-csv></v-btn>
    <!-- data export options end -->

    <!-- roles table start -->
    <v-data-table
      :headers="headersUserRole"
      :items="itemsUserRole"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar
          flat
        >
          
          <v-dialog
            v-model="dialog"
            max-width="800"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="primary"
                dark
                class="mb-2"
                v-bind="attrs"
                v-on="on"
                absolute
                right
              >
                New Item
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                    <v-form ref="form">
                        <v-row>
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >                     
                                <v-text-field
                                    v-model="editedItem.roleName"
                                    label="Name"
                                    :rules="[rules.required]"
                                    clearable                       
                                ></v-text-field>                    
                            </v-col>
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >                     
                                <v-text-field
                                    v-model="editedItem.displayName"
                                    label="Display Name"
                                    :rules="[rules.required]"
                                    clearable                       
                                ></v-text-field>                    
                            </v-col>
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >                     
                                <v-text-field
                                    v-model="editedItem.roleDesc"
                                    label="Description"
                                    clearable                       
                                ></v-text-field>                    
                            </v-col>                                            
                        </v-row>
                    </v-form>                
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="close"
                >
                  Cancel
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="save"
                >
                  Save
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogMenu" max-width="600">
            <v-card>
              <v-list>
                <v-list-item v-for="item in menus" :key="item.appMenuId">                 
                  <v-list-item-icon><v-icon>{{item.iconClass}}</v-icon></v-list-item-icon>
                  <v-list-item-content v-text="item.menuTitle"></v-list-item-content>
                  <v-list-item-action><v-checkbox v-model="item.isSelected" @click="assignToRole(item)"></v-checkbox></v-list-item-action>             
                </v-list-item>
              </v-list>
            </v-card>          
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:[`item.actions`]="{item}">
        <v-icon
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          class="mr-2"
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
        <v-icon
          small
          @click="getMenus(item)"
        >
          menu
        </v-icon>
      </template>
    </v-data-table>
    <!-- roles table start -->
  </v-container>
</template>

<script>
import Message from '../../components/common/Message'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'userRoleList',
    components:{
      Message
    },
    data(){
      return{
        menus:null,
        rules:{
          required:value=>!!value||'Required'
        },
        selectedRole:null,
        dialog:false,
        dialogDelete:false,
        dialogMenu:false,
        headersUserRole:[
          {text:'Name',value:'roleName'},
          {text:'Display Name',value:'displayName'},
          {text:'Description',value:'roleDesc'},
          {text: 'Actions', value: 'actions', sortable: false},
        ],
        itemsUserRole:[],
        editedIndex:-1,
        editedItem:{
          userRoleId:'',
          roleName:'',
          displayName:'',
          roleDesc:''
        },
        defaultItem:{
          userRoleId:'',
          roleName:'',
          displayName:'',
          roleDesc:''
        },
        excelColumnsRole : [
          {label: "Role",field: "roleName"},
          {label: "Display",field: "displayName"},
          {label: 'Description', field: 'roleDesc' }         
        ]
      }
    },
    methods:{
      //export pdf
      generatePdf(){
        const doc = new jsPDF()
        doc.autoTable({
          margin: { top: 10,left:2,right:1 },
          body: this.itemsUserRole,
          columns: [
            { header: 'Role', dataKey: 'roleName' },
            { header: 'Display', dataKey: 'displayName' },
            { header: 'Description', dataKey: 'roleDesc' }
          ],
        })
        doc.save('role-table.pdf')
      },
      //menu assign to role
      assignToRole(item){
        const objMenuOperation={
          menuId:item.appMenuId,
          userRoleId:this.selectedRole,
          addedBy:parseInt(localStorage.getItem('loggedUserId')),
        }
        this.$store.dispatch('menu/assignNewMenu',objMenuOperation)
        .then(response=>{
          if(response.status==202){
            this.$root.$emit('message_from_parent',response.data.responseMsg)
          }
        })
        .catch(err=>{
          console.log(err)
        })
      },
      //get menus for selected role
      getMenus(item){
        this.selectedRole=item.userRoleId
        this.getAppMenu(this.selectedRole)
        this.dialogMenu=true
      },
      //get all menu
      getAppMenu(roleId){         
          this.$store.dispatch('menu/fetchAllMenu',roleId)
          .then((response)=>{
              if(response.status==200){
                this.menus=response.data            
              }
          })
          .catch((err)=>console.log(err))
      },
      //get all roles
      initialize(){
        this.$store.dispatch('dashboard/applyLoading')
        this.$store.dispatch('user/fetchUserRoles')
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          this.itemsUserRole=response.data
        })
        .catch((err)=>{
            console.log(err)
        })
      },
      //intialize data to edit
      editItem(item){
        this.editedIndex=this.itemsUserRole.indexOf(item)
        this.editedItem=Object.assign({},item)
        this.dialog=true
      },
      //intialize data to delete
      deleteItem(item){
        this.editedItem = Object.assign({}, item)
        this.dialogDelete=true
      },
      //confirm data to delete
      deleteItemConfirm () {
        this.$store.dispatch('user/deleteUserRole',this.editedItem.userRoleId)
        .then(response=>{
            if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.responseMsg)
              this.$store.dispatch('user/fetchUserRoles')
              .then((response)=>{
                  this.itemsUserRole=response.data                  
              })
              .catch((err)=>{
                  console.log(err)
              })
            }else if(response.status==202){
              this.$root.$emit('message_from_parent_long',response.data.responseMsg)
            }
        })
        .catch(err=>{
            console.log(err)
        })
        this.closeDelete()
      },
      //close dialog for save and edit
      close(){
        this.dialog=false
        this.$nextTick(()=>{             
            this.editedItem=Object.assign({},this.defaultItem)
            this.editedIndex=-1
        })
      },
      //close dialog for delete
      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
      },
      //create and update
      save () {
        if(this.$refs.form.validate()){
          if (this.editedIndex > -1) {
            //edit
            const objUserRole={
              userRoleId:this.editedItem.userRoleId,
              roleName:this.editedItem.roleName,
              displayName:this.editedItem.displayName,
              roleDesc:this.editedItem.roleDesc,
              lastUpdatedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            this.$store.dispatch('user/updateUserRole',objUserRole)
            .then(response=>{           
              if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.responseMsg)
              this.$store.dispatch('user/fetchUserRoles')
              .then((response)=>{
                  this.close()
                  this.itemsUserRole=response.data
              })
              .catch((err)=>{
                  console.log(err)
              })
              }else if(response.status==202){
                this.$root.$emit('message_from_parent_long',response.data.responseMsg)
              }
            })
              .catch(err=>{
                console.log(err)
              })
            } else {
            //insert
            const objUserRole={
              roleName:this.editedItem.roleName,
              displayName:this.editedItem.displayName,
              roleDesc:this.editedItem.roleDesc,
              addedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            this.$store.dispatch('user/createUserRole',objUserRole)
            .then(response=>{
              if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.responseMsg)
                this.$store.dispatch('user/fetchUserRoles')
                .then((response)=>{
                  this.close()
                  this.itemsUserRole=response.data
                })
                .catch((err)=>{
                  console.log(err)
                })
              }else if(response.status==202){
                  this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                }
              })
              .catch(err=>{
                 console.log(err)
              })
            }
            
          }
            
        },
      },
    computed:{
      formTitle(){
        return this.editedIndex===-1?'New Role':'Edit Role'
      }
    },
    watch:{
      dialog (val) {         
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },
    created(){
      this.initialize()
    }
}
</script>