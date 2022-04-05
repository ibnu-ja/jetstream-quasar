
<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/inertia-vue3'
import { inject, ref } from 'vue'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useWidth } from '@/composables/useWidth'

const route: any = inject('route')

const showP = ref(true)

const form = useForm({
  password: ''
})
const password = ref<HTMLInputElement>()

function submit () {
  form.post(route('password.confirm'), {
    onFinish: () => {
      form.reset()
    },
    onError: () => {
      password.value?.focus()
    }
  })
}

const { width } = useWidth()
</script>

<template>
  <web-layout>
    <Head title="Secure Area" />
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
          <q-card-section class="q-gutter-md">
            <p>
              This is a secure area of the application. Please confirm your
              password before continuing.
            </p>

            <q-input
              ref="password"
              v-model="form.password"
              :error="!!form.errors.password"
              :error-message="form.errors.password"
              :type="showP? 'password' : 'text'"
              outlined
              label="Password"
              lazy-rules
            >
              <template #append>
                <q-icon
                  :name="showP ? 'mdi-eye' : 'mdi-eye-off'"
                  class="cursor-pointer"
                  @click="showP = !showP"
                />
              </template>
            </q-input>
          </q-card-section>
          <q-separator />
          <q-card-actions align="right">
            <q-btn
              color="primary"
              flat
              type="submit"
            >
              Confirm
            </q-btn>
          </q-card-actions>
        </q-form>
      </q-card>
    </q-page>
  </web-layout>
</template>
