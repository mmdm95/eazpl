<script setup lang="ts">
import {ChevronDown} from '@lucide/vue'
import {computed} from 'vue'
import {cn} from '@/utils'
import {resolveClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import AccordionSection from './AccordionSection.vue'
import type {AccordionItem, AccordionModelValue, AccordionProps} from './types'

defineOptions({name: 'BaseAccordion'})

const props = withDefaults(defineProps<AccordionProps>(), {
  modelValue: undefined,
  multiple: false,
  transitionDuration: 300,
  classes: undefined,
  items: () => [],
})

const emit = defineEmits<{
  'update:modelValue': [value: AccordionModelValue]
  toggle: [item: AccordionItem, active: boolean]
}>()

const activeValues = computed<string[]>(() => {
  if (props.multiple) {
    return props.modelValue === undefined
      ? []
      : (props.modelValue as Array<string | number>).map(String)
  }
  return props.modelValue === undefined ? [] : [String(props.modelValue as string | number)]
})

function isActive(item: AccordionItem): boolean {
  return activeValues.value.includes(String(item.value))
}

function toggle(item: AccordionItem): void {
  const active = isActive(item)

  if (props.multiple) {
    const current = activeValues.value
    const next = active
      ? current.filter((value) => value !== String(item.value))
      : [...current, String(item.value)]
    emit('update:modelValue', next)
  } else {
    emit('update:modelValue', active ? null : item.value)
  }

  emit('toggle', item, !active)
}

function itemClass(item: AccordionItem): string {
  return resolveClasses(
    props.classes,
    'item',
    cn(
      'rounded-card border border-border bg-surface transition-colors duration-200',
      isActive(item)
        ? 'border-border-strong shadow-sm shadow-shadow'
        : 'hover:border-border-strong',
    ),
  )
}

function headerClass(item: AccordionItem): string {
  return resolveClasses(
    props.classes,
    'header',
    cn(
      'flex w-full items-center justify-between gap-control-gap-md px-content-sm py-control-padding-y-sm text-start text-control-sm font-semibold text-content transition-colors duration-200',
      item.disabled
        ? 'cursor-not-allowed opacity-50'
        : 'cursor-pointer hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-primary',
    ),
  )
}

function contentClass(): string {
  return resolveClasses(
    props.classes,
    'content',
    'overflow-hidden text-control-sm leading-relaxed text-content-muted',
  )
}

function itemIconClass(item: AccordionItem): string {
  return resolveClasses(
    props.classes,
    'icon',
    cn('h-icon-sm w-icon-sm shrink-0 text-primary', item.disabled && 'opacity-70'),
  )
}
</script>

<template>
  <div
    :class="resolveClasses(props.classes, 'root', 'flex flex-col gap-control-gap-sm')"
    :data-multiple="props.multiple"
  >
    <div v-for="item in props.items" :key="item.value" :class="itemClass(item)">
      <h3 :class="resolveClasses(props.classes, 'headerWrapper', 'm-0')">
        <button
          type="button"
          :class="headerClass(item)"
          :disabled="item.disabled"
          :aria-expanded="isActive(item)"
          :aria-controls="`accordion-content-${String(item.value)}`"
          :id="`accordion-header-${String(item.value)}`"
          @click="toggle(item)"
        >
          <slot name="header" :item="item" :active="isActive(item)">
            <BaseLucideIcon
              v-if="item.icon"
              :icon="item.icon"
              :classes="{ root: itemIconClass(item) }"
            />
            <span :class="resolveClasses(props.classes, 'headerTitle', 'flex-1')">
              <AccordionSection :section="item.header"/>
            </span>
          </slot>
          <BaseLucideIcon
            :icon="ChevronDown"
            :classes="{
              root: resolveClasses(
                props.classes,
                'headerIcon',
                cn(
                  'h-icon-sm w-icon-sm shrink-0 text-content-subtle transition-transform duration-300',
                  isActive(item) ? 'rotate-180 text-primary' : '',
                ),
              ),
            }"
          />
        </button>
      </h3>
      <div
        :id="`accordion-content-${String(item.value)}`"
        role="region"
        :aria-labelledby="`accordion-header-${String(item.value)}`"
        :class="
          resolveClasses(
            props.classes,
            'contentWrapper',
            cn(
              'grid transition-[grid-template-rows] ease-out',
              isActive(item) ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]',
            ),
          )
        "
        :style="{ transitionDuration: `${props.transitionDuration}ms` }"
      >
        <div :class="contentClass()">
          <div
            :class="
              resolveClasses(props.classes, 'contentInner', 'px-content-sm pb-content-sm pt-0')
            "
          >
            <slot :name="`content-${String(item.value)}`" :item="item" :active="isActive(item)">
              <AccordionSection :section="item.content"/>
            </slot>
            <footer
              v-if="item.footer || $slots[`footer-${String(item.value)}`]"
              :class="
                resolveClasses(
                  props.classes,
                  'footer',
                  'mt-content-sm flex items-center gap-control-gap-md border-t border-border pt-content-sm text-caption text-content-subtle',
                )
              "
            >
              <slot :name="`footer-${String(item.value)}`" :item="item" :active="isActive(item)">
                <AccordionSection :section="item.footer"/>
              </slot>
            </footer>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
