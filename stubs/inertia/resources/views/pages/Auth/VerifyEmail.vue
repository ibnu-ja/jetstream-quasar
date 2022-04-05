<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/inertia-vue3'
import { computed, inject } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useWidth } from '@/composables/useWidth'

const route: any = inject('route')
const form = useForm({})
const props = defineProps<{status: string}>()

function submit () {
  form.post(route('verification.send'))
}

function logout () {
  Inertia.post(route('logout'))
}

const verificationLinkSent = computed(() => {
  return props.status === 'verification-link-sent'
})

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
        <q-card-section>
          <h6 class="text-center">
            Email Verification
          </h6>
          <p> Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
          <p
            v-show="verificationLinkSent"
            class="text-positive"
          >
            A new verification link has been sent to the email address you provided during registration.
          </p>
        </q-card-section>
        <q-card-actions>
          <q-btn
            flat
            color="primary"
            label="Resend Verification Email"
            @click="submit"
          />
          <q-space />
          <a
            href="#"
            @click.prevent="logout"
          >
            Logout
          </a>
        </q-card-actions>
      </q-card>
    </q-page>
  </web-layout>
</template>
