<script setup lang="ts">
import { Check, ChevronDown, LoaderCircle, X } from '@lucide/vue'
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { cn } from '@/utils'
import { resolveClasses, sizeClasses, variantClasses } from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type { DropdownOption, DropdownProps, DropdownValue } from './types'
import { t } from '@/i18n'

defineOptions({ name: 'BaseDropdown' })

const props = withDefaults(defineProps<DropdownProps>(), {
  modelValue: undefined,
  options: () => [],
  variant: 'outline',
  size: 'md',
  placement: 'bottom-start',
  icon: undefined,
  loadingIcon: undefined,
  clearIcon: undefined,
  iconPosition: 'left',
  placeholder: undefined,
  label: undefined,
  disabled: false,
  loading: false,
  clearable: false,
  closeOnSelect: true,
  transitionDuration: 200,
  name: undefined,
  id: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: DropdownValue | null]
  change: [value: DropdownValue]
  clear: []
  open: []
  close: []
}>()

const rootRef = ref<HTMLDivElement | null>(null)
const isOpen = ref(false)
const activeIndex = ref(-1)
const generatedId = `base-dropdown-${Math.random().toString(36).slice(2, 10)}`
const dropdownId = computed(() => props.id ?? generatedId)
const selectedOption = computed(() =>
  props.options.find((option) => option.value === props.modelValue),
)
const enabledOptions = computed(() => props.options.filter((option) => !option.disabled))
const isDisabled = computed(() => props.disabled || props.loading)

function onDocumentClick(event: MouseEvent): void {
  if (!rootRef.value?.contains(event.target as Node)) close()
}

watch(isOpen, (open) => {
  if (typeof document === 'undefined') return
  if (open) {
    emit('open')
    document.addEventListener('mousedown', onDocumentClick)
  } else {
    emit('close')
    document.removeEventListener('mousedown', onDocumentClick)
  }
})

onBeforeUnmount(() => {
  if (typeof document !== 'undefined') document.removeEventListener('mousedown', onDocumentClick)
})

function open(): void {
  if (isDisabled.value || isOpen.value) return
  activeIndex.value = enabledOptions.value.findIndex((option) => option.value === props.modelValue)
  isOpen.value = true
}

function close(): void {
  if (!isOpen.value) return
  isOpen.value = false
  activeIndex.value = -1
}

function toggle(): void {
  if (isOpen.value) close()
  else open()
}

function select(option: DropdownOption): void {
  if (option.disabled || props.disabled) return
  emit('update:modelValue', option.value)
  emit('change', option.value)
  if (props.closeOnSelect) close()
}

function clear(): void {
  emit('update:modelValue', null)
  emit('clear')
  close()
}

function moveActive(direction: 1 | -1): void {
  if (!enabledOptions.value.length) return
  const currentIndex = activeIndex.value
  activeIndex.value =
    currentIndex === -1
      ? direction === 1
        ? 0
        : enabledOptions.value.length - 1
      : (currentIndex + direction + enabledOptions.value.length) % enabledOptions.value.length
}

function onKeydown(event: KeyboardEvent): void {
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    if (!isOpen.value) open()
    else moveActive(1)
  } else if (event.key === 'ArrowUp') {
    event.preventDefault()
    if (!isOpen.value) open()
    else moveActive(-1)
  } else if (event.key === 'Enter' && isOpen.value) {
    event.preventDefault()
    const option = enabledOptions.value[activeIndex.value]
    if (option) select(option)
  } else if (event.key === 'Escape') {
    close()
  } else if (event.key === 'Tab') {
    close()
  }
}

function optionClass(option: DropdownOption): string {
  const index = props.options.indexOf(option)
  return resolveClasses(
    props.classes,
    'option',
    cn(
      'flex w-full rounded-overlay cursor-pointer items-center justify-between gap-control-gap-md px-control-padding-x-md py-control-padding-y-sm text-start text-control-sm transition-colors duration-150',
      option.value === props.modelValue
        ? 'bg-primary-soft text-primary'
        : 'text-content hover:bg-surface-hover',
      option.disabled && 'cursor-not-allowed opacity-50',
      index === activeIndex.value && 'bg-surface-hover',
    ),
  )
}

function placementClass(): string {
  const placements: Record<NonNullable<DropdownProps['placement']>, string> = {
    'bottom-start': 'top-full start-0 mt-overlay-gap',
    'bottom-end': 'top-full end-0 mt-overlay-gap',
    'top-start': 'bottom-full start-0 mb-overlay-gap',
    'top-end': 'bottom-full end-0 mb-overlay-gap',
  }
  return placements[props.placement]
}
</script>

