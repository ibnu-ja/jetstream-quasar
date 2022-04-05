<script lang="ts" setup>
import { computed, inject } from 'vue'
import { Link as inertiaLink, usePage } from '@inertiajs/inertia-vue3'
// import AppSettingsTheme from '@/views/layouts/settings/Theme.vue'
// import JetstreamFlash from '../components/JetstreamFlashBanner.vue'
import Progress from './Progress.vue'
import { User } from '@/scripts/types/inertia-props'
import { useQuasar } from 'quasar'
// import { storeToRefs } from 'pinia'
// import { useUserStore } from '@/scripts/store/user'
const route: any = inject('route')
const user = computed(() => usePage().props.value.user as User)
const $q = useQuasar()
// $q.dark.set(true) // set dark theme
// const { theme } = storeToRefs(useUserStore())
</script>

<template>
  <Progress />
  <q-layout view="hhh lpR fff">
    <q-header
      :bordered="!$q.dark.isActive"
      :elevated="$q.dark.isActive"
      :class="[$q.dark.isActive ? 'bg-primary text-white' : 'bg-white text-primary']"
    >
      <q-toolbar>
        <q-toolbar-title>
          <inertia-link
            href="/"
            class="nostyle cursor-pointer"
          >
            <q-avatar>
              <img :src="$q.dark.isActive ? 'https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg' : 'https://cdn.quasar.dev/logo-v2/svg/logo.svg'">
            </q-avatar>
          </inertia-link>
          <span v-show="$q.screen.gt.sm">
            Title
          </span>
        </q-toolbar-title>
        <q-space />

        <inertia-link
          v-if="user"
          href="/dashboard"
          class="nostyle"
        >
          <q-btn
            flat
            label="Dashboard"
          />
        </inertia-link>

        <template v-else>
          <inertia-link
            v-if="!route().current('login') && !route().current('register')"
            class="q-mx-xs nostyle"
            :href="route('login')"
          >
            <q-btn
              flat
              label="Login"
            />
          </inertia-link>
          <inertia-link
            v-if="!route().current('login') && !route().current('register')"
            class="q-mx-xs nostyle"
            :href="route('register')"
          >
            <q-btn
              outline
              label="Register"
            />
          </inertia-link>
          <div v-if="route().current('login') || route().current('register')">
            <div
              v-if="route().current('login')"
              transition-show="fade"
              transition-hide="fade"
            >
              Don't have an account?
              <inertia-link
                :href="route('register')"
                class="text-underline"
              >
                Sign up
              </inertia-link>
            </div>
            <div
              v-else
              key="logininertia-link"
            >
              Already have an account?
              <inertia-link :href="route('login')">
                Sign in
              </inertia-link>
            </div>
          </div>
        </template>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <slot />
    </q-page-container>

    <q-footer
      :bordered="!$q.dark.isActive"
      :elevated="$q.dark.isActive"
      :class="[$q.dark.isActive ? 'bg-primary text-white' : 'bg-white text-primary']"
    >
      <q-toolbar>
        <div class="text-center">
          2022 iji
        </div>
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>
