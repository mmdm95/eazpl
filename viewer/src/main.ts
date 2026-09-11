import { createApp } from 'vue'
import { createPinia } from 'pinia'

import './assets/css/main.css'

import App from './App.vue'
import { applyBaseTheme } from './components/base'
import router from './router'

applyBaseTheme('light')

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
