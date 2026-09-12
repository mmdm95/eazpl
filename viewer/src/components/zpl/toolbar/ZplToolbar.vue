<script setup lang="ts">
import {
  Copy,
  Download,
  Grid3x3,
  Hand,
  Magnet,
  Maximize,
  MousePointer2,
  Play,
  Redo,
  Undo,
  ZoomIn,
  ZoomOut,
} from '@lucide/vue'
import { BaseButton, BaseDropdown, BaseInput } from '@/components/base'
import type { DropdownOption } from '@/components/base'
import type { ZplToolbarEmits, ZplToolbarProps } from './types'

defineProps<ZplToolbarProps>()
const emit = defineEmits<ZplToolbarEmits>()

const dpiOptions: DropdownOption[] = [
  { value: 203, label: '203 DPI' },
  { value: 300, label: '300 DPI' },
  { value: 600, label: '600 DPI' },
]
const orientationOptions: DropdownOption[] = [
  { value: 'portrait', label: 'Portrait' },
  { value: 'landscape', label: 'Landscape' },
]

const tools = [
  { id: 'selection', label: 'Selection', icon: MousePointer2 },
  { id: 'hand', label: 'Hand', icon: Hand },
] as const
</script>
<template>
  <header
    class="flex flex-wrap items-center justify-between gap-2 border-b border-border bg-surface px-3 py-2"
  >
    <div class="flex flex-wrap items-center gap-2">
      <div class="w-20">
        <BaseInput
          :model-value="label.width"
          type="number"
          label="Width"
          min="1"
          @update:model-value="emit('update:label', { width: Number($event) })"
        />
      </div>
      <div class="w-20">
        <BaseInput
          :model-value="label.height"
          type="number"
          label="Height"
          min="1"
          @update:model-value="emit('update:label', { height: Number($event) })"
        />
      </div>
      <div class="w-24">
        <BaseDropdown
          :model-value="label.dpi"
          :options="dpiOptions"
          label="DPI"
          @update:model-value="emit('update:label', { dpi: Number($event) })"
        />
      </div>
      <div class="w-28">
        <BaseDropdown
          :model-value="label.orientation ?? 'portrait'"
          :options="orientationOptions"
          label="Orientation"
          @update:model-value="emit('update:label', { orientation: String($event) })"
        />
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <div
        class="flex items-center gap-0.5 rounded-control border border-border bg-surface-muted p-0.5"
      >
        <BaseButton
          v-for="tool in tools"
          :key="tool.id"
          size="sm"
          :variant="activeTool === tool.id ? 'primary' : 'ghost'"
          :icon="tool.icon"
          :aria-label="tool.label"
          :aria-pressed="activeTool === tool.id"
          @click="emit('set-tool', tool.id)"
        >
        </BaseButton>
      </div>

      <div
        class="flex items-center gap-0.5 rounded-control border border-border bg-surface-muted p-0.5"
      >
        <BaseButton
          size="sm"
          :variant="grid.enabled ? 'primary' : 'ghost'"
          :icon="Grid3x3"
          aria-label="Toggle grid"
          :aria-pressed="grid.enabled"
          @click="emit('update:grid', { enabled: !grid.enabled })"
        >
        </BaseButton>
        <BaseButton
          size="sm"
          :variant="grid.snap ? 'primary' : 'ghost'"
          :icon="Magnet"
          aria-label="Toggle snap to grid"
          :aria-pressed="grid.snap"
          @click="emit('update:grid', { snap: !grid.snap })"
        >
        </BaseButton>
      </div>

      <div class="w-16">
        <BaseInput
          :model-value="grid.size"
          type="number"
          label="Grid"
          min="1"
          @update:model-value="emit('update:grid', { size: Number($event) })"
        />
      </div>
      <BaseButton size="sm" variant="outline" :icon="ZoomOut" aria-label="Zoom out" @click="emit('zoom-out')" />
      <span class="w-12 text-center text-xs font-medium text-content-muted">
        {{ Math.round(zoom * 100) }}%
      </span>
      <BaseButton size="sm" variant="outline" :icon="ZoomIn" aria-label="Zoom in" @click="emit('zoom-in')" />
      <BaseButton size="sm" variant="outline" :icon="Maximize" aria-label="Fit to viewport" @click="emit('fit')" />
      <BaseButton size="sm" variant="outline" :icon="Undo" aria-label="Undo" :disabled="!canUndo" @click="emit('undo')" />
      <BaseButton size="sm" variant="outline" :icon="Redo" aria-label="Redo" :disabled="!canRedo" @click="emit('redo')" />
      <BaseButton size="sm" variant="primary" :icon="Play" :loading="generating" @click="emit('generate')">
        Generate
      </BaseButton>
      <BaseButton v-if="hasOutput" size="sm" variant="outline" :icon="Copy" aria-label="Copy ZPL" @click="emit('copy')" />
      <BaseButton v-if="hasOutput" size="sm" variant="outline" :icon="Download" aria-label="Download ZPL" @click="emit('download')" />
    </div>
  </header>
</template>
