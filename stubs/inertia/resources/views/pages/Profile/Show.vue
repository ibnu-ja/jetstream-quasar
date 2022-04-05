<script lang="ts" setup>
import { Jetstream, Session, User } from '@/scripts/types/inertia-props'
import DashboardLayout from '@/views/layouts/DashboardLayout.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import LogoutOtherBrowserSessionsForm from './Partials/LogoutOtherBrowserSessionsForm.vue'
import TwoFactorAuthenticationForm from './Partials/TwoFactorAuthenticationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
defineProps<{sessions: Session[]}>()
const user = computed(() => usePage().props.value.user as User)
const jetstream = computed(() => usePage().props.value.jetstream as Jetstream)
</script>

<template>
  <dashboard-layout title="Profile">
    <q-page
      padding
      class="fit column content-stretch"
    >
      <div v-if="jetstream.canUpdateProfileInformation">
        <update-profile-information-form :user="user" />

        <q-separator class="q-my-md" />
      </div>

      <div v-if="jetstream.canUpdatePassword">
        <update-password-form />

        <q-separator class="q-my-md" />
      </div>
      <div v-if="jetstream.canManageTwoFactorAuthentication">
        <two-factor-authentication-form />

        <q-separator class="q-my-md" />
      </div>

      <logout-other-browser-sessions-form
        :sessions="sessions"
      />
      <template v-if="jetstream.hasAccountDeletionFeatures">
        <q-separator class="q-my-md" />

        <delete-user-form />
      </template>
    </q-page>
  </dashboard-layout>
</template>
