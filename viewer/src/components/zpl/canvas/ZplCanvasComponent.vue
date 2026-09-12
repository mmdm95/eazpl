<script setup lang="ts">
import {RotateCw} from '@lucide/vue'
import type {CSSProperties} from 'vue'
import {computed, ref} from 'vue'
import {BaseLucideIcon} from '@/components/base'
import type {ZplCanvasComponentEmits, ZplCanvasComponentProps} from './types'

const props = defineProps<ZplCanvasComponentProps>()
const emit = defineEmits<ZplCanvasComponentEmits>()
const root = ref<HTMLElement | null>(null)

type DragMode = 'move' | 'resize' | 'rotate'

interface DragState {
  mode: DragMode
  pointerId: number
  startX: number
  startY: number
  componentX: number
  componentY: number
  width: number
  height: number
}

const dragState = ref<DragState | null>(null)

const style = computed<CSSProperties>(() => ({
  left: `${props.instance.x * props.zoom}px`,
  top: `${props.instance.y * props.zoom}px`,
  width: `${(props.instance.width ?? 100) * props.zoom}px`,
  height: `${(props.instance.height ?? 30) * props.zoom}px`,
  transform: `rotate(${props.instance.rotation ?? 0}deg)`,
}))

const previewText = computed(() => {
  const value =
    props.instance.attributes.text ??
    props.instance.attributes.value ??
    props.instance.attributes.data ??
    props.instance.attributes.items
  return typeof value === 'string' && value ? value : props.definition.name
})

const imageSource = computed(() => {
  const source = props.instance.attributes.source
  return typeof source === 'string' && source.startsWith('data:image/') ? source : null
})

function startDrag(event: PointerEvent, mode: DragMode): void {
  event.stopPropagation()
  emit('select', props.instance.id)
  if (props.locked) return
  if (mode === 'rotate' && props.definition.rotatable === false) return
  emit('beginChange')
  dragState.value = {
    mode,
    pointerId: event.pointerId,
    startX: event.clientX,
    startY: event.clientY,
    componentX: props.instance.x,
    componentY: props.instance.y,
    width: props.instance.width ?? 100,
    height: props.instance.height ?? 30,
  }
  ;(event.currentTarget as HTMLElement).setPointerCapture(event.pointerId)
}

function onPointerMove(event: PointerEvent): void {
  const state = dragState.value
  if (!state || state.pointerId !== event.pointerId) return
  const deltaX = (event.clientX - state.startX) / props.zoom
  const deltaY = (event.clientY - state.startY) / props.zoom

  if (state.mode === 'move') {
    emit('move', props.instance.id, state.componentX + deltaX, state.componentY + deltaY)
    return
  }

  if (state.mode === 'resize') {
    emit('resize', props.instance.id, state.width + deltaX, state.height + deltaY)
    return
  }

  const bounds = root.value?.getBoundingClientRect()
  if (!bounds) return
  const centerX = bounds.left + bounds.width / 2
  const centerY = bounds.top + bounds.height / 2
  const rotation =
    (Math.atan2(event.clientY - centerY, event.clientX - centerX) * 180) / Math.PI + 90
  emit('rotate', props.instance.id, (rotation + 360) % 360)
}

function endDrag(event: PointerEvent): void {
  if (dragState.value?.pointerId !== event.pointerId) return
  dragState.value = null
}
</script>
<template>
  <article
    ref="root"
    :class="[
      'absolute select-none rounded-control border bg-surface transition-shadow',
      selected
        ? 'z-20 border-primary shadow-control-lg'
        : 'z-10 border-border hover:border-primary/60',
    ]"
    :style="style"
    tabindex="0"
    role="button"
    :aria-label="`${definition.name} at ${instance.x}, ${instance.y}`"
    @pointerdown="startDrag($event, 'move')"
    @pointermove="onPointerMove"
    @pointerup="endDrag"
    @pointercancel="endDrag"
  >
    <div
      class="pointer-events-none flex h-full w-full items-center justify-center overflow-hidden p-1"
    >
      <img
        v-if="definition.preview === 'image' && imageSource"
        :src="imageSource"
        :alt="definition.name"
        class="h-full w-full object-contain"
      />
      <span
        v-else-if="definition.preview === 'text'"
        class="w-full truncate text-center text-xs text-content"
      >
        {{ previewText }}
      </span>
      <div
        v-else-if="definition.preview === 'shape' && instance.type === 'diagonal-line'"
        class="h-full w-full"
        :style="{
          background:
            instance.attributes.orientation === 'L'
              ? 'linear-gradient(to top right, transparent 48%, currentColor 48%, currentColor 52%, transparent 52%)'
              : 'linear-gradient(to bottom right, transparent 48%, currentColor 48%, currentColor 52%, transparent 52%)',
        }"
      />
      <div
        v-else-if="
          definition.preview === 'shape' &&
          (instance.type === 'circle' || instance.type === 'ellipse')
        "
        class="h-full w-full border-2 border-content"
        :class="instance.type === 'circle' ? 'rounded-full' : 'rounded-[50%]'"
      />
      <div
        v-else-if="definition.preview === 'shape' && instance.type === 'vertical-line'"
        class="h-full w-full bg-content"
      />
      <div
        v-else-if="definition.preview === 'shape'"
        class="h-full w-full rounded-control border-2 border-content"
      />
      <div
        v-else-if="definition.preview === 'table'"
        class="grid h-full w-full gap-px bg-border"
        :style="{
          gridTemplateColumns: `repeat(${Math.max(1, Number(instance.attributes.columns ?? 2))}, minmax(0, 1fr))`,
        }"
      >
        <div
          v-for="index in Math.max(1, Number(instance.attributes.columns ?? 2)) * 3"
          :key="index"
          class="min-h-0 bg-surface"
        />
      </div>
      <div v-else class="h-[70%] w-[70%] rounded-control bg-content/90"/>
    </div>

    <span
      v-if="selected && !locked && definition.rotatable !== false"
      class="absolute -top-5 left-1/2 flex h-5 w-5 -translate-x-1/2 cursor-grab items-center justify-center rounded-pill border border-primary bg-surface text-primary shadow-control transition hover:bg-primary-soft"
      title="Rotate"
      aria-hidden="true"
      @pointerdown="startDrag($event, 'rotate')"
    >
      <BaseLucideIcon :icon="RotateCw" class="h-3 w-3"/>
    </span>
    <span
      v-if="selected && !locked && definition.resizable !== false"
      class="absolute -bottom-1 -right-1 h-3 w-3 cursor-nwse-resize rounded-pill border border-primary bg-primary"
      @pointerdown="startDrag($event, 'resize')"
    />
  </article>
</template>
