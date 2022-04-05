<script lang="ts" setup>
import { Jetstream, User } from '@/scripts/types/inertia-props'
import { Ref, computed, inject, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import FormSection from '@/views/components/FormSection.vue'
import { Inertia } from '@inertiajs/inertia'
const route: any = inject('route')
const props = defineProps<{user: User}>()
const jetstream = computed(() => usePage().props.value.jetstream as Jetstream)
const form = useForm({
  _method: 'PUT',
  name: props.user?.name,
  email: props.user?.email,
  photo: null as File | null
})
const photoPreview: Ref = ref(null)
const photo: Ref<HTMLInputElement | null> = ref(null)
function updateProfileInformation () {
  if (photo.value) { form.photo = photo.value!.files![0] }
  form.post(route('user-profile-information.update'), {
    errorBag: 'updateProfileInformation',
    preserveScroll: true,
    onSuccess: () => (clearPhotoFileInput())
  })
}
function selectNewPhoto () {
  photo.value!.click()
}
function updatePhotoPreview () {
  const file = photo.value!.files![0]
  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target!.result
  }
  reader.readAsDataURL(file)
}
function deletePhoto () {
  Inertia.delete(route('current-user-photo.destroy'), {
    preserveScroll: true,
    onSuccess: () => (photoPreview.value = null)
  })
}
function clearPhotoFileInput () {
  photo.value = null
}
</script>

<template>
  <form-section @submitted="updateProfileInformation">
    <template #title>
      Profile Information
    </template>

    <template #description>
      Update your account's profile information and email address.
    </template>

    <template #form>
      <q-card-section class="q-gutter-md q-pt-none">
        <!-- Profile Photo -->
        <div
          v-if="jetstream.managesProfilePhotos"
          class="q-gutter-x-md"
        >
          <!-- Profile Photo File Input -->
          <input
            ref="photo"
            style="display: none;"
            type="file"
            @change="updatePhotoPreview"
          >
          <!-- New Profile Photo Preview -->
          <!-- TODO enable transition -->
          <q-avatar size="100px">
            <q-img
              v-if="photoPreview"
              :src="photoPreview"
            />
            <q-img
              v-else
              :src="user.profile_photo_url"
              :alt="user.name"
            />
          </q-avatar>

          <q-btn
            class="q-mx-sm"
            outline
            label="Select A New Photo"
            color="info"
            @click.prevent="selectNewPhoto"
          />
          <q-btn
            v-if="user.profile_photo_path"
            class="q-mx-sm"
            outline
            label="Remove Photo"
            color="negative"
            @click.prevent="deletePhoto"
          />
        </div>

        <!-- Name -->
        <q-input
          v-model="form.name"
          label="Name"
          type="text"
          outlined
          autocomplete="name"
          :error="!!form.errors.name"
          :error-message="form.errors.name"
        />

        <!-- Email -->
        <q-input
          v-model="form.email"
          label="Email"
          type="email"
          outlined
          :error="!!form.errors.email"
          :error-message="form.errors.email"
        />
      </q-card-section>
    </template>

    <template #actions>
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
        flat
        :disabled="form.processing"
        @click="form.reset()"
      >
        Reset
      </q-btn>
      <q-btn
        flat
        :disabled="form.processing"
        type="submit"
        color="primary"
      >
        Save
      </q-btn>
    </template>
  </form-section>
</template>
