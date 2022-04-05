<script lang="ts" setup>
import { Head, useForm } from '@inertiajs/inertia-vue3'
import { inject, ref } from 'vue'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
// import ValidationErrors from '@/views/components/ValidationErrors.vue'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useQuasar } from 'quasar'
import { useWidth } from '@/composables/useWidth'

const route: any = inject('route')
const recovery = ref(false)
const code = ref(null)
const form = useForm({
  code: '',
  recovery_code: ''
})

const $q = useQuasar()

const { width } = useWidth()

const recoveryCode = ref(null)

function toggleRecovery () {
  recovery.value = recovery.value !== true
  form.clearErrors()
  form.reset()
}

function submit () {
  form.post(route('two-factor.login'))
}
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
          <q-card-section>
            <h6>
              Two Factor authentication
            </h6>
            <template v-if="!recovery">
              <p>
                Please confirm access to your account by entering the authentication code provided by your authenticator application.
              </p>
              <q-input
                ref="code"
                v-model="form.code"
                :error="!!form.errors.code"
                :error-message="form.errors.code"
                type="text"
                inputmode="numeric"
                autofocus
                autocomplete="one-time-code"
                outlined
                label="Code"
                required
              />
            </template>

            <template v-else>
              <p>
                Please confirm access to your account by entering one of your emergency recovery codes.
              </p>
              <q-input
                ref="recoveryCode"
                v-model="form.recovery_code"
                :error="!!form.errors.code"
                :error-message="form.errors.code"
                type="text"
                inputmode="numeric"
                autofocus
                autocomplete="one-time-code"
                outlined
                label="Recovery Code"
                required
              />
            </template>
          </q-card-section>
          <q-card-actions>
            <a
              href="#"
              @click.prevent="toggleRecovery"
              v-text="
                !recovery ? 'Use recovery code' : 'Use authenticator code'
              "
            />
            <q-space />
            <q-btn
              type="submit"
              color="primary"
              flat
              label="Sign In"
              :disabled="form.processing"
            />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-page>
  </web-layout>
</template>
