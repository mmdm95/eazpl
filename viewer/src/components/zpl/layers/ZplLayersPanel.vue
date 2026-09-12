<script setup lang="ts">
import {computed} from 'vue'
import {ArrowDown, ArrowUp, Copy, Eye, EyeOff, Lock, LockOpen, Trash2} from '@lucide/vue'
import type {ZplLayersPanelEmits, ZplLayersPanelProps} from './types'
import {BaseTooltip} from '@/components/base'

const props = defineProps<ZplLayersPanelProps>()
const emit = defineEmits<ZplLayersPanelEmits>()

const names = computed(
  () => new Map(props.definitions.map((definition) => [definition.type, definition.name])),
)

const actionClass =
  'flex h-6 w-6 items-center justify-center rounded-control text-content-muted transition hover:bg-surface-muted hover:text-content'
</script>
<template>
  <section class="flex h-64 min-h-0 shrink-0 flex-col gap-2 border-t border-border pt-3">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-content-muted">Layers</h2>

    <div
      v-if="components.length === 0"
      class="rounded-control border border-border bg-surface p-3 text-sm text-content-muted"
    >
      No placed components yet.
    </div>

    <ul v-else class="flex-1 space-y-1 overflow-y-auto pr-1">
      <li
        v-for="(component, index) in [...components].reverse()"
        :key="component.id"
        :class="[
          'flex items-center gap-2 rounded-control border px-2 py-1 transition',
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
          <span class="block truncate text-xs font-medium text-content">
            {{ names.get(component.type) ?? component.type }}
          </span>
          <span class="block text-[10px] text-content-muted">
            {{ component.x }}, {{ component.y }} · layer {{ components.length - index }}
          </span>
        </button>
        <div class="flex items-center gap-0.5">
          <BaseTooltip :content="`${component.visible === false ? 'Show' : 'Hide'} layer`">
            <button
              :class="actionClass"
              type="button"
              :aria-label="`${component.visible === false ? 'Show' : 'Hide'} layer`"
              @click.stop="emit('toggle-visibility', component.id)"
            >
              <component :is="component.visible === false ? EyeOff : Eye" class="h-3.5 w-3.5"/>
            </button>
          </BaseTooltip>
          <BaseTooltip :content="`${component.locked ? 'Unlock' : 'Lock'} layer`">
            <button
              :class="actionClass"
              type="button"
              :aria-label="`${component.locked ? 'Unlock' : 'Lock'} layer`"
              @click.stop="emit('toggle-lock', component.id)"
            >
              <component :is="component.locked ? Lock : LockOpen" class="h-3.5 w-3.5"/>
            </button>
          </BaseTooltip>
          <BaseTooltip content="Move layer up">
            <button
              :class="actionClass"
              type="button"
              aria-label="Move layer up"
              @click.stop="emit('move', component.id, 1)"
            >
              <ArrowUp class="h-3.5 w-3.5"/>
            </button>
          </BaseTooltip>
          <BaseTooltip content="Move layer down">
            <button
              :class="actionClass"
              type="button"
              aria-label="Move layer down"
              @click.stop="emit('move', component.id, -1)"
            >
              <ArrowDown class="h-3.5 w-3.5"/>
            </button>
          </BaseTooltip>
          <BaseTooltip content="Duplicate layer">
            <button
              :class="actionClass"
              type="button"
              aria-label="Duplicate layer"
              @click.stop="emit('duplicate', component.id)"
            >
              <Copy class="h-3.5 w-3.5"/>
            </button>
          </BaseTooltip>
          <BaseTooltip content="Delete layer">
            <button
              :class="actionClass"
              type="button"
              aria-label="Delete layer"
              @click.stop="emit('remove', component.id)"
            >
              <Trash2 class="h-3.5 w-3.5"/>
            </button>
          </BaseTooltip>
        </div>
      </li>
    </ul>
  </section>
</template>
