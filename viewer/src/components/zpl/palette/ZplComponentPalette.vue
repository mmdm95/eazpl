<script setup lang="ts">
import {GripVertical} from '@lucide/vue'
import type {ComputedRef} from 'vue'
import {computed} from 'vue'
import {BaseAlert, BaseBadge, BaseButton, BaseLucideIcon, BaseTooltip} from '@/components/base'
import type {ZplComponentDefinition} from '@/types/zpl'
import type {ZplComponentPaletteEmits, ZplComponentPaletteProps} from './types'

const props = defineProps<ZplComponentPaletteProps>()
const emit = defineEmits<ZplComponentPaletteEmits>()

const grouped: ComputedRef<Array<{ category: string; items: ZplComponentDefinition[] }>> = computed(
  () => {
    const groups = new Map<string, ZplComponentDefinition[]>()
    for (const definition of props.definitions) {
      const category = definition.category ?? 'Components'
      groups.set(category, [...(groups.get(category) ?? []), definition])
    }
    return [...groups.entries()].map(([category, items]) => ({category, items}))
  },
)

function onDragStart(event: DragEvent, definition: ZplComponentDefinition): void {
  event.dataTransfer?.setData('application/x-zpl-component', definition.type)
  if (event.dataTransfer) event.dataTransfer.effectAllowed = 'copy'
}
</script>
<template>
  <section class="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-semibold uppercase tracking-wide text-content-muted">Components</h2>
      <BaseBadge variant="outline" size="sm">{{ definitions.length }}</BaseBadge>
    </div>

    <div v-if="loading" class="space-y-2">
      <div
        v-for="index in 5"
        :key="index"
        class="h-10 animate-pulse rounded-control bg-surface-muted"
      />
    </div>

    <BaseAlert v-else-if="error" variant="danger" :description="error"/>

    <div v-else class="space-y-4">
      <section v-for="group in grouped" :key="group.category" class="space-y-2">
        <h3 class="text-xs font-semibold uppercase tracking-wide text-content-muted">
          {{ group.category }}
        </h3>
        <article
          v-for="definition in group.items"
          :key="definition.type"
          draggable="true"
          class="group flex h-10 cursor-grab items-center gap-2 rounded-control border border-border bg-surface px-2 transition hover:border-primary hover:shadow-control active:cursor-grabbing"
          tabindex="0"
          role="button"
          :aria-label="`Add ${definition.name}`"
          @dragstart="onDragStart($event, definition)"
          @keydown.enter.prevent="emit('add', definition)"
          @keydown.space.prevent="emit('add', definition)"
        >
          <BaseLucideIcon :icon="GripVertical" class="h-3.5 w-3.5 text-content-muted"/>
          <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-medium text-content">{{ definition.name }}</p>
          </div>
          <BaseTooltip :content="`Add ${definition.name}`">
            <BaseButton
              size="sm"
              variant="ghost"
              :aria-label="`Add ${definition.name}`"
              @click="emit('add', definition)"
            >
              +
            </BaseButton>
          </BaseTooltip>
        </article>
      </section>
    </div>
  </section>
</template>
