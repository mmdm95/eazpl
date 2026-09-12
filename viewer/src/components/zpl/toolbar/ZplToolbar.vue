<script setup lang="ts">
import {
  Copy,
  Download,
  Frame,
  Maximize,
  Minimize,
  Play,
  Redo,
  Settings,
  Undo,
  ZoomIn,
  ZoomOut,
} from '@lucide/vue'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { BaseButton, BaseDropdown, BaseInput, BaseTooltip } from '@/components/base'
import type { DropdownOption } from '@/components/base'
import type { ZplToolbarEmits, ZplToolbarProps } from './types'

defineProps<ZplToolbarProps>()
const emit = defineEmits<ZplToolbarEmits>()

const settingsOpen = ref(false)
const settingsButton = ref<HTMLElement | null>(null)

const dpiOptions: DropdownOption[] = [
  { value: 203, label: '203 DPI' },
  { value: 300, label: '300 DPI' },
  { value: 600, label: '600 DPI' },
]
const orientationOptions: DropdownOption[] = [
  { value: 'portrait', label: 'Portrait' },
  { value: 'landscape', label: 'Landscape' },
]

function onDocumentPointerDown(event: PointerEvent): void {
  if (!settingsOpen.value || settingsButton.value?.contains(event.target as Node)) return
  settingsOpen.value = false
}

function onKeydown(event: KeyboardEvent): void {
  if (event.key === 'Escape') settingsOpen.value = false
}

onMounted(() => {
  document.addEventListener('pointerdown', onDocumentPointerDown)
  document.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
  document.removeEventListener('pointerdown', onDocumentPointerDown)
  document.removeEventListener('keydown', onKeydown)
})
</script>
<template>
  <header
    class="flex flex-wrap items-center justify-between gap-2 border-b border-border bg-surface px-3 py-2"
  >
    <div ref="settingsButton" class="relative">
      <BaseButton
        size="sm"
        variant="outline"
        :icon="Settings"
        :aria-expanded="settingsOpen"
        :aria-haspopup="true"
        @click="settingsOpen = !settingsOpen"
      >
        Label settings
      </BaseButton>

      <div
        v-if="settingsOpen"
        class="absolute top-full z-30 mt-2 w-72 rounded-control border border-border bg-surface-raised p-3 shadow-xl shadow-shadow"
      >
        <div class="grid grid-cols-2 gap-3">
          <BaseInput
            :model-value="label.width"
            type="number"
            label="Width"
            min="1"
            @update:model-value="emit('update:label', { width: Number($event) })"
          />
          <BaseInput
            :model-value="label.height"
            type="number"
            label="Height"
            min="1"
            @update:model-value="emit('update:label', { height: Number($event) })"
          />
          <BaseDropdown
            :model-value="label.dpi"
            :options="dpiOptions"
            label="DPI"
            @update:model-value="emit('update:label', { dpi: Number($event) })"
          />
          <BaseDropdown
            :model-value="label.orientation ?? 'portrait'"
            :options="orientationOptions"
            label="Orientation"
            @update:model-value="emit('update:label', { orientation: String($event) })"
          />
          <div class="col-span-2">
            <BaseInput
              :model-value="grid.size"
              type="number"
              label="Grid size"
              min="1"
              @update:model-value="emit('update:grid', { size: Number($event) })"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <BaseTooltip content="Zoom out">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="ZoomOut"
          aria-label="Zoom out"
          @click="emit('zoom-out')"
        />
      </BaseTooltip>
      <span class="w-12 text-center text-xs font-medium text-content-muted">
        {{ Math.round(zoom * 100) }}%
      </span>
      <BaseTooltip content="Zoom in">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="ZoomIn"
          aria-label="Zoom in"
          @click="emit('zoom-in')"
        />
      </BaseTooltip>
      <BaseTooltip content="Fit to viewport">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="Frame"
          aria-label="Fit to viewport"
          @click="emit('fit')"
        />
      </BaseTooltip>
      <BaseTooltip :content="fullscreen ? 'Exit fullscreen' : 'Enter fullscreen'">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="fullscreen ? Minimize : Maximize"
          :aria-label="fullscreen ? 'Exit fullscreen' : 'Enter fullscreen'"
          @click="emit('fullscreen')"
        />
      </BaseTooltip>
      <BaseTooltip content="Undo">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="Undo"
          aria-label="Undo"
          :disabled="!canUndo"
          @click="emit('undo')"
        />
      </BaseTooltip>
      <BaseTooltip content="Redo">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="Redo"
          aria-label="Redo"
          :disabled="!canRedo"
          @click="emit('redo')"
        />
      </BaseTooltip>
      <BaseButton
        size="sm"
        variant="primary"
        :icon="Play"
        :loading="generating"
        @click="emit('generate')"
      >
        Generate
      </BaseButton>
      <BaseTooltip v-if="hasOutput" content="Copy ZPL">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="Copy"
          aria-label="Copy ZPL"
          @click="emit('copy')"
        />
      </BaseTooltip>
      <BaseTooltip v-if="hasOutput" content="Download ZPL">
        <BaseButton
          size="sm"
          variant="outline"
          :icon="Download"
          aria-label="Download ZPL"
          @click="emit('download')"
        />
      </BaseTooltip>
    </div>
  </header>
</template>
