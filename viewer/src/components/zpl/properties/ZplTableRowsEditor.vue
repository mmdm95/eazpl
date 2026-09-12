<script setup lang="ts">
import {computed, watch} from 'vue'

const props = defineProps<{
  columns: number
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const rows = computed<string[][]>(() => {
  const value = typeof props.modelValue === 'string' ? props.modelValue : JSON.stringify(props.modelValue ?? [])

  try {
    const parsed = JSON.parse(value) as unknown
    if (!Array.isArray(parsed)) return []
    return parsed.map((row) => (Array.isArray(row) ? row.map(String) : []))
  } catch {
    return []
  }
})

const hasHeader = computed(() => rows.value.length > 0)
const bodyRows = computed(() => rows.value.slice(1))

function emitRows(nextRows: string[][]): void {
  emit('update:modelValue', JSON.stringify(nextRows))
}

function normalizeRows(value: string[][]): string[][] {
  return value.map((row) => {
    const nextRow = row.slice(0, props.columns)
    while (nextRow.length < props.columns) nextRow.push('')
    return nextRow
  })
}

function setHeaderEnabled(enabled: boolean): void {
  const currentHeader = rows.value[0] ?? Array.from({length: props.columns}, () => '')
  const currentBody = bodyRows.value
  emitRows(normalizeRows(enabled ? [currentHeader, ...currentBody] : currentBody))
}

function updateCell(rowOffset: number, columnIndex: number, value: string): void {
  const nextRows = rows.value.map((row) => [...row])
  if (!nextRows[rowOffset]) return
  nextRows[rowOffset][columnIndex] = value
  emitRows(nextRows)
}

function addRow(): void {
  emitRows(normalizeRows([...rows.value, Array.from({length: props.columns}, () => '')]))
}

function removeRow(rowOffset: number): void {
  emitRows(rows.value.filter((_, index) => index !== rowOffset))
}

watch(
  () => props.columns,
  () => emitRows(normalizeRows(rows.value)),
  {immediate: true},
)
</script>
<template>
  <section class="space-y-3 rounded-control border border-border bg-surface p-3">
    <div class="flex items-center justify-between gap-3">
      <div>
        <p class="text-sm font-medium text-content">Table rows</p>
        <p class="text-xs text-content-muted">Use <code>~BR</code> in a cell to force a line break.</p>
      </div>
      <label class="inline-flex items-center gap-2 text-xs font-medium text-content">
        <input
          type="checkbox"
          class="h-4 w-4 rounded border-border text-primary"
          :checked="hasHeader"
          @change="setHeaderEnabled(($event.target as HTMLInputElement).checked)"
        />
        Header
      </label>
    </div>

    <div v-if="hasHeader" class="overflow-x-auto">
      <div class="min-w-max rounded-control border border-primary/40 bg-primary-soft p-2">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-primary">Header</p>
        <div class="flex gap-2">
          <input
            v-for="(cell, columnIndex) in rows[0]"
            :key="columnIndex"
            type="text"
            class="h-8 w-28 rounded-control border border-border bg-surface px-2 text-xs text-content"
            :aria-label="`Header column ${columnIndex + 1}`"
            :value="cell"
            placeholder="Use ~BR"
            @input="updateCell(0, columnIndex, ($event.target as HTMLInputElement).value)"
          />
        </div>
      </div>
    </div>

    <div v-if="bodyRows.length === 0" class="rounded-control border border-dashed border-border p-3 text-xs text-content-muted">
      No data rows. Add one to populate the table.
    </div>

    <div v-for="(row, rowOffset) in bodyRows" :key="rowOffset" class="flex items-start gap-2">
      <div class="flex gap-2 overflow-x-auto">
        <input
          v-for="(cell, columnIndex) in row"
          :key="columnIndex"
          type="text"
          class="h-8 w-28 rounded-control border border-border bg-surface px-2 text-xs text-content"
          :aria-label="`Row ${rowOffset + 1}, column ${columnIndex + 1}`"
          :value="cell"
          placeholder="Use ~BR"
          @input="updateCell(rowOffset + 1, columnIndex, ($event.target as HTMLInputElement).value)"
        />
      </div>
      <button
        type="button"
        class="mt-1 h-6 rounded-pill px-2 text-xs font-medium text-danger transition hover:bg-danger-soft"
        :aria-label="`Remove row ${rowOffset + 1}`"
        @click="removeRow(rowOffset + 1)"
      >
        Remove
      </button>
    </div>

    <button
      type="button"
      class="h-8 w-full rounded-control border border-border text-xs font-medium text-content transition hover:border-primary hover:text-primary"
      @click="addRow"
    >
      Add data row
    </button>
  </section>
</template>
