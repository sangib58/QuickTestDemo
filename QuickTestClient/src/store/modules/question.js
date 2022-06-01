//vuex functions for question
import axios from 'axios'
import config from './../../config'

const state={
    questions:[],
    confirmation:{},
    quiz:{},
    quizes:[],
    imagePath:''
};

const getters={
    questions:state=>state.questions,
    confirmation:state=>state.confirmation,
    quiz:state=>state.quiz,
    quizes:state=>state.quizes,
    imagePath:state=>state.imagePath,
};

const actions={
    fetchQuizWithQuestionsCount({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuizWithQuestionCount')
            .then((response)=>{
                commit('setQuizesWithQuestionsCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchQuizQuestions({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Quiz/GetQuizQuestionList/${id}`)
            .then((response)=>{
                commit('setQuestions',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createQuestion({commit},objQuestion) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuizQuestion',objQuestion)
            .then((response)=>{
                commit('newQuestion',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateQuestion({commit},objQuestion) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/UpdateQuizQuestion',objQuestion)
            .then((response)=>{
                commit('editQuestion',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteQuestion({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Quiz/DeleteSingleQuizQuestion/${id}`)
            .then((response)=>{
                commit('deleteSingleQuestion',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    storeSingleQuiz({commit},obj) {
        commit('setSingleQuiz',obj)
    },
    uploadImage({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/UploadQuestionImage',objFormData)
            .then((response)=>{
                commit('imageUpload',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    
};

const mutations ={
    setQuestions:(state,questions)=>{
        state.questions=questions
    },
    deleteSingleQuestion:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setSingleQuiz:(state,obj)=>{
        state.quiz=obj
    },
    setQuizesWithQuestionsCount:(state,quizes)=>{
        state.quizes=quizes
    },
    imageUpload:(state,data)=>{
        state.imagePath=data.dbPath
    },
    newQuestion:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editQuestion:(state,confirmation)=>{
        state.confirmation=confirmation
    },
};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}