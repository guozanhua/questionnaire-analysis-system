import Vue from 'vue';
import ViewUI from 'view-design';
import VueRouter from 'vue-router';
import Routers from './router';
import Util from './libs/util';
import App from './app.vue';
import axios from 'axios';
import Vuex from 'vuex';
import store from './vuex/store';

import 'view-design/dist/styles/iview.css';

import VueClipboard from 'vue-clipboard2'; //复制到剪切板功能
Vue.use(VueClipboard)

import echarts from 'echarts'
Vue.prototype.$echarts = echarts

Vue.use(VueRouter);
Vue.use(ViewUI);
Vue.prototype.$axios = axios;
Vue.prototype.$util = Util;

// 路由配置
const RouterConfig = {
    mode: 'hash',
    routes: Routers
};
const router = new VueRouter(RouterConfig);

router.beforeEach((to, from, next) => {
    ViewUI.LoadingBar.start();
    Util.title(to.meta.title);
    next();
});

router.afterEach((to, from, next) => {
    ViewUI.LoadingBar.finish();
    window.scrollTo(0, 0);
});

new Vue({
    el: '#app',
    router: router,
    store,
    render: h => h(App)
});
