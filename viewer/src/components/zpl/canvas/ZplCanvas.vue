<script setup lang="ts">
import { computed, ref } from 'vue'
import type { CSSProperties } from 'vue'
import { ZplCanvasComponent } from './'
import type { ZplCanvasEmits, ZplCanvasProps } from './types'

const props = defineProps<ZplCanvasProps>()
const emit = defineEmits<ZplCanvasEmits>()

const gridStyle = computed<CSSProperties>(() => {
  const size = props.grid.size * props.zoom
  return {
    backgroundImage:
      'linear-gradient(to right, rgba(100, 116, 139, 0.25) 1px, transparent 1px), linear-gradient(to bottom, rgba(100, 116, 139, 0.25) 1px, transparent 1px)',
    backgroundSize: `${size}px ${size}px`,
  }
})

const visibleComponents = computed(() =>
  props.components.filter((component) => component.visible !== false),
)

interface PanState {
  pointerId: number
  clientX: number
  clientY: number
  scrollLeft: number
  scrollTop: number
}

const panState = ref<PanState | null>(null)

const definitionMap = computed(
  () => new Map(props.definitions.map((definition) => [definition.type, definition])),
)

function onDrop(event: DragEvent): void {
  const type = event.dataTransfer?.getData('application/x-zpl-component')
  if (!type || !event.currentTarget) return
  const bounds = (event.currentTarget as HTMLElement).getBoundingClientRect()
  emit(
    'add',
    type,
    (event.clientX - bounds.left) / props.zoom,
    (event.clientY - bounds.top) / props.zoom,
  )
}

function snap(value: number): number {
  if (!props.grid.enabled || !props.grid.snap) return value
  return Math.round(value / props.grid.size) * props.grid.size
}

function onViewportPointerDown(event: PointerEvent): void {
  if (props.activeTool !== 'hand') {
    emit('select', null)
    return
  }

  if (event.button !== 0) return
  event.preventDefault()
  const viewport = event.currentTarget as HTMLElement
  panState.value = {
    pointerId: event.pointerId,
    clientX: event.clientX,
    clientY: event.clientY,
    scrollLeft: viewport.scrollLeft,
    scrollTop: viewport.scrollTop,
  }
  viewport.setPointerCapture(event.pointerId)
}

function onViewportPointerMove(event: PointerEvent): void {
  const state = panState.value
  if (!state || state.pointerId !== event.pointerId) return
  const viewport = event.currentTarget as HTMLElement
  viewport.scrollLeft = state.scrollLeft - (event.clientX - state.clientX)
  viewport.scrollTop = state.scrollTop - (event.clientY - state.clientY)
}

function endPan(event: PointerEvent): void {
  if (panState.value?.pointerId !== event.pointerId) return
  panState.value = null
}
</script>
<template>
  <div
    :class="[
      'relative flex h-full w-full items-center justify-center overflow-auto bg-surface-muted p-6 select-none',
      activeTool === 'hand' ? 'cursor-grab active:cursor-grabbing' : 'cursor-default',
    ]"
    @pointerdown="onViewportPointerDown"
    @pointermove="onViewportPointerMove"
    @pointerup="endPan"
    @pointercancel="endPan"
  >
    <div
      class="relative shrink-0 border border-border bg-white shadow-control-lg"
      :style="[
        { width: `${label.width * zoom}px`, height: `${label.height * zoom}px` },
        grid.enabled ? gridStyle : undefined,
      ]"
      @pointerdown.stop
      @dragover.prevent
      @drop.prevent="onDrop"
    >
      <ZplCanvasComponent
        v-for="component in visibleComponents"
        :key="component.id"
        :instance="component"
        :definition="definitionMap.get(component.type)!"
        :zoom="zoom"
        :selected="component.id === selectedComponentId"
        :locked="component.locked ?? false"
        :class="activeTool === 'hand' ? 'pointer-events-none' : undefined"
        @select="emit('select', $event)"
        @begin-change="emit('beginChange')"
        @move="(id, x, y) => emit('move', id, snap(x), snap(y))"
        @resize="(id, width, height) => emit('resize', id, snap(width), snap(height))"
        @rotate="(id, rotation) => emit('rotate', id, rotation)"
      />

      <div
        v-if="components.length === 0"
        class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center gap-2 text-center"
      >
        <p class="text-base font-semibold text-content">Drop ZPL components here</p>
        <p class="max-w-xs text-sm text-content-muted">
          Drag an item from the palette or press Add to place it at the top-left of the label.
        </p>
      </div>
    </div>
  </div>
</template>
