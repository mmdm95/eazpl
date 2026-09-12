<script setup lang="ts">
import {Check, Minus} from '@lucide/vue'
import {computed} from 'vue'
import {cn} from '@/utils'
import {resolveClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {CheckboxModelValue, CheckboxProps} from './types'

defineOptions({name: 'BaseCheckbox'})

const props = withDefaults(defineProps<CheckboxProps>(), {
  modelValue: false,
  value: '',
  disabled: false,
  readonly: false,
  required: false,
  indeterminate: false,
  label: undefined,
  description: undefined,
  error: undefined,
  name: undefined,
  id: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: CheckboxModelValue]
  change: [value: CheckboxModelValue]
}>()

const generatedId = `base-checkbox-${Math.random().toString(36).slice(2, 10)}`
const checkboxId = computed(() => props.id ?? generatedId)
const isChecked = computed(() =>
  Array.isArray(props.modelValue) ? props.modelValue.includes(props.value) : props.modelValue,
)
const describedBy = computed(() =>
  props.error
    ? `${checkboxId.value}-error`
    : props.description
      ? `${checkboxId.value}-description`
      : undefined,
)

function nextValue(checked: boolean): CheckboxModelValue {
  if (!Array.isArray(props.modelValue)) return checked
  return checked
    ? [...props.modelValue, props.value]
    : props.modelValue.filter((item) => item !== props.value)
}

function onChange(event: Event): void {
  if (props.disabled || props.readonly) {
    ;(event.target as HTMLInputElement).checked = isChecked.value
    return
  }

  const value = nextValue((event.target as HTMLInputElement).checked)
  emit('update:modelValue', value)
  emit('change', value)
}
</script>

<template>
  <div :class="resolveClasses(props.classes, 'root', 'flex flex-col gap-control-gap-sm')">
    <label
      :class="
        resolveClasses(
          props.classes,
          'labelWrapper',
          cn(
            'inline-flex cursor-pointer items-start gap-control-gap-md',
            props.disabled && 'cursor-not-allowed opacity-60',
          ),
        )
      "
    >
      <span
        :class="resolveClasses(props.classes, 'inputWrapper', 'relative inline-flex mt-field-label-gap')">
        <input
          :id="checkboxId"
          type="checkbox"
          :class="
            resolveClasses(
              props.classes,
              'input',
              cn(
                'peer h-icon-sm w-icon-sm cursor-pointer appearance-none rounded-control border border-border-strong bg-surface transition-all duration-200 checked:border-primary checked:bg-primary indeterminate:border-primary indeterminate:bg-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
                props.disabled && 'cursor-not-allowed',
              ),
            )
          "
          :name="props.name"
          :checked="isChecked"
          :disabled="props.disabled"
          :required="props.required"
          :aria-invalid="Boolean(props.error)"
          :aria-describedby="describedBy"
          @change="onChange"
        />
        <span
          :class="
            resolveClasses(
              props.classes,
              'iconWrapper',
              'pointer-events-none absolute inset-0 flex items-center justify-center text-content-inverted',
            )
          "
        >
          <BaseLucideIcon
            v-if="props.indeterminate && !isChecked"
            :icon="Minus"
            :size="13"
            :classes="{ root: resolveClasses(props.classes, 'icon', 'h-icon-xs w-icon-xs') }"
          />
          <BaseLucideIcon
            v-else-if="isChecked"
            :icon="props.icon ?? Check"
            :size="13"
            :classes="{ root: resolveClasses(props.classes, 'icon', 'h-icon-xs w-icon-xs') }"
          />
        </span>
      </span>

      <span v-if="props.label || $slots.default" class="flex flex-col gap-field-label-gap">
        <span
          :class="
            resolveClasses(props.classes, 'label', 'text-control-sm font-medium text-content-muted')
          "
        >
          <slot>{{ props.label }}</slot>
        </span>
        <span
          v-if="props.description"
          :class="resolveClasses(props.classes, 'description', 'text-caption text-content-subtle')"
        >
          {{ props.description }}
        </span>
      </span>
    </label>

    <p
      v-if="props.error"
      :id="`${checkboxId}-error`"
      :class="resolveClasses(props.classes, 'error', 'text-caption font-medium text-danger')"
    >
      {{ props.error }}
    </p>
  </div>
</template>
