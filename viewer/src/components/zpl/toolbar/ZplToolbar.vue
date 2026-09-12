<script setup lang="ts">
import {Copy, Download, Maximize, Play, Redo, Undo, ZoomIn, ZoomOut} from '@lucide/vue'
import {BaseButton, BaseDropdown, BaseInput, BaseSwitch} from '@/components/base'
import type {DropdownOption} from '@/components/base'
import type {ZplToolbarEmits, ZplToolbarProps} from './types'

defineProps<ZplToolbarProps>()
const emit = defineEmits<ZplToolbarEmits>()

const dpiOptions: DropdownOption[] = [
  {value: 203, label: '203 DPI'},
  {value: 300, label: '300 DPI'},
  {value: 600, label: '600 DPI'},
]
const orientationOptions: DropdownOption[] = [
  {value: 'portrait', label: 'Portrait'},
  {value: 'landscape', label: 'Landscape'},
]
</script>
<template>
  <header class="flex flex-wrap items-end justify-between gap-4 border-b border-border bg-surface p-4">
    <div class="flex flex-wrap items-end gap-3">
      <div class="w-28">
        <BaseInput
          :model-value="label.width"
          type="number"
          label="Width"
          min="1"
          @update:model-value="emit('update:label', {width: Number($event)})"
        />
      </div>
      <div class="w-28">
        <BaseInput
          :model-value="label.height"
          type="number"
          label="Height"
          min="1"
          @update:model-value="emit('update:label', {height: Number($event)})"
        />
      </div>
      <div class="w-32">
        <BaseDropdown
          :model-value="label.dpi"
          :options="dpiOptions"
          label="DPI"
          @update:model-value="emit('update:label', {dpi: Number($event)})"
        />
      </div>
      <div class="w-36">
        <BaseDropdown
          :model-value="label.orientation ?? 'portrait'"
          :options="orientationOptions"
          label="Orientation"
          @update:model-value="emit('update:label', {orientation: String($event)})"
        />
      </div>
    </div>

    <div class="flex flex-wrap items-end gap-3">
      <BaseSwitch
        :model-value="grid.enabled"
        label="Grid"
        @update:model-value="emit('update:grid', {enabled: $event})"
      />
      <BaseSwitch
        :model-value="grid.snap"
        label="Snap"
        @update:model-value="emit('update:grid', {snap: $event})"
      />
      <div class="w-28">
        <BaseInput
          :model-value="grid.size"
          type="number"
          label="Grid size"
          min="1"
          @update:model-value="emit('update:grid', {size: Number($event)})"
        />
      </div>
      <BaseButton variant="outline" :icon="ZoomOut" @click="emit('zoom-out')">Out</BaseButton>
      <span class="w-14 pb-2 text-center text-sm font-medium text-content-muted">
        {{ Math.round(zoom * 100) }}%
      </span>
      <BaseButton variant="outline" :icon="ZoomIn" @click="emit('zoom-in')">In</BaseButton>
      <BaseButton variant="outline" :icon="Maximize" @click="emit('fit')">Fit</BaseButton>
      <BaseButton variant="outline" :icon="Undo" :disabled="!canUndo" @click="emit('undo')">Undo</BaseButton>
      <BaseButton variant="outline" :icon="Redo" :disabled="!canRedo" @click="emit('redo')">Redo</BaseButton>
      <BaseButton variant="primary" :icon="Play" :loading="generating" @click="emit('generate')">
        Generate
      </BaseButton>
      <BaseButton v-if="hasOutput" variant="outline" :icon="Copy" @click="emit('copy')">Copy</BaseButton>
      <BaseButton v-if="hasOutput" variant="outline" :icon="Download" @click="emit('download')">
        Download
      </BaseButton>
    </div>
  </header>
</template>
