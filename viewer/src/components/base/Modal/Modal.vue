<script setup lang="ts">
import {X} from '@lucide/vue'
import {computed, onBeforeUnmount, watch} from 'vue'
import {cn} from '@/utils'
import {
  overlayContainerClass,
  type OverlayDragOffset,
  overlayPanelEnterClass,
  resolveClasses,
} from '../shared'
import {useOverlayDrag} from '../useOverlayDrag'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {ModalProps} from './types'
import {t} from '@/i18n'

defineOptions({name: 'BaseModal'})

const props = withDefaults(defineProps<ModalProps>(), {
  modelValue: false,
  position: 'center',
  closable: true,
  closeOnEsc: true,
  closeOnBackdrop: true,
  lockScroll: true,
  overlayBlur: false,
  draggable: false,
  transitionDuration: 300,
  width: '100%',
  maxWidth: '32rem',
  title: undefined,
  icon: undefined,
  closeIcon: undefined,
  zIndex: 50,
  classes: undefined,
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  show: []
  hide: []
  'after-leave': []
  'drag-start': [event: PointerEvent]
  drag: [event: PointerEvent, offset: OverlayDragOffset]
  'drag-end': [event: PointerEvent, offset: OverlayDragOffset]
}>()

let previousOverflow = ''
let hasBeenVisible = false

const {
  setPanelRef,
  isDragging,
  dragStyle,
  onPointerDown: onDragPointerDown,
  resetDrag,
} = useOverlayDrag({
  enabled: computed(() => props.draggable && props.modelValue),
  onDragStart: (event) => emit('drag-start', event),
  onDrag: (event, offset) => emit('drag', event, offset),
  onDragEnd: (event, offset) => emit('drag-end', event, offset),
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

function dimensionStyle(value: string | number | undefined): string | undefined {
  if (value === undefined) return undefined
  return typeof value === 'number' ? `${value}px` : value
}

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
              'fixed inset-0 flex bg-overlay p-overlay-padding sm:p-content-md',
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
          enter-to-class="translate-x-0 translate-y-0 scale-100 opacity-100"
          leave-from-class="translate-x-0 translate-y-0 scale-100 opacity-100"
          :leave-to-class="overlayPanelEnterClass(props.position)"
        >
          <div
            v-if="props.modelValue"
            :ref="setPanelRef"
            :class="
              resolveClasses(
                props.classes,
                'panel',
                'relative flex max-h-full w-full flex-col overflow-hidden rounded-overlay bg-surface-raised shadow-2xl shadow-shadow',
              )
            "
            :style="{
              width: dimensionStyle(props.width),
              maxWidth: dimensionStyle(props.maxWidth),
              ...dragStyle,
            }"
            role="dialog"
            aria-modal="true"
            :aria-label="props.title"
          >
            <header
              v-if="props.title || props.icon || props.closable || props.draggable || $slots.header"
              :class="
                cn(
                  resolveClasses(
                    props.classes,
                    'header',
                    'flex items-start justify-between gap-control-gap-lg border-b border-border px-content-md py-content-sm',
                  ),
                  resolveClasses(
                    props.classes,
                    'dragHandle',
                    cn(
                      props.draggable && isDragging && 'cursor-grabbing',
                      props.draggable && !isDragging && 'cursor-move touch-none select-none',
                    ),
                  ),
                )
              "
              @pointerdown="onDragPointerDown"
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
                :aria-label="t('common.closeModal')"
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
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
