<template>
<v-container class="px-0">
    <Message/>
    <!-- data export options start -->
    <v-btn @click="generatePdf" small outlined>PDF</v-btn>
    <vue-excel-xlsx class="btnExcel" :data="itemsMenu" :columns="excelColumnsMenu" :filename="'menus-table'" :sheetname="'menus'">EXCEL</vue-excel-xlsx>
    <v-btn small outlined><download-csv :data="itemsMenu" name= "menus-table.csv">CSV</download-csv></v-btn>
    <!-- data export options end -->

    <!-- app menu table start -->
    <v-data-table
      :headers="headersMenu"
      :items="itemsMenu"
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
                        md="6"
                      >                     
                        <v-text-field
                          v-model="editedItem.menuTitle"
                          label="Menu Title"
                          :rules="[rules.required]" 
                          clearable                      
                        ></v-text-field>   

                      </v-col>                                          
                      <v-col
                        cols="12"
                        sm="6"
                        md="6"                                                              
                      >                     
                        <v-text-field
                          v-model="editedItem.url"
                          label="URL"
                          :rules="[rules.required]"                        
                          clearable                  
                        ></v-text-field>   

                      </v-col>                                      
                    </v-row>
                    <v-row>         
                      <v-col
                        cols="12"
                        sm="6"
                        md="6"
                      >                     
                        <v-text-field
                          v-model="editedItem.sortOrder"
                          label="Order No."
                          type="number"
                          :rules="[rules.required]" 
                          clearable                      
                        ></v-text-field>   

                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        md="6"
                      >                     
                        <v-text-field
                          v-model="editedItem.iconClass"
                          label="Icon(Material design icon)" 
                          :rules="[rules.required]"
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
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
      </template>
    </v-data-table>
  <!-- app menu table end -->
</v-container>
</template>

<script>
import Message from '../../components/common/Message'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'BrowseList',
    components:{
        Message
    },
    data(){
        return{
            rules:{
              required:value=>!!value||'Required'
            },
            dialog:false,
            dialogDelete:false,
            headersMenu:[
                {text:'Menu Title',value:'menuTitle'},
                {text:'URL',value:'url'},
                {text:'Order No.',value:'sortOrder'},
                {text:'Icon(Material-Icon)',value:'iconClass'},
                {text: 'Actions', value: 'actions', sortable: false },
            ],
            itemsMenu:[],
            editedIndex:-1,
            editedItem:{
                appMenuId:'',
                menuTitle:'',
                url:'',              
                sortOrder:'',
                iconClass:'',           
            },
            defaultItem:{
                appMenuId:'',
                menuTitle:'',
                url:'',
                isSubMenu:'',
                sortOrder:'',
                iconClass:'',
            },
            excelColumnsMenu : [
                {label: "Menu Title",field: "menuTitle"},
                {label: 'URL', field: 'url' },
                {label: 'Menu Order', field: 'sortOrder' },
                {label: 'Icon name(material)', field: 'iconClass' }             
            ],
        }
    },
    methods:{
      //export pdf
      generatePdf(){
        const doc = new jsPDF()
        doc.autoTable({
          margin: { top: 10,left:2,right:1 },
          body: this.itemsMenu,
          columns: [
              { header: 'Menu Title', dataKey: 'menuTitle' },
              { header: 'URL', dataKey: 'url' },
              { header: 'Menu Order', dataKey: 'sortOrder' },
              { header: 'Icon name(material)', dataKey: 'iconClass' }
          ],
        })
        doc.save('menu-table.pdf')
      },

      //get all menu
      initialize(){
        this.$store.dispatch('dashboard/applyLoading')
        this.$store.dispatch('menu/fetchMenu')
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          this.itemsMenu=response.data
        })
        .catch((err)=>{
          console.log(err)
        })
      },
      //intialize data to edit
      editItem(item){          
        this.editedIndex=this.itemsMenu.indexOf(item)
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
          this.$store.dispatch('menu/deleteMenu',this.editedItem.appMenuId)
          .then(response=>{
            if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.responseMsg)
                this.$store.dispatch('menu/fetchMenu')
                .then((response)=>{
                    this.itemsMenu=response.data                  
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
      save (){
        if(this.$refs.form.validate()){
          if(this.editedIndex>-1){
            const objMenu={
              appMenuId:this.editedItem.appMenuId,           
              menuTitle:this.editedItem.menuTitle,
              url:this.editedItem.url,             
              sortOrder:this.editedItem.sortOrder,
              iconClass:this.editedItem.iconClass,
              lastUpdatedBy:parseInt(localStorage.getItem('loggedUserId'))
            }
            this.$store.dispatch('menu/updateMenu',objMenu)
            .then(response=>{              
              if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.responseMsg)
                this.$store.dispatch('menu/fetchMenu')
                .then((response)=>{
                  this.close()
                  this.itemsMenu=response.data
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

          }else{          
            const objMenu={
              menuTitle:this.editedItem.menuTitle,
              url:this.editedItem.url,             
              sortOrder:this.editedItem.sortOrder,
              iconClass:this.editedItem.iconClass,
              addedBy:parseInt(localStorage.getItem('loggedUserId'))
            }
            this.$store.dispatch('menu/createMenu',objMenu)
            .then(response=>{
              if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.responseMsg)
                this.$store.dispatch('menu/fetchMenu')
                .then((response)=>{
                  this.close()
                  this.itemsMenu=response.data
                })
                .catch((err)=>{
                  console.log(err)
                })
              }else if(response.status==202){
                this.$root.$emit('message_from_parent',response.data.responseMsg)
              }
            })
            .catch(err=>{               
              console.log(err)            
            })
          }         
        }
      }
    },
    computed:{
      formTitle:function(){
        return this.editedIndex===-1?'New Menu':'Edit Menu'
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