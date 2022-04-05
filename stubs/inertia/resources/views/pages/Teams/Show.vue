<script lang="ts" setup>
import { Permission, Role, Team } from '@/scripts/types/inertia-props'
import DashboardLayout from '@/views/layouts/DashboardLayout.vue'
import DeleteTeamForm from './Partials/DeleteTeamForm.vue'
import TeamMemberManager from './Partials/TeamMemberManager.vue'
import UpdateTeamNameForm from './Partials/UpdateTeamNameForm.vue'
defineProps<{team: Team, availableRoles: Role[], permissions: Permission}>()
</script>

<template>
  <dashboard-layout title="Team Settings">
    <q-page
      padding
      class="fit column content-stretch"
    >
      <update-team-name-form
        :team="team"
        :permissions="permissions"
      />

      <q-separator class="q-my-md" />

      <team-member-manager
        :team="team"
        :available-roles="availableRoles"
        :user-permissions="permissions"
      />

      <template v-if="permissions.canDeleteTeam && ! team.personal_team">
        <q-separator class="q-my-md" />

        <delete-team-form :team="team" />
      </template>
    </q-page>
  </dashboard-layout>
</template>
