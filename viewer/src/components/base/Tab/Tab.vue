<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, ref, watch, type CSSProperties } from 'vue'

import { provideBaseTabContext, type BaseTabContext } from './context'
import TabHeader from './TabHeader.vue'
import TabPanel from './TabPanel.vue'
import TabSection from './TabSection.vue'
import Tabs from './Tabs.vue'
import { resolveClasses } from '../shared'
import type { TabItem, TabItemValue, TabProps } from './types'

defineOptions({ name: 'BaseTab' })

const props = withDefaults(defineProps<TabProps>(), {
  modelValue: undefined,
  orientation: 'horizontal',
  variant: 'underline',
  transitionDuration: 300,
  items: () => [],
  idPrefix: 'tab',
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: TabItemValue]
  change: [value: TabItemValue]
}>()

const registeredItems = ref<TabItem[]>([])
const internalValue = ref<TabItemValue | undefined>()
const tabElements = new Map<TabItemValue, HTMLElement>()
const indicator = ref({ x: 0, y: 0, width: 0, height: 0 })

const contextItems = computed(() => (props.items.length > 0 ? props.items : registeredItems.value))
const activeValue = computed<TabItemValue | undefined>(() => {
  const requestedValue = props.modelValue ?? internalValue.value
  const requestedItem = contextItems.value.find((item) => item.value === requestedValue)
  if (requestedItem) return requestedItem.value

  return contextItems.value.find((item) => !item.disabled)?.value ?? contextItems.value[0]?.value
})
const activeItem = computed(() =>
  contextItems.value.find((item) => item.value === activeValue.value),
)

function tabId(value: TabItemValue): string {
  return `${props.idPrefix}-${String(value)}`
}

function panelId(value: TabItemValue): string {
  return `${props.idPrefix}-panel-${String(value)}`
}

function registerTab(item: TabItem): void {
  const index = registeredItems.value.findIndex((current) => current.value === item.value)
  if (index === -1) {
    registeredItems.value = [...registeredItems.value, item]
    return
  }

  registeredItems.value = registeredItems.value.map((current, currentIndex) =>
    currentIndex === index ? item : current,
  )
}

function unregisterTab(value: TabItemValue): void {
  registeredItems.value = registeredItems.value.filter((item) => item.value !== value)
}

function registerElement(value: TabItemValue, element: HTMLElement | null): void {
  if (element) tabElements.set(value, element)
  else tabElements.delete(value)
}

function parsePixelLength(value: string | undefined): number {
  const parsedValue = Number.parseFloat(value ?? '0')
  return Number.isFinite(parsedValue) ? parsedValue : 0
}

function updateIndicator(): void {
  if (activeValue.value === undefined) {
    indicator.value = { x: 0, y: 0, width: 0, height: 0 }
    return
  }

  const element = tabElements.get(activeValue.value)
  if (!element) {
    indicator.value = { x: 0, y: 0, width: 0, height: 0 }
    return
  }

  const nav = element.closest('[role="tablist"]')
  const elementRect = element.getBoundingClientRect()
  const navRect = nav?.getBoundingClientRect()
  const navStyle = nav ? window.getComputedStyle(nav) : undefined
  const isRtl = window.getComputedStyle(element).direction === 'rtl'
  const inlineStartPosition = navRect
    ? isRtl
      ? navRect.right -
        parsePixelLength(navStyle?.borderRightWidth) -
        parsePixelLength(navStyle?.paddingRight)
      : navRect.left +
        parsePixelLength(navStyle?.borderLeftWidth) +
        parsePixelLength(navStyle?.paddingLeft)
    : 0

  indicator.value = {
    x: isRtl ? inlineStartPosition - elementRect.right : elementRect.left - inlineStartPosition,
    y: element.offsetTop,
    width: element.offsetWidth,
    height: element.offsetHeight,
  }
}

