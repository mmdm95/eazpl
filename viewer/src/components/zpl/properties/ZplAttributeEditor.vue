<script setup lang="ts">
import {computed} from 'vue'
import {BaseDropdown, BaseInput, BaseSwitch, BaseTextarea} from '@/components/base'
import type {DropdownOption} from '@/components/base'
import type {ZplAttributeEditorEmits, ZplAttributeEditorProps} from './types'

const props = defineProps<ZplAttributeEditorProps>()
const emit = defineEmits<ZplAttributeEditorEmits>()

const stringValue = computed(() => {
  if (props.modelValue === null || props.modelValue === undefined) return ''
  if (typeof props.modelValue === 'string') return props.modelValue
  return JSON.stringify(props.modelValue)
})

const options = computed<DropdownOption[]>(() =>
  (props.definition.options ?? []).map((option) => ({
    value: option.value as string | number,
    label: option.label,
  })),
)

const guide = computed(() => {
  if (props.definition.name === 'fontName') {
    return 'Printer fonts are single characters: A-Z or 0-9.'
  }

  if (props.definition.name === 'mode') {
    return 'N = none, U = UCC case, A = automatic, D = UCC/EAN.'
  }

  return ''
})

function onFile(event: Event): void {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = () => emit('update:modelValue', String(reader.result))
  reader.readAsDataURL(file)
}
</script>
<template>
  <div class="space-y-1">
    <BaseInput
      v-if="definition.type === 'string' || definition.type === 'color'"
      :model-value="stringValue"
      :type="definition.type === 'color' ? 'color' : 'text'"
      :label="definition.label"
      :required="definition.required"
      @update:model-value="emit('update:modelValue', $event)"
    />

    <BaseTextarea
      v-else-if="definition.type === 'text'"
      :model-value="stringValue"
      :label="definition.label"
      :required="definition.required"
      :rows="3"
      @update:model-value="emit('update:modelValue', $event)"
    />

    <BaseInput
      v-else-if="definition.type === 'number'"
      :model-value="stringValue"
      type="number"
      :label="definition.label"
      :required="definition.required"
      @update:model-value="emit('update:modelValue', $event === '' ? null : Number($event))"
    />

    <BaseSwitch
      v-else-if="definition.type === 'boolean'"
      :model-value="Boolean(modelValue)"
      :label="definition.label"
      @update:model-value="emit('update:modelValue', $event)"
    />

    <BaseDropdown
      v-else-if="definition.type === 'select'"
      :model-value="(modelValue as string | number | null)"
      :options="options"
      :label="definition.label"
      @update:model-value="emit('update:modelValue', $event)"
    />

    <BaseTextarea
      v-else-if="definition.type === 'json'"
      :model-value="stringValue"
      :label="definition.label"
      :required="definition.required"
      :rows="5"
      @update:model-value="emit('update:modelValue', $event)"
    />

    <div v-else-if="definition.type === 'image'" class="space-y-2">
      <label class="text-sm font-medium text-content" :for="`attribute-${definition.name}`">
        {{ definition.label }}
      </label>
      <input
        :id="`attribute-${definition.name}`"
        type="file"
        accept="image/*"
        class="w-full rounded-control border border-border bg-surface text-sm text-content file:mr-3 file:rounded-pill file:border-0 file:bg-primary-soft file:px-3 file:py-1 file:text-primary"
        @change="onFile"
      />
      <img
        v-if="typeof modelValue === 'string' && modelValue.startsWith('data:image/')"
        :src="modelValue"
        :alt="definition.label"
        class="max-h-32 rounded-control border border-border object-contain"
      />
    </div>

    <p v-if="guide" class="text-xs text-content-muted">{{ guide }}</p>
  </div>
</template>
