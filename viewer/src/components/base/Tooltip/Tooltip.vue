<script setup lang="ts">
import {computed, nextTick, onBeforeUnmount, ref, useSlots, watch} from 'vue'
import {resolveClasses} from '../shared'
import type {TooltipPlacement, TooltipProps} from './types'

defineOptions({name: 'BaseTooltip'})

const props = withDefaults(defineProps<TooltipProps>(), {
  content: undefined,
  placement: 'top',
  delay: 100,
  transitionDuration: 150,
  disabled: false,
  showArrow: true,
  id: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  show: []
  hide: []
}>()

const isVisible = ref(false)
const isPositioned = ref(false)
const timeoutId = ref<number | undefined>(undefined)
const generatedId = `base-tooltip-${Math.random().toString(36).slice(2, 10)}`
const tooltipId = computed(() => props.id ?? generatedId)
const triggerRef = ref<HTMLSpanElement | null>(null)
const contentRef = ref<HTMLSpanElement | null>(null)
const slots = useSlots()
const hasContent = computed(() => Boolean(props.content) || Boolean(slots.content))
const position = ref({
  x: 0,
  y: 0,
  arrowX: 0,
  arrowY: 0,
  placement: props.placement,
})
const viewportPadding = 8
const overlayGap = 8
const arrowSize = 6

function clearTimeout(): void {
  if (timeoutId.value === undefined) return
  window.clearTimeout(timeoutId.value)
  timeoutId.value = undefined
}

function scheduleShow(): void {
  clearTimeout()
  if (props.disabled || !hasContent.value) return
  timeoutId.value = window.setTimeout(
    () => {
      isVisible.value = true
    },
    Math.max(0, props.delay),
  )
}

function scheduleHide(): void {
  clearTimeout()
  timeoutId.value = window.setTimeout(() => {
    if (!isVisible.value) return
    isVisible.value = false
    emit('hide')
  }, props.delay)
}

function hide(): void {
  clearTimeout()
  if (!isVisible.value) return
  isVisible.value = false
  emit('hide')
}

function onKeydown(event: KeyboardEvent): void {
  if (event.key === 'Escape') hide()
}

function placementSpace(placement: TooltipPlacement, triggerRect: DOMRect): number {
  if (placement === 'top') return triggerRect.top - viewportPadding - overlayGap
  if (placement === 'right') {
    return window.innerWidth - triggerRect.right - viewportPadding - overlayGap
  }
  if (placement === 'bottom') {
    return window.innerHeight - triggerRect.bottom - viewportPadding - overlayGap
  }
  return triggerRect.left - viewportPadding - overlayGap
}

function clamp(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max)
}

function positionTooltip(): void {
  const trigger = triggerRef.value?.firstElementChild as HTMLElement | null
  const tooltip = contentRef.value
  if (!trigger || !tooltip) return

  const triggerRect = trigger.getBoundingClientRect()
  const tooltipRect = tooltip.getBoundingClientRect()

  // NOTE: triggerRect/tooltipRect come from getBoundingClientRect(), which is
  // always reported in physical viewport coordinates. That means `left` and
  // `right` here already mean "physically left" and "physically right" of the
  // trigger, regardless of the document's writing direction. There is no need
  // (and it's actively wrong) to flip left/right based on `dir="rtl"` — doing
  // so used to place the tooltip on the opposite side from what was requested
  // whenever the page was RTL.
  const preferredPlacement: TooltipPlacement = props.placement ?? 'top'
  const oppositePlacement: TooltipPlacement =
    preferredPlacement === 'top'
      ? 'bottom'
      : preferredPlacement === 'right'
        ? 'left'
        : preferredPlacement === 'bottom'
          ? 'top'
          : 'left'
  const placements: TooltipPlacement[] = [
    preferredPlacement,
    oppositePlacement,
    'top',
    'right',
    'bottom',
    'left',
  ]
  let placement = preferredPlacement
  const requiredSize = (candidate: TooltipPlacement) =>
    candidate === 'top' || candidate === 'bottom' ? tooltipRect.height : tooltipRect.width

  if (placementSpace(preferredPlacement, triggerRect) < requiredSize(preferredPlacement)) {
    placement =
      placementSpace(oppositePlacement, triggerRect) >= requiredSize(oppositePlacement)
        ? oppositePlacement
        : ([...placements].sort(
          (first, second) =>
            placementSpace(second, triggerRect) - placementSpace(first, triggerRect),
        )[0] ?? 'bottom')
  }

  let x = 0
  let y = 0
  if (placement === 'top') {
    x = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2
    y = triggerRect.top - overlayGap - tooltipRect.height
  } else if (placement === 'right') {
    x = triggerRect.right + overlayGap
    y = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2
  } else if (placement === 'bottom') {
    x = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2
    y = triggerRect.bottom + overlayGap
  } else {
    x = triggerRect.left - overlayGap - tooltipRect.width
    y = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2
  }

  x = clamp(
    x,
    viewportPadding,
    Math.max(viewportPadding, window.innerWidth - viewportPadding - tooltipRect.width),
  )
  y = clamp(
    y,
    viewportPadding,
    Math.max(viewportPadding, window.innerHeight - viewportPadding - tooltipRect.height),
  )

  position.value = {
    x,
    y,
    arrowX: clamp(
      triggerRect.left + triggerRect.width / 2 - x,
      arrowSize,
      Math.max(arrowSize, tooltipRect.width - arrowSize),
    ),
    arrowY: clamp(
      triggerRect.top + triggerRect.height / 2 - y,
      arrowSize,
      Math.max(arrowSize, tooltipRect.height - arrowSize),
    ),
    placement,
  }
}

