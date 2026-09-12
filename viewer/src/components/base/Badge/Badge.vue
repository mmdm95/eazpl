<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/utils'

import { resolveClasses } from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type { BadgeProps } from './types'

defineOptions({ name: 'BaseBadge' })

const props = withDefaults(defineProps<BadgeProps>(), {
  variant: 'primary',
  size: 'md',
  iconPosition: 'left',
  icon: undefined,
  classes: undefined,
})

const sizeClass = computed(
  () =>
    ({
      sm: 'gap-control-gap-sm px-control-padding-x-sm text-caption',
      md: 'gap-control-gap-sm px-control-padding-x-sm text-control-sm',
      lg: 'gap-control-gap-md px-control-padding-x-md text-control-md',
    })[props.size],
)

const variantClass = computed(
  () =>
    ({
      primary: 'border-transparent bg-primary text-content-inverted',
      secondary: 'border-transparent bg-secondary text-content-inverted',
      outline: 'border-border-strong bg-surface text-content',
      ghost: 'border-transparent bg-surface-muted text-content-muted',
      danger: 'border-transparent bg-danger text-content-inverted',
      success: 'border-transparent bg-success text-content-inverted',
    })[props.variant],
)

const iconClass = computed(() =>
  resolveClasses(
    props.classes,
    'icon',
    cn('h-icon-sm w-icon-sm shrink-0', props.size === 'lg' && 'h-icon-md w-icon-md'),
  ),
)

const rootClass = computed(() =>
  resolveClasses(
    props.classes,
    'root',
    cn(
      'inline-flex select-none items-center justify-center rounded-pill border py-0.5 font-medium',
      sizeClass.value,
      variantClass.value,
    ),
  ),
)
</script>

<template>
  <span :class="rootClass">
    <BaseLucideIcon
      v-if="props.icon && props.iconPosition === 'left'"
      :icon="props.icon"
      :classes="{ root: iconClass }"
    />
    <span
      v-if="$slots.default"
      :class="resolveClasses(props.classes, 'label', 'inline-flex items-center')"
    >
      <slot />
    </span>
    <BaseLucideIcon
      v-if="props.icon && props.iconPosition === 'right'"
      :icon="props.icon"
      :classes="{ root: iconClass }"
    />
  </span>
</template>
