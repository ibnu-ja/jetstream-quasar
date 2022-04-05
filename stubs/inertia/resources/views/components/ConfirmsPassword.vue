
<script lang="ts" setup>
import { inject, nextTick, ref, watch } from 'vue'
import axios from 'axios'

const emit = defineEmits([
  'confirmed'
])

const route: any = inject('route')

defineProps({
  title: {
    type: String,
    default: 'Confirm Password'
  },
  content: {
    type: String,
    default: 'For your security, please confirm your password to continue.'
  },
  button: {
    type: String,
    default: 'Confirm'
  }
})
const password = ref<HTMLInputElement>()
const showP = ref(false)
const confirmingPassword = ref(false)
const form = ref({
  processing: false,
  password: ''
})
const error = ref<string>()

function startConfirmingPassword () {
  axios.get(route('password.confirmation')).then(response => {
    if (response.data.confirmed) {
      emit('confirmed')
    } else {
      confirmingPassword.value = true
      setTimeout(() => password.value?.focus(), 250)
    }
  })
}
watch(confirmingPassword, () => {
  form.value.password = ''
  error.value = undefined
})

function confirmPassword () {
  form.value.processing = true

  axios
    .post(route('password.confirm'), {
      password: form.value.password
    })
    .then(() => {
      confirmingPassword.value = false
      nextTick(() => emit('confirmed'))
    })
    .catch(e => {
      error.value = e.response.data.errors.password[0]
      nextTick(() => {
        setTimeout(() => password.value?.focus(), 250)
      })
    })
    .finally(() => (form.value.processing = false))
}
</script>

<template>
  <span>
    <span @click="startConfirmingPassword">
      <slot />
    </span>

    <q-dialog v-model="confirmingPassword">
      <q-card>
        <q-form
          @submit.prevent="confirmPassword"
        >
          <q-card-section class="q-gutter-md">
            <h6>
              {{ title }}
            </h6>
            <p>
              {{ content }}
            </p>

            <q-input
              ref="password"
              v-model="form.password"
              :error="!!error"
              :error-message="error"
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
          <q-card-actions
            class="q-gutter-md"
            align="right"
          >
            <q-btn
              flat
              label="Nevermind"
              @click="confirmingPassword = false"
            />

            <q-btn
              flat
              color="primary"
              :disabled="form.processing"
              type="submit"
            >
              {{ button }}
            </q-btn>
          </q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>
  </span>
</template>
