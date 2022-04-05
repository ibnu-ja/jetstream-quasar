<script lang="ts" setup>
import { Inertia } from '@inertiajs/inertia'
import { ref } from 'vue'
import { useQuasar } from 'quasar'
const progress = ref(0)
const loading = ref(false)

const props = defineProps({
  trickleSpeed: {
    type: Number,
    default: 200
  },
  color: {
    type: String,
    default: 'primary'
  }
})

Inertia.on('start', () => startProgress())

Inertia.on('finish', () => done())

function startProgress () {
  progress.value = 0
  loading.value = true
  const interval = setInterval(() => {
    if (progress.value >= 0.994) {
      clearInterval(interval)
    } else {
      let amount: number
      if (progress.value >= 0 && progress.value < 0.2) amount = 0.1
      else if (progress.value >= 0.2 && progress.value < 0.5) amount = 0.04
      else if (progress.value >= 0.5 && progress.value < 0.8) amount = 0.02
      else if (progress.value >= 0.8 && progress.value < 0.99) amount = 0.005
      else amount = 0
      progress.value = clamp(progress.value + amount, 0, 0.994)
    }
  }, props.trickleSpeed)
}

// stop the progress bar
function done () {
  progress.value = 1
  setTimeout(() => {
    loading.value = false
  }, 350)
}
function clamp (n: number, min: number, max: number) {
  if (n < min) return min
  if (n > max) return max
  return n
}

const $q = useQuasar()
</script>

<template>
  <q-linear-progress
    v-show="loading"
    :animation-speed="trickleSpeed"
    style="position: fixed !important; top: 0 !important; z-index: 2001;"
    :value="progress"
    :color="$q.dark.isActive ? color + '-dark' : color"
  />
</template>
