//integrating all vuex modules
import Vuex from 'vuex'
import Vue from 'vue'
import dashboard from './modules/dashboard'
import user from './modules/user'
import menu from './modules/menu'
import question from './modules/question'
import quiz from './modules/quiz'
import report from './modules/report'
import settings from './modules/settings'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex);

export default new Vuex.Store({
    modules:{
        dashboard,
        user,
        menu,
        question,
        quiz,
        report,
        settings
    },
    plugins:[createPersistedState()],
});