<template>
<!-- app dashboard start -->
    <v-container class="px-0">
        <v-row v-if="itemsQuiz.length==0 && roleName=='Student'" justify="center" class="font-weight-bold">No Assessment Available!</v-row>
        <v-row v-else-if="itemsQuiz.length>0 && roleName=='Student'" justify="center" class="font-weight-bold">Available Assessments</v-row>
        <!-- execute for candidate start -->
        <v-row justify="center" v-if="roleName=='Student'">
            <v-col cols="12" sm="6" md="4" v-for="item in itemsQuiz" :key="item.quizTopicId">
                <v-card color="grey lighten-3" shaped elevation="6">
                    <v-card-text class="pb-0">
                        <p class="title text--primary">{{item.quizTitle}}</p>
                    </v-card-text>
                    <v-list class="pa-0">
                        <v-list-item class="py-0">
                            <v-list-item-icon><v-icon>timer</v-icon></v-list-item-icon>
                            <v-list-item-title>Time</v-list-item-title>
                            <v-list-item-title>{{item.quizTime==0?'':item.quizTime+' minutes'}}</v-list-item-title>
                        </v-list-item>
                        <v-list-item class="py-0">
                            <v-list-item-icon><v-icon>grade</v-icon></v-list-item-icon>
                            <v-list-item-title>Marks</v-list-item-title>
                            <v-list-item-title>{{item.quizTotalMarks==0?'':item.quizTotalMarks}}</v-list-item-title>
                        </v-list-item>
                        <v-list-item class="py-0">
                            <v-list-item-icon><v-icon>help</v-icon></v-list-item-icon>
                            <v-list-item-title>Questions</v-list-item-title>
                            <v-list-item-title>{{item.questionsCount}}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <form v-bind:action="stripeUrl" method="GET" v-if="item.quizPrice>0 && item.paymentCount==0">
                            <input type="hidden" name="quizTopicId" v-bind:value="item.quizTopicId">
                            <input type="hidden" name="quizEmail" v-bind:value="getUserEmail">
                            <input type="hidden" name="addedBy" v-bind:value="getUserId">
                            <input type="hidden" name="location" v-bind:value="getWindowLocation">
                            <input type="hidden" name="currency" v-bind:value="getCurrency">
                            
                            <v-btn type="submit" class="white--text" color="grey darken-2" @click="saveQuizTopicId(item)">Pay {{getCurrencySymbol}}{{item.quizPrice}}</v-btn>
                        </form>
                        <v-btn v-else class="white--text" color="grey darken-2" @click="startQuiz(item)">Start</v-btn>
                    </v-card-actions>                   
                </v-card>
            </v-col>
        </v-row>
        <!-- execute for candidate end -->

        <!-- execute for admin start -->
        <v-row v-if="roleName=='Admin'">
            <v-col class="pl-2" cols="12" sm="6" md="3">
                <v-card :to="{name:'Users'}" class="grey lighten-1">
                    <v-card-title>
                        <v-icon x-large left>mdi-account-multiple</v-icon>       
                    </v-card-title>
                    <v-card-text>Candidates <span class="black--text">{{totalStudents}}</span></v-card-text>
                </v-card>
            </v-col>
            <v-col class="pl-2" cols="12" sm="6" md="3">
                <v-card :to="{name:'QuizTopics'}" class="grey lighten-2">
                    <v-card-title>
                        <v-icon x-large left>emoji_objects</v-icon>       
                    </v-card-title>
                    <v-card-text>Assessments <span class="black--text">{{totalQuizes}}</span></v-card-text>
                </v-card>
            </v-col>
            <v-col class="pl-2" cols="12" sm="6" md="3">
                <v-card :to="{name:'QuizTopics'}" class="grey lighten-3">
                    <v-card-title>
                        <v-icon x-large left>emoji_objects</v-icon>       
                    </v-card-title>
                    <v-card-text>Live Assessments <span class="black--text">{{liveQuizes}}</span></v-card-text>
                </v-card>
            </v-col>
            <v-col class="pl-2" cols="12" sm="6" md="3">
                <v-card :to="{name:'Quizes'}" class="grey lighten-4">
                    <v-card-title>
                        <v-icon x-large left>help_center</v-icon>       
                    </v-card-title>
                    <v-card-text>Questions <span class="black--text">{{totalQuestions}}</span></v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <!-- execute for admin end -->

        <!-- various charts -->
        <Analytics class="pa-0" v-if="roleName=='Admin'"/>
    </v-container>
<!-- app dashboard end -->
</template>

<script>
import config from '../../config'
import Analytics from '../../components/common/Analytics'

export default {
    name:'Dashboard',
    components:{
        Analytics
    },
    data(){
        return{           
            itemsQuiz:[],  
            userInfo:{},
            totalStudents:'',
            totalQuizes:'',
            liveQuizes:'',
            totalQuestions:'',
            userId:null
        }
    },
    methods:{
        //fetch tests for candidate
        initializeStudent(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('dashboard/fetchLiveQuizes',this.userInfo.email)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsQuiz=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },

        //fetch status for admin
        initializeAdmin(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('dashboard/fetchSummary')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                if(response.status==200){
                    this.totalStudents=response.data.totalStudents
                    this.totalQuizes=response.data.totalQuizes
                    this.liveQuizes=response.data.liveQuizes
                    this.totalQuestions=response.data.totalQuestions
                }
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        
        //start a test
        startQuiz(item){
            this.$store.dispatch('dashboard/applyLoading')
            const objResponseInitial={
                userId:parseInt(localStorage.getItem('loggedUserId')),
                email:this.userInfo.email,
                quizTopicId:item.quizTopicId,
                quizTitle:item.quizTitle,
                quizMark:item.quizTotalMarks,
                quizPassMarks:item.quizPassMarks,
                quizTime:item.quizTime,
                addedBy:parseInt(localStorage.getItem('loggedUserId'))
            }
            this.$store.dispatch('dashboard/createInitialResponse',objResponseInitial)
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                if(response.status==200){
                    this.$store.dispatch('dashboard/startQuiz',item)
                    this.$store.dispatch('dashboard/saveQuizLiveTime',item.quizTime)
                    this.$router.push({name:'StartQuiz'})
                }
            })
        },
        saveQuizTopicId(item){
            localStorage.setItem('quizTopicId', item.quizTopicId)
        },
    },
    computed:{   
        //get user role
        roleName(){
            return this.userInfo.roleName
        },
        //get payment currency symbol
        getCurrencySymbol(){
            return this.$store.getters['settings/currencySymbol']
        },
        //get currency name
        getCurrency(){
            return this.$store.getters['settings/currency']
        },
        //get user email
        getUserEmail(){
            return this.userInfo.email
        },
        //get user id
        getUserId(){
            return this.userInfo.userId
        },
        //get browser url
        getWindowLocation(){
            return window.location.href;
        },
        //payment url
        stripeUrl(){
            return config.hostname+'/payment'
        }    
    },
    created(){
        this.userInfo=this.$store.getters['dashboard/userInfo']
        this.$store.dispatch('dashboard/changeVisibility')
        this.$store.dispatch('dashboard/saveQuestionSerial',1)
        this.$store.dispatch('dashboard/saveQuizFlag',false)
        if(this.userInfo.roleName=='Student'){      
            this.initializeStudent()
        }else if(this.userInfo.roleName=='Admin'){
            this.initializeAdmin()
        }
    }
}
</script>