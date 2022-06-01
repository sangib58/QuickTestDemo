//vuex functions for user
import axios from 'axios'
import config from './../../config'

const state={
    users:[],
    userRoles:[],
    menuGroups:[],
    browses:[],
    confirmation:{},
    imagePath:'',
    singleUser:{},
};
const getters={
    userList:state=>state.users,
    RoleList:state=>state.userRoles, 
    menuGroupList:state=>state.menuGroups,
    browses:state=>state.browses,
    confirmation:state=>state.confirmation,
    imagePath:state=>state.imagePath,
    singleUser:state=>state.singleUser,
};
const actions={
    fetchUsers({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/User/GetUserList')
            .then((response)=>{
                commit('setUsers',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createUser({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/CreateUser',objUser)
            .then((response)=>{
                commit('newUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateUser({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/User/UpdateUser',objUser)
            .then((response)=>{
                commit('editUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteUser({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/User/DeleteSingleUser/${id}`)
            .then((response)=>{
                commit('deleteSingleUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchUserRoles({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/User/GetUserRoleList')
            .then((response)=>{
                commit('setUserRoles',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createUserRole({commit},objUserRole) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/CreateUserRole',objUserRole)
            .then((response)=>{
                commit('newUserRole',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateUserRole({commit},objUserRole) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/User/UpdateUserRole',objUserRole)
            .then((response)=>{
                commit('editUserRole',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteUserRole({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/User/DeleteSingleRole/${id}`)
            .then((response)=>{
                commit('deleteSingleUserRole',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    unLockUser({commit},obj) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/Unlock',obj)
            .then((response)=>{
                commit('passwordEncrypt',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changePassword({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/User/ChangeUserPassword',objUser)
            .then((response)=>{
                commit('passwordChange',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    resetPassword({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/User/ResetUserPassword',objUser)
            .then((response)=>{
                commit('passwordChange',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    uploadImage({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/Upload',objFormData)
            .then((response)=>{
                commit('imageUpload',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateUserProfile({commit},objUserProfile) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/User/UpdateUserProfile',objUserProfile)
            .then((response)=>{
                commit('updateProfile',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchSingleUser({commit},userId) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/User/GetSingleUser/${userId}`)
            .then((response)=>{
                commit('singleUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchBrowseList({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/User/GetBrowseList')
            .then((response)=>{
                commit('setBrowseList',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createRegistration({commit},objRegister) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/User/StudentRegistration',objRegister)
            .then((response)=>{
                commit('newUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchUserInfo({commit},email){
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/User/GetUserInfoForForgetPassword/${email}`)
            .then(function(response){
                commit('singleUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
         })
    },
};
const mutations={
    setUsers:(state,users)=>{
        state.users=users
    },
    newUser:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editUser:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleUser:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setUserRoles:(state,userRoles)=>{
        state.userRoles=userRoles
    },
    setMenuGroups:(state,menuGroups)=>{
        state.menuGroups=menuGroups
    },
    newUserRole:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editUserRole:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleUserRole:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    passwordEncrypt:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    passwordChange:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    imageUpload:(state,data)=>{
        state.imagePath=data.dbPath
    },
    updateProfile:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    singleUser:(state,data)=>{
        state.singleUser=data
    },
    setBrowseList:(state,data)=>{
        state.browses=data
    },
};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}