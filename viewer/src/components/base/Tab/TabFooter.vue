<script setup lang="ts">
import { resolveClasses } from '../shared'
import TabSection from './TabSection.vue'
import type { TabFooterProps } from './types'

defineOptions({ name: 'BaseTabFooter' })

const props = withDefaults(defineProps<TabFooterProps>(), {
  content: undefined,
  classes: undefined,
})
</script>

<template>
  <footer
    :class="
      resolveClasses(
        props.classes,
        'root',
        'mt-content-sm flex items-center gap-control-gap-md border-t border-border pt-content-sm text-caption text-content-subtle',
      )
    "
  >
    <slot>
      <TabSection :section="props.content" />
    </slot>
  </footer>
</template>
