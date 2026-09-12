<script setup lang="ts">
import {resolveClasses} from '../shared'
import TabSection from './TabSection.vue'
import type {TabContentProps} from './types'

defineOptions({name: 'BaseTabContent'})

const props = withDefaults(defineProps<TabContentProps>(), {
  content: undefined,
  classes: undefined,
})
</script>

<template>
  <div :class="resolveClasses(props.classes, 'root', 'min-w-0 flex-1')">
    <slot>
      <TabSection :section="props.content"/>
    </slot>
  </div>
</template>