const contentStyle = computed(() => ({
  transform: `translate3d(${position.value.x}px, ${position.value.y}px, 0)`,
  maxWidth: `calc(100vw - ${viewportPadding * 2}px)`,
  visibility: isPositioned.value ? ('visible' as const) : ('hidden' as const),
}))

const arrowFillColor = 'var(--color-surface-raised)'
const arrowBorderColor = 'var(--color-border)'

const arrowStyle = computed(() => {
  const n = arrowSize
  const common = {
    width: '0px',
    height: '0px',
    borderStyle: 'solid',
  }

  const overlap = 2

  if (position.value.placement === 'top') {
    return {
      ...common,
      left: `${position.value.arrowX}px`,
      top: `calc(100% - ${overlap}px)`,
      transform: 'translateX(-50%)',
      borderWidth: `${n}px ${n}px 0 ${n}px`,
      borderColor: `${arrowFillColor} transparent transparent transparent`,
      filter: `drop-shadow(0 1px 0 ${arrowBorderColor})`,
    }
  }
  if (position.value.placement === 'bottom') {
    return {
      ...common,
      left: `${position.value.arrowX}px`,
      top: `-${n - overlap}px`,
      transform: 'translateX(-50%)',
      borderWidth: `0 ${n}px ${n}px ${n}px`,
      borderColor: `transparent transparent ${arrowFillColor} transparent`,
      filter: `drop-shadow(0 -1px 0 ${arrowBorderColor})`,
    }
  }
  if (position.value.placement === 'right') {
    return {
      ...common,
      left: `-${n - overlap}px`,
      top: `${position.value.arrowY}px`,
      transform: 'translateY(-50%)',
      borderWidth: `${n}px ${n}px ${n}px 0`,
      borderColor: `transparent ${arrowFillColor} transparent transparent`,
      filter: `drop-shadow(-1px 0 0 ${arrowBorderColor})`,
    }
  }

  return {
    ...common,
    left: `calc(100% - ${overlap}px)`,
    top: `${position.value.arrowY}px`,
    transform: 'translateY(-50%)',
    borderWidth: `${n}px 0 ${n}px ${n}px`,
    borderColor: `transparent transparent transparent ${arrowFillColor}`,
    filter: `drop-shadow(1px 0 0 ${arrowBorderColor})`,
  }
})

watch(
  [isVisible, tooltipId],
  ([visible, id]) => {
    const trigger = triggerRef.value?.firstElementChild
    if (!trigger) return
    if (visible) trigger.setAttribute('aria-describedby', id)
    else trigger.removeAttribute('aria-describedby')
  },
  {flush: 'post'},
)

watch(isVisible, async (visible) => {
  if (!visible) {
    isPositioned.value = false
    emit('hide')
    return
  }

  await nextTick()
  positionTooltip()
  isPositioned.value = true
  emit('show')
})

watch(
  () => [props.content, props.placement] as const,
  async () => {
    if (!isVisible.value) return
    await nextTick()
    positionTooltip()
  },
)

watch(
  () => props.disabled,
  (disabled) => {
    if (disabled) hide()
  },
)

if (typeof window !== 'undefined') {
  window.addEventListener('resize', positionTooltip)
  window.addEventListener('scroll', positionTooltip, true)
}

onBeforeUnmount(clearTimeout)
onBeforeUnmount(() => triggerRef.value?.firstElementChild?.removeAttribute('aria-describedby'))
onBeforeUnmount(() => {
  if (typeof window === 'undefined') return
  window.removeEventListener('resize', positionTooltip)
  window.removeEventListener('scroll', positionTooltip, true)
})
</script>

<template>
  <span
    :class="resolveClasses(props.classes, 'root', 'relative inline-flex max-w-full')"
    @pointerenter="scheduleShow"
    @pointerleave="scheduleHide"
    @focusin="scheduleShow"
    @focusout="scheduleHide"
    @keydown="onKeydown"
  >
    <span
      ref="triggerRef"
      :class="resolveClasses(props.classes, 'trigger', 'inline-flex max-w-full')"
    >
      <slot/>
    </span>

    <Teleport to="body">
      <Transition
        :duration="props.transitionDuration"
        enter-active-class="transition-opacity ease-out"
        leave-active-class="transition-opacity ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <span
          v-if="isVisible"
          class="pointer-events-none fixed left-0 top-0 z-50 w-max"
          :style="contentStyle"
        >
          <span
            v-if="props.showArrow"
            :class="resolveClasses(props.classes, 'arrow', 'absolute z-1')"
            :style="arrowStyle"
            aria-hidden="true"
          />
          <span
            ref="contentRef"
            :id="tooltipId"
            :class="
              resolveClasses(
                props.classes,
                'content',
                'relative z-0 block w-max max-w-full rounded-control border border-border bg-surface-raised px-control-padding-x-sm py-control-padding-y-sm text-control-sm text-content shadow-lg shadow-shadow',
              )
            "
            role="tooltip"
          >
            <slot name="content">{{ props.content }}</slot>
          </span>
        </span>
      </Transition>
    </Teleport>
  </span>
</template>
