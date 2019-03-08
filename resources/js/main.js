import Vue from 'vue'
import Vuetify from 'vuetify'
import './plugins/vuetify'
import router from './router/index'
import { store } from './store/store'
import App from './App.vue'
import './registerServiceWorker'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Composer from './components/composer.vue'
import AppSnackbar from './components/snackbar.vue'
import AppLoading from './components/loading.vue'

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = process.env.MIX_VUE_APP_BASE_API_URL;

Vue.component('app-composer', Composer);
Vue.component('app-snackbar', AppSnackbar);
Vue.component('app-loading', AppLoading);

Vue.use(VueAxios, axios);
Vue.use(Vuetify);

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');
