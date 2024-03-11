import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import './registerServiceWorker'
import vuetify from './plugins/vuetify'
import VueI18n from 'vue-i18n'
import axios from 'axios'
import JsonExcel from 'vue-json-excel'
import tinymce from 'vue-tinymce-editor'
import VTooltip from 'v-tooltip'
import money from 'v-money'
import ZmTreeOrg from 'zm-tree-org';
import "zm-tree-org/lib/zm-tree-org.css";
import moment from 'moment'
import SelectUser from "@/components/SelectUser";

Vue.use(ZmTreeOrg);

Vue.use(VTooltip)
Vue.use(money, { precision: 4 })
Vue.component('tinymce', tinymce)

Vue.component('downloadExcel', JsonExcel);
Vue.component('SelectUser', SelectUser);


// import VueQuillEditor from 'vue-quill-editor'

// import 'quill/dist/quill.core.css' // import styles
// import 'quill/dist/quill.snow.css' // for snow theme
// import 'quill/dist/quill.bubble.css' // for bubble theme

Vue.config.productionTip = false

// Vue.use(VueQuillEditor, /* { default global options } */ )

Vue.use(VueI18n);

// Create VueI18n instance with options
import uz_latin from '../src/components/languages/uz_latin'
import uz_cyril from '../src/components/languages/uz_cyril'
import ru from '../src/components/languages/ru'
// import en from '../src/components/languages/en'
import Cookies from "js-cookie"

// Create VueI18n instance with options
let locale = 'uz_latin'
if (store.state.locale != '') {
    locale = store.state.locale
} else {
    store.dispatch('setLocale', locale)
}
const i18n = new VueI18n({
    locale: locale,
    silentTranslationWarn: true,
    messages: {
        'uz_latin': uz_latin,
        'uz_cyril': uz_cyril,
        'ru': ru,
    },
})

Vue.config.productionTip = false
Vue.config.disableNoTranslationWarning = true
Vue.config.silent = false

Vue.prototype.moment = moment;

//-------------------------------------------------------------------------------------

let localStorage = window.localStorage;
let access_token = localStorage.getItem('access_token');

if(access_token){
    axios.defaults.headers.common = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': access_token
    }
}

axios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (error.response.status == 401)
        router.push("/login");
    // return Promise.reject(error.message);
});

//-------------------------------------------------------------------------------------------

// Vue.prototype.$process = process.env;

new Vue({
    i18n,
    router,
    store,
    vuetify,
    render: h => h(App)
}).$mount('#app')
