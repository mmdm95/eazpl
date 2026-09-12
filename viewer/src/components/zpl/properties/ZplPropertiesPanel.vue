<script setup lang="ts">
import {computed} from 'vue'
import {BaseCard, BaseInput} from '@/components/base'
import {ZplAttributeEditor, ZplTableRowsEditor} from './'
import type {ZplPropertiesPanelEmits, ZplPropertiesPanelProps} from './types'

const props = defineProps<ZplPropertiesPanelProps>()
const emit = defineEmits<ZplPropertiesPanelEmits>()

const visibleAttributes = computed(() =>
  (props.definition?.attributes ?? []).filter(
    (attribute) => !(props.instance?.type === 'table' && attribute.name === 'rows'),
  ),
)
</script>
<template>
  <section class="flex h-full flex-col gap-4 overflow-y-auto">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-content-muted">Properties</h2>

    <BaseCard v-if="!instance || !definition" variant="outline">
      <p class="text-sm font-medium text-content">No component selected</p>
      <p class="mt-1 text-sm text-content-muted">
        Select an item on the canvas to edit its position, size, and backend-defined attributes.
      </p>
    </BaseCard>

    <template v-else>
      <BaseCard>
        <div class="mb-3">
          <h3 class="text-base font-semibold text-content">{{ definition.name }}</h3>
          <p class="text-xs text-content-muted">{{ definition.description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <BaseInput
            :model-value="instance.x"
            type="number"
            label="X"
            min="0"
            @update:model-value="emit('update-geometry', instance.id, {x: Number($event)})"
          />
          <BaseInput
            :model-value="instance.y"
            type="number"
            label="Y"
            min="0"
            @update:model-value="emit('update-geometry', instance.id, {y: Number($event)})"
          />
          <BaseInput
            :model-value="instance.width"
            type="number"
            label="Width"
            min="1"
            @update:model-value="emit('update-geometry', instance.id, {width: Number($event)})"
          />
          <BaseInput
            :model-value="instance.height"
            type="number"
            label="Height"
            min="1"
            @update:model-value="emit('update-geometry', instance.id, {height: Number($event)})"
          />
          <BaseInput
            v-if="definition.rotatable !== false"
            :model-value="instance.rotation ?? 0"
            type="number"
            label="Rotation"
            min="0"
            max="359"
            @update:model-value="emit('update-geometry', instance.id, {rotation: Number($event)})"
          />
        </div>
      </BaseCard>

      <BaseCard>
        <div class="space-y-4">
          <ZplAttributeEditor
            v-for="attribute in visibleAttributes"
            :key="attribute.name"
            :definition="attribute"
            :model-value="instance.attributes[attribute.name] ?? attribute.default ?? null"
            @update:model-value="emit('update-attribute', instance.id, attribute.name, $event)"
          />

          <ZplTableRowsEditor
            v-if="instance.type === 'table' && definition.attributes.some((attribute) => attribute.name === 'rows')"
            :columns="Number(instance.attributes.columns ?? 2)"
            :model-value="instance.attributes.rows"
            @update:model-value="emit('update-attribute', instance.id, 'rows', $event)"
          />
        </div>
      </BaseCard>
    </template>
  </section>
</template>
