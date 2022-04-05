<script lang="ts" setup>
import { inject, nextTick, ref } from 'vue'
import AppActionSection from '@/views/components/ActionSection.vue'
import { Session } from '@/scripts/types/inertia-props'
import { useForm } from '@inertiajs/inertia-vue3'
defineProps<{
  sessions: Session[]
}>()
const route: any = inject('route')
const confirmingLogout = ref(false)
const form = useForm({
  password: ''
})
const showP = ref(true)
// TODO focus on form
function confirmLogout () {
  confirmingLogout.value = true
  nextTick(() => {
    setTimeout(() => password.value?.focus())
  })
}
const password = ref<HTMLInputElement>()
function logoutOtherBrowserSessions () {
  form.delete(route('other-browser-sessions.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => {
      nextTick(() => {
        setTimeout(() => {
          password.value?.focus()
        })
      })
    },
    onFinish: () => form.reset()
  })
}
function closeModal () {
  confirmingLogout.value = false
  form.reset()
}
</script>

<template>
  <app-action-section>
    <template #title>
      Browser Sessions
    </template>

    <template #description>
      Manage and logout your active sessions on other browsers and devices.
    </template>

    <template #content>
      <q-card-section class="q-gutter-md q-pt-none">
        <p>
          If necessary, you may logout of all of your other browser sessions
          across all of your devices. Some of your recent sessions are listed
          below; however, this list may not be exhaustive. If you feel your
          account has been compromised, you should also update your password.
        </p>
        <!-- TODO add max width to dialog -->
        <q-dialog v-model="confirmingLogout">
          <q-card>
            <q-form @submit.prevent="logoutOtherBrowserSessions">
              <q-card-section>
                <h6>
                  Logout Other Browser Sessions
                </h6>

                <p>
                  Please enter your password to confirm you would like to logout of
                  your other browser sessions across all of your devices.
                </p>
                <q-input
                  ref="password"
                  v-model="form.password"
                  :error="!!form.errors.password"
                  :error-message="form.errors.password"
                  :type="showP? 'password' : 'text'"
                  outlined
                  label="Password"
                  lazy-rules
                >
                  <template #append>
                    <q-icon
                      :name="showP ? 'mdi-eye' : 'mdi-eye-off'"
                      class="cursor-pointer"
                      @click="showP = !showP"
                    />
                  </template>
                </q-input>
              </q-card-section>

              <q-card-actions align="right">
                <q-btn
                  outline
                  flat
                  label="Nevermind"
                  @click="closeModal"
                />

                <q-btn
                  color="primary"
                  flat
                  label="Logout Other Browser Sessions"
                  :disabled="form.processing"
                  type="submit"
                />
              </q-card-actions>
            </q-form>
          </q-card>
        </q-dialog>

        <!-- Other Browser Sessions -->
        <q-list
          v-if="sessions.length > 0"
        >
          <q-item
            v-for="(session, i) in sessions"
            :key="i"
            :active="session.is_current_device"
            active-color="primary"
          >
            <q-item-section avatar>
              <q-icon
                v-if="session.agent?.is_desktop"
                size="50px"
                name="mdi-desktop-tower-monitor"
              />
              <q-icon
                v-else
                size="50"
                name="mdi-cellphone"
              />
            </q-item-section>
            <q-item-section>
              <q-item-label>
                {{ session.agent?.platform }} -
                {{ session.agent?.browser }}
              </q-item-label>
              <q-item-label caption>
                {{ session.ip_address }},
                <span
                  v-if="session.is_current_device"
                  class="text-success"
                >
                  This device
                </span>
                <span v-else>Last active {{ session.last_active }}</span>
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
        <transition
          leave-active-class="transition ease-in duration-1000"
          leave-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-show="form.recentlySuccessful"
            class="text-sm text-gray-600"
          >
            Saved. Done.
          </div>
        </transition>
      </q-card-section>
    </template>
    <template #actions>
      <q-btn
        color="primary"
        flat
        label="Logout Other Browser Sessions"
        @click="confirmLogout"
      />
    </template>
  </app-action-section>
</template>
