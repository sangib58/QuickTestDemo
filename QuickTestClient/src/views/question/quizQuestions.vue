<template>
<!-- question manage view -->
    <v-container class="px-0">
        <Message/>
        <v-row justify="center">
            <v-col cols="12" md="4" class="font-weight-black"><h3><v-icon @click="switchToList()">arrow_back</v-icon>{{this.quizObj.quizTitle}}</h3></v-col>
        </v-row>
        <v-row>
            <!-- data export options start -->
            <v-col cols="12" md="3">
                <v-btn @click="generatePdf" small outlined>PDF</v-btn>
                <vue-excel-xlsx class="btnExcel" :data="itemsQuestions" :columns="excelColumnsQuestions" :filename="'questions-table'" :sheetname="'questions'">EXCEL</vue-excel-xlsx>
                <v-btn small outlined><download-csv :data="itemsQuestions" name= "questions-table.csv">CSV</download-csv></v-btn>
            </v-col>
            <!-- data export options end -->
            <v-spacer></v-spacer>    
            <!-- csv file start -->      
            <v-col cols="12" md="3">
                <v-file-input                 
                    label="CSV Upload"                  
                    class="pa-0"   
                    @change="OnCsvSelected"                    
                    show-size
                >
                </v-file-input>
            </v-col>          
            <v-col cols="12" md="2">
                <v-btn class="text-capitalize" @click="downloadSampleCsv" outlined small>Sample CSV<v-icon right dark>upload_file</v-icon></v-btn>
            </v-col>
            <v-col cols="12" md="3">
                <v-btn class="text-capitalize" @click="downloadInstructionsPdf" outlined small>Upload Instructions<v-icon right dark>picture_as_pdf</v-icon></v-btn>
            </v-col>
            <!-- csv file end -->           
        </v-row>
        
        <!-- question table start --> 
        <v-data-table           
            :headers="headersQuestions"
            :items="itemsQuestions"                     
        >
            <template v-slot:top>
                <v-toolbar
                    flat
                >         
                    <v-dialog
                        v-model="dialog"
                        max-width="900"
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
                                                sm="12" 
                                                md="8"                                                                                            
                                            >
                                                <v-textarea
                                                    v-model="editedItem.questionDetail"
                                                    label="Question"
                                                    :rules="[rules.required]"
                                                    rows="1"
                                                    clearable                       
                                                ></v-textarea>
                                            </v-col>  
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="2"
                                            >
                                                <v-checkbox
                                                    v-model="editedItem.isCodeSnippet"
                                                    label="Code Snippet"                                                                                                    
                                                    clearable                       
                                                ></v-checkbox>
                                            </v-col>  
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="2"
                                                v-if="chkPerQuestionMarkVisibility==true"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.perQuestionMark"
                                                    :rules="[rules.required,rules.minimumVal]" 
                                                    type="number"                                                  
                                                    label="Marks"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>                                                                                                                                                                                     
                                        </v-row>
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-select
                                                    v-model="typeOptionSelect"
                                                    label="Question Type"
                                                    :items="typeOptionItems"
                                                    item-text="questionTypeName"
                                                    item-value="questionTypeId"
                                                    :rules="[rules.required]"
                                                    v-on:change="chkQuestionType"                                                                                              
                                                    clearable
                                                    return-object                       
                                                ></v-select> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-select
                                                    v-model="lavelOptionSelect"
                                                    label="Complexity Lavel"
                                                    :items="lavelOptionItems"
                                                    item-text="questionLavelName"
                                                    item-value="questionLavelId"
                                                    :rules="[rules.required]"                                                                                              
                                                    clearable
                                                    return-object                       
                                                ></v-select> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-select
                                                    v-model="categoryOptionSelect"
                                                    label="Question Category"
                                                    :items="categoryOptionItems"
                                                    item-text="questionCategoryName"
                                                    item-value="questionCategoryId"
                                                    :rules="[rules.required]"                                                                                              
                                                    clearable
                                                    return-object                       
                                                ></v-select> 
                                            </v-col>
                                        </v-row>                                      
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.videoPath"
                                                    label="Question Video(youtube embed iframe)"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"  
                                                sm="6"               
                                                md="4"
                                            >
                                                <v-file-input
                                                    v-model="imageFile"
                                                    accept="image/*"
                                                    label="Question Image"
                                                    prepend-icon="mdi-camera"
                                                    @change="onFileSelected"                       
                                                    show-size
                                                >
                                                </v-file-input>                    
                                            </v-col>
                                            <v-col
                                                cols="12"  
                                                sm="3"                 
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewImage"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                        </v-row>
                                        <v-row
                                            v-if="chkAllOptionsVisible"
                                        >                                           
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.optionA"
                                                    label="Option-A"
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
                                                    v-model="editedItem.optionB"
                                                    label="Option-B"
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
                                                    v-model="editedItem.optionC"
                                                    label="Option-C"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row
                                            v-if="chkAllOptionsVisible"
                                        >
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.optionD"
                                                    label="Option-D"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.optionE"
                                                    label="Option-E"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                                v-if="chkAnswerSingleVisibility==true"
                                            >
                                                <v-select
                                                    v-model="editedItem.correctOption"
                                                    label="Correct Answer"
                                                    :items="options"
                                                    :rules="[rules.required]"                                                                                                      
                                                    clearable
                                                >                                                   
                                                </v-select>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                                v-if="chkAnswerMultipleVisibility==true"
                                            >
                                                <v-select
                                                    v-model="editedItem.correctOption"
                                                    label="Correct Answer"
                                                    :items="options"
                                                    :rules="[rules.required]"                                                   
                                                    multiple
                                                    clearable
                                                >                                                   
                                                </v-select>
                                            </v-col>
                                        </v-row>                                       
                                        <v-row>
                                            <v-col
                                                cols="12"
                                            >
                                                <v-textarea
                                                    v-model="editedItem.answerExplanation"
                                                    label="Answer Explanation"
                                                    rows="1"
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
                    class="mr-2"
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:[`item.perQuestionMark`]="{item}">
                <span>{{formatMarks(item)}}</span> 
            </template>
            <template v-slot:[`item.correctOption`]="{item}">
                <span>{{formatCorrectOption(item)}}</span> 
            </template>
        </v-data-table>
    <!-- question table end --> 
    </v-container>
