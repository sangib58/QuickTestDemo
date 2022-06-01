<template>
<!-- report for admin  -->
    <v-container class="px-0">
        <!-- filter area start -->
        <v-row justify="center">
            <v-col cols="12" sm="6" md="4">
                <v-select
                    v-model="selectedReportType"
                    label="Select one to filter"
                    :items="typeOfReports"
                    item-text="reportTypeName"
                    item-value="reportTypeId"
                    v-on:change="filterResult"                   
                    prepend-icon="filter_alt"
                    menu-props="auto"
                    return-object
                    clearable
                >
                </v-select>
            </v-col>
            <v-col cols="12" sm="6" md="4">
                <v-select
                    v-model="selectedQuiz"
                    label="Select one to filter"
                    :items="quizes"
                    item-text="quizTitle"
                    item-value="quizTopicId"
                    v-on:change="filterResult"
                    prepend-icon="filter_alt"
                    menu-props="auto"
                    return-object
                    clearable
                >
                </v-select>
            </v-col>
        </v-row>
        <!-- filter area end -->

        <!-- email to selected candidates start -->
        <v-dialog
            v-model="dialogEmail"
            max-width="700"
            disabled
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
                    @click="chkQuizSelected()"
                >
                    Sent Email
                </v-btn>
            </template>
            <v-card>
                <v-card-title><v-row justify="center">Sent email to checked students</v-row></v-card-title>
                <v-card-text>
                    <v-form ref="form">                       
                        <v-row>
                            <v-col
                                cols="12"
                            >                     
                                <v-text-field
                                    v-model="emailSubject"                                       
                                    label="Subject"                                     
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
                                    v-model="emailBody"                                       
                                    label="Body"                                      
                                    :rules="[rules.required]"
                                    rows="5"
                                    auto-grow
                                    clearable                                                             
                                ></v-textarea>                                    
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary"
                        dark
                        @click="sentEmail"
                        class="mr-2"                      
                    >
                    Sent Email
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- email to selected candidates start -->

        <!-- report table with export start -->
        <v-row class="pt-4">
            <v-col cols="12" sm="8" md="8">
                <v-btn @click="generatePdf" small outlined>PDF</v-btn>
                <vue-excel-xlsx class="btnExcel" :data="itemsResults" :columns="excelColumnsResults" :filename="'quiz-result-table'" :sheetname="'quizes'">EXCEL</vue-excel-xlsx>
                <v-btn small outlined><download-csv :data="itemsResults" name= "quiz-result-table.csv">CSV</download-csv></v-btn>
            </v-col>           
            <v-col cols="12" sm="4" md="4">
                <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-col>
        </v-row>
        
        <v-data-table
            v-model="selectedEmails"
            :headers="headersResults"
            :items="itemsResults"
            :search="search"
            :single-select="singleSelect"
            item-key="dateAdded"
            show-select
            class="elevation-1"
        >
            <template v-slot:top>
                <v-dialog
                    v-model="dialogExamine"
                    max-width="1000"
                >
                    <v-card>
                        <v-card-text>
                            <v-container class="px-0"> 
                                <v-row justify="center">
                                    <v-col cols="6" sm="3" class="font-weight-black"><h3>Pending Examine</h3></v-col>
                                </v-row>                                  
                                <v-row>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Name</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{participantNameExamine}}</span>                                          
                                    </v-col>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Email</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{participantEmailExamine}}</span>
                                    </v-col>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Title</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{quizNameExamine}}</span>
                                    </v-col>
                                </v-row>                                                                  
                                <v-row>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Time Taken</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{quizAssignTimeExamine}}</span>
                                    </v-col>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Marks</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{quizAssignMarksExamine}}</span>
                                    </v-col>                                  
                                </v-row>                               
                                <v-simple-table
                                    fixed-header
                                >
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Answer Input</th>
                                            <th>Question Marks</th>
                                            <th>Marks Obtain</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="answerSubmit">
                                        <tr v-for="item in itemsSingleExamine" :key="item.quizResponseDetailId">
                                            <td>{{item.questionDetail}}</td>
                                            <td>{{item.userAnswer}}</td>
                                            <td v-bind:id="'q-'+item.quizResponseDetailId">{{item.questionMark}}</td>
                                            <td><v-text-field v-bind:id="'m-'+item.quizResponseDetailId" type="number" :rules="[rules.minimumMark]"></v-text-field></td>
                                            <td><v-btn color="primary" @click="submitAnswer(item.quizResponseDetailId)" small>save</v-btn></td>
                                        </tr>
                                    </tbody>
                                </v-simple-table>                            
                            </v-container>                           
                        </v-card-text>
                    </v-card>
                </v-dialog>
                <v-dialog
                    v-model="dialogReport"
                    max-width="1000"
                >
                    <v-card>
                        <v-card-text>
                            <v-container class="px-0"> 
                                <v-row justify="center">
                                    <v-col cols="6" sm="2" class="font-weight-black"><h3>Result Sheet</h3></v-col>
                                </v-row>                                  
                                <v-row>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Name</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{participantName}}</span>                                          
                                    </v-col>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Email</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{participantEmail}}</span>
                                    </v-col>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Title</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{quizName}}</span>
                                    </v-col>
                                </v-row>                                                                  
                                <v-row>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Time Taken</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{quizAssignTime}}</span>
                                    </v-col>
                                    <v-col cols="12" sm="1">
                                        <span class="font-weight-medium">Marks</span>
                                    </v-col>
                                    <v-col cols="12" sm="3">
                                        <span>{{quizAssignMarks}}</span>
                                    </v-col>
                                    <v-col cols="12" sm="2">
                                        <span class="font-weight-medium">Obtained</span>
                                    </v-col>
                                    <v-col cols="12" sm="2">
                                        <span>{{userObtainedMarks}}</span>
                                    </v-col>
                                </v-row>
                                <v-data-table
                                    :headers="headersSingleResult"
                                    :items="itemsSingleResult"
                                    disable-pagination
                                    hide-default-footer
                                >
                                    <template v-slot:[`item.isAnswerSkipped`]="{item}">
                                        <span>{{formatAnswerSkipped(item)}}</span> 
                                    </template>
                                    <template v-slot:[`item.questionMark`]="{item}">
                                        <span>{{formatQuestionMarks(item)}}</span> 
                                    </template>
                                    <template v-slot:[`item.userObtainedQuestionMark`]="{item}">
                                        <span>{{formatUserMarks(item)}}</span> 
                                    </template>
                                    <template v-slot:[`item.correctAnswer`]="{item}">
                                        <span>{{formatCorrectAnswer(item)}}</span> 
                                    </template>
                                </v-data-table>
                            </v-container>                           
                        </v-card-text>
                    </v-card>
                </v-dialog>
            </template>
            <template v-slot:[`item.actions`]="{item}">
                <v-icon
                    class="mr-2"
                    small
                    v-if="chkReportVisibility(item)"
                    @click="getDetailResult(item)"
                >
                    description
                </v-icon>
                <v-icon
                    class="mr-2"
                    small   
                    v-if="chkReviewVisibility(item)"
                    @click="getExamineData(item)"                
                >
                    remove_red_eye
                </v-icon>              
            </template>
            <template v-slot:[`item.quizMark`]="{item}">
                <span>{{formatQuizMarks(item)}}</span> 
            </template>
            <template v-slot:[`item.userObtainedQuizMark`]="{item}">
                <span>{{formatObtainedMarks(item)}}</span> 
            </template>
            <template v-slot:[`item.isExamined`]="{item}">
                <span>{{formatStatus(item)}}</span> 
            </template>
            <template v-slot:[`item.quizTime`]="{item}">
                <span>{{formatQuizTime(item)}}</span> 
            </template>
            <template v-slot:[`item.dateAdded`]="{item}">
                <span>{{formatDateTime(item)}}</span> 
            </template>
        </v-data-table>
        <!-- report table with export end -->
    </v-container>
