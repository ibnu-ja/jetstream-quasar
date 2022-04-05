<script lang="ts" setup>
import { computed, useSlots } from 'vue'

const slots = useSlots()

const hasActions = computed(() => !!slots.actions)

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
          <slot name="content" />
          <!-- </q-card-section> -->
          <template v-if="hasActions">
            <q-separator />

            <q-card-actions>
              <slot name="actions" />
            </q-card-actions>
          </template>
        </q-card>
      </div>
    </div>
  </div>
</template>
