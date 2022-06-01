<template>
<!-- report for candidate  -->
    <v-container class="px-0">
        <!-- filter area start -->
        <v-row justify="center">
            <v-col cols="12" sm="8" md="6">
                <v-select
                    label="Select one to filter"
                    :items="quizes"
                    item-text="quizTitle"
                    item-value="quizTopicId"
                    v-on:change="filterResult"
                    prepend-icon="emoji_objects"
                    menu-props="auto"
                    return-object
                    clearable
                >
                </v-select>
            </v-col>
        </v-row>
        <v-row justify="end">           
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
        <!-- filter area end -->
        
        <!-- report table with result sheet start -->
        <v-data-table
            :headers="headersResults"
            :items="itemsResults"
            :search="search"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-dialog
                    v-model="dialog"
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
                    @click="getDetailResult(item)"
                >
                    description
                </v-icon>
                <v-icon
                    small
                    v-if="chkCertificateVisibility(item)"
                    @click="getCertificate(item)"
                >
                    card_giftcard
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
        <!-- report table with result sheet end -->
    </v-container>
</template>

<script>
export default {
    name:'Students',
    data(){
        return{
            search: '',
            dialog:false,
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
            itemsSingleResult:[],
            headersFiltered:[
                {text:'Question',value:'questionDetail'},
                {text:'Your Answer',value:'userAnswer'},
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
            marksObtained:''                   
        }
    },
    methods:{    
        //get filtered tests by test id 
        getQuizesFiltered(id){
            this.$store.dispatch('report/fetchQuizesFiltered',id)
            .then((response)=>{
                this.quizes=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //get filteded test result
        quizResultsFiltered(id){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('report/fetchResultsFiltered',id)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsResults=response.data
                this.filteredItemsResults=this.itemsResults
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //filter by test id
        filterResult(obj){    
            if(obj==null){
                this.itemsResults=this.filteredItemsResults
            }else{
                this.itemsResults=this.filteredItemsResults.filter(function(item){
                    return item.quizTopicId==obj.quizTopicId
                })
            }
        },
        //single test result for result sheet
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
        getDetailResult(item){
            this.dialog=true
            this.quizResult(item.quizResponseInitialId)           
        },
        //check certificate visibility for a test result
        chkCertificateVisibility(item){
            if(this.userInfo.roleName=='Admin'){
                return false
            }
            else if(item.certificateTemplateId!=null && item.quizPassMarks==0){
                return true
            }else if(item.certificateTemplateId!=null && item.quizPassMarks!=0 && item.userObtainedQuizMark>=item.quizPassMarks){
                return true
            }else{
                return false
            }
        },
        //get certificate for a test result
        getCertificate(item){
            this.$store.dispatch('report/storeCertificateInfo',item)
            this.$router.push({name:'Template'})
        },

        //format portion start
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
        //format portion end
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
        }
    },
    created(){
        this.userInfo=this.$store.getters['dashboard/userInfo']
        if(this.userInfo.roleName=='Student'){
            this.quizResultsFiltered(this.userInfo.userId)
            this.getQuizesFiltered(this.userInfo.userId)
        }
    }
}
</script>