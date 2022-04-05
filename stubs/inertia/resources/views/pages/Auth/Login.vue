
<script lang="ts" setup>
import { Head, Link as inertiaLink, useForm } from '@inertiajs/inertia-vue3'
import { inject, ref } from 'vue'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useQuasar } from 'quasar'
import { useWidth } from '@/composables/useWidth'

defineProps<{canResetPassword: boolean, status: string}>()

const route: any = inject('route')

route().current('login')

// const { xs } = useDisplay()
const form = useForm({
  email: '',
  password: '',
  remember: ''
})

const showP = ref(true)

function submit () {
  form.post(route('login'), {
    onFinish: () => form.reset('password')
  })
}

const $q = useQuasar()

const { width } = useWidth()

</script>

<template>
  <Head title="Login" />
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
          <q-card-section class="q-gutter-md">
            <h6 class="text-center">
              Login
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

            <q-checkbox
              v-model="form.remember"
              label="Remember me"
              true-value="on"
              false-value=""
            />
          </q-card-section>
          <q-separator />
          <q-card-actions align="right">
            <inertia-link
              v-if="canResetPassword"
              class="q-mx-md"
              :href="route('password.request')"
            >
              Forgot your password?
            </inertia-link>
            <q-btn
              color="primary"
              flat
              type="submit"
            >
              Login
            </q-btn>
          </q-card-actions>
        </q-form>
      </q-card>
    </q-page>
  </web-layout>
</template>
