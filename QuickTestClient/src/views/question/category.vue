<template>
    <v-container class="px-0">
        <Message/>
        <!-- data export options start -->
        <v-btn @click="generatePdf" small outlined>PDF</v-btn>
        <vue-excel-xlsx class="btnExcel" :data="itemsCategory" :columns="excelColumnsCategory" :filename="'questions-category-table'" :sheetname="'questions'">EXCEL</vue-excel-xlsx>
        <v-btn small outlined><download-csv :data="itemsCategory" name= "questions-category-table.csv">CSV</download-csv></v-btn>
        <!-- data export options end -->

        <!-- category table start -->
        <v-data-table
        :headers="headersCategory"
        :items="itemsCategory"
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
                                v-model="editedItem.questionCategoryName"
                                label="Question Category Name"
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
    <!-- category table end -->
    </v-container>
</template>

<script>
import Message from '../../components/common/Message'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'Category',
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
            headersCategory:[
                {text:'ID',value:'questionCategoryId'},
                {text:'Question Category Name',value:'questionCategoryName'},
                {text: 'Actions', value: 'actions', sortable: false },
            ],
            itemsCategory:[],
            editedIndex:-1,
            editedItem:{
                questionCategoryId:'',
                questionCategoryName:'',           
            },
            defaultItem:{
                questionCategoryId:'',
                questionCategoryName:'',
            },
            excelColumnsCategory : [
                {label:'ID',field:'questionCategoryId'},
                {label:'Question Category Name',field:'questionCategoryName'}                    
           ],
        }
    },
    methods:{
        //export pdf
        generatePdf(){
            const doc = new jsPDF()
            doc.autoTable({
                body: this.itemsCategory,
                columns: [
                    {header:'ID',dataKey:'questionCategoryId'},
                    {header:'Question Category Name',dataKey:'questionCategoryName'}                                       
                ],
            })
            doc.save('questions-category-table.pdf')
        },
        //get all category
        initialize(){
          this.$store.dispatch('dashboard/applyLoading')
          this.$store.dispatch('quiz/fetchCategories')
          .then((response)=>{
            this.$store.dispatch('dashboard/cancelLoading')
            this.itemsCategory=response.data
          })
          .catch((err)=>{
            console.log(err)
          })
        },
        //intialize data to edit
        editItem(item){          
          this.editedIndex=this.itemsCategory.indexOf(item)
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
            this.$store.dispatch('quiz/deleteCategory',this.editedItem.questionCategoryId)
            .then(response=>{
              if(response.status==200){
                  this.$root.$emit('message_from_parent',response.data.responseMsg)
                  this.$store.dispatch('quiz/fetchCategories')
                  .then((response)=>{
                    this.itemsCategory=response.data                  
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
        save(){
            if(this.$refs.form.validate()){
                if(this.editedIndex>-1){
                    const objCategory={
                        questionCategoryId:this.editedItem.questionCategoryId,           
                        questionCategoryName:this.editedItem.questionCategoryName.trim(),
                        lastUpdatedBy:parseInt(localStorage.getItem('loggedUserId'))
                    }
                    this.$store.dispatch('quiz/updateCategory',objCategory)
                    .then(response=>{              
                        if(response.status==200){
                            this.$root.$emit('message_from_parent',response.data.responseMsg)
                            this.$store.dispatch('quiz/fetchCategories')
                            .then((response)=>{
                                this.close()
                                this.itemsCategory=response.data
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
                    const objCategory={          
                        questionCategoryName:this.editedItem.questionCategoryName.trim(),
                        addedBy:parseInt(localStorage.getItem('loggedUserId'))
                    }
                    this.$store.dispatch('quiz/createCategory',objCategory)
                    .then(response=>{              
                        if(response.status==200){
                            this.$root.$emit('message_from_parent',response.data.responseMsg)
                            this.$store.dispatch('quiz/fetchCategories')
                            .then((response)=>{
                                this.close()
                                this.itemsCategory=response.data
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
        }
    },
    computed:{
      formTitle:function(){
        return this.editedIndex===-1?'New Category':'Edit Category'
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