<template>
  <div
    ref="rootRef"
    :class="
      resolveClasses(props.classes, 'root', 'relative flex w-full flex-col gap-control-gap-sm')
    "
    @keydown="onKeydown"
  >
    <label
      v-if="props.label"
      :for="dropdownId"
      :class="
        resolveClasses(props.classes, 'label', 'text-control-sm font-medium text-content-muted')
      "
    >
      {{ props.label }}
    </label>

    <div :class="resolveClasses(props.classes, 'triggerWrapper', 'relative')">
      <button
        :id="dropdownId"
        type="button"
        :class="
          resolveClasses(
            props.classes,
            'trigger',
            cn(
              'flex w-full items-center justify-between gap-control-gap-md rounded-control border font-medium transition-all duration-200 focus-visible:ring-2 focus-visible:ring-offset-2',
              sizeClasses[props.size],
              variantClasses[props.variant],
              props.clearable && selectedOption && 'pe-dropdown-clear-space',
              isDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer',
            ),
          )
        "
        :disabled="isDisabled"
        :aria-haspopup="'listbox'"
        :aria-expanded="isOpen"
        @click="toggle"
      >
        <span class="flex min-w-0 flex-1 items-center gap-control-gap-md">
          <BaseLucideIcon
            v-if="props.icon && props.iconPosition === 'left'"
            :icon="props.icon"
            :classes="{
              root: resolveClasses(props.classes, 'icon', 'h-icon-sm w-icon-sm shrink-0'),
            }"
          />
          <span
            :class="
              resolveClasses(
                props.classes,
                selectedOption ? 'value' : 'placeholder',
                selectedOption ? 'truncate text-content' : 'truncate text-content-subtle',
              )
            "
          >
            {{ selectedOption?.label ?? props.placeholder ?? t('common.selectOption') }}
          </span>
          <BaseLucideIcon
            v-if="props.icon && props.iconPosition === 'right'"
            :icon="props.icon"
            :classes="{
              root: resolveClasses(props.classes, 'icon', 'h-icon-sm w-icon-sm shrink-0'),
            }"
          />
        </span>

        <span class="flex shrink-0 items-center gap-control-gap-sm">
          <BaseLucideIcon
            v-if="props.loading"
            :icon="props.loadingIcon ?? LoaderCircle"
            :classes="{
              root: resolveClasses(
                props.classes,
                'loadingIcon',
                'h-icon-sm w-icon-sm animate-spin text-primary',
              ),
            }"
          />
          <BaseLucideIcon
            :icon="ChevronDown"
            :classes="{
              root: resolveClasses(
                props.classes,
                'triggerIcon',
                cn(
                  'h-icon-sm w-icon-sm shrink-0 text-content-subtle transition-transform duration-200',
                  isOpen ? 'rotate-180' : '',
                ),
              ),
            }"
          />
        </span>
      </button>

      <button
        v-if="!props.loading && props.clearable && selectedOption"
        type="button"
        :class="
          resolveClasses(
            props.classes,
            'clearButton',
            'dropdown-clear-position absolute top-1/2 -translate-y-1/2 rounded p-field-action-padding text-content-subtle transition-colors hover:text-content focus-visible:outline-2 focus-visible:outline-primary',
          )
        "
        :aria-label="t('common.clearOption')"
        @click.stop="clear"
      >
        <BaseLucideIcon
          :icon="props.clearIcon ?? X"
          :classes="{ root: resolveClasses(props.classes, 'clearIcon', 'h-icon-sm w-icon-sm') }"
        />
      </button>

      <Transition
        :duration="props.transitionDuration"
        enter-active-class="transition-all ease-out"
        leave-active-class="transition-all ease-in"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <ul
          v-if="isOpen"
          :class="
            resolveClasses(
              props.classes,
              'menu',
              cn(
                'absolute z-40 max-h-menu-max-height w-full overflow-y-auto rounded-overlay border border-border bg-surface-raised p-menu-padding shadow-xl shadow-shadow',
                placementClass(),
              ),
            )
          "
          role="listbox"
          :aria-labelledby="dropdownId"
          tabindex="-1"
        >
          <li
            v-for="option in props.options"
            :key="option.value"
            :class="optionClass(option)"
            role="option"
            :aria-selected="option.value === props.modelValue"
            :aria-disabled="option.disabled"
            @click="select(option)"
            @mousemove="activeIndex = props.options.indexOf(option)"
          >
            <span class="flex min-w-0 items-center gap-control-gap-md">
              <BaseLucideIcon
                v-if="option.icon"
                :icon="option.icon"
                :classes="{
                  root: resolveClasses(
                    props.classes,
                    'optionIcon',
                    'h-icon-sm w-icon-sm shrink-0 text-primary',
                  ),
                }"
              />
              <span :class="resolveClasses(props.classes, 'optionLabel', 'truncate')">
                {{ option.label }}
              </span>
            </span>
            <BaseLucideIcon
              v-if="option.value === props.modelValue"
              :icon="Check"
              :classes="{
                root: resolveClasses(
                  props.classes,
                  'selectedIcon',
                  'h-icon-sm w-icon-sm shrink-0 text-primary',
                ),
              }"
            />
          </li>
        </ul>
      </Transition>
    </div>

    <input
      v-if="props.name"
      type="hidden"
      :name="props.name"
      :value="props.modelValue ?? ''"
      aria-hidden="true"
    />
  </div>
</template>
