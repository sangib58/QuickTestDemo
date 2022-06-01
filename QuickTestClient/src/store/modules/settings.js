//vuex functions for settings
import axios from 'axios'
import config from './../../config'

const state={
    faqs:[],
    confirmation:{},
    emailConfirmation:false,
    allSettings:{},
    appBarColor:'',
    footerColor:'',
    bgColor:'',
    siteTitle:'Quick Test',
    description:'',
    footerText:'© 2022 Copyright QuickTest',
    defaultEmail: 'admin@quicktest.com',
    logoPath:'',
    faviconPath:'',
    allowWelcomeEmail:true,
    allowFaq:true,
    endExam:false,
    logoOnExamPage:true,
    paidRegistration:true,
    refreshCount:0,
    currency:'',
    currencySymbol:'',
    stripeSecretKey:''
};

const getters={
    emailConfirmation:state=>state.emailConfirmation,
    faqs:state=>state.faqs,
    confirmation:state=>state.confirmation,
    allSettings:state=>state.allSettings,
    appBarColor:state=>state.appBarColor,
    footerColor:state=>state.footerColor,
    bgColor:state=>state.bgColor,
    siteTitle:state=>state.siteTitle,
    description:state=>state.description,
    footerText:state=>state.footerText,
    logoPath:state=>state.logoPath,
    faviconPath:state=>state.faviconPath,
    endExam:state=>state.endExam,
    logoOnExamPage:state=>state.logoOnExamPage,
    paidRegistration:state=>state.paidRegistration,
    refreshCount:state=>state.refreshCount,
    currency:state=>state.currency,
    currencySymbol:state=>state.currencySymbol,
    stripeSecretKey:state=>state.stripeSecretKey,
};

const actions={
    checkEmailSettings({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Settings/ChkEmailConfiguration')
            .then((response)=>{
                commit('setEmailConfirmation',response.data)
                //console.log(response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchSiteSettings({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Settings/GetSiteSettings')
            .then((response)=>{
                commit('setSettings',response.data)
                //console.log(response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateSettings({commit},objSettings) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Settings/UpdateSettings',objSettings)
            .then((response)=>{
                commit('editSettings',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateClientUrl({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Settings/UpdateClientUrl',obj)
            .then((response)=>{
                commit('setClientUrl',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    uploadLogo({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/UploadLogo',objFormData)
            .then((response)=>{
                commit('logoUpload',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    uploadFavicon({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/UploadFavicon',objFormData)
            .then((response)=>{
                commit('faviconUpload',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchfaqs({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Settings/GetFaqList')
            .then((response)=>{
                commit('setFaqs',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createFaq({commit},objFaq) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/CreateFaq',objFaq)
            .then((response)=>{
                commit('newFaq',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateFaq({commit},objFaq) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Settings/UpdateFaq',objFaq)
            .then((response)=>{
                commit('editFaq',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteFaq({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Settings/DeleteFaq/${id}`)
            .then((response)=>{
                commit('deleteSingleFaq',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    passwordEmailSent({commit},objEmail) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/SendPasswordResetLink',objEmail)
            .then((response)=>{
                commit('receiveEmail',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    welcomeEmailSent({commit},objEmail) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Settings/SendWelcomeMail',objEmail)
            .then((response)=>{
                commit('receiveEmail',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changeAppBarColor({commit},val){
        commit('setAppBarColor',val)
    },
    changeFooterColor({commit},val){
        commit('setFooterColor',val)
    },
    changeBgColor({commit},val){
        commit('setBgColor',val)
    },
    changeSiteTitle({commit},val){
        commit('setSiteTitle',val)
    },
    changeSiteDescription({commit},val){
        commit('setSiteDescription',val)
    },
    changeFooterText({commit},val){
        commit('setFooterText',val)
    },
    changeRefreshCount({commit}){
        commit('setRefreshCount')      
    },
    resetRefreshCount({commit}){
        commit('resetRefreshCount')
    },
};

const mutations ={
    setEmailConfirmation:(state,emailConfirmation)=>{
        state.emailConfirmation=emailConfirmation
    },
    setSettings:(state,allSettings)=>{
        state.allSettings=allSettings
        state.appBarColor=allSettings.appBarColor
        state.footerColor=allSettings.footerColor
        state.bgColor=allSettings.bodyColor
        state.siteTitle=allSettings.siteTitle
        state.description=allSettings.welComeMessage
        state.footerText=allSettings.copyRightText
        state.defaultEmail=allSettings.defaultEmail
        state.logoPath=allSettings.logoPath
        state.allowWelcomeEmail=allSettings.allowWelcomeEmail
        state.allowFaq=allSettings.allowFaq
        state.endExam=allSettings.endExam
        state.logoOnExamPage=allSettings.logoOnExamPage
        state.paidRegistration=allSettings.paidRegistration
        state.currency=allSettings.currency
        state.currencySymbol=allSettings.currencySymbol
        state.stripeSecretKey=allSettings.stripeSecretKey
    },
    editSettings:(state,allSettings)=>{
        state.allSettings=allSettings
    },
    setClientUrl:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    logoUpload:(state,data)=>{
        state.logoPath=data.dbPath
    },   
    faviconUpload:(state,data)=>{
        state.faviconPath=data.dbPath
    },   
    setFaqs:(state,faqs)=>{
        state.faqs=faqs
    },
    newFaq:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editFaq:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleFaq:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    receiveEmail:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setAppBarColor:(state,val)=>{
        state.appBarColor=val
    },
    setFooterColor:(state,val)=>{
        state.footerColor=val
    },
    setBgColor:(state,val)=>{
        state.bgColor=val
    },
    setSiteTitle:(state,val)=>{
        state.siteTitle=val
    },
    setSiteDescription:(state,val)=>{
        state.description=val
    },
    setFooterText:(state,val)=>{
        state.footerText=val
    },
    setRefreshCount:(state)=>{
        state.refreshCount+=1
    },
    resetRefreshCount:(state)=>{
        state.refreshCount=0
    },
};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}