</template>

<script>
import jsPDF from 'jspdf'
import 'jspdf-autotable'
import config from '../../config'

export default {
    name:'Admin',
    data(){
        return{
            allSettings:{},
            isEmailAllowed:false,
            dialogEmail:false,
            selectedEmails:[],
            singleSelect: false,
            rules:{
                required:value=>!!value||'Required',
                minimumVal:value=>value>0,
                minimumMark:value=>value>=0
            },
            emailSubject:'',
            emailBody:'',

            selectedReportType:null,
            selectedQuiz:null,
            typeOfReports:[],
            search: '',
            dialogReport:false,
            dialogExamine:false,
            userInfo:{},
            quizes:[],
            itemsResults:[],
            filteredItemsResults:[],
            headersResults:[
                {text:'Name',value:'fullName'},
                {text:'Email',value:'email'},
                {text:'Title',value:'quizTitle'},
                {text:'Time(min)',value:'quizTime'},
                {text:'Time taken(min)',value:'timeTaken'},
                {text:'Marks',value:'quizMark'},
                {text:'Marks Obtained',value:'userObtainedQuizMark'},
                {text:'Status',value:'isExamined'},               
                {text:'Attempt Number',value:'attemptCount'},
                {text:'Attempt Date-Time',value:'dateAdded'},
                {text:'Actions', value:'actions', sortable: false },
            ],            
            itemsSingleExamine:[],
            itemsSingleResult:[],
            headersFiltered:[
                {text:'Question',value:'questionDetail'},
                {text:'Answer Input',value:'userAnswer'},
                {text:'Correct Answer',value:'correctAnswer'},
                {text:'Skipped?',value:'isAnswerSkipped'},
                {text:'Explanation',value:'answerExplanation'},
                {text:'Question Marks',value:'questionMark'},
                {text:'Your Marks',value:'userObtainedQuestionMark'},
            ],
            allowCorrectOption:null,
            name:'',
            email:'',
            quizTitle:'',
            timeTaken:'',
            quizMarks:'',
            marksObtained:'',
            nameExamine:'',
            emailExamine:'',
            quizTitleExamine:'',
            timeTakenExamine:'',
            quizMarksExamine:'',
            excelColumnsResults : [
                {label:'Name',field:'fullName'},
                {label:'Email',field:'email'},
                {label:'Title',field:'quizTitle'},
                {label:'Time(min)',field:'quizTime'},
                {label:'Time taken(min)',field:'timeTaken'},
                {label:'Marks',field:'quizMark'},
                {label:'Marks Obtained',field:'userObtainedQuizMark'},               
                {label:'Attempt Number',field:'attemptCount'},  
                {label:'Date-Time',field:'dateAdded'},      
            ]          
        }
    },
    methods:{  
        //export data pdf   
        generatePdf(){
            const doc = new jsPDF()
            doc.autoTable({
                margin: { top: 10,left:2,right:1 },
                body: this.itemsResults,
                columns: [
                    {header:'Name',dataKey:'fullName'},
                    {header:'Email',dataKey:'email'},
                    {header:'Title',dataKey:'quizTitle'},
                    {header:'Time(min)',dataKey:'quizTime'},
                    {header:'Taken(min)',dataKey:'timeTaken'},
                    {header:'Mark',dataKey:'quizMark'},
                    {header:'Obtain',dataKey:'userObtainedQuizMark'},               
                    {header:'Attempt Number',dataKey:'attemptCount'},
                    {header:'Date-Time',dataKey:'dateAdded'},
                ],
            })
            doc.save('quiz-results-table.pdf')
        },
        //check selected candidate to sent email
        chkQuizSelected(){
            if(this.selectedEmails.length==0){
                this.dialogEmail=false
                this.$root.$emit('message_from_parent_long','No checked candidates found! First check candidates from list then sent email to them')
            }else{
                this.dialogEmail=true
            }         
        },
        //main sent function
        sentEmail(){
            if(this.$refs.form.validate()){
                var logoPath=config.hostname+(this.$store.getters['settings/logoPath']==null?'':this.$store.getters['settings/logoPath'].replace(/\\/g, '/'))
                var siteUrl=window.location.origin
                var body=this.emailBody
                var subject=this.emailSubject

                var inputEmails=this.selectedEmails.map(e=>e.email)
                var formatEmails=[]
                for(var i=0;i<inputEmails.length;i++){
                    if(inputEmails[i].length>3){
                        formatEmails.push({'email':inputEmails[i],'logoPath':logoPath,'siteUrl':siteUrl,'subject':subject,'body':body})
                    }           
                }
                this.$store.dispatch('report/SentCheckedEmailToNotifyStudents',formatEmails)
                .then(response=>{
                    if(response.status==202){
                        this.$root.$emit('message_from_parent_long',response.data.responseMsg)                    
                    }else if(response.status==200){
                        this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                        this.dialogEmail=false
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
            }
        },
        //put a mark for descriptive question
        submitAnswer(id){
            var questionMark=document.getElementById('q-'+id).innerHTML          
            var inputMark=document.getElementById('m-'+id).value
            if(inputMark=='' || inputMark<0){
                this.$root.$emit('message_from_parent_long','Marks Obtain can not be empty and less then 0!')
            }else if(inputMark>questionMark){
                this.$root.$emit('message_from_parent_long','Marks Obtain can not greater than Question Marks!')
            }else{
                const obj={
                    id:id,
                    marks:document.getElementById('m-'+id).value
                }
                this.$store.dispatch('report/saveMarksObtain',obj)
                .then(response=>{
                    if(response.status==200){
                        this.quizExamine(parseInt(response.data.responseMsg))
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
            }
        },
        //get report types
        reportTypes(){
            this.$store.dispatch('report/fetchReportTypes')
            .then((response)=>{
                this.typeOfReports=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //fetch all quizzes
        getQuizes(){
            this.$store.dispatch('report/fetchQuizes')
            .then((response)=>{
                this.quizes=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //fetch all tests result
        quizResults(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('report/fetchResults')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsResults=response.data
                this.filteredItemsResults=this.itemsResults
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //filter results from all tests
        filterResult(){  
            var isExamined=null, quizTopicId=null 
            if(this.selectedReportType!=null){
                if(this.selectedReportType.reportTypeId==1){
                    isExamined=false
                }else if(this.selectedReportType.reportTypeId==2){
                    isExamined=true
                }
            }else{
                isExamined=null
            }
            
            if(this.selectedQuiz!=null){
                quizTopicId=this.selectedQuiz.quizTopicId
            }else{
                quizTopicId=null
            }

            if(isExamined==null && quizTopicId==null){
                this.itemsResults=this.filteredItemsResults
            }else if(isExamined!=null && quizTopicId==null){
                this.itemsResults=this.filteredItemsResults.filter(function(item){
                    return item.isExamined==isExamined
                })
            }else if(isExamined==null && quizTopicId!=null){
                this.itemsResults=this.filteredItemsResults.filter(function(item){
                    return item.quizTopicId==quizTopicId
                })
            }else if(isExamined!=null && quizTopicId!=null){
                this.itemsResults=this.filteredItemsResults.filter(function(item){
                    return item.quizTopicId==quizTopicId && item.isExamined==isExamined
                })
            }         
        },   
        //get test result by test id   
        quizResult(id){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('dashboard/fetchFinishedExamResult',id)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsSingleResult=response.data

                this.allowCorrectOption=this.itemsSingleResult[0].allowCorrectOption
                this.name=this.itemsSingleResult[0].fullName
                this.email=this.itemsSingleResult[0].email
                this.quizTitle=this.itemsSingleResult[0].quizTitle
                this.timeTaken=this.itemsSingleResult[0].timeTaken
                this.quizMarks=this.itemsSingleResult[0].quizMark==0?'n/a':this.itemsSingleResult[0].quizMark
                this.marksObtained=this.itemsSingleResult[0].quizMark==0?'n/a':(this.itemsSingleResult[0].userObtainedQuizMark>this.itemsSingleResult[0].quizMark?this.itemsSingleResult[0].quizMark:this.itemsSingleResult[0].userObtainedQuizMark)
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //examine a test
        quizExamine(id){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('dashboard/fetchPendingExamine',id)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsSingleExamine=response.data
                if(response.data.length>0){
                    this.nameExamine=this.itemsSingleExamine[0].fullName
                    this.emailExamine=this.itemsSingleExamine[0].email
                    this.quizTitleExamine=this.itemsSingleExamine[0].quizTitle
                    this.timeTakenExamine=this.itemsSingleExamine[0].timeTaken
                    this.quizMarksExamine=this.itemsSingleExamine[0].quizMark==0?'n/a':this.itemsSingleExamine[0].quizMark
                }else if(response.data.length==0 && this.dialogExamine==true){
                    this.quizResults()
                    this.dialogExamine=false
                }
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        getDetailResult(item){
            this.dialogReport=true
            this.quizResult(item.quizResponseInitialId)           
        },
        getExamineData(item){
            this.dialogExamine=true
            this.quizExamine(item.quizResponseInitialId)
        },
        //check email settings
        chkEmailAllowed(){
            this.$store.dispatch('settings/checkEmailSettings')
            .then(response=>{
                this.isEmailAllowed=response.data
            })
            .catch(err=>{
                console.log(err)
            })
        },
        //check report visibility for already examined test
        chkReportVisibility(item){
            return item.isExamined==true?true:false
        },
        chkReviewVisibility(item){
            return item.isExamined==true?false:true
        },
        //formatted portion start
        formatQuizTime(item){
            return item.quizTime==0?'n/a':item.quizTime
        },
        formatQuizMarks(item){
            return item.quizMark==0?'n/a':item.quizMark
        },
        formatObtainedMarks(item){
            return item.isExamined==false?'':(item.quizMark==0?'n/a':item.userObtainedQuizMark)
        },
        formatStatus(item){           
            return item.isExamined==true && item.quizMark>0 && (item.userObtainedQuizMark>=item.quizPassMarks)?'Passed':(item.quizMark==0?'n/a':(item.isExamined==false?'Pending':'Failed'))
        },
        formatAnswerSkipped(item){
            return item.isAnswerSkipped==true?'yes':'no'
        },
        formatQuestionMarks(item){
            return item.questionMark==0?'n/a':item.questionMark
        },
        formatUserMarks(item){
            return item.questionMark==0?'n/a':item.userObtainedQuestionMark
        },
        formatDateTime(item){
            return item.dateAdded.replace('T',' ').substring(0,19)
        },
        formatCorrectAnswer(item){
            return item.correctAnswer!='' && item.correctAnswer!=null?item.correctAnswer.replace(/#####/g,','):''
        },
        //formatted portion end
    },
    computed:{
        participantName:function(){
            return this.name
        },
        participantEmail:function(){
            return this.email
        },
        quizName:function(){
            return this.quizTitle
        },
        quizAssignTime:function(){
            return this.timeTaken
        },
        quizAssignMarks:function(){
            return this.quizMarks
        },
        userObtainedMarks:function(){
            return this.marksObtained
        },
        headersSingleResult:function(){
            return this.userInfo.roleName=='Admin'?this.headersFiltered:
            (this.userInfo.roleName=='Student' && this.allowCorrectOption==true?this.headersFiltered:this.headersFiltered.filter(function(item){
                return item.text!='Correct Answer'
            }))
        },
        participantNameExamine:function(){
            return this.nameExamine
        },
        participantEmailExamine:function(){
            return this.emailExamine
        },
        quizNameExamine:function(){
            return this.quizTitleExamine
        },
        quizAssignTimeExamine:function(){
            return this.timeTakenExamine
        },
        quizAssignMarksExamine:function(){
            return this.quizMarksExamine
        },
    },
    created(){
        this.userInfo=this.$store.getters['dashboard/userInfo']
        this.allSettings=this.$store.getters['settings/allSettings']
        this.reportTypes()
        this.getQuizes()
        this.quizResults()   
        this.chkEmailAllowed()    
    }
}
</script>