<script lang="ts" setup>
import { inject, ref } from 'vue'
import ActionSection from '@/views/components/ActionSection.vue'
import { Team } from '@/scripts/types/inertia-props'
import { useForm } from '@inertiajs/inertia-vue3'
const props = defineProps<{team: Team}>()
const route: any = inject('route')
const confirmingTeamDeletion = ref(false)
const form = useForm({})
function confirmTeamDeletion () {
  confirmingTeamDeletion.value = true
}
function deleteTeam () {
  form.delete(route('teams.destroy', props.team), {
    errorBag: 'deleteTeam'
  })
}
</script>

<template>
  <action-section>
    <template #title>
      Delete Team
    </template>

    <template #description>
      Permanently delete this team.
    </template>

    <template #content>
      <q-card-section class="q-gutter-md q-pt-none">
        <p>
          Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain.
        </p>
      </q-card-section>
    </template>
    <template #actions>
      <q-btn
        flat
        color="negative"
        @click="confirmTeamDeletion"
      >
        Delete Team
      </q-btn>
    </template>
  </action-section>

  <!-- Delete Team Confirmation Dialog -->
  <q-dialog v-model="confirmingTeamDeletion">
    <q-card>
      <q-card-section>
        <h6>
          Delete Team
        </h6>

        <p>
          Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted.
        </p>
      </q-card-section>
      <q-card-actions>
        <q-space />
        <q-btn
          flat
          @click="confirmingTeamDeletion = false"
        >
          Cancel
        </q-btn>
        <q-btn
          color="negative"
          flat
          :disabled="form.processing"
          @click="deleteTeam"
        >
          Delete Team
        </q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
