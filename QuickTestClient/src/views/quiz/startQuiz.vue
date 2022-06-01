<template>
    <v-container pa-0>
        <!-- app logo  -->
        <v-row justify="center" class="pb-3"><v-img :src="logoImg" max-height="50" max-width="100" contain></v-img></v-row>
        <!-- test header start -->
        <v-row class="black" justify="center">
            <v-col cols="8" class="white--text"><h2>{{quiz.quizTitle}}</h2></v-col>
            <v-spacer></v-spacer>
            <v-col cols="2" class="white--text"><span id="displayTimeSecond"></span></v-col>
            <v-spacer></v-spacer>
            <v-col cols="2"><v-icon class="white--text" @click="quizEnd()">highlight_off</v-icon></v-col>
            <v-spacer></v-spacer>        
        </v-row>
        <!-- test header end -->

        <!-- test area start -->
        <v-row justify="center">
            <v-col cols="12" md="6">
                <v-form ref="form">
                    <v-card>
                        <v-card-text>
                            <v-row>
                                <v-col cols="9">Question {{this.serialToShow}} of {{this.quiz.questionsCount}}</v-col>
                                <v-col cols="3" v-if="this.eachQuestionMark>0">Marks: {{this.eachQuestionMark}}</v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="9">Category: {{question.questionCategoryName}}</v-col>
                                <v-col cols="3">Lavel: {{question.questionLavelName}}</v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-text class="black--text">
                            <h3 v-if="question.isCodeSnippet==true"><pre>{{question.questionDetail}}</pre></h3>
                            <h3 v-else>{{question.questionDetail}}</h3>
                        </v-card-text>
                        
                        <v-list v-if="question.questionTypeId==1">
                            <v-list-item>
                                <v-list-item-action><v-checkbox v-model="responseA"></v-checkbox></v-list-item-action>
                                <v-list-item-content>{{question.optionA}}</v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-action><v-checkbox v-model="responseB"></v-checkbox></v-list-item-action>
                                <v-list-item-content>{{question.optionB}}</v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="question.optionC!='' && question.optionC!=null">
                                <v-list-item-action><v-checkbox v-model="responseC"></v-checkbox></v-list-item-action>
                                <v-list-item-content>{{question.optionC}}</v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="question.optionD!='' && question.optionD!=null">
                                <v-list-item-action><v-checkbox v-model="responseD"></v-checkbox></v-list-item-action>
                                <v-list-item-content>{{question.optionD}}</v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="question.optionE!='' && question.optionE!=null">
                                <v-list-item-action><v-checkbox v-model="responseE"></v-checkbox></v-list-item-action>
                                <v-list-item-content>{{question.optionE}}</v-list-item-content>
                            </v-list-item>
                        </v-list>
                        <v-card-text v-else>
                            <v-textarea v-model="answerDescriptiveByUser" label="Write your answer here" rows="5" auto-grow clearable></v-textarea>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn @click="submitAnswer(question)" class="black--text">Submit Answer</v-btn>
                            <v-btn @click="skipQuestion(question)" class="black--text">Skip</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-col>
            <v-col v-if="question.imagePath!=null || question.videoPath!=null" cols="12" md="6">
                <v-card>
                    <v-tabs>
                        <v-tab v-if="question.imagePath!=null"><v-icon>image</v-icon></v-tab>
                        <v-tab v-if="question.videoPath!=null"><v-icon>videocam</v-icon></v-tab>
                        <v-tab-item v-if="question.imagePath!=null">
                            <v-img :src=this.hostUrl+question.imagePath></v-img>
                        </v-tab-item>
                        <v-tab-item v-html="question.videoPath" v-if="question.videoPath!=null"></v-tab-item>
                    </v-tabs>
                </v-card>
            </v-col>
        </v-row>
        <!-- test area end -->
    </v-container>
</template>

<script>
import config from '../../config'

