<script lang="ts" setup>
import FormSection from '@/views/components/FormSection.vue'
import { inject } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
const route: any = inject('route')
// eslint-disable-next-line vue/require-prop-types
const props = defineProps(['team', 'permissions'])
const form = useForm({
  name: props.team.name
})

function updateTeamName () {
  form.put(route('teams.update', props.team), {
    errorBag: 'updateTeamName',
    preserveScroll: true
  })
}
</script>

<template>
  <form-section @submitted="updateTeamName">
    <template #title>
      Team Details
    </template>

    <template #description>
      Create a new team to collaborate with others on projects.
    </template>

    <template #form>
      <q-list>
        <q-item-label
          header
          class="q-py-none"
        >
          Team Owner
        </q-item-label>
        <q-item>
          <q-item-section avatar>
            <q-avatar
              size="30"
            >
              <q-img
                :src="team.owner.profile_photo_url"
                :alt="team.owner.name"
              />
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ team.owner.name }}</q-item-label>
            <q-item-label caption>
              {{ team.owner.email }}
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
      <q-card-section>
        <q-input
          v-model="form.name"
          outlined
          :error-message="form.errors.name"
          :disabled="!permissions.canUpdateTeam"
          :error="!!form.errors.name"
          label="Team name"
        />
      </q-card-section>
    </template>

    <template
      v-if="permissions.canUpdateTeam"
      #actions
    >
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
        color="primary"
        flat
        :disabled="form.processing"
        type="submit"
      >
        Save
      </q-btn>
    </template>
  </form-section>
</template>
