import { computed } from '@vue/reactivity'
import { useQuasar } from 'quasar'

export function useWidth () {
  const $q = useQuasar()
  const width = computed(() => {
    if ($q.screen.xs) return '100%'
    else if ($q.screen.sm) return '70%'
    else if ($q.screen.md) return '40%'
    else return '30%'
  })
  return { width }
}
