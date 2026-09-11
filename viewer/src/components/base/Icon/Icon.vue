<script setup lang="ts">
import { resolveClasses } from '../shared'
import type { IconProps } from './types'

defineOptions({ name: 'BaseLucideIcon' })

const props = withDefaults(defineProps<IconProps>(), {
  size: 16,
  strokeWidth: 2,
  classes: undefined,
})
</script>

<template>
  <component
    :is="props.icon"
    :size="props.size"
    :stroke-width="props.strokeWidth"
    :class="resolveClasses(props.classes, 'root', 'shrink-0')"
    aria-hidden="true"
  />
</template>
