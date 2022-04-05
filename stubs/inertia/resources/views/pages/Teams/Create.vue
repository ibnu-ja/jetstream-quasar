<script lang="ts" setup>
import CreateTeamForm from './Partials/CreateTeamForm.vue'
import DashboardLayout from '@/views/layouts/DashboardLayout.vue'
</script>

<template>
  <dashboard-layout title="Create Team">
    <q-page
      padding
      class="fit column content-stretch"
    >
      <create-team-form />
    </q-page>
  </dashboard-layout>
</template>