</template>
<script>
import Message from '../../components/common/Message'
import config from '../../config'
import jsPDF from 'jspdf'
import 'jspdf-autotable'
import axios from 'axios'

export default{
    name:'quizQuestions',
    components:{
      Message
    },
    data(){
        return{
            allOptionsVisible:true,
            typeOptionSelect: null,
            typeOptionItems:[],
            lavelOptionSelect: null,
            lavelOptionItems:[],
            categoryOptionSelect: null,
            categoryOptionItems:[],            
            previewImage:null,
            imageFile:null,
            selectedFile:null,
            quizObj:{},
            dialog:false,
            dialogDelete:false,
            options:['Option-A','Option-B','Option-C','Option-D','Option-E'],
            rules:{
                required:value=>!!value||'Required',
                minimumVal:value=>value>0
            },
            headersQuestions:[
                {text:'Questions',value:'questionDetail'},
                {text:'Category',value:'questionCategoryName'},
                {text:'Marks',value:'perQuestionMark'},
                {text:'Option-A',value:'optionA'},
                {text:'Option-B',value:'optionB'},
                {text:'Option-C',value:'optionC'},
                {text:'Option-D',value:'optionD'},
                {text:'Correct Answer',value:'correctOption'},
                {text: 'Actions', value: 'actions',sortable: false},
            ],
            itemsQuestions:[],
            editedIndex:-1,
            editedItem:{
                quizQuestionId:null,
                quizTopicId:null,               
                questionDetail:'',
                perQuestionMark:0,
                questionTypeId:null,
                questionLavelId:null,
                questionCategoryId:null,
                optionA:'',
                optionB:'',
                optionC:'',
                optionD:'',
                optionE:'',
                correctOption:'',
                correctOptionText:'',
                answerExplanation:'',
                imagePath:'',
                videoPath:'',
                isCodeSnippet:false,                
            },
            defaultItem:{
                quizQuestionId:null,
                quizTopicId:null,
                questionDetail:'',
                perQuestionMark:0,
                questionTypeId:null,
                questionLavelId:null,
                questionCategoryId:null,
                optionA:'',
                optionB:'',
                optionC:'',
                optionD:'',
                optionE:'',
                correctOption:'',
                correctOptionText:'',
                answerExplanation:'',
                imagePath:'',
                videoPath:'',
                isCodeSnippet:false,              
            },
            excelColumnsQuestions : [
                {label:'Questions',field:'questionDetail'},
                {label:'Mark',field:'perQuestionMark'},
                {label:'Option-A',field:'optionA'},
                {label:'Option-B',field:'optionB'},
                {label:'Option-C',field:'optionC'},
                {label:'Option-D',field:'optionD'},
                {label:'Option-E',field:'optionE'},
                {label:'Correct Answer',field:'correctOption'},  
                {label:'Answer Explanation',field:'answerExplanation'},
                {label:'Is Code Snippet',field:'isCodeSnippet'},       
           ],
        }        
    },
    methods:{
        //get pdf
        generatePdf(){
            const doc = new jsPDF()
            doc.autoTable({
                styles:{cellWidth:23},
                margin: { top: 10,left:2,right:1 },
                body: this.itemsQuestions,
                columns: [
                    {header:'Questions',dataKey:'questionDetail'},
                    {header:'Mark',dataKey:'perQuestionMark'},
                    {header:'OptionA',dataKey:'optionA'},
                    {header:'OptionB',dataKey:'optionB'},
                    {header:'OptionC',dataKey:'optionC'},
                    {header:'OptionD',dataKey:'optionD'},
                    {header:'OptionE',dataKey:'optionE'},
                    {header:'Answer',dataKey:'correctOption'},  
                    {header:'Explain',dataKey:'answerExplanation'},                  
                ],
            })
            doc.save('questions-table.pdf')
        },
        //get sample csv
        downloadSampleCsv(){
            axios({
                url: window.location.origin+'/upload/sample-upload.csv',
                method: 'GET',
                responseType: 'blob'
            })
            .then((response) => {
                const url = window.URL
                    .createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'sample-upload.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
        },
        //get question upload instruction pdf
        downloadInstructionsPdf(){
            axios({
                url: window.location.origin+'/upload/Instructions-to-upload-questions.pdf',
                method: 'GET',
                responseType: 'blob'
            })
            .then((response) => {
                const url = window.URL
                    .createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Instructions-to-upload-questions.pdf');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
        },
        //upload question csv
        OnCsvSelected(file){
            this.$store.dispatch('dashboard/applyLoading')
            if(file!=null){
                if(file.name.includes('.csv')){
                    let fd=new FormData()
                    fd.append('csv',file)
                    this.$store.dispatch('quiz/uploadQuestionCsv',fd)
                    .then(response=>{               
                        if(response.status==200){
                            var csvPath='\\'+response.data              
                            const obj={
                                path:csvPath,
                                quizTopicId:this.quizObj.quizTopicId,
                                addedBy:parseInt(localStorage.getItem('loggedUserId'))
                            }
                            this.$store.dispatch('quiz/readQuestionCsvData',obj)
                            .then(response=>{
                                if(response.status==200){
                                    this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                                    this.$store.dispatch('question/fetchQuizQuestions',this.quizObj.quizTopicId)
                                    .then((response)=>{
                                        this.itemsQuestions=response.data
                                    })
                                }else if(response.status==202){
                                    this.$root.$emit('message_from_parent_long','Not uploaded! Please check your CSV carefully also follow upload instructions.')
                                }
                            })                 
                        }
                    })
                }else{
                    this.$root.$emit('message_from_parent_long','This is not an CSV file ! Only CSV file upload is allowed')
                }
            }
            file=null
            this.$store.dispatch('dashboard/cancelLoading')
        },
        //format per question mark
        formatMarks(item){
            return item.perQuestionMark==0?'':item.perQuestionMark
        },
        //format correct answer
        formatCorrectOption(item){
            return (item.correctOption!='' && item.correctOption!=null)?item.correctOption.replace(/#####/g,','):''
        },
        //upload question image
        onFileSelected(e){           
            if(e!=null){
                this.selectedFile=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedFile)
                reader.onload=e=>{
                    this.previewImage=e.target.result
                }
            }else{               
                this.selectedFile=null
                this.previewImage=null
                this.editedItem.imagePath=''
            }                      
        },
        //switch to test list
        switchToList(){
            this.$router.push({name:'Quizes'})
        },
        //check mcq & descriptive type
        chkQuestionType(obj){
            if(obj==null){
                this.allOptionsVisible=true
            }else if(obj.questionTypeId==1){
                this.allOptionsVisible=true
            }else if(obj.questionTypeId==2){
                this.allOptionsVisible=false
            }
        },
        //check type options
        getTypeOptions(){
            this.$store.dispatch('quiz/fetchTypeOptions')
            .then((response)=>{
                this.typeOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //check lavel options
        getLavelOptions(){
            this.$store.dispatch('quiz/fetchLavelOptions')
            .then((response)=>{
                this.lavelOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //check category options
        getCategoryOptions(){
            this.$store.dispatch('quiz/fetchCategoryOptions')
            .then((response)=>{
                this.categoryOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //get all questions
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('question/fetchQuizQuestions',this.quizObj.quizTopicId)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                if(response.data.length>0){
                    this.itemsQuestions=response.data
                }              
            })
            .catch((err)=>{
                console.log(err)
            })           
        },
        //select mcq option
        selectOption(val,item){
            if(val==item.optionA){
                this.editedItem.correctOption='Option-A'
            }else if(val==item.optionB){
                this.editedItem.correctOption='Option-B'
            }else if(val==item.optionC){
                this.editedItem.correctOption='Option-C'
            }else if(val==item.optionD){
                this.editedItem.correctOption='Option-D'
            }else if(val==item.optionE){
                this.editedItem.correctOption='Option-E'
            }
        },
        //set correct answer
        selectCorrectAnswer(item){           
            if(this.quizObj.quizMarkOptionId!=2&&this.quizObj.allowMultipleAnswer==false){
                this.selectOption(item.correctOption,item)               
            }else if(this.quizObj.quizMarkOptionId!=2&&this.quizObj.allowMultipleAnswer==true){
                var allOptions=item.correctOption+'#####'
                var seperatedOptions=allOptions.split('#####')
                var selections=''
                this.editedItem.correctOption=''
                for(var i=0;i<seperatedOptions.length-1;i++){
                    this.selectOption(seperatedOptions[i],item)
                    selections+=this.editedItem.correctOption+'#####'
                }
                this.editedItem.correctOption=selections.split('#####')
            }
        },
        //question edit preview
        editItem(item){
            this.previewImage=item.imagePath!=''?config.hostname+item.imagePath:''

            this.typeOptionSelect={questionTypeId:item.questionTypeId}
            this.chkQuestionType(this.typeOptionSelect)
            this.lavelOptionSelect={questionLavelId:item.questionLavelId}
            this.categoryOptionSelect={questionCategoryId:item.questionCategoryId}

            this.editedIndex=this.itemsQuestions.indexOf(item)           
            this.editedItem=Object.assign({},item)
            this.selectCorrectAnswer(item)
            this.dialog=true
        },
        //delete dialog
        deleteItem(item){
            this.editedItem = Object.assign({}, item)
            this.dialogDelete=true
        },
        //delete confirm
        deleteItemConfirm () {       
            this.$store.dispatch('question/deleteQuestion',this.editedItem.quizQuestionId)
            .then(response=>{
                if(response.status==200){
                    this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                    this.$store.dispatch('question/fetchQuizQuestions',this.quizObj.quizTopicId)
                    .then((response)=>{
                        this.itemsQuestions=response.data                  
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
        //close dialog for save & edit
        close(){
            this.dialog=false
            this.$nextTick(()=>{    
                this.typeOptionSelect=null
                this.lavelOptionSelect=null
                this.categoryOptionSelect=null
                this.imageFile=null
                this.selectedFile=null
                this.previewImage=null    
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
        //set correct answer for single option
        setCurrentAnswerSingle(option){
            if(option=='Option-A'){
                this.editedItem.correctOptionText=this.editedItem.optionA!=null?this.editedItem.optionA:''
            }else if(option=='Option-B'){
                this.editedItem.correctOptionText=this.editedItem.optionB!=null?this.editedItem.optionB:''
            }else if(option=='Option-C'){
                this.editedItem.correctOptionText=this.editedItem.optionC!=null?this.editedItem.optionC:''
            }else if(option=='Option-D'){
                this.editedItem.correctOptionText=this.editedItem.optionD!=null?this.editedItem.optionD:''
            }else if(option=='Option-E'){
                this.editedItem.correctOptionText=this.editedItem.optionE!=null?this.editedItem.optionE:''
            }
        },
        //set correct answer for multiple option
        setCurrentAnswerMultiple(options){  
            var answers=''
            this.editedItem.correctOptionText=''   
            for(var i=0;i<options.length;i++){
                this.setCurrentAnswerSingle(options[i])
                if(options[i]!='' && this.editedItem.correctOptionText!=null && this.editedItem.correctOptionText!=''){
                    answers+=this.editedItem.correctOptionText+'#####'
                }
            }
            this.editedItem.correctOptionText=answers.trim().slice(0,-5)
        },
        //insert and update question
        insertOrUpdateQuestion(){
            if(this.typeOptionSelect.questionTypeId==1&&this.quizObj.quizMarkOptionId!=2&&this.quizObj.allowMultipleAnswer==false){
                this.setCurrentAnswerSingle(this.editedItem.correctOption)
            }else if(this.typeOptionSelect.questionTypeId==1&&this.quizObj.quizMarkOptionId!=2&&this.quizObj.allowMultipleAnswer==true){
                this.setCurrentAnswerMultiple(this.editedItem.correctOption)
            }
            const objQuestion={
                quizTopicId:this.quizObj.quizTopicId,
                questionDetail:this.editedItem.questionDetail.trim(),
                questionTypeId:this.typeOptionSelect.questionTypeId,
                questionLavelId:this.lavelOptionSelect.questionLavelId,
                questionCategoryId:this.categoryOptionSelect.questionCategoryId,
                optionA:this.typeOptionSelect.questionTypeId==1?this.editedItem.optionA:'',
                optionB:this.typeOptionSelect.questionTypeId==1?this.editedItem.optionB:'',
                optionC:this.typeOptionSelect.questionTypeId==1?this.editedItem.optionC:'',
                optionD:this.typeOptionSelect.questionTypeId==1?this.editedItem.optionD:'',
                optionE:this.typeOptionSelect.questionTypeId==1?this.editedItem.optionE:'',
                correctOption:this.typeOptionSelect.questionTypeId==1?this.editedItem.correctOptionText:'',
                answerExplanation:this.editedItem.answerExplanation,
                imagePath:this.editedItem.imagePath,
                videoPath:this.editedItem.videoPath,
                isCodeSnippet:this.editedItem.isCodeSnippet, 
                addedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            if(this.typeOptionSelect.questionTypeId==1&&this.quizObj.quizMarkOptionId!=2&&this.editedItem.correctOptionText==''){
                this.$root.$emit('message_from_parent_long','Correct answer empty!please check.')
            }else if(this.editedItem.videoPath!=null && this.editedItem.videoPath!='' && this.editedItem.videoPath.toLowerCase().indexOf("https://www.youtube.com/embed")==-1){
                this.$root.$emit('message_from_parent_long','Only youtube embed iframe is allowed!please check the Question video.')
            }else{
                if (this.editedIndex > -1){
                    objQuestion.quizQuestionId=this.editedItem.quizQuestionId
                    objQuestion.lastUpdatedBy=parseInt(localStorage.getItem('loggedUserId'))
                    if(this.quizObj.quizMarkOptionId==3){
                        objQuestion.perQuestionMark=this.editedItem.perQuestionMark
                    }

                    this.$store.dispatch('question/updateQuestion',objQuestion)
                    .then(response=>{    
                        if(response.status==200){
                            this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                            this.$store.dispatch('question/fetchQuizQuestions',this.quizObj.quizTopicId)
                            .then((response)=>{
                                this.close()
                                this.itemsQuestions=response.data                  
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
                    if(this.quizObj.quizMarkOptionId==3){
                        objQuestion.perQuestionMark=this.editedItem.perQuestionMark
                    }
                    this.$store.dispatch('question/createQuestion',objQuestion)
                    .then(response=>{
                        if(response.status==200){
                            this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                            this.$store.dispatch('question/fetchQuizQuestions',this.quizObj.quizTopicId)
                            .then((response)=>{
                                this.close()
                                this.itemsQuestions=response.data                  
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
        save(){
            if(this.$refs.form.validate()){
                if(this.selectedFile!=null){
                 let fd=new FormData()
                 fd.append('image',this.selectedFile)
                 this.$store.dispatch('question/uploadImage',fd)               
                    .then(response=>{                       
                        if(response.status==200){
                            this.editedItem.imagePath=response.data                           
                            this.insertOrUpdateQuestion()
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })
                }else{
                    this.insertOrUpdateQuestion()
                }
            }
        }
    },
    computed:{
        formTitle(){
            return this.editedIndex===-1?'New Question':'Edit Question'
        },
        chkMarksVisibility(){
            return this.quizObj.quizMarkOptionId!=2?true:false
        },
        chkAnswerSingleVisibility(){
            return this.quizObj.quizMarkOptionId!=2&&this.quizObj.allowMultipleAnswer==false?true:false
        },
        chkAnswerMultipleVisibility(){
            return this.quizObj.quizMarkOptionId!=2&&this.quizObj.allowMultipleAnswer==true?true:false
        },
        chkPerQuestionMarkVisibility(){
            return this.quizObj.quizMarkOptionId==3?true:false
        },
        chkAllOptionsVisible(){
            return this.allOptionsVisible
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
        this.quizObj=this.$store.getters['question/quiz']
        this.initialize()
        this.getCategoryOptions()
        this.getTypeOptions()
        this.getLavelOptions()
    }
}
</script>