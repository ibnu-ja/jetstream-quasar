// Import icon libraries
import '@quasar/extras/roboto-font-latin-ext/roboto-font-latin-ext.css'
import '@quasar/extras/mdi-v6/mdi-v6.css'

import '@quasar/extras/animate/fadeInLeft.css'
import '@quasar/extras/animate/fadeOutRight.css'

import '@quasar/extras/animate/fadeIn.css'
import '@quasar/extras/animate/fadeOut.css'

import 'quasar/src/css/index.sass'
import { createApp, h } from 'vue'
// import { InertiaProgress } from '@inertiajs/progress'
import { Quasar } from 'quasar'
import { Ziggy } from './ziggy'
import { ZiggyVue } from 'ziggy/vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { importPageComponent } from '@/scripts/vite/import-page-component'
import quasarIconSet from 'quasar/icon-set/svg-mdi-v6'

createInertiaApp({
  resolve: (name) => importPageComponent(name, import.meta.glob('../views/pages/**/*.vue')),
  setup ({ el, app, props, plugin }) {
    createApp({ render: () => h(app, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(Quasar, {
        plugins: {}, // import Quasar plugins and add here
        iconSet: quasarIconSet
      })
      .mount(el)
  }
})
