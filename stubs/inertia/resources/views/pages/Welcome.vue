<script lang="ts" setup>
import { Head } from '@inertiajs/inertia-vue3'
import LaravelLogo from '@/views/components/LaravelLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useQuasar } from 'quasar'
defineProps<{canLogin: boolean, canRegister: boolean, laravelVersion: string, phpVersion: string}>()

const $q = useQuasar()
const widthSize = () => {
  if ($q.screen.gt.sm) return '50%'
  else return '100%'
}

</script>

<template>
  <Head title="Welcome" />
  <web-layout>
    <q-page
      :padding="$q.screen.gt.sm"
      :class="{'q-px-xl q-mx-lg' : $q.screen.gt.sm}"
    >
      <laravel-logo
        class="q-my-lg q-mx-md"
        style="width: auto; height: 5rem;"
      />
      <q-card
        flat
        bordered
      >
        <q-card-section
          :class="{'no-padding': $q.screen.lt.sm}"
          :horizontal="$q.screen.gt.sm"
        >
          <div :style="{width: widthSize()}">
            <q-item>
              <q-item-section avatar>
                <q-icon name="mdi-book-open-blank-variant" />
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  <h6 class="no-margin">
                    <a
                      href="https://laravel.com/docs"
                      class="nostyle cursor-pointer text-underline"
                      target="_blank"
                      rel="noopener noreferrer"
                    >Documentation
                    </a>
                  </h6>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-card-section class="q-pt-none">
              Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading all of the documentation from beginning to end.
            </q-card-section>
          </div>

          <q-separator :vertical="$q.screen.gt.sm" />

          <div :style="{width: widthSize()}">
            <q-item>
              <q-item-section avatar>
                <q-icon name="mdi-camera" />
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  <h6 class="no-margin">
                    <a
                      href="https://laracasts.com/"
                      class="nostyle cursor-pointer text-underline"
                      target="_blank"
                      rel="noopener noreferrer"
                    >Laracasts
                    </a>
                  </h6>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-card-section class="q-pt-none">
              Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.
            </q-card-section>
          </div>
        </q-card-section>
        <q-separator />
        <q-card-section
          :class="{'no-padding': $q.screen.lt.sm}"
          :horizontal="$q.screen.gt.sm"
        >
          <div :style="{width: widthSize()}">
            <q-item>
              <q-item-section avatar>
                <q-icon name="mdi-newspaper" />
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  <h6 class="no-margin">
                    <a
                      href="https://laravel-news.com/"
                      target="_blank"
                      class="nostyle cursor-pointer text-underline"
                    >Laravel News</a>
                  </h6>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-card-section class="q-pt-none">
              Laravel News is a community driven portal and newsletter aggregating all of the latest and most important news in the Laravel ecosystem, including new package releases and tutorials.
            </q-card-section>
          </div>

          <q-separator :vertical="$q.screen.gt.sm" />

          <div :style="{width: widthSize()}">
            <q-item>
              <q-item-section avatar>
                <q-avatar>
                  <img :src="$q.dark.isActive ? 'https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg' : 'https://cdn.quasar.dev/logo-v2/svg/logo.svg'">
                </q-avatar>
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  <h6 class="no-margin">
                    <a
                      href="http://quasar.dev/"
                      class="nostyle cursor-pointer text-underline"
                      target="_blank"
                      rel="noopener noreferrer"
                    >Quasar
                    </a>
                  </h6>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-card-section class="q-pt-none">
              Laravel Jetstream using Quasar Framework, open source framework for building web applications. Build high performance & high quality application with <strong>Vue.js 3</strong>.
            </q-card-section>
          </div>
        </q-card-section>
      </q-card>

      <p class="text-right">
        Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
      </p>
    </q-page>
  </web-layout>
</template>
