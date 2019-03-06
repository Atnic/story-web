import Vue from 'vue'
import './plugins/vuetify'
import router from './router/index'
import { store } from './store/store'
import App from './App.vue'
import './registerServiceWorker'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Vuetify from 'vuetify'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = process.env.MIX_VUE_APP_BASE_API_URL;

Vue.use(Vuetify);
Vue.use(VueAxios, axios)

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
