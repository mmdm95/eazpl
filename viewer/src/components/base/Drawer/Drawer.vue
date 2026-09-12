<script setup lang="ts">
import {X} from '@lucide/vue'
import {computed, type CSSProperties, onBeforeUnmount, watch} from 'vue'
import {cn} from '@/utils'
import {overlayContainerClass, overlayPanelEnterClass, resolveClasses} from '../shared'
import {type DrawerCloseEdge, useDrawerDismissDrag} from '../useDrawerDismissDrag'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {DrawerProps} from './types'
import {t} from '@/i18n'

defineOptions({name: 'BaseDrawer'})

const props = withDefaults(defineProps<DrawerProps>(), {
  modelValue: false,
  position: 'right',
  size: 'md',
  closable: true,
  closeOnEsc: true,
  closeOnBackdrop: true,
  lockScroll: true,
  overlayBlur: false,
  draggable: false,
  dragThreshold: 72,
  transitionDuration: 300,
  title: undefined,
  icon: undefined,
  closeIcon: undefined,
  zIndex: 50,
  width: undefined,
  height: undefined,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  show: []
  hide: []
  'after-leave': []
  'drag-start': [event: PointerEvent]
  drag: [event: PointerEvent, progress: number]
  'drag-end': [event: PointerEvent, progress: number]
}>()

const sizeMap = {
  sm: {width: '18rem', height: '30vh'},
  md: {width: '22rem', height: '45vh'},
  lg: {width: '28rem', height: '65vh'},
  full: {width: '100vw', height: '100vh'},
}
let previousOverflow = ''
let hasBeenVisible = false

const {
  closeEdge,
  isDragging,
  dragProgress,
  dragDistance,
  onPointerDown: onDismissPointerDown,
  onIndicatorClick,
  markDragged,
  resetDrag,
} = useDrawerDismissDrag({
  enabled: computed(() => props.draggable && props.modelValue && props.closable),
  position: computed(() => props.position),
  threshold: computed(() => props.dragThreshold),
  onDragStart: (event) => emit('drag-start', event),
  onDrag: (event, progress) => emit('drag', event, progress),
  onDragEnd: (event, progress) => emit('drag-end', event, progress),
  onClose: close,
})

const activeEdges = computed(() => new Set(props.position.split('-')))
const drawerWidth = computed(() => {
  if (props.width) return props.width
  if (activeEdges.value.has('left') && activeEdges.value.has('right')) return '100%'
  if (activeEdges.value.has('left') || activeEdges.value.has('right'))
    return sizeMap[props.size].width
  return '100%'
})
const drawerHeight = computed(() => {
  if (props.height) return props.height
  if (activeEdges.value.has('top') && activeEdges.value.has('bottom')) return '100%'
  if (activeEdges.value.has('top') || activeEdges.value.has('bottom'))
    return sizeMap[props.size].height
  if (props.size === 'full') return '100vh'
  return '100%'
})

const dragHandlePlacement = computed(() => {
  const placements: Record<DrawerCloseEdge, string> = {
    top: 'bottom-0 left-1/2 h-drag-handle-area w-drag-handle-length -translate-x-1/2',
    right: 'left-0 top-1/2 h-drag-handle-length w-drag-handle-area -translate-y-1/2',
    bottom: 'top-0 left-1/2 h-drag-handle-area w-drag-handle-length -translate-x-1/2',
    left: 'right-0 top-1/2 h-drag-handle-length w-drag-handle-area -translate-y-1/2',
  }
  return placements[closeEdge.value]
})

const dragIndicatorStyle = computed<CSSProperties>(() => {
  const scale = 0.5 + dragProgress.value / 2
  return {
    transform:
      closeEdge.value === 'top' || closeEdge.value === 'bottom'
        ? `scaleX(${scale})`
        : `scaleY(${scale})`,
    backgroundColor: dragProgress.value > 0 ? 'var(--ui-color-primary)' : undefined,
  }
})

const dragStyle = computed<CSSProperties>(() => {
  if (!dragDistance.value) return {}

  const distance = dragDistance.value
  const offset =
    closeEdge.value === 'right'
      ? {x: distance, y: 0}
      : closeEdge.value === 'left'
        ? {x: -distance, y: 0}
        : {x: 0, y: closeEdge.value === 'bottom' ? distance : -distance}

  return {transform: `translate3d(${offset.x}px, ${offset.y}px, 0)`}
})

function close(): void {
  if (!props.closable) return
  emit('update:modelValue', false)
}

function onKeydown(event: KeyboardEvent): void {
  if (event.key === 'Escape' && props.closeOnEsc) {
    event.stopPropagation()
    close()
  }
}

function dimensionStyle(value: string | number): string {
  return typeof value === 'number' ? `${value}px` : value
}

watch(
  () => props.modelValue,
  (visible) => {
    if (typeof document === 'undefined') return

    if (visible) {
      resetDrag()
      emit('show')
      hasBeenVisible = true
      if (props.lockScroll) {
        previousOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
      }
      document.addEventListener('keydown', onKeydown)
    } else {
      if (hasBeenVisible) emit('hide')
      document.removeEventListener('keydown', onKeydown)
      if (props.lockScroll) document.body.style.overflow = previousOverflow
    }
  },
  {immediate: true},
)

onBeforeUnmount(() => {
  if (typeof document === 'undefined') return
  document.removeEventListener('keydown', onKeydown)
  if (props.lockScroll && props.modelValue) document.body.style.overflow = previousOverflow
})

function onBackdropClick(): void {
  if (props.closeOnBackdrop) close()
}
</script>

<template>
  <Teleport to="body">
    <Transition
      appear
      :duration="{ enter: props.transitionDuration, leave: 200 }"
      enter-active-class="transition-opacity ease-out"
      leave-active-class="transition-opacity ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
      @after-leave="emit('after-leave')"
    >
      <div
        v-if="props.modelValue"
        :class="
          resolveClasses(
            props.classes,
            'root',
            cn(
              'fixed inset-0 flex bg-overlay',
              overlayContainerClass(props.position),
              props.overlayBlur ? 'backdrop-blur-sm' : '',
            ),
          )
        "
        :style="{ zIndex: props.zIndex }"
        role="presentation"
        @click.self="onBackdropClick"
      >
        <Transition
          appear
          :duration="{ enter: props.transitionDuration, leave: 200 }"
          enter-active-class="transition-all ease-out"
          leave-active-class="transition-all ease-in"
          :enter-from-class="overlayPanelEnterClass(props.position)"
          enter-to-class="translate-x-0 translate-y-0 opacity-100"
          leave-from-class="translate-x-0 translate-y-0 opacity-100"
          :leave-to-class="overlayPanelEnterClass(props.position)"
        >
          <section
            v-if="props.modelValue"
            :class="
              resolveClasses(
                props.classes,
                'panel',
                'relative flex max-h-full max-w-full flex-col overflow-hidden rounded-overlay bg-surface-raised shadow-2xl shadow-shadow',
              )
            "
            :style="{
              width: dimensionStyle(drawerWidth),
              height: dimensionStyle(drawerHeight),
              ...dragStyle,
            }"
            role="dialog"
            aria-modal="true"
            :aria-label="props.title"
          >
            <button
              v-if="props.draggable"
              type="button"
              :class="
                resolveClasses(
                  props.classes,
                  'dragHandle',
                  cn(
                    'absolute z-20 flex cursor-grab touch-none select-none items-center justify-center bg-transparent transition-opacity focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
                    dragHandlePlacement,
                    isDragging ? 'cursor-grabbing opacity-100' : 'opacity-75 hover:opacity-100',
                  ),
                )
              "
              :aria-label="t('common.closeDrawer')"
              :title="t('common.dragToClose')"
              @pointerdown="onDismissPointerDown"
              @pointerup="markDragged"
              @pointercancel="markDragged"
              @click="onIndicatorClick"
            >
              <span
                :class="
                  resolveClasses(
                    props.classes,
                    'dragIndicator',
                    cn(
                      'block rounded-pill bg-border-strong transition-transform duration-150',
                      closeEdge === 'top' || closeEdge === 'bottom'
                        ? 'h-drag-handle-thickness w-drag-handle-length'
                        : 'h-drag-handle-length w-drag-handle-thickness',
                    ),
                  )
                "
                :style="dragIndicatorStyle"
                aria-hidden="true"
              />
            </button>

            <header
              v-if="props.title || props.icon || props.closable || props.draggable || $slots.header"
              :class="
                resolveClasses(
                  props.classes,
                  'header',
                  'flex items-center justify-between gap-control-gap-lg border-b border-border px-content-md py-content-sm',
                )
              "
            >
              <slot name="header" :title="props.title" :close="close">
                <div class="flex min-w-0 items-center gap-control-gap-md">
                  <BaseLucideIcon
                    v-if="props.icon"
                    :icon="props.icon"
                    :classes="{
                      root: resolveClasses(
                        props.classes,
                        'icon',
                        'h-icon-md w-icon-md text-primary',
                      ),
                    }"
                  />
                  <h2
                    :class="
                      resolveClasses(
                        props.classes,
                        'title',
                        'text-title font-semibold text-content',
                      )
                    "
                  >
                    {{ props.title }}
                  </h2>
                </div>
              </slot>
              <button
                v-if="props.closable"
                type="button"
                :class="
                  resolveClasses(
                    props.classes,
                    'close',
                    'rounded-control p-field-action-padding text-content-subtle transition-colors hover:bg-surface-hover hover:text-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary',
                  )
                "
                :aria-label="t('common.closeDrawer')"
                @click="close"
              >
                <slot name="close">
                  <BaseLucideIcon
                    :icon="props.closeIcon ?? X"
                    :classes="{
                      root: resolveClasses(props.classes, 'closeIcon', 'h-icon-md w-icon-md'),
                    }"
                  />
                </slot>
              </button>
            </header>

            <div
              :class="
                resolveClasses(
                  props.classes,
                  'content',
                  'flex-1 overflow-y-auto px-content-md py-content-sm text-control-sm text-content-muted',
                )
              "
            >
              <slot :close="close"/>
            </div>

            <footer
              v-if="$slots.footer"
              :class="
                resolveClasses(
                  props.classes,
                  'footer',
                  'flex justify-end gap-control-gap-md border-t border-border px-content-md py-content-sm',
                )
              "
            >
              <slot name="footer" :close="close"/>
            </footer>
          </section>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
