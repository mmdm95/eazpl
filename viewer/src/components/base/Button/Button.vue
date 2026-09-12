<script setup lang="ts">
import {LoaderCircle} from '@lucide/vue'
import {computed} from 'vue'
import {cn} from '@/utils'
import {resolveClasses, sizeClasses, variantClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {ButtonProps} from './types'

defineOptions({name: 'BaseButton'})

const props = withDefaults(defineProps<ButtonProps>(), {
  type: 'button',
  variant: 'primary',
  size: 'md',
  iconPosition: 'left',
  icon: undefined,
  loadingIcon: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const isDisabled = computed(() => props.disabled || props.loading)
const activeIcon = computed(() =>
  props.loading ? (props.loadingIcon ?? LoaderCircle) : props.icon,
)
const iconClass = computed(() =>
  resolveClasses(
    props.classes,
    'icon',
    cn(
      'h-icon-sm w-icon-sm shrink-0',
      props.loading && 'animate-spin',
      isDisabled.value && 'opacity-70',
    ),
  ),
)
const rootClass = computed(() =>
  resolveClasses(
    props.classes,
    'root',
    cn(
      'inline-flex select-none items-center justify-center rounded-control font-medium transition-all duration-200 active:scale-motion-press',
      'border focus-visible:ring-2 focus-visible:ring-offset-2',
      sizeClasses[props.size],
      variantClasses[props.variant],
      isDisabled.value && 'cursor-not-allowed opacity-60',
      props.block && 'flex w-full',
      props.rounded && 'rounded-pill',
    ),
  ),
)

function onClick(event: MouseEvent): void {
  if (isDisabled.value) {
    event.preventDefault()
    return
  }
  emit('click', event)
}
</script>

<template>
  <button :type="props.type" :class="rootClass" :disabled="isDisabled" @click="onClick">
    <BaseLucideIcon
      v-if="activeIcon && props.iconPosition === 'left'"
      :icon="activeIcon"
      :classes="{ root: iconClass }"
    />
    <span
      v-if="$slots.default"
      :class="resolveClasses(props.classes, 'label', 'inline-flex items-center')"
    >
      <slot/>
    </span>
    <BaseLucideIcon
      v-if="activeIcon && props.iconPosition === 'right'"
      :icon="activeIcon"
      :classes="{ root: iconClass }"
    />
  </button>
</template>
