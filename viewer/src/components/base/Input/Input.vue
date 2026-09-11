<script setup lang="ts">
import { LoaderCircle, X } from '@lucide/vue'
import { computed, ref } from 'vue'
import { cn } from '@/utils'
import { resolveClasses } from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type { InputProps } from './types'
import { t } from '@/i18n'

defineOptions({ name: 'BaseInput' })

const props = withDefaults(defineProps<InputProps>(), {
  type: 'text',
  size: 'md',
  variant: 'outline',
  iconPosition: 'left',
  disabled: false,
  readonly: false,
  required: false,
  invalid: false,
  loading: false,
  clearable: false,
  modelValue: '',
  label: undefined,
  placeholder: undefined,
  name: undefined,
  autocomplete: undefined,
  description: undefined,
  error: undefined,
  icon: undefined,
  loadingIcon: undefined,
  clearIcon: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  clear: []
  focus: [event: FocusEvent]
  blur: [event: FocusEvent]
}>()

const inputRef = ref<HTMLInputElement | null>(null)
const generatedId = `base-input-${Math.random().toString(36).slice(2, 10)}`
const inputId = computed(() => props.id ?? generatedId)
const canClear = computed(
  () => props.clearable && props.modelValue !== '' && !props.disabled && !props.readonly,
)
const describedBy = computed(() =>
  props.error
    ? `${inputId.value}-error`
    : props.description
      ? `${inputId.value}-description`
      : undefined,
)

const rootClass = computed(() =>
  resolveClasses(props.classes, 'root', 'flex w-full flex-col gap-control-gap-sm'),
)
const inputWrapperClass = computed(() =>
  resolveClasses(
    props.classes,
    'inputWrapper',
    cn(
      'flex items-center rounded-control border bg-surface transition-all duration-200',
      props.size === 'sm'
        ? 'h-control-height-sm px-control-padding-x-sm text-control-sm gap-control-gap-sm'
        : props.size === 'lg'
          ? 'h-control-height-lg px-control-padding-x-lg text-control-lg gap-control-gap-lg'
          : 'h-control-height-md px-control-padding-x-md text-control-md gap-control-gap-md',
      props.variant === 'filled'
        ? 'border-transparent bg-surface-muted hover:bg-surface-hover'
        : 'border-border hover:border-border-strong',
      props.invalid
        ? 'border-danger ring-2 ring-danger-soft'
        : 'focus-within:border-primary focus-within:ring-2 focus-within:ring-primary-soft',
      props.disabled ? 'cursor-not-allowed bg-surface-muted opacity-70' : '',
      props.readonly ? 'bg-surface-muted' : '',
    ),
  ),
)
const inputClass = computed(() =>
  resolveClasses(
    props.classes,
    'input',
    cn(
      'h-full w-full min-w-0 bg-transparent text-content outline-none placeholder:text-content-subtle disabled:cursor-not-allowed',
      props.disabled ? 'text-content-subtle' : '',
    ),
  ),
)
const iconClass = computed(() =>
  resolveClasses(
    props.classes,
    'icon',
    cn('h-icon-sm w-icon-sm shrink-0', props.disabled && 'opacity-70'),
  ),
)
const loadingIconClass = computed(() =>
  resolveClasses(
    props.classes,
    'loadingIcon',
    'h-icon-sm w-icon-sm shrink-0 animate-spin text-primary',
  ),
)
const clearIconClass = computed(() =>
  resolveClasses(props.classes, 'clearIcon', 'h-icon-sm w-icon-sm'),
)

function onInput(event: Event): void {
  emit('update:modelValue', (event.target as HTMLInputElement).value)
}

function clear(): void {
  emit('update:modelValue', '')
  emit('clear')
  inputRef.value?.focus()
}

defineExpose({
  focus: () => inputRef.value?.focus(),
  blur: () => inputRef.value?.blur(),
  clear,
})
</script>

<template>
  <div :class="rootClass">
    <label
      v-if="props.label"
      :for="inputId"
      :class="
        resolveClasses(props.classes, 'label', 'text-control-sm font-medium text-content-muted')
      "
    >
      {{ props.label }}
      <span v-if="props.required" class="text-danger" aria-hidden="true">*</span>
    </label>

    <div :class="inputWrapperClass" :data-invalid="props.invalid">
      <slot name="prefix" :disabled="props.disabled" :size="props.size">
        <BaseLucideIcon
          v-if="props.icon && props.iconPosition === 'left'"
          :icon="props.icon"
          :classes="{ root: iconClass }"
        />
      </slot>

      <input
        :id="inputId"
        ref="inputRef"
        :type="props.type"
        :value="props.modelValue"
        :class="inputClass"
        :name="props.name"
        :placeholder="props.placeholder"
        :autocomplete="props.autocomplete"
        :disabled="props.disabled"
        :readonly="props.readonly"
        :required="props.required"
        :aria-invalid="props.invalid"
        :aria-describedby="describedBy"
        @input="onInput"
        @focus="emit('focus', $event)"
        @blur="emit('blur', $event)"
      />

      <BaseLucideIcon
        v-if="props.loading"
        :icon="props.loadingIcon ?? LoaderCircle"
        :classes="{ root: loadingIconClass }"
      />

      <button
        v-else-if="canClear"
        type="button"
        :class="
          resolveClasses(
            props.classes,
            'clearButton',
            'rounded p-field-action-padding text-content-subtle transition-colors hover:text-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
          )
        "
        :aria-label="t('common.clearInput')"
        @click.stop="clear"
      >
        <slot name="clearIcon">
          <BaseLucideIcon :icon="props.clearIcon ?? X" :classes="{ root: clearIconClass }" />
        </slot>
      </button>

      <slot name="suffix" :disabled="props.disabled" :size="props.size">
        <BaseLucideIcon
          v-if="props.icon && props.iconPosition === 'right'"
          :icon="props.icon"
          :classes="{ root: iconClass }"
        />
      </slot>
    </div>

    <p
      v-if="props.error"
      :id="`${inputId}-error`"
      :class="resolveClasses(props.classes, 'error', 'text-caption font-medium text-danger')"
    >
      {{ props.error }}
    </p>
    <p
      v-else-if="props.description"
      :id="`${inputId}-description`"
      :class="resolveClasses(props.classes, 'description', 'text-caption text-content-subtle')"
    >
      {{ props.description }}
    </p>
  </div>
</template>
