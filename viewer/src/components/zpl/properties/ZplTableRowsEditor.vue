<script setup lang="ts">
import {ref, watch} from 'vue'
import {Trash} from "@lucide/vue";
import {BaseButton} from "@/components/base";
import BaseCheckbox from "@/components/base/Checkbox/Checkbox.vue";

const props = defineProps<{
  columns: number
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const headerRow = ref<string[]>([])
const dataRows = ref<string[][]>([])
const hasHeader = ref(false)
let lastEmittedValue = ''

function parseRows(): void {
  const value =
    typeof props.modelValue === 'string' ? props.modelValue : JSON.stringify(props.modelValue ?? [])
  if (value === lastEmittedValue) return

  try {
    const parsed = JSON.parse(value) as unknown
    if (!Array.isArray(parsed)) {
      headerRow.value = normalizeRow([])
      dataRows.value = []
      hasHeader.value = false
      return
    }
    const rows = parsed.map((row) => (Array.isArray(row) ? row.map(String) : []))
    // Never re-infer whether there's a header from row count — that would
    // silently swallow the first data row as a "header" on every reparse.
    // Header state only ever changes explicitly, via the checkbox.
    if (hasHeader.value) {
      headerRow.value = normalizeRow(rows[0] ?? [])
      dataRows.value = rows.slice(1).map(normalizeRow)
    } else {
      dataRows.value = rows.map(normalizeRow)
    }
  } catch {
    headerRow.value = normalizeRow([])
    dataRows.value = []
    hasHeader.value = false
  }
}

function emitRows(nextRows: string[][]): void {
  lastEmittedValue = JSON.stringify(nextRows)
  emit('update:modelValue', lastEmittedValue)
}

function normalizeRow(row: string[]): string[] {
  const nextRow = row.slice(0, props.columns)
  while (nextRow.length < props.columns) nextRow.push('')
  return nextRow
}

function normalizeRows(value: string[][]): string[][] {
  return value.map(normalizeRow)
}

function setHeaderEnabled(enabled: boolean): void {
  if (enabled === hasHeader.value) return
  hasHeader.value = enabled
  if (enabled) {
    // headerRow starts as [] and setHeaderEnabled previously only padded
    // the *emitted* copy, not this ref — so the template's v-for had
    // nothing to iterate over and rendered zero header cells.
    headerRow.value = normalizeRow(headerRow.value)
  }
  const nextRows = enabled ? [headerRow.value, ...dataRows.value] : dataRows.value
  emitRows(normalizeRows(nextRows))
}

function updateHeaderCell(columnIndex: number, value: string): void {
  headerRow.value = normalizeRow([...headerRow.value])
  headerRow.value[columnIndex] = value
  emitRows(normalizeRows([headerRow.value, ...dataRows.value]))
}

function updateDataCell(rowIndex: number, columnIndex: number, value: string): void {
  dataRows.value = dataRows.value.map((row, currentIndex) => {
    if (currentIndex !== rowIndex) return row
    const nextRow = [...row]
    nextRow[columnIndex] = value
    return nextRow
  })
  emitRows(normalizeRows(hasHeader.value ? [headerRow.value, ...dataRows.value] : dataRows.value))
}

function addRow(): void {
  dataRows.value = [...dataRows.value, Array.from({length: props.columns}, () => '')]
  emitRows(normalizeRows(hasHeader.value ? [headerRow.value, ...dataRows.value] : dataRows.value))
}

function removeRow(rowIndex: number): void {
  dataRows.value = dataRows.value.filter((_, currentIndex) => currentIndex !== rowIndex)
  emitRows(normalizeRows(hasHeader.value ? [headerRow.value, ...dataRows.value] : dataRows.value))
}

watch(() => props.modelValue, parseRows, {immediate: true})

watch(
  () => props.columns,
  () => {
    headerRow.value = normalizeRow(headerRow.value)
    dataRows.value = dataRows.value.map(normalizeRow)
    emitRows(normalizeRows(hasHeader.value ? [headerRow.value, ...dataRows.value] : dataRows.value))
  },
)
</script>
<template>
  <section class="space-y-3 rounded-control border border-border bg-surface p-3">
    <div class="flex items-center justify-between gap-3">
      <div>
        <p class="text-sm font-medium text-content">Table rows</p>
        <p class="text-xs text-content-muted">
          Use multiline text to create multiple lines in a cell.
        </p>
      </div>
      <label class="inline-flex items-center gap-2 text-xs font-medium text-content">
        <BaseCheckbox
          :model-value="hasHeader"
          label="Header"
          @update:model-value="(value) => setHeaderEnabled(Array.isArray(value) ? value.length > 0 : value)"
        />
      </label>
    </div>

    <div v-if="hasHeader" class="overflow-x-auto">
      <div class="min-w-max rounded-control border border-primary/40 bg-primary-soft p-2">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-primary">Header</p>
        <div class="flex gap-2">
          <textarea
            v-for="(cell, columnIndex) in headerRow"
            :key="columnIndex"
            rows="2"
            class="w-28 resize-y rounded-control border border-border bg-surface px-2 py-1 text-xs text-content"
            :aria-label="`Header column ${columnIndex + 1}`"
            :value="cell"
            placeholder="One line per row"
            @input="updateHeaderCell(columnIndex, ($event.target as HTMLTextAreaElement).value)"
          ></textarea>
        </div>
      </div>
    </div>

    <div
      v-if="dataRows.length === 0"
      class="rounded-control border border-dashed border-border p-3 text-xs text-content-muted"
    >
      No data rows. Add one to populate the table.
    </div>

    <div v-for="(row, rowOffset) in dataRows" :key="rowOffset" class="flex items-start gap-2">
      <div class="flex gap-2 overflow-x-auto">
        <textarea
          v-for="(cell, columnIndex) in row"
          :key="columnIndex"
          rows="2"
          class="w-28 resize-y rounded-control border border-border bg-surface px-2 py-1 text-xs text-content"
          :aria-label="`Row ${rowOffset + 1}, column ${columnIndex + 1}`"
          :value="cell"
          placeholder="One line per row"
          @input="
            updateDataCell(rowOffset, columnIndex, ($event.target as HTMLTextAreaElement).value)
          "
        ></textarea>
      </div>
      <BaseButton
        size="sm"
        variant="outline"
        :icon="Trash"
        icon-only
        :aria-label="`Remove row ${rowOffset + 1}`"
        @click="removeRow(rowOffset)"
      />
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
