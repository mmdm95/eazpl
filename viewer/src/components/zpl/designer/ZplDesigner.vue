<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { ZplCanvas } from '@/components/zpl/canvas'
import { ZplLayersPanel } from '@/components/zpl/layers'
import { ZplComponentPalette } from '@/components/zpl/palette'
import { ZplPropertiesPanel } from '@/components/zpl/properties'
import { ZplToolbar } from '@/components/zpl/toolbar'
import { useZplComponents } from '@/composables/zpl/useZplComponents'
import { useZplDesigner } from '@/composables/zpl/useZplDesigner'
import { generateZpl } from '@/services/zpl'
import type { ZplComponentDefinition } from '@/types/zpl'
import type { ZplDesignerNotification } from './types'

const componentService = useZplComponents()
const designer = useZplDesigner()
const viewport = ref<HTMLElement | null>(null)
const generatedZpl = ref('')
const generatedPreview = ref<string | null>(null)
const previewError = ref<string | null>(null)
const generating = ref(false)
const notification = ref<ZplDesignerNotification | null>(null)

const definitionMap = computed(
  () =>
    new Map(componentService.components.value.map((definition) => [definition.type, definition])),
)

const selectedDefinition = computed(
  () =>
    (designer.selectedComponent.value &&
      definitionMap.value.get(designer.selectedComponent.value.type)) ??
    null,
)

function showNotification(type: ZplDesignerNotification['type'], message: string): void {
  notification.value = { type, message }
  window.setTimeout(() => {
    if (notification.value?.message === message) notification.value = null
  }, 4000)
}

function addDefinition(definition: ZplComponentDefinition): void {
  designer.addComponent(definition, 20, 20)
}

function addByType(type: string, x: number, y: number): void {
  const definition = definitionMap.value.get(type)
  if (!definition) return
  designer.addComponent(definition, x, y)
}

async function generate(): Promise<void> {
  generating.value = true
  try {
    const result = await generateZpl(designer.state.value)
    generatedZpl.value = result.zpl
    generatedPreview.value = result.preview
    previewError.value = result.previewError
    showNotification('success', 'ZPL generated successfully.')
  } catch (error) {
    showNotification('error', error instanceof Error ? error.message : 'Unable to generate ZPL.')
  } finally {
    generating.value = false
  }
}

async function copyOutput(): Promise<void> {
  if (!generatedZpl.value) return
  try {
    await navigator.clipboard.writeText(generatedZpl.value)
    showNotification('success', 'ZPL copied to clipboard.')
  } catch {
    showNotification('error', 'Unable to access the clipboard.')
  }
}

