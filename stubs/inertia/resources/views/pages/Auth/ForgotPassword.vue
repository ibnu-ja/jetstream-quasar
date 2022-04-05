<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/inertia-vue3'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { inject } from 'vue'
import { useWidth } from '@/composables/useWidth'

const route: any = inject('route')
const form = useForm({
  email: ''
})

defineProps<{status: string}>()

function submit () {
  form.post(route('password.email'))
}

const { width } = useWidth()
</script>

<template>
  <Head title="Email Verification" />
  <web-layout>
    <q-page
      class="fit column items-center content-center bg-grey-2"
      :class="$q.screen.lt.sm ? 'justify-start': 'justify-center'"
      :padding="$q.screen.gt.sm"
    >
      <jet-authentication-card-logo />
      <q-card
        :bordered="$q.screen.lt.sm"
        :flat="$q.screen.lt.sm"
        :square="$q.screen.lt.sm"
        :style="'width: ' + width"
      >
        <q-form @submit.prevent="submit">
          <q-card-section>
            <p>
              Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </p>
            <p
              v-show="status"
              class="text-positive"
            >
              {{ status }}
            </p>
            <q-input
              v-model="form.email"
              :error="!!form.errors.email"
              :error-message="form.errors.email"
              outlined
              label="Email"
              lazy-rules
            />
          </q-card-section>
          <q-card-actions align="right">
            <q-btn
              flat
              color="primary"
              label="Email password reset link"
              @click="submit"
            />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-page>
  </web-layout>
</template>
