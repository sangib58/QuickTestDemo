<template>
<!-- result after attend a test view -->
    <v-container class="px-0">
        <!-- examined pending start -->
        <div v-if="showResult==false">
            <v-row justify="center">
               <h3>This Assessment contains descriptive question. You will find this 
                result on Reports after reviewing by Admin.</h3>
            </v-row>
        </div> 
        <!-- examined pending end -->  

        <!-- result sheet start -->    
        <div v-else ref="quizResult">
            <v-row v-if="downloadVisible==true" justify="end" class="pr-3">
                <v-icon medium @click="generateResultPdf">picture_as_pdf</v-icon>
            </v-row>
            <v-row justify="center">
                <v-col cols="4" sm="3" md="2" class="font-weight-black"><h3>Result Sheet</h3></v-col>
            </v-row>
            <v-row class="pl-4">
                <v-col cols="12" sm="6" md="1">
                    <span class="font-weight-medium">Student</span>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <span>{{this.name}}</span>
                </v-col>
                <v-col cols="12" sm="6" md="1">
                    <span class="font-weight-medium">Email</span>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <span>{{this.email}}</span>
                </v-col>
                <v-col cols="12" sm="6" md="1">
                    <span class="font-weight-medium">Title</span>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <span>{{this.quizTitle}}</span>
                </v-col>
            </v-row>
            <v-row class="pl-4">
                <v-col cols="12" sm="6" md="1">
                    <span class="font-weight-medium">Time Taken</span>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <span>{{this.timeTaken}}</span>
                </v-col>
                <v-col cols="12" sm="6" md="1">
                    <span class="font-weight-medium">Marks</span>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <span>{{this.quizMarks}}</span>
                </v-col>
                <v-col cols="12" sm="6" md="1">
                    <span class="font-weight-medium">Obtained</span>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <span>{{this.marksObtained}}</span>
                </v-col>
            </v-row>
            <v-data-table
                :headers="headersResult"
                :items="itemsResult"
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
        </div>  
    <!-- result sheet start -->     
    </v-container>
</template>

<script>
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'

export default {
    name:'quizResult',
    data(){
        return{
            headers:[
                {text:'Question',value:'questionDetail'},
                {text:'Your Answer',value:'userAnswer'},
                {text:'Correct Answer',value:'correctAnswer'},
                {text:'Skipped?',value:'isAnswerSkipped'},
                {text:'Explanation',value:'answerExplanation'},
                {text:'Question Marks',value:'questionMark'},
                {text:'Your Marks',value:'userObtainedQuestionMark'},
            ],
            name:'',
            email:'',
            quizTitle:'',
            timeTaken:'',
            quizMarks:'',
            marksObtained:'',
            itemsResult:[],
            allowCorrectOption:null,
            isShownResult:false
        }
    },
    methods:{
        //get pdf
        generateResultPdf(){
            var pdfRef = this.$refs.quizResult;
            html2canvas(pdfRef).then(canvas => {
                var imgData = canvas.toDataURL('image/png');
                var doc = new jsPDF({
                    orientation:'p'
                });
                var imgWidth = 210; 
                var pageHeight = 295;  
                var imgHeight = canvas.height * imgWidth / canvas.width;
                var heightLeft = imgHeight;
                var position = 10;

                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position += heightLeft - imgHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                doc.save('Result.pdf');
            })
        },
        //result data
        quizResult(id){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('dashboard/fetchFinishedExamInfo',id)
            .then((response)=>{
                if(response.status==200){
                    if(response.data.isExamined){
                        this.isShownResult=true
                        this.$store.dispatch('dashboard/fetchFinishedExamResult',id)
                        .then((response)=>{
                            this.itemsResult=response.data

                            this.name=this.itemsResult[0].fullName
                            this.email=this.itemsResult[0].email
                            this.quizTitle=this.itemsResult[0].quizTitle
                            this.timeTaken=this.itemsResult[0].timeTaken
                            this.quizMarks=this.itemsResult[0].quizMark==0?'n/a':this.itemsResult[0].quizMark
                            this.marksObtained=this.itemsResult[0].quizMark==0?'n/a':(this.itemsResult[0].userObtainedQuizMark>this.itemsResult[0].quizMark?this.itemsResult[0].quizMark:this.itemsResult[0].userObtainedQuizMark)
                        })
                        .catch((err)=>{
                            console.log(err)
                        })
                    }else{
                        this.isShownResult=false
                    }                  
                }
                this.$store.dispatch('dashboard/cancelLoading')
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //answer skipped text format
        formatAnswerSkipped(item){
            return item.isAnswerSkipped==true?'yes':'no'
        },
        //question mark formatted
        formatQuestionMarks(item){
            return item.questionMark==0?'n/a':item.questionMark
        },
        //user obtained per question mark formatted
        formatUserMarks(item){
            return item.questionMark==0?'n/a':item.userObtainedQuestionMark
        },
        //correct answer formatted
        formatCorrectAnswer(item){
            return (item.correctAnswer!='' && item.correctAnswer!=null)?item.correctAnswer.replace(/#####/g,','):''
        },
    },
    computed:{
        headersResult(){
            return this.allowCorrectOption==true?this.headers:this.headers.filter(function(item){
                return item.text!='Correct Answer'
            })
        },
        downloadVisible(){
            return window.innerWidth>=960?true:false
        },
        showResult(){
            return this.isShownResult
        }
    },
    mounted(){
        if(this.$store.getters['dashboard/resultFlag']==false){
            this.$router.push({name:'Dashboard'})
        }
    },
    //execute before exit this page
    beforeDestroy(){
        this.$store.dispatch('dashboard/saveResultFlag',false)
    },
    created(){
        this.$store.dispatch('dashboard/changeVisibility')
        this.allowCorrectOption=this.$store.getters['dashboard/quiz'].allowCorrectOption
        this.quizResult(this.$store.getters['dashboard/responseInitial'].quizResponseInitialId)      
    }
}
</script>