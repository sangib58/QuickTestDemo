//vuex functions for report
import axios from 'axios'
import config from './../../config'

const state={
    reportTypes:[],
    quizes:[],
    results:[],
    certificateInfo:{},
    singleTemplate:{},
    confirmation:{}
};

const getters={
    reportTypes:state=>state.reportTypes,
    quizes:state=>state.quizes,
    results:state=>state.results,
    certificateInfo:state=>state.certificateInfo,
    singleTemplate:state=>state.singleTemplate,
    confirmation:state=>state.confirmation,
};

const actions={
    fetchReportTypes({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Report/GetReportType')
            .then((response)=>{
                commit('setReportType',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchQuizes({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Quiz/GetQuizesForReports')
            .then((response)=>{
                commit('setQuizes',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchQuizesFiltered({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Report/GetFilteredQuiz/${id}`)
            .then((response)=>{
                commit('setQuizes',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchResults({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Report/GetResults')
            .then((response)=>{
                commit('setResults',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchResultsFiltered({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Report/GetResultsById/${id}`)
            .then((response)=>{
                commit('setResults',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchResultByTopic({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Report/GetResultByTopic/${id}`)
            .then((response)=>{
                commit('setResults',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchCertificateData({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Quiz/GetSingleTemplate/${id}`)
            .then((response)=>{
                commit('setSingleCertificate',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    storeCertificateInfo({commit},info){
        commit('setCertificateInfo',info)
    },
    SentCheckedEmailToNotifyStudents({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/SentEmailToCheckedStudents',obj)
            .then((response)=>{
                commit('sentEmailChecked',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    saveMarksObtain({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+`/api/Report/UpdateMarksObtain/${obj.id}/${obj.marks}`)
            .then((response)=>{    
                commit('setMarks',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
};

const mutations ={
    setReportType:(state,reportTypes)=>{
        state.reportTypes=reportTypes
    },
    setQuizes:(state,quizes)=>{
        state.quizes=quizes
    },
    setResults:(state,results)=>{
        state.results=results
    },
    setCertificateInfo:(state,info)=>{
        state.certificateInfo=info
    },
    setSingleCertificate:(state,info)=>{
        state.singleTemplate=info
    },
    sentEmailChecked:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setMarks:(state,confirmation)=>{
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