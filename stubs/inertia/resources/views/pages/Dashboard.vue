<template>
  <dashboard-layout :title="'Dashboard'">
    <q-page
      :padding="$q.screen.gt.sm"
    >
      <welcome />
    </q-page>
  </dashboard-layout>
</template>

<script lang="ts" setup>
import DashboardLayout from '@/views/layouts/DashboardLayout.vue'
import Welcome from '@/views/components/Welcome.vue'
</script>