function downloadOutput(): void {
  if (!generatedZpl.value) return
  const blob = new Blob([generatedZpl.value], { type: 'text/plain' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'label.zpl'
  link.click()
  URL.revokeObjectURL(url)
}

function fit(): void {
  if (!viewport.value) return
  designer.fitToViewport(viewport.value.clientWidth, viewport.value.clientHeight)
}

function onKeydown(event: KeyboardEvent): void {
  const target = event.target as HTMLElement | null
  if (target && ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName)) return
  const selectedId = designer.selectedComponent.value?.id

  if ((event.key === 'Delete' || event.key === 'Backspace') && selectedId) {
    event.preventDefault()
    designer.removeComponent(selectedId)
  }

  if (event.key.toLowerCase() === 'z' && (event.ctrlKey || event.metaKey)) {
    event.preventDefault()
    if (event.shiftKey) designer.redo()
    else designer.undo()
  }

  if (event.key.toLowerCase() === 'd' && (event.ctrlKey || event.metaKey) && selectedId) {
    event.preventDefault()
    designer.duplicateComponent(selectedId)
  }
}

function onResize(): void {
  fit()
}

onMounted(async () => {
  window.addEventListener('resize', onResize)
  window.addEventListener('keydown', onKeydown)
  await componentService.load()
  fit()
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  window.removeEventListener('keydown', onKeydown)
})
</script>
<template>
  <main class="flex h-screen w-full flex-col overflow-hidden bg-surface-muted text-content">
    <ZplToolbar
      :label="designer.state.value.label"
      :grid="designer.state.value.grid"
      :zoom="designer.state.value.zoom"
      :can-undo="designer.canUndo.value"
      :can-redo="designer.canRedo.value"
      :generating="generating"
      :has-output="generatedZpl !== ''"
      :active-tool="designer.state.value.activeTool"
      @update:label="designer.updateLabel"
      @update:grid="designer.updateGrid"
      @set-tool="designer.setActiveTool"
      @zoom-in="designer.setZoom(designer.state.value.zoom + 0.1)"
      @zoom-out="designer.setZoom(designer.state.value.zoom - 0.1)"
      @fit="fit"
      @undo="designer.undo"
      @redo="designer.redo"
      @generate="generate"
      @copy="copyOutput"
      @download="downloadOutput"
    />

    <div
      v-if="notification"
      :class="[
        'px-4 py-2 text-sm',
        notification.type === 'success'
          ? 'border-b border-success/30 bg-success-soft text-success'
          : 'border-b border-danger/30 bg-danger-soft text-danger',
      ]"
      role="status"
    >
      {{ notification.message }}
    </div>

    <div class="grid min-h-0 flex-1 grid-cols-[280px_minmax(0,1fr)_340px]">
      <aside class="flex min-h-0 flex-col gap-4 border-e border-border bg-surface p-4">
        <ZplComponentPalette
          :definitions="componentService.components.value"
          :loading="componentService.loading.value"
          :error="componentService.error.value"
          @add="addDefinition"
        />
        <ZplLayersPanel
          :components="designer.state.value.components"
          :definitions="componentService.components.value"
          :selected-component-id="designer.state.value.selectedComponentId"
          @select="designer.selectComponent"
          @duplicate="designer.duplicateComponent"
          @remove="designer.removeComponent"
          @move="designer.moveLayer"
          @toggle-visibility="designer.toggleComponentVisibility"
          @toggle-lock="designer.toggleComponentLock"
        />
      </aside>

      <section ref="viewport" class="min-h-0 min-w-0">
        <ZplCanvas
          :label="designer.state.value.label"
          :grid="designer.state.value.grid"
          :zoom="designer.state.value.zoom"
          :components="designer.state.value.components"
          :definitions="componentService.components.value"
          :selected-component-id="designer.state.value.selectedComponentId"
          :active-tool="designer.state.value.activeTool"
          @add="addByType"
          @begin-change="designer.recordHistory"
          @select="designer.selectComponent"
          @move="designer.moveComponent"
          @resize="designer.resizeComponent"
          @rotate="designer.rotateComponent"
        />
      </section>

      <aside
        class="flex min-h-0 flex-col gap-4 overflow-y-auto border-s border-border bg-surface p-4"
      >
        <ZplPropertiesPanel
          :instance="designer.selectedComponent.value"
          :definition="selectedDefinition"
          @update-attribute="designer.updateAttribute"
          @update-geometry="designer.updateGeometry"
        />

        <section class="flex min-h-64 flex-1 flex-col">
          <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-content-muted">
            ZPL preview
          </h2>
          <img
            v-if="generatedPreview"
            :src="generatedPreview"
            alt="Backend-generated label preview"
            class="mb-3 max-h-72 w-full rounded-control border border-border bg-white object-contain"
          />
          <p
            v-else-if="previewError"
            class="mb-3 rounded-control border border-danger/30 bg-danger-soft p-2 text-xs text-danger"
          >
            {{ previewError }}
          </p>
          <pre
            class="min-h-40 flex-1 overflow-auto rounded-control border border-border bg-surface-muted p-3 text-xs text-content"
            >{{ generatedZpl || 'Generated ZPL will appear here.' }}</pre>
        </section>
      </aside>
    </div>
  </main>
</template>
