import laravel, { callArtisan, findPhpPath } from 'vite-plugin-laravel'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'
import { defineConfig } from 'vite'
import inertia from './resources/scripts/vite/inertia-layout'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    inertia(),
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: 'resources/css/quasar-variables.scss'
    }),
    laravel({
      watch: [
        {
          condition: (file) => file.includes('routes/'),
          handle: () => callArtisan(findPhpPath(), 'ziggy:generate', './resources/scripts/ziggy.js')
        }
      ]
    })
  ]
})
