<script lang="ts" setup>
import { Head, Link as InertiaLink, useForm, usePage } from '@inertiajs/inertia-vue3'
import { computed, inject, ref } from 'vue'
import JetAuthenticationCardLogo from '@/views/components/AuthenticationCardLogo.vue'
import { Jetstream } from '@/scripts/types/inertia-props'
import WebLayout from '@/views/layouts/WebLayout.vue'
import { useWidth } from '@/composables/useWidth'
const route: any = inject('route')
const showP = ref(true)
const showCP = ref(true)
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false
})
const jetstream = computed(() => usePage().props.value.jetstream as Jetstream)
function submit () {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}

const { width } = useWidth()
</script>

<template>
  <Head title="Register" />

  <web-layout>
    <q-page
      class="fit column items-center content-center bg-grey-2"
      :class="$q.screen.lt.sm ? 'justify-start': 'justify-center'"
      :padding="$q.screen.gt.sm"
    >
      <jet-authentication-card-logo />
      <q-card
        :bordered="$q.screen.lt.sm"
        :flat="$q.screen.lt.sm"
        :square="$q.screen.lt.sm"
        :style="'width: ' + width"
      >
        <q-form @submit.prevent="submit">
          <q-card-section class="q-gutter-md">
            <h6 class="text-center no-margin">
              Register
            </h6>
            <q-input
              v-model="form.email"
              :error="!!form.errors.email"
              :error-message="form.errors.email"
              outlined
              label="Email"
              lazy-rules
            />

            <q-input
              v-model="form.name"
              :error="!!form.errors.name"
              :error-message="form.errors.name"
              outlined
              label="Name"
              lazy-rules
            />

            <q-input
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

            <q-input
              v-model="form.password_confirmation"
              :error="!!form.errors.password_confirmation"
              :error-message="form.errors.password_confirmation"
              :type="showCP? 'password' : 'text'"
              outlined
              label="Password confirmation"
              lazy-rules
            >
              <template #append>
                <q-icon
                  :name="showCP ? 'mdi-eye' : 'mdi-eye-off'"
                  class="cursor-pointer"
                  @click="showCP = !showCP"
                />
              </template>
            </q-input>

            <q-field
              v-if="jetstream.hasTermsAndPrivacyPolicyFeature"
              borderless
              :error="!!form.errors.terms"
              :error-message="form.errors.terms"
              dense
            >
              <template #control>
                <q-checkbox
                  v-model="form.terms"
                  :color="!!form.errors.terms ? 'negative' : 'primary'"
                >
                  <span :class="{'text-negative': !!form.errors.terms }">
                    I agree to the <a
                      :href="route('terms.show')"
                      target="_blank"
                      @click.stop
                    >Terms of Service</a> and <a
                      :href="route('policy.show')"
                      target="_blank"
                      @click.stop
                    >Privacy Policy</a>
                  </span>
                </q-checkbox>
              </template>
            </q-field>
          </q-card-section>
          <q-separator />
          <q-card-actions align="right">
            <inertia-link
              :href="route('login')"
              class="q-mx-md"
            >
              Already registered?
            </inertia-link>
            <q-btn
              color="primary"
              flat
              type="submit"
            >
              Register
            </q-btn>
          </q-card-actions>
        </q-form>
      </q-card>
    </q-page>
  </web-layout>
</template>
