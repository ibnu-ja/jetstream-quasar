
<script lang="ts" setup>
import { copyToClipboard } from 'quasar'
// import { mdiContentCopy } from '@quasar/extras/mdi-v6'
import { ref } from 'vue'

const props = defineProps<{
  text: string
}>()
let timer: number | undefined
const copied = ref(false)
function copy () {
  copyToClipboard(props.text)
    .then(() => {
      copied.value = true
      clearTimeout(timer)
      timer = setTimeout(() => {
        copied.value = false
        // timer = undefined
        // delete timer
        timer = 0
      }, 2000)
    })
    .catch(() => {})
}
</script>

<template>
  <div class="relative">
    <q-btn
      color="primary"
      round
      dense
      flat
      icon="mdi-content-copy"
      @click="copy"
    >
      <q-tooltip>Copy to Clipboard</q-tooltip>
    </q-btn>
    <transition
      enter-active-class="animated fadeIn"
      leave-active-class="animated fadeOut"
    >
      <q-badge
        v-show="copied"
        class="absolute"
        style="top: 8px; right: 58px;"
        color="primary"
      >
        Copied to clipboard
      </q-badge>
    </transition>
  </div>
</template>
