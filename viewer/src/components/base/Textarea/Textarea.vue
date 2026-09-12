<script setup lang="ts">
import {X} from '@lucide/vue'
import {computed, nextTick, ref} from 'vue'
import {cn} from '@/utils'
import {resolveClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {TextareaProps} from './types'
import {t} from '@/i18n'

defineOptions({name: 'BaseTextarea'})

const props = withDefaults(defineProps<TextareaProps>(), {
  modelValue: '',
  rows: 4,
  resize: 'vertical',
  variant: 'outline',
  disabled: false,
  readonly: false,
  required: false,
  invalid: false,
  clearable: false,
  autosize: false,
  label: undefined,
  placeholder: undefined,
  name: undefined,
  description: undefined,
  error: undefined,
  id: undefined,
  maxLength: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  clear: []
  focus: [event: FocusEvent]
  blur: [event: FocusEvent]
}>()

const textareaRef = ref<HTMLTextAreaElement | null>(null)
const generatedId = `base-textarea-${Math.random().toString(36).slice(2, 10)}`
const textareaId = computed(() => props.id ?? generatedId)
const canClear = computed(
  () => props.clearable && props.modelValue !== '' && !props.disabled && !props.readonly,
)
const describedBy = computed(() =>
  props.error
    ? `${textareaId.value}-error`
    : props.description
      ? `${textareaId.value}-description`
      : undefined,
)

function onInput(event: Event): void {
  emit('update:modelValue', (event.target as HTMLTextAreaElement).value)
  resize()
}

function resize(): void {
  if (!props.autosize || !textareaRef.value) return
  textareaRef.value.style.height = 'auto'
  textareaRef.value.style.height = `${textareaRef.value.scrollHeight}px`
}

function clear(): void {
  emit('update:modelValue', '')
  emit('clear')
  void nextTick(() => {
    resize()
    textareaRef.value?.focus()
  })
}

defineExpose({
  focus: () => textareaRef.value?.focus(),
  blur: () => textareaRef.value?.blur(),
  clear,
})
</script>

<template>
  <div :class="resolveClasses(props.classes, 'root', 'flex w-full flex-col gap-control-gap-sm')">
    <label
      v-if="props.label"
      :for="textareaId"
      :class="
        resolveClasses(props.classes, 'label', 'text-control-sm font-medium text-content-muted')
      "
    >
      {{ props.label }}
      <span v-if="props.required" class="text-danger" aria-hidden="true">*</span>
    </label>

    <div :class="resolveClasses(props.classes, 'wrapper', 'relative')">
      <textarea
        :id="textareaId"
        ref="textareaRef"
        :value="props.modelValue"
        :class="
          resolveClasses(
            props.classes,
            'textarea',
            cn(
              'w-full rounded-control border px-control-padding-x-md py-control-padding-y-sm text-control-md leading-relaxed text-content transition-all duration-200 placeholder:text-content-subtle focus:outline-none disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-content-subtle read-only:bg-surface-muted',
              props.variant === 'filled'
                ? 'border-transparent bg-surface-muted hover:bg-surface-hover focus-within:ring-2 focus-within:ring-primary-soft'
                : 'border-border bg-surface hover:border-border-strong focus-within:border-primary focus-within:ring-2 focus-within:ring-primary-soft',
              props.invalid ? 'border-danger ring-2 ring-danger-soft' : '',
              props.resize === 'none'
                ? 'resize-none'
                : props.resize === 'horizontal'
                  ? 'resize-x'
                  : props.resize === 'vertical'
                    ? 'resize-y'
                    : 'resize',
            ),
          )
        "
        :name="props.name"
        :rows="props.rows"
        :placeholder="props.placeholder"
        :disabled="props.disabled"
        :readonly="props.readonly"
        :required="props.required"
        :maxlength="props.maxLength"
        :aria-invalid="props.invalid"
        :aria-describedby="describedBy"
        @input="onInput"
        @focus="emit('focus', $event)"
        @blur="emit('blur', $event)"
      />

      <button
        v-if="canClear"
        type="button"
        :class="
          resolveClasses(
            props.classes,
            'clearButton',
            'absolute end-field-action-offset top-field-action-offset rounded p-field-action-padding text-content-subtle transition-colors hover:text-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
          )
        "
        :aria-label="t('common.clearTextarea')"
        @click.stop="clear"
      >
        <BaseLucideIcon
          :icon="X"
          :classes="{ root: resolveClasses(props.classes, 'clearIcon', 'h-icon-sm w-icon-sm') }"
        />
      </button>
    </div>

    <p
      v-if="props.error"
      :id="`${textareaId}-error`"
      :class="resolveClasses(props.classes, 'error', 'text-caption font-medium text-danger')"
    >
      {{ props.error }}
    </p>
    <p
      v-else-if="props.description"
      :id="`${textareaId}-description`"
      :class="resolveClasses(props.classes, 'description', 'text-caption text-content-subtle')"
    >
      {{ props.description }}
    </p>
  </div>
</template>
