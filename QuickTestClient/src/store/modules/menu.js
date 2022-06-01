//vuex functions for menu
import axios from 'axios'
import config from './../../config'

const state={
    appMenu:[],
    confirmation:{}
};
const getters={
    menuList:state=>state.appMenu,
    confirmation:state=>state.confirmation 
};
const actions={
    fetchMenu({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Menu/GetMenuList')
            .then((response)=>{
                commit('setMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createMenu({commit},objMenu) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Menu/CreateMenu',objMenu)
            .then((response)=>{
                commit('newMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateMenu({commit},objMenu) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Menu/UpdateMenu',objMenu)
            .then((response)=>{
                commit('editMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteMenu({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Menu/DeleteSingleMenu/${id}`)
            .then((response)=>{
                commit('deleteSingleMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchAllMenu({commit},userRoleId) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Menu/GetAllMenu/${userRoleId}`)
            .then((response)=>{
                commit('getMenus',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    assignNewMenu({commit},objMenuOperation) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Menu/MenuAssign',objMenuOperation)
            .then((response)=>{
                commit('menuAssign',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
};
const mutations={
    setMenu:(state,appMenu)=>{
        state.appMenu=appMenu
    },
    newMenu:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editMenu:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleMenu:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    getMenus:(state,appMenu)=>{
        state.appMenu=appMenu
    },
    menuAssign:(state,confirmation)=>{
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