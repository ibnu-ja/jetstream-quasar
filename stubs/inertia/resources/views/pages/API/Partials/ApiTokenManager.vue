<script lang="ts" setup>
import { Jetstream, Token } from '@/scripts/types/inertia-props'
import { computed, inject, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import ActionSection from '@/views/components/ActionSection.vue'
import CodeBlock from '@/views/components/CodeBlock.vue'
import FormSection from '@/views/components/FormSection.vue'

const props = defineProps<{tokens: Token[], availablePermissions: string[], defaultPermissions: string[] }>()
const route : any = inject('route')
const jetstream = computed(() => usePage().props.value.jetstream as Jetstream)
const createApiTokenForm = useForm({
  name: '',
  permissions: props.defaultPermissions
})
const updateApiTokenForm = useForm({
  permissions: [] as string[]
})

const deleteApiTokenForm = useForm({})
const displayingToken = ref(false)
const managingPermissionsFor = ref<Token | null>(null)
const showManagingPermissionModal = ref(false)
const apiTokenBeingDeleted = ref<Token | null>(null)
const showApiTokenDeletionModal = ref(false)

function createApiToken () {
  createApiTokenForm.post(route('api-tokens.store'), {
    preserveScroll: true,
    onSuccess: () => {
      displayingToken.value = true
      createApiTokenForm.reset()
    }
  })
}
function manageApiTokenPermissions (token: Token) {
  showManagingPermissionModal.value = true
  updateApiTokenForm.permissions = token.abilities
  managingPermissionsFor.value = token
}
function updateApiToken () {
  updateApiTokenForm.put(route('api-tokens.update', managingPermissionsFor.value), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      managingPermissionsFor.value = null
      showManagingPermissionModal.value = false
    }
  })
}
function confirmApiTokenDeletion (token: Token) {
  showApiTokenDeletionModal.value = true
  apiTokenBeingDeleted.value = token
}
function deleteApiToken () {
  deleteApiTokenForm.delete(route('api-tokens.destroy', apiTokenBeingDeleted.value), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      apiTokenBeingDeleted.value = null
      showApiTokenDeletionModal.value = false
    }
  })
}
</script>

<template>
  <form-section @submitted="createApiToken">
    <template #title>
      Create API Token
    </template>

    <template #description>
      API tokens allow third-party services to authenticate with our application on your behalf.
    </template>

    <template #form>
      <!-- Token Name -->
      <q-card-section class="q-gutter-md q-pt-none">
        <q-input
          v-model="createApiTokenForm.name"
          label="Name"
          type="text"
          outlined
          autocomplete="name"
          :error-message="createApiTokenForm.errors.name"
        />
        <!-- Token Permissions -->
        <q-checkbox
          v-for="(permission, key) in props.availablePermissions"
          :key="key"
          v-model="createApiTokenForm.permissions"
          :label="permission"
          :val="permission"
          hide-details
        />
      </q-card-section>
    </template>
    <template #actions>
      <transition
        leave-active-class="transition ease-in duration-1000"
        leave-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-show="createApiTokenForm.recentlySuccessful"
          class="text-sm text-gray-600"
        >
          Created.
        </div>
      </transition>
      <q-space />
      <q-btn
        flat
        :disabled="createApiTokenForm.processing"
        type="submit"
        color="primary"
      >
        Create
      </q-btn>
    </template>
  </form-section>

  <template v-if="tokens.length > 0">
    <q-separator class="q-my-md" />

    <action-section>
      <template #title>
        Manage API Tokens
      </template>

      <template #description>
        You may delete any of your existing tokens if they are no longer needed.
      </template>

      <template #content>
        <q-list dense>
          <q-item
            v-for="token in tokens"
            :key="token.id"
          >
            <q-item-section>
              <q-item-label v-text="token.name" />
            </q-item-section>
            <q-item-section side>
              <div class="q-gutter-sm">
                <q-btn
                  v-if="availablePermissions.length > 0"
                  color="primary"
                  flat
                  @click.prevent="manageApiTokenPermissions(token)"
                >
                  Permissions
                </q-btn>
                <q-btn
                  v-if="availablePermissions.length > 0"
                  color="negative"
                  flat
                  @click.prevent="confirmApiTokenDeletion(token)"
                >
                  Delete
                </q-btn>
              </div>
            </q-item-section>
          </q-item>
        </q-list>
      </template>
    </action-section>
  </template>

  <!-- Token Value Modal -->
  <q-dialog
    v-model="displayingToken"
  >
    <q-card>
      <q-card-section>
        <h6>
          API Token
        </h6>
        <p>
          Please copy your new API token. For your security, it won't be shown again.
        </p>
        <q-card
          bordered
          flat
        >
          <code-block
            v-if="jetstream.flash.token"
            lang="text"
            :code="jetstream.flash.token"
          />
        </q-card>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          flat
          label="Close"
          @click="displayingToken = false"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <!-- API Token Permissions Modal -->
  <q-dialog
    v-model="showManagingPermissionModal"
  >
    <q-card>
      <q-card-section>
        <h6>
          API Token Permissions
        </h6>

        <q-checkbox
          v-for="(permission, key) in props.availablePermissions"
          :key="key"
          v-model="updateApiTokenForm.permissions"
          :label="permission"
          :val="permission"
          hide-details
        />
      </q-card-section>

      <q-card-actions>
        <q-space />
        <q-btn
          flat
          label="Cancel"
          @click="showManagingPermissionModal = false"
        />
        <q-btn
          color="primary"
          flat
          label="Save"
          :disabled="updateApiTokenForm.processing"
          @click="updateApiToken"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <!-- Delete Token Confirmation Modal -->
  <q-dialog
    v-model="showApiTokenDeletionModal"
  >
    <q-card>
      <q-card-section>
        <h6>
          Delete API Token
        </h6>
        <p>
          Are you sure you would like to delete this API token?
        </p>
      </q-card-section>
      <q-card-actions>
        <q-space />
        <q-btn
          flat
          label="Cancel"
          q-btn
          @click="showApiTokenDeletionModal = false"
        />

        <q-btn
          color="negative"
          flat
          :disabled="deleteApiTokenForm.processing"
          label="Delete"
          @click="deleteApiToken"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
