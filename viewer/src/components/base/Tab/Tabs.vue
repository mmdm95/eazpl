<script setup lang="ts">
import {computed, nextTick, onBeforeUnmount, onMounted, ref, watch} from 'vue'
import {cn} from '@/utils'

import {mergeTabClasses, useBaseTabContext} from './context'
import type {TabsProps} from './types'

defineOptions({name: 'BaseTabs'})

const props = withDefaults(defineProps<TabsProps>(), {
  classes: undefined,
})

const context = useBaseTabContext()
const navRef = ref<HTMLElement | null>(null)
let resizeObserver: ResizeObserver | undefined

const rootClass = computed(() =>
  mergeTabClasses(
    props.classes,
    context.classes.value,
    'tabs',
    'nav',
    cn(
      'relative',
      context.orientation.value === 'horizontal'
        ? 'flex w-full items-stretch gap-control-gap-sm border-b border-border'
        : 'flex flex-col gap-control-gap-sm border-s border-border',
    ),
  ),
)

const indicatorClass = computed(() =>
  mergeTabClasses(
    undefined,
    context.classes.value,
    'indicator',
    undefined,
    cn(
      'pointer-events-none absolute bg-primary transition-all ease-out',
      context.variant.value === 'pill' ? 'z-0 rounded-control bg-primary-soft' : 'z-0',
      context.orientation.value === 'horizontal'
        ? '-bottom-px start-0 rounded-pill'
        : '-start-px top-0 rounded-pill',
    ),
  ),
)

function onKeydown(event: KeyboardEvent): void {
  const nextKey = context.orientation.value === 'horizontal' ? 'ArrowRight' : 'ArrowDown'
  const previousKey = context.orientation.value === 'horizontal' ? 'ArrowLeft' : 'ArrowUp'
  if (event.key !== nextKey && event.key !== previousKey) return

  event.preventDefault()
  context.moveSelection(event.key === nextKey ? 1 : -1)
}

watch(
  () => [context.activeValue.value, context.orientation.value, context.variant.value],
  () => {
    void nextTick(context.updateIndicator)
  },
)

onMounted(() => {
  void nextTick(context.updateIndicator)
  if (typeof ResizeObserver === 'undefined' || !navRef.value) return

  resizeObserver = new ResizeObserver(context.updateIndicator)
  resizeObserver.observe(navRef.value)
})

onBeforeUnmount(() => {
  resizeObserver?.disconnect()
})
</script>

<template>
  <div
    ref="navRef"
    :class="rootClass"
    role="tablist"
    :aria-orientation="context.orientation.value"
    @keydown="onKeydown"
  >
    <span :class="indicatorClass" :style="context.indicatorStyle.value" aria-hidden="true"/>
    <slot/>
  </div>
</template>
