<script lang="ts" setup>
import { inject, ref } from 'vue'
import ActionSection from '@/views/components/ActionSection.vue'
import { useForm } from '@inertiajs/inertia-vue3'
const route: any = inject('route')
const confirmingUserDeletion = ref(false)
const form = useForm({
  password: ''
})
const password = ref<HTMLInputElement>()
const showP = ref(false)
function confirmUserDeletion () {
  confirmingUserDeletion.value = true
  setTimeout(() => password.value?.focus(), 300)
}
function deleteUser () {
  form.delete(route('current-user.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => password.value?.focus(),
    onFinish: () => form.reset()
  })
}
function closeModal () {
  confirmingUserDeletion.value = false
  form.reset()
}
// defineExpose(password)
</script>

<template>
  <action-section>
    <template #title>
      Delete Account
    </template>

    <template #description>
      Permanently delete your account.
    </template>

    <template #content>
      <q-card-section class="q-gutter-md q-pt-none">
        <p>
          Once your account is deleted, all of its resources and data will be
          permanently deleted. Before deleting your account, please download any
          data or information that you wish to retain.
        </p>
      </q-card-section>
    </template>
    <template #actions>
      <!-- TODO add max width to dialog -->
      <q-dialog v-model="confirmingUserDeletion">
        <q-card>
          <q-form @submit.prevent="deleteUser">
            <q-card-section class="q-gutter-md">
              <h6>
                Delete Account
              </h6>
              <p>
                Are you sure you want to delete your account? Once your account is
                deleted, all of its resources and data will be permanently deleted.
                Please enter your password to confirm you would like to permanently
                delete your account.
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

            <q-card-actions>
              <q-space />
              <q-btn
                outlined
                flat
                label="Nevermind"
                @click="closeModal"
              />

              <q-btn
                color="negative"
                :disabled="form.processing"
                type="submit"
                flat
                label="Delete Account"
              />
            </q-card-actions>
          </q-form>
        </q-card>
      </q-dialog>
      <q-btn
        color="negative"
        flat
        label="Delete Account"
        @click="confirmUserDeletion"
      />
    </template>
  </action-section>
</template>
