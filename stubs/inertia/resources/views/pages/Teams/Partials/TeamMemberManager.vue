<script lang="ts" setup>
import { Invitation, Permission, Role, Team, User } from '@/scripts/types/inertia-props'
import { computed, inject, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import ActionSection from '@/views/components/ActionSection.vue'
import FormSection from '@/views/components/FormSection.vue'
import { Inertia } from '@inertiajs/inertia'
const props = defineProps<{team: Team, availableRoles: Role[], userPermissions: Permission}>()
const route: any = inject('route')
const currentUser = computed(() => usePage().props.value.user as User)
const addTeamMemberForm = useForm({
  email: '',
  role: props.availableRoles[0].key
})
const updateRoleForm = useForm({
  role: props.availableRoles[0].key
})
const leaveTeamForm = useForm({})
const removeTeamMemberForm = useForm({})
const currentlyManagingRole = ref(false)
const managingRoleFor = ref<User | null>(null)
const confirmingLeavingTeam = ref(false)
const teamMemberBeingRemoved = ref<boolean | User>(false)
const confirmingRemovingTeamMember = ref(false)
function addTeamMember () {
  addTeamMemberForm.post(route('team-members.store', props.team), {
    errorBag: 'addTeamMember',
    preserveScroll: true,
    onSuccess: () => addTeamMemberForm.reset()
  })
}
function cancelTeamInvitation (invitation: Invitation) {
  Inertia.delete(route('team-invitations.destroy', invitation), {
    preserveScroll: true
  })
}
function manageRole (teamMember: User) {
  managingRoleFor.value = teamMember
  updateRoleForm.role = teamMember.membership!.role
  currentlyManagingRole.value = true
}
function updateRole () {
  updateRoleForm.put(route('team-members.update', [props.team, managingRoleFor.value]), {
    preserveScroll: true,
    onSuccess: () => (currentlyManagingRole.value = false)
  })
}
function confirmLeavingTeam () {
  confirmingLeavingTeam.value = true
}
function leaveTeam () {
  leaveTeamForm.delete(route('team-members.destroy', [props.team, currentUser]))
}
function confirmTeamMemberRemoval (teamMember: User) {
  confirmingRemovingTeamMember.value = true
  teamMemberBeingRemoved.value = teamMember
}
function removeTeamMember () {
  removeTeamMemberForm.delete(route('team-members.destroy', [props.team, teamMemberBeingRemoved.value]), {
    errorBag: 'removeTeamMember',
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => (teamMemberBeingRemoved.value = false)
  })
}
function displayableRole (role: string) : string {
  return props.availableRoles.find(r => r.key === role)!.name
}
</script>

<template>
  <div v-if="userPermissions.canAddTeamMembers">
    <!-- Add Team Member -->
    <form-section @submitted="addTeamMember">
      <template #title>
        Add Team Member
      </template>

      <template #description>
        Add a new team member to your team, allowing them to collaborate with you.
      </template>

      <template #form>
        <q-card-section class="q-gutter-md q-pt-none q-pb-none">
          <p>
            Please provide the email address of the person you would like to add to this team.
          </p>

          <q-input
            v-model="addTeamMemberForm.email"
            :error-message="addTeamMemberForm.errors.email"
            :error="!!addTeamMemberForm.errors.email"
            outlined
            label="Email"
          />

          <q-item-label
            header
            class="q-pa-sm q-px-md"
          >
            Role
          </q-item-label>
        </q-card-section>
        <q-list>
          <q-item
            v-for="(role) in availableRoles"
            :key="role.key"
            v-ripple
            tag="label"
          >
            <q-item-section
              side
              top
            >
              <q-radio
                v-model="addTeamMemberForm.role"
                :val="role.key"
              />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ role.name }}</q-item-label>
              <q-item-label caption>
                {{ role.description }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </template>

      <template #actions>
        <transition
          enter-active-class="animated fadeInLeft"
          leave-active-class="animated fadeOutRight"
        >
          <div v-show="addTeamMemberForm.recentlySuccessful">
            Saved.
          </div>
        </transition>
        <q-space />

        <q-btn
          flat
          color="primary"
          type="submit"
          :disabled="addTeamMemberForm.processing"
        >
          Add
        </q-btn>
      </template>
    </form-section>
  </div>

  <div v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers">
    <q-separator class="q-my-md" />

    <!-- Team Member Invitations -->
    <action-section>
      <template #title>
        Pending Team Invitations
      </template>

      <template #description>
        These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation.
      </template>

      <!-- Pending Team Member Invitation List -->
      <template #content>
        <q-list
          dense
          class="q-pb-md"
        >
          <q-item
            v-for="invitation in team.team_invitations"
            :key="invitation.id"
          >
            <q-item-section>
              <q-item-label v-text="invitation.email" />
            </q-item-section>
            <q-item-section
              top
              side
            >
              <q-btn
                v-if="userPermissions.canRemoveTeamMembers"
                color="negative"
                flat
                @click="cancelTeamInvitation(invitation)"
              >
                Cancel
              </q-btn>
            </q-item-section>
          </q-item>
        </q-list>
      </template>
    </action-section>
  </div>

  <div v-if="team.users.length > 0">
    <q-separator class="q-my-md" />

    <!-- Manage Team Members -->
    <action-section>
      <template #title>
        Team Members
      </template>

      <template #description>
        All of the people that are part of this team.
      </template>

      <!-- Team Member List -->
      <template #content>
        <q-list
          dense
          class="q-pb-md"
        >
          <q-item
            v-for="user in team.users"
            :key="user.id"
          >
            <q-item-section avatar>
              <q-avatar>
                <q-img
                  :src="user.profile_photo_url"
                  :alt="user.name"
                />
              </q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label v-text="user.name" />
            </q-item-section>
            <q-item-section
              side
              top
            >
              <div>
                <q-btn
                  v-if="userPermissions.canAddTeamMembers && availableRoles.length"
                  flat
                  :label="displayableRole(user.membership!.role)"
                  @click="manageRole(user)"
                />
                <template
                  v-else-if="availableRoles.length"
                >
                  {{ displayableRole(user.membership!.role) }}
                </template>
                <q-btn
                  v-if="currentUser.id === user.id"
                  class="d-none d-sm-flex mx-1 text-capitalize"
                  color="negative"
                  flat
                  @click="confirmLeavingTeam"
                >
                  Leave
                </q-btn>
                <q-btn
                  v-if="userPermissions.canRemoveTeamMembers"
                  color="negative"
                  flat
                  @click="confirmTeamMemberRemoval(user)"
                >
                  Remove
                </q-btn>
              </div>
            </q-item-section>
          </q-item>
        </q-list>
      </template>
    </action-section>
  </div>

  <!-- Role Management Dialog -->
  <q-dialog
    v-model="currentlyManagingRole"
  >
    <q-card>
      <q-card-section>
        <h6>
          Manage Role
        </h6>
      </q-card-section>
      <q-list dense>
        <q-list>
          <q-item
            v-for="(role) in availableRoles"
            :key="role.key"
            v-ripple
            tag="label"
          >
            <q-item-section
              side
              top
            >
              <q-radio
                v-model="updateRoleForm.role"
                :val="role.key"
              />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ role.name }}</q-item-label>
              <q-item-label caption>
                {{ role.description }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-list>
      <q-card-actions>
        <q-space />
        <q-btn
          flat
          @click="currentlyManagingRole = false"
        >
          Cancel
        </q-btn>
        <q-btn
          color="primary"
          flat
          :disabled="updateRoleForm.processing"
          @click="updateRole"
        >
          Save
        </q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
  <!-- Leave Team Confirmation Modal -->
  <q-dialog
    v-model="confirmingLeavingTeam"
  >
    <q-card>
      <q-card-section>
        <h6>
          Leave Team
        </h6>
        <p>
          Are you sure you would like to leave this team?
        </p>
      </q-card-section>
      <q-card-actions>
        <q-space />
        <q-btn
          flat
          @click="confirmingLeavingTeam = false"
        >
          Cancel
        </q-btn>
        <q-btn
          color="negative"
          flat
          :disabled="leaveTeamForm.processing"
          @click="leaveTeam"
        >
          Leave
        </q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
  <!-- Remove Team Member Confirmation Modal -->
  <q-dialog
    v-model="confirmingRemovingTeamMember"
  >
    <q-card>
      <q-card-section>
        <h6>
          Remove Team Member
        </h6>
        <p>
          Are you sure you would like to remove this person from the team?
        </p>
      </q-card-section>
      <q-card-actions>
        <q-space />
        <q-btn
          flat
          @click="confirmingRemovingTeamMember = false"
        >
          Cancel
        </q-btn>
        <q-btn
          color="negative"
          flat
          :disabled="removeTeamMemberForm.processing"
          @click="removeTeamMember"
        >
          Remove
        </q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
