<script setup lang="ts">
import {computed} from 'vue'
import type {CSSProperties} from 'vue'
import {ZplCanvasComponent} from './'
import type {ZplCanvasEmits, ZplCanvasProps} from './types'

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

const definitionMap = computed(
  () => new Map(props.definitions.map((definition) => [definition.type, definition])),
)

function onDrop(event: DragEvent): void {
  const type = event.dataTransfer?.getData('application/x-zpl-component')
  if (!type || !event.currentTarget) return
  const bounds = (event.currentTarget as HTMLElement).getBoundingClientRect()
  emit('add', type, (event.clientX - bounds.left) / props.zoom, (event.clientY - bounds.top) / props.zoom)
}

function snap(value: number): number {
  if (!props.grid.enabled || !props.grid.snap) return value
  return Math.round(value / props.grid.size) * props.grid.size
}
</script>
<template>
  <div
    class="relative flex h-full w-full items-center justify-center overflow-auto bg-surface-muted p-6"
    @pointerdown="emit('select', null)"
  >
    <div
      class="relative shrink-0 border border-border bg-white shadow-control-lg"
      :style="[{width: `${label.width * zoom}px`, height: `${label.height * zoom}px`}, grid.enabled ? gridStyle : undefined]"
      @pointerdown.stop
      @dragover.prevent
      @drop.prevent="onDrop"
    >
      <ZplCanvasComponent
        v-for="component in components"
        :key="component.id"
        :instance="component"
        :definition="definitionMap.get(component.type)!"
        :zoom="zoom"
        :selected="component.id === selectedComponentId"
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
