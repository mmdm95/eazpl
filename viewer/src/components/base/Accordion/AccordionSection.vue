<script setup lang="ts">
import type {Component} from 'vue'

import type {AccordionSection} from './types'

defineOptions({name: 'BaseAccordionSection'})

const props = defineProps<{
  section?: AccordionSection
}>()

function isComponent(section: AccordionSection): section is Component {
  return typeof section !== 'string'
}
</script>

<template>
  <component :is="props.section" v-if="props.section && isComponent(props.section)"/>
  <template v-else-if="props.section">{{ props.section }}</template>
</template>
