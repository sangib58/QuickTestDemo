<template>
    <v-container class="px-0">
        <Message/>
        <!-- data export options start -->
        <v-btn @click="generatePdf" small outlined>PDF</v-btn>
        <vue-excel-xlsx class="btnExcel" :data="itemsTopics" :columns="excelColumnsTopics" :filename="'topics-table'" :sheetname="'quiz'">EXCEL</vue-excel-xlsx>
        <v-btn small outlined><download-csv :data="itemsTopics" name= "topics-table.csv">CSV</download-csv></v-btn>
        <!-- data export options end -->

        <!-- assessment table start -->
        <v-data-table
        :headers="headersTopics"
        :items="itemsTopics"
        class="elevation-1"
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
                                                sm="6"
                                                md="4"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.quizTitle"
                                                    label="Title"
                                                    :rules="[rules.required]"
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >                     
                                                <v-select
                                                    v-model="markOptionSelect"
                                                    label="Mark Option"
                                                    :items="markOptionItems"
                                                    item-text="quizMarkOptionName"
                                                    item-value="quizMarkOptionId"
                                                    :rules="[rules.required]"   
                                                    v-on:change="chkMarkOptions"                                                    
                                                    clearable
                                                    return-object                       
                                                ></v-select>                                         
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="2"
                                            >
                                                <v-checkbox
                                                    v-model="editedItem.allowMultipleAttempt"
                                                    label="Multiple Attempt"
                                                >
                                                </v-checkbox>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="2"
                                                v-if="chkMultipleInputVisibility==true"
                                            >
                                                <v-checkbox
                                                    v-model="editedItem.allowMultipleInputByUser"
                                                    label="Multiple Input"
                                                    
                                                >
                                                </v-checkbox>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="2"
                                                v-if="chkMultipleAnswerVisibility==true"
                                            >
                                                <v-checkbox
                                                    v-model="editedItem.allowMultipleAnswer"
                                                    label="Multiple Answer"                                      
                                                >
                                                </v-checkbox>
                                            </v-col>                                                                 
                                        </v-row>
                                        <v-row v-if="chkMarksVisibility==true">                                
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                                v-if="chkQuestionWiseSetVisibility==true"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.quizTotalMarks"                                       
                                                    label="Marks"
                                                    type="number"                                      
                                                    :rules="[rules.required,rules.minimumVal]"
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>                                                                                      
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"                                   
                                            >
                                                <v-text-field
                                                    v-model="editedItem.quizTime"
                                                    type="number"                                       
                                                    label="Required Time(In minutes)"
                                                    :rules="[rules.required,rules.minimumVal]"
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.quizPassMarks"
                                                    label="Pass Marks"
                                                    type="number"
                                                    :rules="[rules.passVal]"                                      
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
                                                <v-menu
                                                    v-model="startTime"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    transition="scale-transition"
                                                    offset-y
                                                    min-width="auto"
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            v-model="editedItem.quizscheduleStartTime"
                                                            label="Schedule Start Date"
                                                            prepend-icon="mdi-calendar"
                                                            readonly
                                                            v-bind="attrs"
                                                            v-on="on"
                                                            clearable                                                         
                                                        ></v-text-field>
                                                    </template>
                                                    <v-date-picker
                                                        v-model="editedItem.quizscheduleStartTime"
                                                        @input="startTime = false"
                                                    ></v-date-picker>
                                                </v-menu>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >
                                                <v-menu
                                                    v-model="endTime"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    transition="scale-transition"
                                                    offset-y
                                                    min-width="auto"
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            v-model="editedItem.quizscheduleEndTime"
                                                            label="Schedule End Date"
                                                            prepend-icon="mdi-calendar"
                                                            readonly
                                                            v-bind="attrs"
                                                            v-on="on"
                                                            clearable                       
                                                        ></v-text-field>
                                                    </template>
                                                    <v-date-picker
                                                        v-model="editedItem.quizscheduleEndTime"
                                                        @input="endTime = false"
                                                    ></v-date-picker>
                                                </v-menu>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >                     
                                                <v-select
                                                    v-model="templateOptionSelect"
                                                    label="Certificate Template"
                                                    :items="templateOptionItems"
                                                    item-text="title"
                                                    item-value="certificateTemplateId" 
                                                    v-on:change="chkTemplateOptions"                                                  
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
                                                <v-checkbox
                                                    v-model="editedItem.allowCorrectOption"
                                                    label="Correct answer display to candidates"
                                                >
                                                </v-checkbox>
                                            </v-col>                                                                                       
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                            >                     
                                                <v-select
                                                    v-model="participantOptionSelect"
                                                    label="Participant Option"
                                                    :items="participantOptionItems"
                                                    item-text="quizParticipantOptionName"
                                                    item-value="quizParticipantOptionId"
                                                    :rules="[rules.required]" 
                                                    v-on:change="chkParticipantOptions"                                                      
                                                    clearable
                                                    return-object                       
                                                ></v-select>                                         
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="4"
                                                v-if="chkParticipantsEmail==true"
                                            >
                                                <v-checkbox
                                                    v-model="emailInvitationToEmail"
                                                    label="Sent Invitation to Email"
                                                >
                                                </v-checkbox>
                                            </v-col>                           
                                        </v-row>
                                        <v-row>                          
                                            <v-col
                                                v-if="chkParticipantsEmail==true"
                                                cols="12"
                                                md="10"                                   
                                            >
                                                <v-textarea
                                                    v-model="participantsEmail"
                                                    label="Participants Email(Comma seperated)"
                                                    hint="sangibruse@gmail.com,shuvo4958@gmail.com,shuvo4958@hotmail.com"                                       
                                                    :rules="[rules.required]"
                                                    rows="3"
                                                    auto-grow
                                                    clearable
                                                ></v-textarea>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                md="2"
                                            >                                              
                                                <v-text-field
                                                    v-model="editedItem.quizPrice"
                                                    label="Price"
                                                    type="number"
                                                    :rules="[rules.passVal]"
                                                    :prefix="getCurrency"                                                                                         
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
                    <v-dialog v-model="dialogDelete" max-width="590px">
                        <v-card>
                            <v-card-title class="headline">Are you sure you want to delete this Assessment?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogStartQuiz" max-width="620px">
                        <v-card>
                            <v-card-title class="headline">Are you sure you want to send this Assessment live?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="startQuizConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogStopQuiz" max-width="570px">
                        <v-card>
                            <v-card-title class="headline">Are You sure to Stop this Assessment from live?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="stopQuizConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:[`item.publish`]="{item}">          
                <v-icon
                    small               
                    v-if="item.isRunning==false"
                    @click="startQuiz(item)"
                >
                    play_arrow
                </v-icon>
                <v-icon
                    small               
                    v-else-if="item.isRunning==true"
                    @click="stopQuiz(item)"
                >
                    block
                </v-icon>
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
            <template v-slot:[`item.allowMultipleInputByUser`]="{item}">
                <span>{{formatMultipleInput(item)}}</span> 
            </template>
            <template v-slot:[`item.allowMultipleAnswer`]="{item}">
                <span>{{formatMultipleAnswer(item)}}</span> 
            </template>
            <template v-slot:[`item.allowMultipleAttempt`]="{item}">
                <span>{{formatMultipleAttempt(item)}}</span> 
            </template>
            <template v-slot:[`item.quizscheduleStartTime`]="{item}">
                <span>{{formatStartTime(item)}}</span> 
            </template>
            <template v-slot:[`item.quizscheduleEndTime`]="{item}">
                <span>{{formatEndTime(item)}}</span> 
            </template>
            <template v-slot:[`item.quizTime`]="{item}">
                <span>{{formatQuizTime(item)}}</span> 
            </template>
            <template v-slot:[`item.quizTotalMarks`]="{item}">
                <span>{{formatQuizMarks(item)}}</span> 
            </template>
            <template v-slot:[`item.isRunning`]="{item}">
                <v-chip text-color="primary" outlined dark>{{formatRunning(item)}}</v-chip> 
            </template>
        </v-data-table>
    <!-- assessment table start -->
    </v-container>
