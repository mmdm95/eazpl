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
    class="flex flex-wrap items-end justify-between gap-4 border-b border-border bg-surface p-4"
  >
    <div class="flex flex-wrap items-end gap-3">
      <div class="w-28">
        <BaseInput
          :model-value="label.width"
          type="number"
          label="Width"
          min="1"
          @update:model-value="emit('update:label', { width: Number($event) })"
        />
      </div>
      <div class="w-28">
        <BaseInput
          :model-value="label.height"
          type="number"
          label="Height"
          min="1"
          @update:model-value="emit('update:label', { height: Number($event) })"
        />
      </div>
      <div class="w-32">
        <BaseDropdown
          :model-value="label.dpi"
          :options="dpiOptions"
          label="DPI"
          @update:model-value="emit('update:label', { dpi: Number($event) })"
        />
      </div>
      <div class="w-36">
        <BaseDropdown
          :model-value="label.orientation ?? 'portrait'"
          :options="orientationOptions"
          label="Orientation"
          @update:model-value="emit('update:label', { orientation: String($event) })"
        />
      </div>
    </div>

    <div class="flex flex-wrap items-end gap-3">
      <div
        class="flex items-center gap-1 rounded-control border border-border bg-surface-muted p-1"
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
          {{ tool.label }}
        </BaseButton>
      </div>

      <div
        class="flex items-center gap-1 rounded-control border border-border bg-surface-muted p-1"
      >
        <BaseButton
          size="sm"
          :variant="grid.enabled ? 'primary' : 'ghost'"
          :icon="Grid3x3"
          aria-label="Toggle grid"
          :aria-pressed="grid.enabled"
          @click="emit('update:grid', { enabled: !grid.enabled })"
        >
          Grid
        </BaseButton>
        <BaseButton
          size="sm"
          :variant="grid.snap ? 'primary' : 'ghost'"
          :icon="Magnet"
          aria-label="Toggle snap to grid"
          :aria-pressed="grid.snap"
          @click="emit('update:grid', { snap: !grid.snap })"
        >
          Snap
        </BaseButton>
      </div>

      <div class="w-28">
        <BaseInput
          :model-value="grid.size"
          type="number"
          label="Grid size"
          min="1"
          @update:model-value="emit('update:grid', { size: Number($event) })"
        />
      </div>
      <BaseButton variant="outline" :icon="ZoomOut" @click="emit('zoom-out')">Out</BaseButton>
      <span class="w-14 pb-2 text-center text-sm font-medium text-content-muted">
        {{ Math.round(zoom * 100) }}%
      </span>
      <BaseButton variant="outline" :icon="ZoomIn" @click="emit('zoom-in')">In</BaseButton>
      <BaseButton variant="outline" :icon="Maximize" @click="emit('fit')">Fit</BaseButton>
      <BaseButton variant="outline" :icon="Undo" :disabled="!canUndo" @click="emit('undo')"
        >Undo</BaseButton
      >
      <BaseButton variant="outline" :icon="Redo" :disabled="!canRedo" @click="emit('redo')"
        >Redo</BaseButton
      >
      <BaseButton variant="primary" :icon="Play" :loading="generating" @click="emit('generate')">
        Generate
      </BaseButton>
      <BaseButton v-if="hasOutput" variant="outline" :icon="Copy" @click="emit('copy')"
        >Copy</BaseButton
      >
      <BaseButton v-if="hasOutput" variant="outline" :icon="Download" @click="emit('download')">
        Download
      </BaseButton>
    </div>
  </header>
</template>