const indicatorStyle = computed<CSSProperties>(() => {
  const isHorizontal = props.orientation === 'horizontal'
  const isPill = props.variant === 'pill'
  const size = isHorizontal ? indicator.value.width : indicator.value.height
  const crossSize = isPill ? (isHorizontal ? indicator.value.height : indicator.value.width) : 2

  return {
    transform: isHorizontal ? undefined : `translate3d(0, ${indicator.value.y}px, 0)`,
    insetInlineStart: isHorizontal ? `${indicator.value.x}px` : undefined,
    width: isHorizontal ? `${size}px` : `${crossSize}px`,
    height: isHorizontal ? `${crossSize}px` : `${size}px`,
    transitionProperty: isHorizontal
      ? 'inset-inline-start, width, height'
      : 'transform, width, height',
    transitionDuration: `${props.transitionDuration}ms`,
    transitionTimingFunction: 'cubic-bezier(0.4, 0, 0.2, 1)',
  }
})

function select(value: TabItemValue): void {
  const item = contextItems.value.find((current) => current.value === value)
  if (!item || item.disabled || value === activeValue.value) return

  if (props.modelValue === undefined) internalValue.value = value
  emit('update:modelValue', value)
  emit('change', value)
}

async function moveSelection(direction: 1 | -1): Promise<void> {
  const enabledItems = contextItems.value.filter((item) => !item.disabled)
  if (enabledItems.length === 0) return

  const currentIndex = enabledItems.findIndex((item) => item.value === activeValue.value)
  const nextItem =
    enabledItems[(currentIndex + direction + enabledItems.length) % enabledItems.length]
  if (!nextItem) return

  select(nextItem.value)
  await nextTick()
  tabElements.get(nextItem.value)?.focus()
}

const context: BaseTabContext = {
  activeValue,
  orientation: computed(() => props.orientation),
  variant: computed(() => props.variant),
  transitionDuration: computed(() => props.transitionDuration),
  classes: computed(() => props.classes),
  indicatorStyle,
  items: contextItems,
  select,
  moveSelection,
  registerTab,
  unregisterTab,
  registerElement,
  updateIndicator,
  tabId,
  panelId,
}

const rootClass = computed(() =>
  resolveClasses(
    props.classes,
    'root',
    props.orientation === 'horizontal' ? 'flex w-full flex-col' : 'flex w-full items-start',
  ),
)

const panelContainerClass = computed(() =>
  resolveClasses(
    props.classes,
    'panelContainer',
    props.orientation === 'horizontal' ? 'w-full min-w-0' : 'min-w-0 flex-1',
  ),
)

provideBaseTabContext(context)

watch(
  () => [props.modelValue, props.orientation, props.variant, contextItems.value],
  () => {
    if (props.modelValue !== undefined) internalValue.value = props.modelValue
    else if (!contextItems.value.some((item) => item.value === internalValue.value)) {
      internalValue.value = contextItems.value.find((item) => !item.disabled)?.value
    }
    void nextTick(updateIndicator)
  },
  { deep: true },
)

onBeforeUnmount(() => {
  tabElements.clear()
})
</script>

<template>
  <div :class="rootClass">
    <slot>
      <Tabs>
        <TabHeader
          v-for="item in contextItems"
          :key="item.value"
          :value="item.value"
          :label="item.label"
          :icon="item.icon"
          :disabled="item.disabled"
        />
      </Tabs>

      <div :class="panelContainerClass">
        <TabPanel
          v-if="activeItem"
          :key="activeItem.value"
          :value="activeItem.value"
          :header="activeItem.header"
          :description="activeItem.description"
          :icon="activeItem.icon"
          :footer="activeItem.footer"
        >
          <template v-if="$slots[`panel-header-${String(activeItem.value)}`]" #header>
            <slot
              :name="`panel-header-${String(activeItem.value)}`"
              :item="activeItem"
              :active="true"
            />
          </template>
          <template #default>
            <slot :name="`panel-${String(activeItem.value)}`" :item="activeItem" :active="true">
              <TabSection :section="activeItem.content" />
            </slot>
          </template>
          <template v-if="$slots[`panel-footer-${String(activeItem.value)}`]" #footer>
            <slot
              :name="`panel-footer-${String(activeItem.value)}`"
              :item="activeItem"
              :active="true"
            />
          </template>
        </TabPanel>
      </div>
    </slot>
  </div>
</template>
