
import Vue from 'vue'
import Vuex from 'vuex'
import Routers from '../router';
Vue.use(Vuex)


export default new Vuex.Store({
    state: {
        downloadApi: '/api/?r=basic/upload/download',
        uploadApi: '/api/?r=basic/upload/upload',
    },
    mutations: {
    },
    getters: {
    },


})