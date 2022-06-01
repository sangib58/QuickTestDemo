//vuex functions for dashboard
import axios from 'axios'
import config from './../../config'

const state={
    isLanding:true,
    status:'',
    token:localStorage.getItem('token')||'',
    user:{},
    confirmation:{},
    menus:[],
    visible:false,
    overlay:false,
    loading:false,
    clientInfo:{},
    quizTime:null,
    quizes:[],
    quiz:{},
    question:{},
    questionSerial:1,
    responseInitial:{},
    quizFlag:false,
    resultFlag:false,
    result:null,
    pendingExamine:null,
    resultInfo:{},
    appSummary:{},
    quizChartDate:[]
};

const getters={
    isLanding:state=>state.isLanding,
    isLoggedIn:state=>!!state.token,
    authStatus:state=>state.status,
    userInfo:state=>state.user,
    menus:state=>state.menus,
    quizTime:state=>state.quizTime,
    quizes:state=>state.quizes,
    quiz:state=>state.quiz,
    question:state=>state.question,
    questionSerial:state=>state.questionSerial,
    responseInitial:state=>state.responseInitial,
    quizFlag:state=>state.quizFlag,
    resultFlag:state=>state.resultFlag,
    resultInfo:state=>state.resultInfo,
    result:state=>state.result,
    pendingExamine:state=>state.pendingExamine,
    appSummary:state=>state.appSummary,
    quizChartDate:state=>state.quizChartDate,
    visible:state=>state.visible,
    overlay:state=>state.overlay,
    loading:state=>state.loading,
    clientInfo:state=>state.clientInfo
};

