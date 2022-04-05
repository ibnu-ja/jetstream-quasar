
<script lang="ts" setup>import { computed, inject, ref } from 'vue'
import AppActionSection from '@/views/components/ActionSection.vue'
import AppConfirmsPassword from '@/views/components/ConfirmsPassword.vue'
import CodeBlock from '@/views/components/CodeBlock.vue'
import { Inertia } from '@inertiajs/inertia'

import { User } from '@/scripts/types/inertia-props'
import axios from 'axios'
import { usePage } from '@inertiajs/inertia-vue3'
// import { useQuasar } from 'quasar'

const enabling = ref(false)
const disabling = ref(false)
const qrCode = ref(null)
const recoveryCodes = ref([])

const route: any = inject('route')
const user = computed(() => usePage().props.value.user as User)

const twoFactorEnabled = computed(function () {
  return !enabling.value && user.value.two_factor_enabled
})

function enableTwoFactorAuthentication () {
  enabling.value = true
  Inertia.post(route('two-factor.enable'), {}, {
    preserveScroll: true,
    onSuccess: () =>
      Promise.all([showQrCode(), showRecoveryCodes()]),
    onFinish: () => (enabling.value = false)
  })
}
const codes = computed(() => {
  return recoveryCodes.value.join('\n')
})
async function showQrCode () {
  const res = await axios.get(route('two-factor.qr-code'))
  qrCode.value = res.data.svg
}
async function showRecoveryCodes () {
  const res = await axios.get(route('two-factor.recovery-codes'))
  recoveryCodes.value = res.data
}
async function regenerateRecoveryCodes () {
  await axios.post('/user/two-factor-recovery-codes')
  showRecoveryCodes()
}
function disableTwoFactorAuthentication () {
  disabling.value = true
  Inertia.delete('/user/two-factor-authentication', {
    preserveScroll: true,
    onSuccess: () => (disabling.value = false)
  })
}
</script>

<template>
  <app-action-section>
    <template #title>
      Two Factor Authentication
    </template>

    <template #description>
      Add additional security to your account using two factor authentication.
    </template>

    <template #content>
      <q-card-section class="q-gutter-md q-pt-none">
        <h6
          v-if="twoFactorEnabled"
        >
          You have enabled two factor authentication.
        </h6>
        <h6 v-else>
          You have not enabled two factor authentication.
        </h6>
        <p>
          When two factor authentication is enabled, you will be prompted for a
          secure, random token during authentication. You may retrieve this
          token from your phone's Google Authenticator application.
        </p>

        <div v-if="twoFactorEnabled">
          <div v-if="qrCode">
            <p>
              Two factor authentication is now enabled. Scan the following QR
              code using your phone's authenticator application.
            </p>

            <q-card
              flat
              class="bg-white q-my-md q-pa-md"
            >
              <!-- eslint-disable vue/no-v-html -->
              <div
                class="text-center"
                v-html="qrCode"
              />
              <!-- eslint-enable vue/no-v-html -->
            </q-card>
          </div>

          <div v-if="recoveryCodes.length > 0">
            <p class="font-semibold">
              Store these recovery codes in a secure password manager. They can
              be used to recover access to your account if your two factor
              authentication device is lost.
            </p>
            <q-card
              bordered
              flat
            >
              <code-block
                lang="text"
                :code="codes"
              />
            </q-card>
          </div>
        </div>
      </q-card-section>
    </template>
    <template #actions>
      <app-confirms-password
        v-if="!twoFactorEnabled"
        @confirmed="enableTwoFactorAuthentication"
      >
        <q-btn
          color="primary"
          flat
          label="Enable"
          :disabled="enabling"
        />
      </app-confirms-password>

      <template v-else>
        <app-confirms-password
          @confirmed="regenerateRecoveryCodes"
        >
          <q-btn
            v-if="recoveryCodes.length > 0"
            outline
            label="Regenerate Recovery Codes"
          />
        </app-confirms-password>

        <app-confirms-password
          @confirmed="showRecoveryCodes"
        >
          <q-btn
            v-if="recoveryCodes.length === 0"
            style="margin-left: 8px;"
            outline
            label="Show Recovery Codes"
          />
        </app-confirms-password>

        <app-confirms-password
          @confirmed="disableTwoFactorAuthentication"
        >
          <q-btn
            flat
            style="margin-left: 8px;"
            color="negative"
            label="Disable"
            :disabled="disabling"
          />
        </app-confirms-password>
      </template>
    </template>
  </app-action-section>
</template>
