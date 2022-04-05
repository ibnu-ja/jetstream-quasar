<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/inertia-vue3'
import { inject, ref } from 'vue'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useWidth } from '@/composables/useWidth'

const route: any = inject('route')

const props = defineProps<{
  email: string
  token: string
}>()

const showP = ref(true)
const showCP = ref(true)
const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

function submit () {
  form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}
const { width } = useWidth()
</script>

<template>
  <Head title="Reset Password" />
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
            <h6 class="text-center">
              Reset Password
            </h6>
            <q-input
              v-model="form.email"
              :error="!!form.errors.email"
              :error-message="form.errors.email"
              outlined
              label="Email"
              lazy-rules
            />

            <q-input
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

            <q-input
              v-model="form.password_confirmation"
              :error="!!form.errors.password_confirmation"
              :error-message="form.errors.password_confirmation"
              :type="showCP? 'password' : 'text'"
              outlined
              label="Password confirmation"
              lazy-rules
            >
              <template #append>
                <q-icon
                  :name="showCP ? 'mdi-eye' : 'mdi-eye-off'"
                  class="cursor-pointer"
                  @click="showCP = !showCP"
                />
              </template>
            </q-input>
          </q-card-section>
          <q-card-actions align="right">
            <q-btn
              flat
              color="primary"
              label="Reset Password"
              type="submit"
            />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-page>
  </web-layout>
</template>