const actions={
    fetchSigninInfo({commit},credential){
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/GetLoginInfo',credential)
            .then(function(response){
            // handle success
            if(response.status==200){
                const token ='Bearer '+response.data.token
                localStorage.setItem('token', token)
                localStorage.setItem('loggedUserId', response.data.obj.userId)
                axios.defaults.headers.common['Authorization'] = token
                commit('auth_success', response.data)               
            }
            resolve(response)   
            })
            .catch(function (error) {
            // handle error
                commit('auth_error')
                localStorage.removeItem('token')
                localStorage.removeItem('loggedUserId')
                localStorage.removeItem('logCode')
                localStorage.removeItem('quizTopicId')
                delete axios.defaults.headers.common['Authorization']
                reject(error)
            })
         })
    },
    signOut({commit}){
        return new Promise((resolve) => {                   
            commit('signOut')
            localStorage.removeItem('token')
            localStorage.removeItem('loggedUserId') 
            localStorage.removeItem('quizTopicId')       
            delete axios.defaults.headers.common['Authorization']
            resolve()
        })
    },
    fetchMenu({commit},info) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Menu/GetSidebarMenu/${info.roleId}`)
            .then((response)=>{
                commit('setMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createLogHistory({commit},objLogHistory) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/CreateLoginHistory',objLogHistory)
            .then((response)=>{
                //console.log(response.data.responseMsg)
                if(response.status==200){
                    localStorage.setItem('logCode',response.data.responseMsg)
                    commit('logHistory',response.data)
                }             
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateLogHistory({commit},logCode) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/User/UpdateLoginHistory/${logCode}`)
            .then((response)=>{
                commit('logHistory',response.data)
                localStorage.removeItem('logCode')
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchLiveQuizes({commit},email) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetRunningQuizes/${email}`)
            .then((response)=>{
                commit('setQuizes',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchSingleQuestion({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Quiz/GetSingleQuestion/${obj.quizId}/${obj.serial}`)
            .then((response)=>{
                commit('setSingleQuestion',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createInitialResponse({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuizResponseInitial',obj)
            .then((response)=>{
                commit('setInitialResponse',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createDetailResponse({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Quiz/CreateQuizResponseDetail',obj)
            .then((response)=>{
                commit('setDetailResponse',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateQuizTakenTime({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Quiz/UpdateQuizTakenTime',obj)
            .then((response)=>{
                commit('changeQuizTakenTime',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchFinishedExamInfo({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Report/GetFinishedExamInfo/${id}`)
            .then((response)=>{
                commit('setResultInfo',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchFinishedExamResult({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Report/GetFinishedExamResult/${id}`)
            .then((response)=>{
                commit('setResult',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchPendingExamine({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Report/GetPendingExamine/${id}`)
            .then((response)=>{
                commit('setExamine',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchSummary({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Dashboard/GetStatus')
            .then((response)=>{
                commit('setAppSummary',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchQuizCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetQuizCountByDate/${id}`)
            .then((response)=>{
                commit('setQuizCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchQuizUserCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetUserCountByQuiz/${id}`)
            .then((response)=>{
                commit('setQuizUserCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchMonthCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetLogInSummaryByMonth/${id}`)
            .then((response)=>{
                commit('setMonthCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchBrowserCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetBrowserCount/${id}`)
            .then((response)=>{
                commit('setBrowserCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchPlatformCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetPlatformCount/${id}`)
            .then((response)=>{
                commit('setPlatformCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchDateCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Dashboard/GetLogInSummaryByDate/${id}`)
            .then((response)=>{
                commit('setDateCount',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    saveQuestionSerial({commit},serial){
        commit('setQuestionSerial',serial)
    },
    saveQuizLiveTime({commit},time){
        commit('setLiveTime',time)
    },
    startQuiz({commit},quiz){
        commit('setStartQuiz',quiz)
    },
    saveQuizFlag({commit},val){
        commit('setQuizFlag',val)
    },
    saveResultFlag({commit},val){
        commit('setResultFlag',val)
    },
    changeVisibility({commit}){
        commit('setVisibility')
    },
    switchLanding({commit},val){
        commit('setLanding',val)
    },
    applyOverlay({commit}){
        commit('setOverlay')
    },
    clearOverlay({commit}){
        commit('resetOverlay')
    },
    applyLoading({commit}){
        commit('setLoading')
    },
    cancelLoading({commit}){
        commit('resetLoading')
    },
    fetchClientInfo({commit}){
        axios.get('https://api.ipify.org?format=json')
        .then((response)=>{
            const { detect } = require('detect-browser');
            const browser = detect();
            const data={
                userIp:response.data.ip,
                browserName:browser.name,
                browserVersion:browser.version,
                platform:browser.os,
            }
            commit('setClientInfo',data)         
        })
    }
};

const mutations ={
    auth_request:(state)=>{
        state.status='loading'
    },
    auth_success:(state, data)=>{
        state.status = 'success'
        state.token = data.token
        state.user = data.obj
    },
    auth_error:(state)=>{
        state.status = 'error'
    },
    signOut:(state)=>{
        state.status = ''
        state.token = ''
        state.visible=false
        state.overlay=false
        state.loading=false
    },
    setMenu:(state,menus)=>{
        state.menus=menus
    },
    logHistory:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setQuizes:(state,quizes)=>{
        state.quizes=quizes
    },
    setLiveTime:(state,time)=>{
        state.quizTime=time
    },
    setStartQuiz:(state,quiz)=>{
        state.quiz=quiz
        state.visible=false
        state.quizFlag=true
    },
    setQuizFlag:(state,flag)=>{
        state.quizFlag=flag
    },
    setResultFlag:(state,flag)=>{
        state.resultFlag=flag
    },
    setSingleQuestion:(state,question)=>{
        state.question=question
    },
    setQuestionSerial:(state,serial)=>{
        state.questionSerial=serial
    },
    setInitialResponse:(state,responseInitial)=>{
        state.responseInitial=responseInitial
    },
    setDetailResponse:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    changeQuizTakenTime:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setResultInfo:(state,info)=>{
        state.resultInfo=info
    },
    setResult:(state,result)=>{
        state.result=result
    },
    setExamine:(state,pendingExamine)=>{
        state.pendingExamine=pendingExamine
    },
    setAppSummary:(state,obj)=>{
        state.appSummary=obj
    },
    setQuizCount:(state,obj)=>{
        state.quizChartDate=obj
    },
    setQuizUserCount:(state,obj)=>{
        state.quizChartDate=obj
    },
    setMonthCount:(state,obj)=>{
        state.quizChartDate=obj
    },
    setBrowserCount:(state,obj)=>{
        state.quizChartDate=obj
    },
    setPlatformCount:(state,obj)=>{
        state.quizChartDate=obj
    },
    setDateCount:(state,obj)=>{
        state.quizChartDate=obj
    },
    setVisibility:(state)=>{
        state.visible=true
    },
    setLanding:(state,value)=>{
        state.isLanding=value
    },
    setOverlay:(state)=>{
        state.visible=false
        state.overlay=true
    },
    resetOverlay:(state)=>{
        state.visible=true
        state.overlay=false
    },
    setLoading:(state)=>{
        state.loading=true
    },
    resetLoading:(state)=>{
        state.loading=false
    },
    setClientInfo:(state,data)=>{
        state.clientInfo=data
    },
};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}