<script setup lang="ts">
import type { Component } from 'vue'

import { resolveClasses } from '../shared'
import type { TabSectionProps } from './types'

defineOptions({ name: 'BaseTabSection' })

const props = withDefaults(defineProps<TabSectionProps>(), {
  section: undefined,
  classes: undefined,
})

function isComponent(section: NonNullable<TabSectionProps['section']>): section is Component {
  return typeof section !== 'string'
}
</script>

<template>
  <span :class="resolveClasses(props.classes, 'root', 'contents')">
    <component :is="props.section" v-if="props.section && isComponent(props.section)" />
    <template v-else-if="props.section">{{ props.section }}</template>
    <slot v-else />
  </span>
</template>
