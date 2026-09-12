<script setup lang="ts">
import {computed} from 'vue'
import {ArrowDown, ArrowUp, Copy, Trash2} from '@lucide/vue'
import {BaseButton} from '@/components/base'
import type {ZplLayersPanelEmits, ZplLayersPanelProps} from './types'

const props = defineProps<ZplLayersPanelProps>()
const emit = defineEmits<ZplLayersPanelEmits>()

const names = computed(
  () => new Map(props.definitions.map((definition) => [definition.type, definition.name])),
)
</script>
<template>
  <section class="flex min-h-0 flex-1 flex-col gap-3">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-content-muted">Layers</h2>

    <div v-if="components.length === 0" class="rounded-control border border-border bg-surface p-3 text-sm text-content-muted">
      No placed components yet.
    </div>

    <ul v-else class="flex-1 space-y-2 overflow-y-auto">
      <li
        v-for="(component, index) in [...components].reverse()"
        :key="component.id"
        :class="[
          'flex items-center gap-2 rounded-control border p-2 transition',
          component.id === selectedComponentId
            ? 'border-primary bg-primary-soft'
            : 'border-border bg-surface hover:border-primary/60',
        ]"
      >
        <button
          type="button"
          class="min-w-0 flex-1 text-start"
          @click="emit('select', component.id)"
        >
          <span class="block truncate text-sm font-medium text-content">
            {{ names.get(component.type) ?? component.type }}
          </span>
          <span class="block text-xs text-content-muted">
            {{ component.x }}, {{ component.y }} · layer {{ components.length - index }}
          </span>
        </button>
        <BaseButton
          size="sm"
          variant="ghost"
          :icon="ArrowUp"
          :aria-label="`Move ${component.type} up`"
          @click="emit('move', component.id, 1)"
        />
        <BaseButton
          size="sm"
          variant="ghost"
          :icon="ArrowDown"
          :aria-label="`Move ${component.type} down`"
          @click="emit('move', component.id, -1)"
        />
        <BaseButton
          size="sm"
          variant="ghost"
          :icon="Copy"
          :aria-label="`Duplicate ${component.type}`"
          @click="emit('duplicate', component.id)"
        />
        <BaseButton
          size="sm"
          variant="ghost"
          :icon="Trash2"
          :aria-label="`Delete ${component.type}`"
          @click="emit('remove', component.id)"
        />
      </li>
    </ul>
  </section>
</template>
