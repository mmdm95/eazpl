<script setup lang="ts">
import {CircleAlert, CircleCheck, Info, X} from '@lucide/vue'
import {computed} from 'vue'
import {cn} from '@/utils'
import {t} from '@/i18n'

import {resolveClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {AlertProps} from './types'

defineOptions({name: 'BaseAlert'})

const props = withDefaults(defineProps<AlertProps>(), {
  variant: 'primary',
  title: undefined,
  description: undefined,
  icon: undefined,
  showIcon: true,
  closable: false,
  closeIcon: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  close: []
}>()

const variantClass = computed(
  () =>
    ({
      primary: 'border-primary-soft bg-primary-soft',
      secondary: 'border-border bg-surface-muted',
      outline: 'border-border-strong bg-surface',
      ghost: 'border-transparent bg-surface-muted',
      danger: 'border-danger-soft bg-danger-soft',
      success: 'border-success-soft bg-success-soft',
    })[props.variant],
)

const activeIcon = computed(() => {
  if (!props.showIcon) return undefined

  return (
    props.icon ??
    {
      primary: Info,
      secondary: Info,
      outline: Info,
      ghost: Info,
      danger: CircleAlert,
      success: CircleCheck,
    }[props.variant]
  )
})

const iconColorClass = computed(
  () =>
    ({
      primary: 'text-primary',
      secondary: 'text-content-muted',
      outline: 'text-content-muted',
      ghost: 'text-content-muted',
      danger: 'text-danger',
      success: 'text-success',
    })[props.variant],
)

const rootClass = computed(() =>
  resolveClasses(
    props.classes,
    'root',
    cn(
      'flex w-full items-start gap-control-gap-md rounded-card border p-content-sm',
      variantClass.value,
    ),
  ),
)
</script>

<template>
  <div role="alert" :class="rootClass">
    <BaseLucideIcon
      v-if="activeIcon"
      :icon="activeIcon"
      :classes="{
        root: resolveClasses(
          props.classes,
          'icon',
          cn('mt-field-label-gap h-icon-md w-icon-md shrink-0', iconColorClass),
        ),
      }"
    />

    <div
      v-if="props.title || props.description || $slots.default"
      :class="resolveClasses(props.classes, 'content', 'min-w-0 flex-1')"
    >
      <h3 :class="resolveClasses(props.classes, 'title', 'text-title font-semibold text-content')">
        <slot name="title">{{ props.title }}</slot>
      </h3>
      <p
        v-if="props.description || $slots.description"
        :class="
          resolveClasses(props.classes, 'description', 'mt-1 text-control-sm text-content-muted')
        "
      >
        <slot name="description">{{ props.description }}</slot>
      </p>
      <div
        v-if="$slots.default"
        :class="
          resolveClasses(
            props.classes,
            'message',
            'mt-content-sm text-control-sm text-content-muted',
          )
        "
      >
        <slot/>
      </div>
    </div>

    <button
      v-if="props.closable"
      type="button"
      :class="
        resolveClasses(
          props.classes,
          'close',
          'rounded-control p-control-padding-y-sm text-content-muted transition hover:bg-surface-hover hover:text-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
        )
      "
      :aria-label="t('common.closeAlert')"
      @click="emit('close')"
    >
      <slot name="close">
        <BaseLucideIcon
          :icon="props.closeIcon ?? X"
          :classes="{
            root: resolveClasses(props.classes, 'closeIcon', 'h-icon-sm w-icon-sm'),
          }"
        />
      </slot>
    </button>
  </div>
</template>
