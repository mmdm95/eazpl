<script setup lang="ts">
import {computed, useSlots} from 'vue'
import {cn} from '@/utils'

import BaseLucideIcon from '../Icon/Icon.vue'
import {mergeTabClasses, useBaseTabContext} from './context'
import TabContent from './TabContent.vue'
import TabFooter from './TabFooter.vue'
import TabSection from './TabSection.vue'
import type {TabPanelProps} from './types'

defineOptions({name: 'BaseTabPanel'})

const props = withDefaults(defineProps<TabPanelProps>(), {
  header: undefined,
  description: undefined,
  icon: undefined,
  content: undefined,
  footer: undefined,
  classes: undefined,
})

const context = useBaseTabContext()
const slots = useSlots()
const isActive = computed(() => context.activeValue.value === props.value)
const hasHeader = computed(() =>
  Boolean(props.header || props.description || props.icon || slots.header),
)

function rootClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabPanel',
    'panel',
    cn(
      'flex min-h-0 flex-1 flex-col text-control-md leading-relaxed text-content-muted',
      context.orientation.value === 'horizontal' ? 'pt-content-sm' : 'ps-content-sm',
    ),
  )
}

function headerClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabPanelHeader',
    undefined,
    'mb-content-sm flex items-start gap-control-gap-md',
  )
}

function iconClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabPanelIcon',
    undefined,
    'mt-field-label-gap h-icon-md w-icon-md shrink-0 text-primary',
  )
}

function titleClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabPanelTitle',
    undefined,
    'text-title font-semibold text-content',
  )
}

function descriptionClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabPanelDescription',
    undefined,
    'mt-1 text-control-sm text-content-muted',
  )
}

function contentClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabContent',
    undefined,
    'flex min-h-0 min-w-0 flex-1 flex-col',
  )
}

function footerClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabFooter',
    undefined,
    'mt-content-sm flex items-center gap-control-gap-md border-t border-border pt-content-sm text-caption text-content-subtle',
  )
}
</script>

<template>
  <div
    v-if="isActive"
    :id="context.panelId(props.value)"
    role="tabpanel"
    :aria-labelledby="context.tabId(props.value)"
    :class="rootClass()"
  >
    <header v-if="hasHeader" :class="headerClass()">
      <slot name="header" :active="isActive">
        <BaseLucideIcon v-if="props.icon" :icon="props.icon" :classes="{ root: iconClass() }"/>
        <div class="min-w-0">
          <h4 v-if="props.header" :class="titleClass()">
            <TabSection :section="props.header"/>
          </h4>
          <p v-if="props.description" :class="descriptionClass()">
            {{ props.description }}
          </p>
        </div>
      </slot>
    </header>

    <TabContent :classes="{ root: contentClass() }">
      <slot>
        <TabSection :section="props.content"/>
      </slot>
    </TabContent>

    <TabFooter
      v-if="props.footer || $slots.footer"
      :content="props.footer"
      :classes="{ root: footerClass() }"
    >
      <slot v-if="$slots.footer" name="footer" :active="isActive"/>
    </TabFooter>
  </div>
</template>
