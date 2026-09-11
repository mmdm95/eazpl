<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/utils'
import { resolveClasses } from '../shared'
import type { RadioOption, RadioProps, RadioValue } from './types'

defineOptions({ name: 'BaseRadio' })

const props = withDefaults(defineProps<RadioProps>(), {
  modelValue: undefined,
  options: () => [],
  orientation: 'vertical',
  disabled: false,
  required: false,
  label: undefined,
  description: undefined,
  error: undefined,
  name: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: RadioValue]
  change: [value: RadioValue]
}>()

const groupName = computed(
  () => props.name ?? `base-radio-${Math.random().toString(36).slice(2, 10)}`,
)

function select(option: RadioOption): void {
  if (option.disabled || props.disabled) return
  emit('update:modelValue', option.value)
  emit('change', option.value)
}

function optionClass(option: RadioOption): string {
  return resolveClasses(
    props.classes,
    'option',
    cn(
      'flex cursor-pointer items-start gap-control-gap-md',
      option.disabled || props.disabled ? 'cursor-not-allowed opacity-60' : '',
    ),
  )
}
</script>

<template>
  <div :class="resolveClasses(props.classes, 'root', 'flex flex-col gap-control-gap-sm')">
    <span
      v-if="props.label"
      :id="`${groupName}-label`"
      :class="
        resolveClasses(
          props.classes,
          'groupLabel',
          'text-control-sm font-medium text-content-muted',
        )
      "
    >
      {{ props.label }}
    </span>

    <div
      role="radiogroup"
      :aria-labelledby="props.label ? `${groupName}-label` : undefined"
      :class="
        resolveClasses(
          props.classes,
          'group',
          cn(
            'flex gap-control-gap-md',
            props.orientation === 'horizontal' ? 'flex-row flex-wrap' : 'flex-col',
          ),
        )
      "
    >
      <label v-for="option in props.options" :key="option.value" :class="optionClass(option)">
        <input
          type="radio"
          :name="groupName"
          :class="
            resolveClasses(
              props.classes,
              'input',
              'mt-field-label-gap h-icon-sm w-icon-sm cursor-pointer accent-primary transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
            )
          "
          :value="option.value"
          :checked="option.value === props.modelValue"
          :disabled="option.disabled || props.disabled"
          :required="props.required"
          @change="select(option)"
        />

        <span class="flex flex-col gap-field-label-gap">
          <span
            :class="
              resolveClasses(
                props.classes,
                'optionLabel',
                'text-control-sm font-medium text-content-muted',
              )
            "
          >
            <slot :name="`option-${String(option.value)}`" :option="option">
              {{ option.label }}
            </slot>
          </span>
          <span
            v-if="option.description"
            :class="
              resolveClasses(props.classes, 'optionDescription', 'text-caption text-content-subtle')
            "
          >
            {{ option.description }}
          </span>
        </span>
      </label>
    </div>

    <p
      v-if="props.error"
      :class="resolveClasses(props.classes, 'error', 'text-caption font-medium text-danger')"
    >
      {{ props.error }}
    </p>
    <p
      v-else-if="props.description"
      :class="resolveClasses(props.classes, 'description', 'text-caption text-content-subtle')"
    >
      {{ props.description }}
    </p>
  </div>
</template>
