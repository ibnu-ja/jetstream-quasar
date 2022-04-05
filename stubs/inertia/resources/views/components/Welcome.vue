<script lang="ts" setup>
import JetApplicationLogo from '@/views/components/ApplicationLogo.vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const widthSize = () => {
  if ($q.screen.gt.sm) return '50%'
  else return '100%'
}
</script>

<template>
  <q-card>
    <q-card-section :class="{'q-px-xl': $q.screen.gt.sm}">
      <jet-application-logo
        style="width: auto; height: 3rem;"
        class="q-mb-md"
      />
      <h3>
        Welcome to your Jetstream application!
      </h3>
      <p>
        Laravel Jetstream provides a beautiful, robust starting point for your next Laravel application. Laravel is designed to help you build your application using a development environment that is simple, powerful, and enjoyable. We believe you should love expressing your creativity through programming, so we have spent time carefully crafting the Laravel ecosystem to be a breath of fresh air. We hope you love it.
      </p>
    </q-card-section>
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
                    class="nostyle cursor-pointer"
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

          <q-card-actions>
            <q-btn
              href="https://laravel.com/docs"
              target="_blank"
              flat
              color="primary"
              icon-right="mdi-arrow-right"
            >
              Explore the documentation
            </q-btn>
          </q-card-actions>
        </div>

        <q-separator :vertical="$q.screen.gt.sm" />

        <div :style="{width: widthSize()}">
          <q-item>
            <q-item-section avatar>
              <q-icon name="mdi-book-open-blank-variant" />
            </q-item-section>

            <q-item-section>
              <q-item-label>
                <h6 class="no-margin">
                  <a
                    href="https://laracasts.com/"
                    class="nostyle cursor-pointer"
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

          <q-card-actions>
            <q-btn
              href="https://laracasts.com/"
              target="_blank"
              flat
              color="primary"
              icon-right="mdi-arrow-right"
            >
              Start watching Laracasts
            </q-btn>
          </q-card-actions>
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
              <q-avatar>
                <img :src="$q.dark.isActive ? 'https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg' : 'https://cdn.quasar.dev/logo-v2/svg/logo.svg'">
              </q-avatar>
            </q-item-section>

            <q-item-section>
              <q-item-label>
                <h6 class="no-margin">
                  <a
                    href="http://quasar.dev/"
                    class="nostyle cursor-pointer"
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

        <q-separator :vertical="$q.screen.gt.sm" />

        <div :style="{width: widthSize()}">
          <q-item>
            <q-item-section avatar>
              <q-icon name="mdi-book-open-blank-variant" />
            </q-item-section>

            <q-item-section>
              <q-item-label>
                <h6 class="no-margin">
                  Authentication
                </h6>
              </q-item-label>
            </q-item-section>
          </q-item>
          <q-card-section class="q-pt-none">
            Authentication and registration views are included with Laravel Jetstream, as well as support for user email verification and resetting forgotten passwords. So, you're free to get started what matters most: building your application.
          </q-card-section>
        </div>
      </q-card-section>
    </q-card>
  </q-card>
</template>
