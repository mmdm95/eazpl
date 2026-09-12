<script setup lang="ts">
import {GripVertical} from '@lucide/vue'
import type {ComputedRef} from 'vue'
import {computed} from 'vue'
import {BaseButton, BaseLucideIcon} from '@/components/base'
import type {ZplComponentDefinition} from '@/types/zpl'
import type {ZplComponentPaletteEmits, ZplComponentPaletteProps} from './types'

const props = defineProps<ZplComponentPaletteProps>()
const emit = defineEmits<ZplComponentPaletteEmits>()

const grouped: ComputedRef<Array<{category: string; items: ZplComponentDefinition[]}>> = computed(() => {
  const groups = new Map<string, ZplComponentDefinition[]>()
  for (const definition of props.definitions) {
    const category = definition.category ?? 'Components'
    groups.set(category, [...(groups.get(category) ?? []), definition])
  }
  return [...groups.entries()].map(([category, items]) => ({category, items}))
})

function onDragStart(event: DragEvent, definition: ZplComponentDefinition): void {
  event.dataTransfer?.setData('application/x-zpl-component', definition.type)
  if (event.dataTransfer) event.dataTransfer.effectAllowed = 'copy'
}
</script>
<template>
  <section class="flex h-full flex-col gap-3 overflow-y-auto">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-semibold uppercase tracking-wide text-content-muted">Components</h2>
      <span class="text-xs text-content-muted">{{ definitions.length }}</span>
    </div>

    <div v-if="loading" class="space-y-2">
      <div v-for="index in 4" :key="index" class="h-14 animate-pulse rounded-control bg-surface-muted" />
    </div>

    <div
      v-else-if="error"
      class="rounded-control border border-danger/30 bg-danger-soft p-3 text-sm text-danger"
      role="alert"
    >
      {{ error }}
    </div>

    <div v-else class="space-y-4">
      <section v-for="group in grouped" :key="group.category" class="space-y-2">
        <h3 class="text-xs font-semibold uppercase tracking-wide text-content-muted">
          {{ group.category }}
        </h3>
        <article
          v-for="definition in group.items"
          :key="definition.type"
          draggable="true"
          class="group flex cursor-grab items-center gap-3 rounded-control border border-border bg-surface p-3 transition hover:border-primary hover:shadow-control active:cursor-grabbing"
          tabindex="0"
          role="button"
          :aria-label="`Add ${definition.name}`"
          @dragstart="onDragStart($event, definition)"
          @keydown.enter.prevent="emit('add', definition)"
          @keydown.space.prevent="emit('add', definition)"
        >
          <BaseLucideIcon :icon="GripVertical" class="h-icon-sm w-icon-sm text-content-muted" />
          <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-content">{{ definition.name }}</p>
            <p class="truncate text-xs text-content-muted">{{ definition.description }}</p>
          </div>
          <BaseButton size="sm" variant="ghost" @click="emit('add', definition)">Add</BaseButton>
        </article>
      </section>
    </div>
  </section>
</template>
