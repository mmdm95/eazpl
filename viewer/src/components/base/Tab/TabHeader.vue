<script setup lang="ts">
import {type ComponentPublicInstance, onBeforeUnmount, watch} from 'vue'
import {cn} from '@/utils'

import BaseLucideIcon from '../Icon/Icon.vue'
import {mergeTabClasses, useBaseTabContext} from './context'
import TabSection from './TabSection.vue'
import type {TabHeaderProps, TabItem} from './types'

defineOptions({name: 'BaseTabHeader'})

const props = withDefaults(defineProps<TabHeaderProps>(), {
  label: undefined,
  icon: undefined,
  disabled: false,
  classes: undefined,
})

const context = useBaseTabContext()
const active = () => context.activeValue.value === props.value

function headerItem(): TabItem {
  return {
    value: props.value,
    label: props.label ?? '',
    icon: props.icon,
    disabled: props.disabled,
  }
}

function setElement(element: Element | ComponentPublicInstance | null): void {
  context.registerElement(props.value, element instanceof HTMLElement ? element : null)
}

function rootClass(): string {
  const isActive = active()
  const horizontal = context.orientation.value === 'horizontal'

  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabHeader',
    'tab',
    cn(
      'relative z-10 inline-flex items-center justify-center gap-control-gap-sm whitespace-nowrap font-medium outline-offset-[-2px] transition-colors duration-200',
      context.variant.value === 'pill'
        ? 'rounded-control'
        : horizontal
          ? 'border-b-2 border-transparent'
          : 'border-s-2 border-transparent',
      horizontal
        ? 'px-control-padding-x-md py-control-padding-y-sm text-control-md'
        : 'px-control-padding-x-md py-control-padding-y-sm text-start text-control-md',
      isActive ? 'text-primary' : 'text-content-muted hover:text-content',
      props.disabled
        ? 'cursor-not-allowed opacity-50'
        : 'cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
      context.variant.value === 'underline' && isActive ? 'border-transparent' : '',
    ),
  )
}

function iconClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabHeaderIcon',
    'icon',
    cn('h-icon-sm w-icon-sm shrink-0', props.disabled && 'opacity-70'),
  )
}

function labelClass(): string {
  return mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabHeaderLabel',
    'label',
    'inline-flex items-center',
  )
}

watch(
  () => props.value,
  (value, previousValue) => {
    if (previousValue !== undefined) {
      context.unregisterTab(previousValue)
      context.registerElement(previousValue, null)
    }
  },
)

watch(
  () => [props.value, props.label, props.icon, props.disabled],
  () => {
    context.registerTab(headerItem())
  },
  {immediate: true},
)

onBeforeUnmount(() => {
  context.unregisterTab(props.value)
  context.registerElement(props.value, null)
})
</script>

<template>
  <button
    :ref="setElement"
    type="button"
    :class="rootClass()"
    role="tab"
    :aria-selected="active()"
    :aria-controls="context.panelId(props.value)"
    :id="context.tabId(props.value)"
    :tabindex="active() || props.disabled ? 0 : -1"
    :disabled="props.disabled"
    @click="context.select(props.value)"
  >
    <slot :active="active()" :disabled="props.disabled">
      <BaseLucideIcon v-if="props.icon" :icon="props.icon" :classes="{ root: iconClass() }"/>
      <span v-if="props.label" :class="labelClass()">
        <TabSection :section="props.label"/>
      </span>
    </slot>
  </button>
</template>
