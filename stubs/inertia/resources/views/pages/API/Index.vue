<script lang="ts" setup>
import ApiTokenManager from './Partials/ApiTokenManager.vue'
import DashboardLayout from '@/views/layouts/DashboardLayout.vue'
import { Token } from '@/scripts/types/inertia-props'
defineProps<{tokens: Token[], availablePermissions: string[], defaultPermissions: string[] }>()
</script>

<template>
  <dashboard-layout title="API Tokens">
    <q-page
      padding
      class="fit column content-stretch"
    >
      <api-token-manager
        :tokens="tokens"
        :available-permissions="availablePermissions"
        :default-permissions="defaultPermissions"
      />
    </q-page>
  </dashboard-layout>
</template>
