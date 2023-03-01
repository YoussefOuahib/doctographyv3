// import "bootstrap/dist/css/bootstrap.min.css"
// import './bootstrap';
// import "bootstrap/dist/js/bootstrap.min.js"
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
import './guard'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import router from './routes/index.js'
import {createApp, onMounted} from 'vue/dist/vue.esm-bundler.js'
import useAuth from "./Composables/auth"
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'
import { abilitiesPlugin } from '@casl/vue'
import ability from './services/ability'
import i18n from './messages';



const app = createApp({
    setup() {
        const { getUser } = useAuth()
        if(JSON.parse(localStorage.getItem('loggedIn'))) {
        onMounted(getUser)
        }
    }
})

const myLightTheme = {
  dark: false,
  colors: {
      background: "#E8E8E8",
      surface: "#E8E8E8",
      primary: "#499167",
      darkPrimary: "#016e2e",
      danger: "#B00020",
      "primary-darken-1": "#3700B3",
      secondary: "#6ECCAF",
      "secondary-darken-1": "#018786",
      "grey-backg" : "#F5F5F5",
      pink : "#a60058",
      darken: "#AAAAAA",
      error: "#B00020",
      info: "#2196F3",
      success: "#4CAF50",
      warning: "#3f0252",
  },
};
const myDarkTheme = {
  dark: true,
  colors: {
      background: "#393939",
      surface: "#393939",
      primary: "#68B984",
      darkPrimary: "#07D698",
      danger: "#E97777",
      "primary-darken-1": "#3700B3",
      secondary: "#6ECCAF",
      "secondary-darken-1": "#07BBDE",
      pink : "#FD80E0",
      "grey-backg" : "#393939",
      darken: "#AAAAAA",
      error: "#ed344c",
      info: "#2196F3",
      success: "#4CAF50",
      warning: "#FED049",
  },
};


const vuetify = createVuetify({
  theme: {
    defaultTheme: localStorage.getItem('userTheme') || 'myLightTheme',
    themes: {
      myLightTheme,
      myDarkTheme,
    }
  },
  components,
  directives,
})





app.component('Bootstrap5Pagination', Bootstrap5Pagination)
app.use(abilitiesPlugin, ability)

app.use(VueSweetalert2)
app.use(router)
app.use(i18n)

app.use(vuetify).mount('#app')
