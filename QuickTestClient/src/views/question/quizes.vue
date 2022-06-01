<template>
    <v-container>
        <!-- filter tests start -->
        <v-row justify="center">          
            <v-col cols="12" sm="8" md="6">
                <v-select
                    v-model="selectedItemCategory"
                    label="Select category to filter"
                    :items="categoryOptionItems"
                    item-text="questionCategoryName"
                    item-value="questionCategoryId"
                    v-on:change="filterQuizes"
                    prepend-icon="filter_alt"
                    menu-props="auto"
                    return-object
                    clearable
                >
                </v-select>
            </v-col>
        </v-row>
        <!-- filter tests end -->

        <!-- all tests start -->
        <v-row>
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
                    <v-card-actions class="font-weight-bold">
                        <v-btn text @click="addQuestions(item)">Add Questions</v-btn>
                    </v-card-actions>                   
                </v-card>       
            </v-col>
        </v-row>
        <!-- all tests end -->
    </v-container>
</template>

<script>
export default {
    name:'Quizes',
    data(){
        return{
            selectedItemCategory:null,
            categoryOptionItems:[],
            itemsQuiz:[],
            filteredItemsQuiz:[]
        }
    },
    methods:{
        //get tests with question count
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('question/fetchQuizWithQuestionsCount')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsQuiz=response.data
                this.filteredItemsQuiz=this.itemsQuiz
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //get category options
        getCategoryOptions(){
            this.$store.dispatch('quiz/fetchCategoryOptions')
            .then((response)=>{
                this.categoryOptionItems=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //filter test
        filterQuizes(obj){          
            if(obj==null){
                this.itemsQuiz=this.filteredItemsQuiz
            }else{
                this.itemsQuiz=this.filteredItemsQuiz.filter(function(item){
                    return item.categories.includes(obj.questionCategoryId.toString())==true
                })
            }
        },
        //switch to question add view
        addQuestions(item){
            this.$store.dispatch('question/storeSingleQuiz',item)
            this.$router.push({name:'QuizQuestions'})
        }
    },
    created(){
        this.initialize()
        this.getCategoryOptions()
    }
}
</script>
