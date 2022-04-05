<script lang="ts" setup>
import { computed, inject } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import FormSection from '@/views/components/FormSection.vue'
import { User } from '@/scripts/types/inertia-props'

const user = computed(() => usePage().props.value.user as User)

const route: any = inject('route')
const form = useForm({
  name: ''
})

function createTeam () {
  form.post(route('teams.store'), {
    errorBag: 'createTeam',
    preserveScroll: true
  })
}
</script>

<template>
  <form-section @submitted="createTeam">
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
                :src="user.profile_photo_url"
                :alt="user.name"
              />
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ user.name }}</q-item-label>
            <q-item-label caption>
              {{ user.email }}
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
      <q-card-section>
        <q-input
          v-model="form.name"
          outlined
          :error-message="form.errors.name"
          :error="!!form.errors.name"
          label="Team name"
        />
      </q-card-section>
    </template>

    <template #actions>
      <q-space />
      <q-btn
        color="primary"
        flat
        :disabled="form.processing"
        type="submit"
      >
        Create
      </q-btn>
    </template>
  </form-section>
</template>
