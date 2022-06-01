//vuex functions for assessment
import axios from 'axios'
import config from './../../config'

const state={
    quizTopics:[],
    objQuizTopics:{},
    categoryOptions:[],
    markOptions:[],
    participantOptions:[],
    confirmation:{},
    emails:'',
    templates:[],
    imagePath:'',
    singleTemplate:{},
    categories:[],
    csvPath:''
};

const getters={
    quizTopics:state=>state.quizTopics,
    objQuizTopics:state=>state.objQuizTopics,
    categoryOptions:state=>state.categoryOptions,
    markOptions:state=>state.markOptions,
    participantOptions:state=>state.participantOptions,
    confirmation:state=>state.confirmation,
    emails:state=>state.emails,
    templates:state=>state.templates,
    imagePath:state=>state.imagePath,
    singleTemplate:state=>state.singleTemplate,
    categories:state=>state.categories,
    csvPath:state=>state.csvPath,
};

const actions={
    fetchTypeOptions({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuestionType')
            .then((response)=>{
                commit('setCategoryOptions',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchLavelOptions({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuestionLavel')
            .then((response)=>{
                commit('setCategoryOptions',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchCategoryOptions({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuestionCategory')
            .then((response)=>{
                commit('setCategoryOptions',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchMarkOptions({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuizMarksOptionList')
            .then((response)=>{
                commit('setMarkOptions',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchParticipantOptions({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuizParticipantOptionList')
            .then((response)=>{
                commit('setParticipantOptions',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchQuizTopics({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuizList')
            .then((response)=>{
                commit('setQuizTopics',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createQuizTopic({commit},objTopic) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuizTopic',objTopic)
            .then((response)=>{
                commit('newTopic',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateQuizTopic({commit},objTopic) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/UpdateQuizTopic',objTopic)
            .then((response)=>{
                commit('updateTopic',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    startQuiz({commit},objTopic) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/StartQuiz',objTopic)
            .then((response)=>{
                commit('updateTopic',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    stopQuiz({commit},objTopic) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/StopQuiz',objTopic)
            .then((response)=>{
                commit('updateTopic',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteQuizTopic({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Quiz/DeleteQuiz/${id}`)
            .then((response)=>{
                commit('deleteSingleQuiz',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    InsertParticipantsEmail({commit},arrEmails) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuizParticipants',arrEmails)
            .then((response)=>{
                commit('insertEmails',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    InviteParticipantsByEmail({commit},arrEmails) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/SendInvitationMail',arrEmails)
            .then((response)=>{
                commit('inviteByEmail',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchParticipantsEmail({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Quiz/GetQuizParticipantsEmail/${id}`)
            .then((response)=>{
                commit('getEmails',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchTemplates({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetCertificateTemplates')
            .then((response)=>{
                commit('getTemplates',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    uploadCertificateImage({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/UploadCertificateImage',objFormData)
            .then((response)=>{
                commit('imageUpload',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createTemplate({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateCertificateTemplate',obj)
            .then((response)=>{
                commit('newTemplate',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateTemplate({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/UpdateCertificateTemplate',obj)
            .then((response)=>{
                commit('updateTemplate',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteTemplate({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Quiz/DeleteSingleTemplate/${id}`)
            .then((response)=>{
                commit('deleteTemplate',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    storeSingleTemplate({commit},item) {
        commit('setTemplate',item)
    },
    fetchCategories({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Quiz/GetQuestionCategory`)
            .then((response)=>{
                commit('getCategories',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createCategory({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuestionCategory',obj)
            .then((response)=>{
                commit('newCategory',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateCategory({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/UpdateQuestionCategory',obj)
            .then((response)=>{
                commit('updateCategory',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteCategory({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Quiz/DeleteSingleQuestionCategory/${id}`)
            .then((response)=>{
                commit('deleteCategory',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createPayment({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuizPayment',obj)
            .then((response)=>{
                commit('setPayment',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    uploadQuestionCsv({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/UploadQuestionCsv',objFormData)
            .then((response)=>{
                commit('csvUpload',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    readQuestionCsvData({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/ReadQuestionUploadCsv',obj)
            .then((response)=>{
                commit('csvUploadConfirmation',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
};

const mutations ={
    setCategoryOptions:(state,options)=>{
        state.categoryOptions=options
    },
    setMarkOptions:(state,options)=>{
        state.markOptions=options
    },
    setParticipantOptions:(state,options)=>{
        state.participantOptions=options
    },
    setQuizTopics:(state,topics)=>{
        state.quizTopics=topics
    },
    newTopic:(state,response)=>{
        state.objQuizTopics=response
    },
    updateTopic:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleQuiz:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    insertEmails:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    inviteByEmail:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    getEmails:(state,emails)=>{
        state.emails=emails
    },
    getTemplates:(state,templates)=>{
        state.templates=templates
    },
    imageUpload:(state,data)=>{
        state.imagePath=data.dbPath
    },
    newTemplate:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    updateTemplate:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteTemplate:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setTemplate:(state,item)=>{
        state.singleTemplate=item
    },
    getCategories:(state,data)=>{
        state.categories=data
    },
    newCategory:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    updateCategory:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteCategory:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setPayment:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    csvUpload:(state,data)=>{
        state.csvPath=data.dbPath
    },
    csvUploadConfirmation:(state,confirmation)=>{
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