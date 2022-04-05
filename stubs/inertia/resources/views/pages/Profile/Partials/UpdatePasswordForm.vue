
<script lang="ts" setup>
import { inject, ref } from 'vue'
import FormSection from '@/views/components/FormSection.vue'
import { useForm } from '@inertiajs/inertia-vue3'

const route: any = inject('route')
const showCurrentPassword = ref(true)
const showPassword = ref(true)
const showPasswordConfirmation = ref(true)

const password = ref<HTMLInputElement>()
const currentPassword = ref<HTMLInputElement>()

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

function updatePassword () {
  form.put(route('user-password.update'), {
    errorBag: 'updatePassword',
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation')
        password.value?.focus()
      }
      if (form.errors.current_password) {
        form.reset('current_password')
        currentPassword.value?.focus()
      }
    }
  })
}
</script>

<template>
  <form-section @submitted="updatePassword">
    <template #title>
      Update Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #form>
      <q-card-section class="q-gutter-md q-pt-none">
        <q-input
          ref="currentPassword"
          v-model="form.current_password"
          label="Current Password"
          outlined
          autocomplete="password"
          :error="!!form.errors.current_password"
          :error-message="form.errors.current_password"
          :type="showCurrentPassword? 'password' : 'text'"
        >
          <template #append>
            <q-icon
              :name="showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'"
              class="cursor-pointer"
              @click="showCurrentPassword = !showCurrentPassword"
            />
          </template>
        </q-input>

        <q-input
          ref="currentPassword"
          v-model="form.password"
          label="New Password"
          outlined
          autocomplete="new-password"
          :error="!!form.errors.password"
          :error-message="form.errors.password"
          :type="showPassword? 'password' : 'text'"
        >
          <template #append>
            <q-icon
              :name="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              class="cursor-pointer"
              @click="showPassword = !showPassword"
            />
          </template>
        </q-input>

        <q-input
          v-model="form.password_confirmation"
          label="Confirm Password"
          outlined
          autocomplete="new-password"
          :error="!!form.errors.password_confirmation"
          :error-message="form.errors.password_confirmation"
          :type="showPasswordConfirmation? 'password' : 'text'"
        >
          <template #append>
            <q-icon
              :name="showPasswordConfirmation ? 'mdi-eye' : 'mdi-eye-off'"
              class="cursor-pointer"
              @click="showPasswordConfirmation = !showPasswordConfirmation"
            />
          </template>
        </q-input>
      </q-card-section>
    </template>

    <template #actions>
      <transition
        enter-active-class="animated fadeInLeft"
        leave-active-class="animated fadeOutRight"
      >
        <div
          v-show="form.recentlySuccessful"
        >
          Saved.
        </div>
      </transition>

      <q-space />

      <q-btn
        flat
        color="primary"
        :disabled="form.processing"
        type="submit"
      >
        Save
      </q-btn>
    </template>
  </form-section>
</template>
