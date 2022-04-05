import { computed, defineComponent, h } from 'vue'
import { highlight, languages } from 'prismjs'
import { useQuasar } from 'quasar'

export default defineComponent({
  name: 'CodePrism',

  props: {
    code: {
      type: String,
      default: ''
    },
    lang: {
      type: String,
      default: ''
    }
  },

  setup (props) {
    const $q = useQuasar()
    const className = computed(() => `language-${props.lang} ${$q.dark.isActive ? 'dark' : ''}`)

    return () => h('pre', { class: 'doc-code ' + className.value }, [
      h('code', {
        class: 'doc-code__inner ' + className.value,
        innerHTML: highlight(
          props.code,
          languages[props.lang],
          props.lang
        )
      })
    ])
  }
})
