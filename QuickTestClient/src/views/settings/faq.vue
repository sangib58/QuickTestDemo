<template>
<!-- faq table for admin start-->
    <v-container class="px-0" v-if="getUserRole=='Admin'">
        <Message/>
        <v-btn @click="generatePdf" small outlined>PDF</v-btn>
        <vue-excel-xlsx class="btnExcel" :data="itemsFaq" :columns="excelColumnsFaq" :filename="'Faq-table'" :sheetname="'FAQ'">EXCEL</vue-excel-xlsx>
        <v-data-table
            :headers="headersFaq"
            :items="itemsFaq"
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
                                            v-model="editedItem.title"
                                            label="Title"
                                            :rules="[rules.required]" 
                                            clearable                      
                                        ></v-text-field>   
                                    </v-col>                                                                                                               
                                </v-row>
                                <v-row>
                                    <v-col
                                        cols="12"                                                                                              
                                    >                     
                                        <v-textarea
                                            v-model="editedItem.description"
                                            label="Description"
                                            :rules="[rules.required]"                        
                                            clearable                  
                                        ></v-textarea>   
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
            <template v-slot:no-data>
                <v-btn
                color="primary"
                @click="initialize"
                >
                Reset
                </v-btn>
            </template>
        </v-data-table>
    </v-container>
<!-- faq table for admin end-->

<!-- faq data show for not admin user start-->
    <v-container class="px-0" v-else>
        <v-expansion-panels focusable>
            <v-expansion-panel v-for="item in itemsFaq" :key="item.faqId">
                <v-expansion-panel-header>{{item.title}}</v-expansion-panel-header>
                <v-expansion-panel-content>{{item.description}}</v-expansion-panel-content>
            </v-expansion-panel>
        </v-expansion-panels>
    </v-container>
<!-- faq data show for not admin user end-->
</template>

<script>
import Message from '../../components/common/Message'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'Faqs',
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
            headersFaq:[
                {text:'Title',value:'title'},
                {text:'Description',value:'description'},
                {text: 'Actions', value: 'actions', sortable: false }
            ],
            itemsFaq:[],
            editedIndex:-1,
            editedItem:{
                faqId:'',
                title:'',
                description:''         
            },
            defaultItem:{
                faqId:'',
                title:'',
                description:''
            },
            excelColumnsFaq : [
                {label:'Title',field:'title'},
                {label:'Description',field:'description'}           
           ],
        }
    },
    methods:{
        //get pdf
        generatePdf(){
            const doc = new jsPDF()
            doc.autoTable({
                margin: { top: 10,left:2,right:1 },
                body: this.itemsFaq,
                columns: [
                    {header:'Title',dataKey:'title'},
                    {header:'Description',dataKey:'description'}
                ],
            })
            doc.save('faq-table.pdf')
        },
        //initialize all faq data
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('settings/fetchfaqs')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsFaq=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //intialize data to edit
        editItem(item){                    
            this.editedIndex=this.itemsFaq.indexOf(item)
            this.editedItem=Object.assign({},item)
            this.dialog=true
        },
        //intialize data to delete
        deleteItem(item){
            this.editedItem = Object.assign({}, item)
            this.dialogDelete=true
        },
        //confirm delete
        deleteItemConfirm () {
            this.$store.dispatch('settings/deleteFaq',this.editedItem.faqId)
            .then(response=>{
            if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.responseMsg)
                this.$store.dispatch('settings/fetchfaqs')
                .then((response)=>{
                    this.itemsFaq=response.data                  
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
        close(){
            this.dialog=false
            this.$nextTick(()=>{
                this.editedItem=Object.assign({},this.defaultItem)
                this.editedIndex=-1
            })
        },
        closeDelete () {
                this.dialogDelete = false
                this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        //create & edit
        save (){
          if(this.$refs.form.validate()){
            if(this.editedIndex>-1){
              const objFaq={
                faqId:this.editedItem.faqId,           
                title:this.editedItem.title,
                description:this.editedItem.description,                           
                lastUpdatedBy:parseInt(localStorage.getItem('loggedUserId'))
              }
              this.$store.dispatch('settings/updateFaq',objFaq)
              .then(response=>{              
                if(response.status==200){
                  this.$root.$emit('message_from_parent',response.data.responseMsg)
                  this.$store.dispatch('settings/fetchfaqs')
                  .then((response)=>{
                    this.close()
                    this.itemsFaq=response.data
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

            }else{          
              const objFaq={
                title:this.editedItem.title,
                description:this.editedItem.description,                            
                addedBy:parseInt(localStorage.getItem('loggedUserId'))
              }
              this.$store.dispatch('settings/createFaq',objFaq)
              .then(response=>{          
                if(response.status==200){
                  this.$root.$emit('message_from_parent',response.data.responseMsg)
                  this.$store.dispatch('settings/fetchfaqs')
                  .then((response)=>{
                    this.close()
                    this.itemsFaq=response.data
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
        return this.editedIndex===-1?'New FAQ':'Edit FAQ'
      },
      getUserRole:function(){
        return this.$store.getters['dashboard/userInfo'].roleName
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