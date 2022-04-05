<template>
  <div class="doc-code-container">
    <code-prism
      :lang="lang"
      :code="code"
      :style="style"
    />
    <div class="doc-code-container__copy absolute">
      <copy-button :text="code" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import CodePrism from './CodePrism'
import CopyButton from './CopyButton.vue'
import { computed } from 'vue'

const props = defineProps({
  code: {
    type: String,
    default: ''
  },
  maxHeight: String,
  lang: {
    type: String,
    default: 'js'
  }
})
const style = computed(() => (
  // eslint-disable-next-line no-void
  props.maxHeight !== void 0
    ? { overflow: 'auto', maxHeight: props.maxHeight }
    : null
))
</script>

<style lang="scss">
.doc-code-container__copy {
  top: 8px;
  right: 16px;
}

.doc-code,
.doc-code__inner {
  margin: 0;
  position: relative;
  font-size: 12px;
  color: $grey;
  word-wrap: normal;
  line-height: 1.5;
  tab-size: 4;
  hyphens: none;
}

.doc-code {
  overflow: visible;
  padding: 0;
}

/**
 * prism.js default theme for JavaScript, CSS and HTML
 * Based on dabblet (http://dabblet.com)
 * @author Lea Verou
 */

code[class*="language-"] {
  color: black;
  background: none;
  text-shadow: 0 1px white;
  font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
  font-size: 1em;
  text-align: left;
  white-space: pre;
  word-spacing: normal;
  word-break: normal;
  word-wrap: normal;
  line-height: 1.5;
  -moz-tab-size: 4;
  -o-tab-size: 4;
  tab-size: 4;
  -webkit-hyphens: none;
  -moz-hyphens: none;
  -ms-hyphens: none;
  hyphens: none;
}

pre[class*="language-"] {
  color: black;
  background: none;
  text-shadow: 0 1px white;
  font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
  font-size: 1em;
  text-align: left;
  white-space: pre;
  word-spacing: normal;
  word-break: normal;
  word-wrap: normal;
  line-height: 1.5;
  -moz-tab-size: 4;
  -o-tab-size: 4;
  tab-size: 4;
  -webkit-hyphens: none;
  -moz-hyphens: none;
  -ms-hyphens: none;
  hyphens: none;

  &::-moz-selection, ::-moz-selection {
    text-shadow: none;
    background: #b3d4fc;
  }
}

code[class*="language-"] {
  &::-moz-selection, ::-moz-selection {
    text-shadow: none;
    background: #b3d4fc;
  }
}

pre[class*="language-"] {
  &::selection, ::selection {
    text-shadow: none;
    background: #b3d4fc;
  }
}

code[class*="language-"] {
  &::selection, ::selection {
    text-shadow: none;
    background: #b3d4fc;
  }
}

@media print {
  code[class*="language-"], pre[class*="language-"] {
    text-shadow: none;
  }
}

/* Code blocks */

pre[class*="language-"] {
  padding: 1em;
  margin: .5em 0;
  overflow: auto;
}

// :not(pre) > code[class*="language-"], pre[class*="language-"] {
//   background: #f5f2f0;
// }

/* Inline code */

:not(pre) > code[class*="language-"] {
  padding: .1em;
  border-radius: .3em;
  white-space: normal;
}

.token {
  &.comment, &.prolog, &.doctype, &.cdata {
    color: slategray;
  }

  &.punctuation {
    color: #999;
  }

  &.namespace {
    opacity: .7;
  }

  &.property, &.tag, &.boolean, &.number, &.constant, &.symbol, &.deleted {
    color: #905;
  }

  &.selector, &.attr-name, &.string, &.char, &.builtin, &.inserted {
    color: #690;
  }

  &.operator, &.entity, &.url {
    color: #9a6e3a;

    /* This background color was intended by the author of this theme. */
    background: hsla(0, 0%, 100%, 0.5);
  }
}

.language-css .token.string, .style .token.string {
  color: #9a6e3a;

  /* This background color was intended by the author of this theme. */
  background: hsla(0, 0%, 100%, 0.5);
}

.token {
  &.atrule, &.attr-value, &.keyword {
    color: #07a;
  }

  &.function, &.class-name {
    color: #DD4A68;
  }

  &.regex, &.important, &.variable {
    color: #e90;
  }

  &.important, &.bold {
    font-weight: bold;
  }

  &.italic {
    font-style: italic;
  }

  &.entity {
    cursor: help;
  }
}

/**
 * prism.js tomorrow night eighties for JavaScript, CoffeeScript, CSS and HTML
 * Based on https://github.com/chriskempson/tomorrow-theme
 * @author Rose Pritchard
 */

code.dark[class*="language-"] {
  color: #ccc;
  background: none;
  text-shadow: none;
  font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
  font-size: 1em;
  text-align: left;
  white-space: pre;
  word-spacing: normal;
  word-break: normal;
  word-wrap: normal;
  line-height: 1.5;
  -moz-tab-size: 4;
  -o-tab-size: 4;
  tab-size: 4;
  -webkit-hyphens: none;
  -moz-hyphens: none;
  -ms-hyphens: none;
  hyphens: none;
}

pre.dark[class*="language-"] {
  color: #ccc;
  background: none;
  text-shadow: none;
  font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
  font-size: 1em;
  text-align: left;
  white-space: pre;
  word-spacing: normal;
  word-break: normal;
  word-wrap: normal;
  line-height: 1.5;
  -moz-tab-size: 4;
  -o-tab-size: 4;
  tab-size: 4;
  -webkit-hyphens: none;
  -moz-hyphens: none;
  -ms-hyphens: none;
  hyphens: none;
  padding: 1em;
  margin: .5em 0;
  overflow: auto;
}

/* Code blocks */

// :not(pre.dark) > code.dark[class*="language-"], pre.dark[class*="language-"] {
//   background: #2d2d2d;
// }

/* Inline code */

:not(pre.dark) > code.dark[class*="language-"] {
  padding: .1em;
  border-radius: .3em;
  white-space: normal;
}

.dark .token {
  &.comment, &.block-comment, &.prolog, &.doctype, &.cdata {
    color: #999;
  }

  &.punctuation {
    color: #ccc;
  }

  &.tag, &.attr-name, &.namespace, &.deleted {
    color: #e2777a;
  }

  &.function-name {
    color: #6196cc;
  }

  &.boolean, &.number, &.function {
    color: #f08d49;
  }

  &.property, &.class-name, &.constant, &.symbol {
    color: #f8c555;
  }

  &.selector, &.important, &.atrule, &.keyword, &.builtin {
    color: #cc99cd;
  }

  &.string, &.char, &.attr-value, &.regex, &.variable {
    color: #7ec699;
  }

  &.operator, &.entity, &.url {
    color: #67cdcc;
  }

  &.important, &.bold {
    font-weight: bold;
  }

  &.italic {
    font-style: italic;
  }

  &.entity {
    cursor: help;
  }

  &.inserted {
    color: green;
  }
}
</style>