</template>

<script>
import config from '../../config'
import Message from '../../components/common/Message'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'Topics',
    components:{
      Message
    },
    data(){
        return{
            allSettings:{},
            isEmailAllowed:false,
            isPaymentAllowed:false,
            emailInvitationToEmail:false,
            topicId:null,
            isParticipantsEmailVisible:false,
            isSetMarksVisible:false,
            isMarksVisible:false,
            isMultipleInputVisible:false,
            isMultipleAnswerVisible:false,
            startTime:false,
            endTime:false,
            participantsEmail:'',
            markOptionSelect: null,
            markOptionItems:[],
            participantOptionSelect: null,
            participantOptionItems:[],
            templateOptionSelect: null,
            templateOptionItems:[],
            quizStartDate:null,
            rules:{
                required:value=>!!value||'Required',
                minimumVal:value=>value>0,
                passVal:value=>value>=0
            },
            dialog:false,
            dialogDelete:false,
            dialogStartQuiz:false,
            dialogStopQuiz:false,
            headersTopics:[
                {text:'Title',value:'quizTitle'},
                {text:'Number of Questions',value:'questionsCount'},
                {text:'Time(min)',value:'quizTime'},
                {text:'Marks',value:'quizTotalMarks'},
                {text:'Multiple Input',value:'allowMultipleInputByUser'},
                {text:'Multiple Answer',value:'allowMultipleAnswer'},
                {text:'Multitle Attempt',value:'allowMultipleAttempt'},
                {text:'Schedule Start Date',value:'quizscheduleStartTime'},
                {text:'Schedule End Date',value:'quizscheduleEndTime'},
                {text:'Status',value:'isRunning'},
                {text:'Go Live', value: 'publish', sortable: false},
                {text:'Actions', value: 'actions', sortable: false},
            ],
            itemsTopics:[],
            editedIndex:-1,
            editedItem:{
                quizTopicId:null,
                quizTitle:'',
                quizTime:0,
                quizMarkOptionId:null,
                quizParticipantOptionId:null,
                certificateTemplateId:null,
                quizTotalMarks:0,
                quizPassMarks:0,
                allowMultipleInputByUser:false,
                allowMultipleAnswer:false,
                allowMultipleAttempt:false,
                allowCorrectOption:false,
                quizscheduleStartTime:null,
                quizscheduleEndTime:null,
                quizPrice:0                
            },
            defaultItem:{
                quizTopicId:null,
                quizTitle:'',
                quizTime:0,
                quizMarkOptionId:null,
                quizParticipantOptionId:null,
                certificateTemplateId:null,
                quizTotalMarks:0,
                quizPassMarks:0,
                allowMultipleInputByUser:false,
                allowMultipleAnswer:false,
                allowMultipleAttempt:false,
                allowCorrectOption:false,
                quizscheduleStartTime:null,
                quizscheduleEndTime:null,
                quizPrice:0              
            },
            excelColumnsTopics : [
                {label:'Title',field:'quizTitle'},
                {label:'Number of Questions',field:'questionsCount'},
                {label:'Time(min)',field:'quizTime'},
                {label:'Marks',field:'quizTotalMarks'},
                {label:'Multiple Input',field:'allowMultipleInputByUser'},
                {label:'Multiple Answer',field:'allowMultipleAnswer'},
                {label:'Multiple Attempt',field:'allowMultipleAttempt'},
                {label:'Schedule Start Time',field:'quizscheduleStartTime'},
                {label:'Schedule End Time',field:'quizscheduleEndTime'}         
           ],
        }
    },
    methods:{
        //download pdf
        generatePdf(){
            const doc = new jsPDF()
            doc.autoTable({
                margin: { top: 10,left:2,right:1 },
                body: this.itemsTopics,
                columns: [
                    {header:'Title',dataKey:'quizTitle'},
                    {header:'Number of Questions',dataKey:'questionsCount'},
                    {header:'Time(min)',dataKey:'quizTime'},
                    {header:'Marks',dataKey:'quizTotalMarks'},
                    {header:'Multiple Input',dataKey:'allowMultipleInputByUser'},
                    {header:'Multiple Answer',dataKey:'allowMultipleAnswer'},
                    {header:'Multiple Attempt',dataKey:'allowMultipleAttempt'},
                    {header:'Schedule Start Time',dataKey:'quizscheduleStartTime'},
                    {header:'Schedule End Time',dataKey:'quizscheduleEndTime'}                    
                ],
            })
            doc.save('assessment-table.pdf')
        },
        //check test participation option
        chkParticipantOptions(obj){           
            this.participantsEmail=''
            this.emailInvitationToEmail=false
            if(obj==null){
                this.isParticipantsEmailVisible=false
            }else if(obj.quizParticipantOptionId==2){
                this.isParticipantsEmailVisible=true
            }else{
                this.isParticipantsEmailVisible=false
            }
        },
        //check test marks set option
        chkMarkOptions(obj){
            this.editedItem.allowMultipleInputByUser=false
            this.editedItem.allowMultipleAnswer=false
            this.editedItem.quizTime=0
            this.editedItem.quizTotalMarks=0
            this.editedItem.quizPassMarks=0
            if(obj==null){
                this.isMarksVisible=false
                this.isMultipleInputVisible=false
                this.isMultipleAnswerVisible=false
            }
            else if(obj.quizMarkOptionId==1){
                this.isMarksVisible=true
                this.isSetMarksVisible=true
                this.isMultipleInputVisible=false
                this.isMultipleAnswerVisible=true
            }
            else if(obj.quizMarkOptionId==2){
                this.isMarksVisible=false
                this.isMultipleInputVisible=true
                this.isMultipleAnswerVisible=false
            }else if(obj.quizMarkOptionId==3){
                this.isMarksVisible=true
                this.isSetMarksVisible=false
                this.isMultipleInputVisible=false
                this.isMultipleAnswerVisible=true
            }
        },
        //check certificate template
        chkTemplateOptions(obj){
            if(obj==null){
                this.editedItem.certificateTemplateId=null
            }else{
                this.editedItem.certificateTemplateId=obj.certificateTemplateId
            }
        },
        //formatting multiple input text
        formatMultipleInput(item){
            return item.allowMultipleInputByUser==true?'yes':'no'
        },
        //formatting multiple answer text
        formatMultipleAnswer(item){
            return item.allowMultipleAnswer==true?'yes':'no'
        },
        //formatting multiple attempt text
        formatMultipleAttempt(item){
            return item.allowMultipleAttempt==true?'yes':'no'
        },
        //formatting start date
        formatStartTime(item){
            return item.quizscheduleStartTime==null?null:item.quizscheduleStartTime.substring(0,10)
        },
        //formatting end date
        formatEndTime(item){
            return item.quizscheduleEndTime==null?null:item.quizscheduleEndTime.substring(0,10)
        },
        //formatting test time 
        formatQuizTime(item){
            return item.quizMarkOptionId==1 || item.quizMarkOptionId==3?item.quizTime:'n/a'
        },  
        //formatting test mark 
        formatQuizMarks(item){
            return item.quizMarkOptionId==1 || item.quizMarkOptionId==3?item.quizTotalMarks:'n/a'
        },
        //check live test
        formatRunning(item){
            if(item.isRunning){
                if(item.quizscheduleStartTime!=null && item.quizscheduleEndTime==null){
                    if(new Date().toJSON().slice(0,10)>=new Date(item.quizscheduleStartTime).toJSON().slice(0,10)){
                        return 'On-live'
                    }else{
                        return ''
                    }
                }else if(item.quizscheduleStartTime!=null && item.quizscheduleEndTime!=null){
                    if(new Date().toJSON().slice(0,10)>=new Date(item.quizscheduleStartTime).toJSON().slice(0,10) && new Date().toJSON().slice(0,10)<=new Date(item.quizscheduleEndTime).toJSON().slice(0,10)){
                        return 'On-live'
                    }else{
                        return ''
                    }
                }else{
                    return 'On-live'
                }
            }else{
                return ''
            }
        },
        //fetch mark set option
        getMarkOptions(){
            this.$store.dispatch('quiz/fetchMarkOptions')
            .then((response)=>{
                this.markOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //fetch participation set option
        getParticipantOptions(){
            this.$store.dispatch('quiz/fetchParticipantOptions')
            .then((response)=>{
                this.participantOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //fetch certificate template set option
        getTemplates(){
            this.$store.dispatch('quiz/fetchTemplates')
            .then((response)=>{
                this.templateOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //get all tests data
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('quiz/fetchQuizTopics')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsTopics=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //edit initialize
        editItem(item){
            item.quizscheduleStartTime=item.quizscheduleStartTime==null?null:item.quizscheduleStartTime.substr(0,10)
            item.quizscheduleEndTime=item.quizscheduleEndTime==null?null:item.quizscheduleEndTime.substr(0,10)
            this.markOptionSelect={quizMarkOptionId:item.quizMarkOptionId}
            this.chkMarkOptions(this.markOptionSelect)
            this.participantOptionSelect={quizParticipantOptionId:item.quizParticipantOptionId}
            this.chkParticipantOptions(this.participantOptionSelect)

            this.templateOptionSelect={certificateTemplateId:item.certificateTemplateId}

            this.editedIndex=this.itemsTopics.indexOf(item)
            this.editedItem=Object.assign({},item)
            if(item.quizParticipantOptionId==2){
                this.$store.dispatch('quiz/fetchParticipantsEmail',item.quizTopicId)
                .then((response)=>{
                    this.participantsEmail=response.data
                })
                .catch((err)=>{
                    console.log(err)
                })
            }
            this.dialog=true
        },
        //test start dialog
        startQuiz(item){
            this.editedItem = Object.assign({}, item)
            this.dialogStartQuiz=true
        },
        //test start confirm
        startQuizConfirm(){
            this.quizStartDate=this.editedItem.quizscheduleStartTime
            this.$store.dispatch('quiz/startQuiz',this.editedItem)
            .then(response=>{
                if(response.status==200){                 
                    if(this.quizStartDate!=null){
                        this.$root.$emit('message_from_parent_long','This assessment will live based on schedule.')
                    }else{
                        this.$root.$emit('message_from_parent',response.data.responseMsg)
                    }
                    this.quizStartDate=null                  
                    this.$store.dispatch('quiz/fetchQuizTopics')
                    .then((response)=>{
                        this.itemsTopics=response.data                  
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
            this.closeDialog()
        },
        //test stop dialog
        stopQuiz(item){
            this.editedItem = Object.assign({}, item)
            this.dialogStopQuiz=true
        },
        //test stop confirm
        stopQuizConfirm(){
            this.$store.dispatch('quiz/stopQuiz',this.editedItem)
            .then(response=>{
                if(response.status==200){
                    this.$root.$emit('message_from_parent',response.data.responseMsg)
                    this.$store.dispatch('quiz/fetchQuizTopics')
                    .then((response)=>{
                        this.itemsTopics=response.data                  
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
            this.closeDialog()
        },
        //test delete dialog
        deleteItem(item){
            this.editedItem = Object.assign({}, item)
            this.dialogDelete=true
        },
        //test delete confirm
        deleteItemConfirm () {        
            this.$store.dispatch('quiz/deleteQuizTopic',this.editedItem.quizTopicId)
            .then(response=>{
                if(response.status==200){
                    this.$root.$emit('message_from_parent',response.data.responseMsg)
                    this.$store.dispatch('quiz/fetchQuizTopics')
                    .then((response)=>{
                        this.itemsTopics=response.data                  
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
            this.closeDialog()
        },
        //close dialog for save and edit
        close(){
            this.dialog=false
            this.$nextTick(()=>{   
                this.markOptionSelect=null
                this.participantOptionSelect=null 
                this.templateOptionSelect=null    
                this.participantsEmail=''     
                this.editedItem=Object.assign({},this.defaultItem)
                this.editedIndex=-1
            })
        },
        //close dialog for delete
        closeDialog () {
            this.dialogDelete = false
            this.dialogStartQuiz=false
            this.dialogStopQuiz=false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        //save comma seperated emails
        saveParticipantsEmail(){
            var inputEmails=this.participantsEmail.trim().split(',')
            var sendEmails=[]
            for(var i=0;i<inputEmails.length;i++){
                if(inputEmails[i].length>5){
                    sendEmails.push({'quizTopicId':this.topicId,'email':inputEmails[i].trim()})
                }           
            }
            this.$store.dispatch('quiz/InsertParticipantsEmail',sendEmails)
            .then(response=>{
                if(response.status==200){
                    if(this.emailInvitationToEmail==true){
                        var logoPath=config.hostname+(this.$store.getters['settings/logoPath']==null?'':this.$store.getters['settings/logoPath'].replace(/\\/g, '/'))
                        var siteUrl=window.location.origin
                        var registrationLink=siteUrl
                        var body=this.$store.getters['settings/siteTitle']+' Admin is invited you to attened an Test. If you not registered yet follow this link '+registrationLink+' to register with this email and attend the Test.'

                        var invitationEmails=[]
                        for(var i=0;i<inputEmails.length;i++){
                            if(inputEmails[i].length>5){
                                invitationEmails.push({'email':inputEmails[i].trim(),'logoPath':logoPath,'siteUrl':siteUrl,'body':body})
                            }           
                        }
                        this.$store.dispatch('quiz/InviteParticipantsByEmail',invitationEmails)
                        .then(response=>{
                            if(response.status==202){
                                this.$root.$emit('message_from_parent_long',response.data.responseMsg)                    
                            }
                        })
                        .catch(err=>{
                            console.log(err)
                        })
                    }
                }
            })
            .catch(err=>{
                console.log(err)
            })
        },
        //cheked app defalt email set ot not
        chkEmailAllowed(){
            if(this.allSettings.defaultEmail!='' && this.allSettings.defaultEmail!=null){
                this.isEmailAllowed=true
            }else{
                this.isEmailAllowed=false
            }
        },
        //payment settings check
        chkPaymentAllowed(){
            if(this.allSettings.currency!=null && this.allSettings.currencySymbol!=null && this.allSettings.stripeSecretKey!=null && this.allSettings.stripePubKey!=null){
                this.isPaymentAllowed=true
            }else{
                this.isPaymentAllowed=false
            }
        },
        //create and update
        save (){
            if(this.$refs.form.validate()){
                const objTopic={
                    quizTitle:this.editedItem.quizTitle.trim(),
                    quizTime:this.editedItem.quizTime,
                    quizTotalMarks:this.editedItem.quizTotalMarks,
                    quizPassMarks:(this.editedItem.quizPassMarks=='' || this.editedItem.quizPassMarks==null)?0:this.editedItem.quizPassMarks,
                    quizMarkOptionId:this.markOptionSelect.quizMarkOptionId,
                    quizParticipantOptionId:this.participantOptionSelect.quizParticipantOptionId,
                    certificateTemplateId:this.editedItem.certificateTemplateId,
                    allowMultipleInputByUser:this.editedItem.allowMultipleInputByUser,
                    allowMultipleAnswer:this.editedItem.allowMultipleAnswer,
                    allowMultipleAttempt:this.editedItem.allowMultipleAttempt,
                    allowCorrectOption:this.editedItem.allowCorrectOption,
                    quizscheduleStartTime:this.editedItem.quizscheduleStartTime,
                    quizscheduleEndTime:this.editedItem.quizscheduleEndTime,
                    quizPrice:this.editedItem.quizPrice=='' || this.editedItem.quizPrice==null?0:this.editedItem.quizPrice,
                    addedBy:parseInt(localStorage.getItem('loggedUserId')),
                }
                if(objTopic.quizPrice>0 && this.isPaymentAllowed==false){
                    this.$root.$emit('message_from_parent_long','Your payment settings not done yet! Please set those then impose price here.')
                }else if(this.editedItem.quizscheduleStartTime==null && this.editedItem.quizscheduleEndTime!=null){
                    this.$root.$emit('message_from_parent_long','No Schedule start date found! please put one.')
                }else if(this.editedItem.quizscheduleStartTime!=null && this.editedItem.quizscheduleEndTime!=null&&new Date(this.editedItem.quizscheduleStartTime)>new Date(this.editedItem.quizscheduleEndTime)){
                    this.$root.$emit('message_from_parent_long','Schedule end date should be greater than or equal to Schedule start date!')
                }else if(objTopic.quizParticipantOptionId==2 && this.participantsEmail.search(',')==-1){
                    this.$root.$emit('message_from_parent_long','No comma seperated emails found! Add some comma seperated emails to Participants Email')
                }else{                   
                    if (this.editedIndex > -1){
                        objTopic.quizTopicId=this.editedItem.quizTopicId
                        objTopic.lastUpdatedBy=parseInt(localStorage.getItem('loggedUserId'))
                        
                        this.$store.dispatch('quiz/updateQuizTopic',objTopic)
                        .then(response=>{
                        if(response.status==200){
                            this.topicId=this.editedItem.quizTopicId
                            this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                            this.$store.dispatch('quiz/fetchQuizTopics')
                            .then((response)=>{                              
                                this.close()
                                this.itemsTopics=response.data
                                if(objTopic.quizParticipantOptionId==2){
                                    this.saveParticipantsEmail()
                                }
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
                        this.$store.dispatch('quiz/createQuizTopic',objTopic)
                        .then(response=>{
                        if(response.status==200){
                            this.topicId=response.data.quizTopicId
                            this.$root.$emit('message_from_parent','Successfully saved')
                            this.$store.dispatch('quiz/fetchQuizTopics')
                            .then((response)=>{                              
                                this.close()
                                this.itemsTopics=response.data
                                if(objTopic.quizParticipantOptionId==2){
                                    this.saveParticipantsEmail()
                                }
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
    },
    computed:{
        formTitle(){
            return this.editedIndex===-1?'New Assessment':'Edit Assessment'
        },
        chkMarksVisibility(){
            return this.isMarksVisible
        },
        chkMultipleInputVisibility(){
            return this.isMultipleInputVisible
        },        
        chkMultipleAnswerVisibility(){
            return this.isMultipleAnswerVisible
        },
        chkParticipantsEmail(){
            return this.isParticipantsEmailVisible
        },
        chkQuestionWiseSetVisibility(){
            return this.isSetMarksVisible
        },
        getCurrency(){
            return this.$store.getters['settings/currencySymbol']
        }
    },
    watch:{
        dialog (val) {         
            val || this.close()
        },
        dialogDelete (val) {
            val || this.closeDialog()
        },
        dialogStartQuiz(val){
            val || this.closeDialog()
        },
        dialogStopQuiz(val){
            val || this.closeDialog()
        },
    },
    created(){
        this.allSettings=this.$store.getters['settings/allSettings']
        this.initialize()
        this.getMarkOptions()
        this.getParticipantOptions()
        this.getTemplates()
        this.chkEmailAllowed()
        this.chkPaymentAllowed()
    }
}
</script>