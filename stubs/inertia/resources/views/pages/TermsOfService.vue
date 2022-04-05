
<script lang="ts" setup>
import { Head } from '@inertiajs/inertia-vue3'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { inject } from 'vue'
import { useQuasar } from 'quasar'

defineProps<{terms: string}>()

const route: any = inject('route')

route().current('login')

const $q = useQuasar()

</script>

<template>
  <Head title="Login" />
  <web-layout>
    <q-page
      class="fit column items-center content-center bg-grey-2 justify-start"
      :padding="$q.screen.gt.sm"
    >
      <jet-authentication-card-logo />

      <div v-html="terms" />
    </q-page>
  </web-layout>
</template>
