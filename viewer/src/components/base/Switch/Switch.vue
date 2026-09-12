<script setup lang="ts">
import {LoaderCircle} from '@lucide/vue'
import {computed} from 'vue'
import {cn} from '@/utils'
import {resolveClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {SwitchProps} from './types'

defineOptions({name: 'BaseSwitch'})

const props = withDefaults(defineProps<SwitchProps>(), {
  modelValue: false,
  size: 'md',
  disabled: false,
  readonly: false,
  loading: false,
  label: undefined,
  description: undefined,
  error: undefined,
  name: undefined,
  value: undefined,
  onIcon: undefined,
  offIcon: undefined,
  loadingIcon: undefined,
  id: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  change: [value: boolean]
}>()

const generatedId = `base-switch-${Math.random().toString(36).slice(2, 10)}`
const switchId = computed(() => props.id ?? generatedId)
const isDisabled = computed(() => props.disabled || props.loading)

function toggle(): void {
  if (props.readonly || isDisabled.value) return
  const value = !props.modelValue
  emit('update:modelValue', value)
  emit('change', value)
}
</script>

<template>
  <div :class="resolveClasses(props.classes, 'root', 'flex flex-col gap-control-gap-sm')">
    <div class="flex items-center gap-control-gap-md">
      <button
        :id="switchId"
        type="button"
        role="switch"
        :aria-checked="props.modelValue"
        :aria-labelledby="props.label ? `${switchId}-label` : undefined"
        :aria-busy="props.loading"
        :disabled="isDisabled"
        :class="
          resolveClasses(
            props.classes,
            'control',
            cn(
              'relative inline-flex shrink-0 items-center rounded-pill border transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
              props.size === 'sm'
                ? 'h-switch-height-sm w-switch-width-sm'
                : props.size === 'lg'
                  ? 'h-switch-height-lg w-switch-width-lg'
                  : 'h-switch-height-md w-switch-width-md',
              props.modelValue
                ? 'border-transparent bg-primary'
                : 'border-transparent bg-border-strong',
              isDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer',
            ),
          )
        "
        @click="toggle"
      >
        <span
          :class="
            resolveClasses(
              props.classes,
              'knob',
            cn(
                'absolute flex items-center justify-center rounded-pill bg-surface shadow-sm transition-all duration-200',
                props.size === 'sm'
                  ? 'h-switch-knob-sm w-switch-knob-sm'
                  : props.size === 'lg'
                    ? 'h-switch-knob-lg w-switch-knob-lg'
                    : 'h-switch-knob-md w-switch-knob-md',
                props.modelValue
                  ? props.size === 'sm'
                    ? 'translate-x-4.5 rtl:-translate-x-4.5'
                    : props.size === 'lg'
                      ? 'translate-x-6 rtl:-translate-x-6'
                      : 'translate-x-5.5 rtl:-translate-x-5.5'
                  : 'inset-s-[0.15rem]',
            ),
            )
          "
        >
          <BaseLucideIcon
            v-if="props.loading"
            :icon="props.loadingIcon ?? LoaderCircle"
            :size="props.size === 'lg' ? 14 : 11"
            :classes="{
              root: resolveClasses(props.classes, 'loadingIcon', 'animate-spin text-primary'),
            }"
          />
          <BaseLucideIcon
            v-else-if="props.modelValue && props.onIcon"
            :icon="props.onIcon"
            :size="props.size === 'lg' ? 14 : 11"
            :classes="{ root: resolveClasses(props.classes, 'onIcon', 'text-primary') }"
          />
          <BaseLucideIcon
            v-else-if="!props.modelValue && props.offIcon"
            :icon="props.offIcon"
            :size="props.size === 'lg' ? 14 : 11"
            :classes="{ root: resolveClasses(props.classes, 'offIcon', 'text-content-subtle') }"
          />
        </span>
      </button>

      <label
        v-if="props.label || $slots.default"
        :id="`${switchId}-label`"
        :for="switchId"
        :class="
          resolveClasses(
            props.classes,
            'label',
            'cursor-pointer text-control-sm font-medium text-content-muted',
          )
        "
      >
        <slot>{{ props.label }}</slot>
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

    <input
      v-if="props.name"
      type="checkbox"
      :name="props.name"
      :value="props.value ?? 'on'"
      :checked="props.modelValue"
      class="hidden"
      tabindex="-1"
      aria-hidden="true"
      @change="toggle"
    />
  </div>
</template>