export default {
    name:'startQuiz',
    data(){
        return{
            quiz:{},
            interval: null,
            quizTime:null,
            questionSerial:null,
            question:{},
            hostUrl:null,
            answerDescriptiveByUser:'',
            responseA:false,
            responseB:false,
            responseC:false,
            responseD:false,
            responseE:false,
            userAnswer:'',
            serialToShow:null,
            eachQuestionMark:0
        }
    },
    methods:{
        //clock timer
        startTimer(counter){
            if(this.quiz.quizTime>0){
                var minutes=0,seconds=0;
                counter=counter*60;
                this.interval=setInterval(()=>{
                    minutes = parseInt(counter / 60, 10);
                    seconds = parseInt(counter % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    document.getElementById('displayTimeSecond').innerHTML=minutes+":"+seconds;
                    this.$store.dispatch('dashboard/saveQuizLiveTime',counter/60);
                    if(counter==0){
                        const objTime={
                            quizResponseInitialId:this.$store.getters['dashboard/responseInitial'].quizResponseInitialId,
                        }
                        this.$store.dispatch('dashboard/updateQuizTakenTime',objTime)
                        this.$router.push({name:'QuizResult'})
                    }
                    counter--;
                },1000)
            }
        },
        //end of test
        quizEnd(){
            this.$store.dispatch('dashboard/changeVisibility')
            this.$store.dispatch('dashboard/saveQuestionSerial',1)
            const objTimeUpdate={
                quizResponseInitialId:this.$store.getters['dashboard/responseInitial'].quizResponseInitialId,
            }
            this.$store.dispatch('dashboard/updateQuizTakenTime',objTimeUpdate)
            this.$router.push({name:'QuizResult'})
        },
        //get each question
        getQuestion(serial){
            this.serialToShow=serial
            const objSendForQuestion={
                quizId:this.quiz.quizTopicId,
                serial:serial
            }
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('dashboard/fetchSingleQuestion',objSendForQuestion)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.question=response.data
                this.eachQuestionMark=response.data.perQuestionMark
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //reset response for next question
        resetResponse(){
            this.answerDescriptiveByUser=''
            this.responseA=false
            this.responseB=false
            this.responseC=false
            this.responseD=false
            this.responseE=false
        },
        //save answer
        answer(question){
            const objAnswer={
                quizResponseInitialId:this.$store.getters['dashboard/responseInitial'].quizResponseInitialId,
                quizQuestionId:question.quizQuestionId,
                questionDetail:question.questionDetail,
                imagePath:question.imagePath,
                videoPath:question.videoPath,
                userAnswer:this.userAnswer,
                isAnswerSkipped:this.userAnswer==''?true:false,
                correctAnswer:question.correctOption,
                answerExplanation:question.answerExplanation,
                questionMark:question.perQuestionMark,
                addedBy:parseInt(localStorage.getItem('loggedUserId'))
            }
            this.$store.dispatch('dashboard/createDetailResponse',objAnswer)
            this.resetResponse()

            var newSerial=question.serialNo+1
            if(newSerial<=this.quiz.questionsCount){
                this.getQuestion(newSerial)
                this.$store.dispatch('dashboard/saveQuestionSerial',newSerial)
            }else if(newSerial>this.quiz.questionsCount){
                this.$router.push({name:'QuizResult'})
            }
        },
        //skip a question
        skipQuestion(question){
            this.userAnswer=''
            this.answer(question)
        },
        //submit a answer
        submitAnswer(question){
            this.userAnswer=''
            if(question.questionTypeId==1){
                if(this.responseA==true){
                    this.userAnswer=question.optionA
                }
                if(this.responseB==true){
                    if(this.userAnswer!=''){
                        this.userAnswer=this.userAnswer+'#####'+question.optionB
                    }else{
                        this.userAnswer=question.optionB
                    }
                }
                if(this.responseC==true){
                    if(this.userAnswer!=''){
                        this.userAnswer=this.userAnswer+'#####'+question.optionC
                    }else{
                        this.userAnswer=question.optionC
                    }
                }
                if(this.responseD==true){
                    if(this.userAnswer!=''){
                        this.userAnswer=this.userAnswer+'#####'+question.optionD
                    }else{
                        this.userAnswer=question.optionD
                    }              
                }
                if(this.responseE==true){
                    if(this.userAnswer!=''){
                        this.userAnswer=this.userAnswer+'#####'+question.optionE
                    }else{
                        this.userAnswer=question.optionE
                    }
                }
                if(this.responseA==false && this.responseB==false && this.responseC==false && this.responseD==false
                && this.responseE==false){
                    this.$root.$emit('message_from_parent','Empty answer!')
                }else if(this.quiz.allowMultipleInputByUser==false && this.userAnswer.split('#####').length>1){
                    this.$root.$emit('message_from_parent','Multiple answer not allowed!')
                }
                else{
                    this.answer(question)
                }
            }else{
                this.userAnswer=this.answerDescriptiveByUser!=null?this.answerDescriptiveByUser.trim():''
                if(this.userAnswer==''){
                    this.$root.$emit('message_from_parent','Empty answer!')
                }else{
                    this.answer(question)
                }
            }
        }
    },
    computed:{
        //get app logo
        logoImg:function(){
            return this.$store.getters['settings/logoOnExamPage']==true?config.hostname+this.$store.getters['settings/logoPath']:''
        }
    },
    mounted(){
        //set timer
        this.startTimer(this.quizTime)
    },
    //execute before exit this page
    beforeDestroy() {
        clearInterval(this.interval)
        this.$store.dispatch('dashboard/saveQuizFlag',false)
        this.$store.dispatch('dashboard/saveResultFlag',true)
        this.$store.dispatch('settings/resetRefreshCount')
    },
    created(){
        if(this.$store.getters['dashboard/quizFlag']==false){
            this.$router.push({name:'Dashboard'})
        }
        this.quizTime=this.$store.getters['dashboard/quizTime']
        this.quiz=this.$store.getters['dashboard/quiz']
        this.questionSerial=this.$store.getters['dashboard/questionSerial']
        this.getQuestion(this.questionSerial) 
        this.hostUrl=config.hostname

        if(this.$store.getters['settings/endExam']==true){
            if(window.location.reload){
                this.$store.dispatch('settings/changeRefreshCount')
                if(this.$store.getters['settings/refreshCount']==2){
                    this.$router.push({name:'QuizResult'})
                }              
            }
        }
    }
}
</script>