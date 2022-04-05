<script lang="ts" setup>
import { computed, useSlots } from 'vue'
const slots = useSlots()
const hasActions = computed(() => !!slots.actions)
defineEmits(['submitted'])
</script>

<template>
  <div class="row q-col-gutter-md">
    <div class="col-12 col-md-4">
      <h5>
        <slot name="title" />
      </h5>

      <p><slot name="description" /></p>
    </div>
    <div class="col-12 col-md-8">
      <div>
        <q-card class="q-pt-md">
          <form @submit.prevent="$emit('submitted')">
            <slot name="form" />
            <template v-if="hasActions">
              <q-separator />

              <q-card-actions>
                <slot name="actions" />
              </q-card-actions>
            </template>
          </form>
        </q-card>
      </div>
    </div>
  </div>
</template>
