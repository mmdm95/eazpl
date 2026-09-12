<script setup lang="ts">
import { Grid3x3, Hand, Magnet, MousePointer2 } from '@lucide/vue'
import { computed, ref, watch } from 'vue'
import type { CSSProperties } from 'vue'
import { BaseButton, BaseTooltip } from '@/components/base'
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
  lastX: number
  lastY: number
}

const panState = ref<PanState | null>(null)
const viewport = ref<HTMLElement | null>(null)
const panOffset = ref({ x: 0, y: 0 })

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

function clampPan(): void {
  const viewportElement = viewport.value
  if (!viewportElement) return
  const boundary = Math.max(
    96,
    Math.round(Math.min(viewportElement.clientWidth, viewportElement.clientHeight) * 0.4),
  )
  panOffset.value = {
    x: Math.min(boundary, Math.max(-boundary, panOffset.value.x)),
    y: Math.min(boundary, Math.max(-boundary, panOffset.value.y)),
  }
}

function snap(value: number): number {
  if (!props.grid.enabled || !props.grid.snap) return value
  return Math.round(value / props.grid.size) * props.grid.size
}

function onViewportPointerDown(event: PointerEvent): void {
  const shouldPan = props.activeTool === 'hand' || event.button === 1

  if (!shouldPan) {
    emit('select', null)
    return
  }

  if (event.button !== 0 && event.button !== 1) return
  event.preventDefault()
  panState.value = {
    pointerId: event.pointerId,
    lastX: event.clientX,
    lastY: event.clientY,
  }
  ;(event.currentTarget as HTMLElement).setPointerCapture(event.pointerId)
}

function onViewportPointerMove(event: PointerEvent): void {
  const state = panState.value
  if (!state || state.pointerId !== event.pointerId) return
  panOffset.value = {
    x: panOffset.value.x + event.clientX - state.lastX,
    y: panOffset.value.y + event.clientY - state.lastY,
  }
  state.lastX = event.clientX
  state.lastY = event.clientY
  clampPan()
}

function endPan(event: PointerEvent): void {
  if (panState.value?.pointerId !== event.pointerId) return
  panState.value = null
}

function onWheel(event: WheelEvent): void {
  if (!event.ctrlKey && !event.metaKey) return
  event.preventDefault()
  const direction = event.deltaY > 0 ? -0.1 : 0.1
  emit('zoom', props.zoom + direction)
}

watch(
  () => props.zoom,
  () => {
    panOffset.value = { x: 0, y: 0 }
  },
)
</script>
<template>
  <div
    ref="viewport"
    :class="[
      'relative flex h-full w-full items-center justify-center overflow-hidden bg-surface-muted p-6 select-none touch-none',
      activeTool === 'hand' ? 'cursor-grab active:cursor-grabbing' : 'cursor-default',
    ]"
    @pointerdown="onViewportPointerDown"
    @pointermove="onViewportPointerMove"
    @pointerup="endPan"
    @pointercancel="endPan"
    @wheel="onWheel"
  >
    <div
      class="pointer-events-none absolute bottom-4 left-4 z-20 flex flex-col gap-2"
      @pointerdown.stop
    >
      <div
        class="pointer-events-auto flex items-center gap-0.5 rounded-control border border-border bg-surface/95 p-0.5 shadow-control backdrop-blur"
      >
        <BaseTooltip content="Selection tool">
          <BaseButton
            size="sm"
            :variant="activeTool === 'selection' ? 'primary' : 'ghost'"
            :icon="MousePointer2"
            aria-label="Selection tool"
            :aria-pressed="activeTool === 'selection'"
            @click="emit('set-tool', 'selection')"
          />
        </BaseTooltip>
        <BaseTooltip content="Hand tool">
          <BaseButton
            size="sm"
            :variant="activeTool === 'hand' ? 'primary' : 'ghost'"
            :icon="Hand"
            aria-label="Hand tool"
            :aria-pressed="activeTool === 'hand'"
            @click="emit('set-tool', 'hand')"
          />
        </BaseTooltip>
      </div>
      <div
        class="pointer-events-auto flex items-center gap-0.5 rounded-control border border-border bg-surface/95 p-0.5 shadow-control backdrop-blur"
      >
        <BaseTooltip content="Toggle grid">
          <BaseButton
            size="sm"
            :variant="grid.enabled ? 'primary' : 'ghost'"
            :icon="Grid3x3"
            aria-label="Toggle grid"
            :aria-pressed="grid.enabled"
            @click="emit('update:grid', { enabled: !grid.enabled })"
          />
        </BaseTooltip>
        <BaseTooltip content="Toggle snap to grid">
          <BaseButton
            size="sm"
            :variant="grid.snap ? 'primary' : 'ghost'"
            :icon="Magnet"
            aria-label="Toggle snap to grid"
            :aria-pressed="grid.snap"
            @click="emit('update:grid', { snap: !grid.snap })"
          />
        </BaseTooltip>
      </div>
    </div>

    <div
      class="relative shrink-0 border border-border bg-white shadow-control-lg"
      :style="[
        {
          width: `${label.width * zoom}px`,
          height: `${label.height * zoom}px`,
          transform: `translate3d(${panOffset.x}px, ${panOffset.y}px, 0)`,
        },
        grid.enabled ? gridStyle : undefined,
      ]"
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
