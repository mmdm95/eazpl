<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { HelpCircle } from '@lucide/vue'
import { ZplCanvas } from '@/components/zpl/canvas'
import { ZplLayersPanel } from '@/components/zpl/layers'
import { ZplComponentPalette } from '@/components/zpl/palette'
import { ZplPropertiesPanel } from '@/components/zpl/properties'
import { ZplToolbar } from '@/components/zpl/toolbar'
import { BaseAlert, BaseModal, BaseTab, BaseTooltip } from '@/components/base'
import { useZplComponents } from '@/composables/zpl/useZplComponents'
import { useZplDesigner } from '@/composables/zpl/useZplDesigner'
import { generateZpl } from '@/services/zpl'
import type { ZplComponentDefinition } from '@/types/zpl'
import type { ZplDesignerNotification } from './types'

const componentService = useZplComponents()
const designer = useZplDesigner()
const mainElement = ref<HTMLElement | null>(null)
const viewport = ref<HTMLElement | null>(null)
const generatedZpl = ref('')
const generatedPreview = ref<string | null>(null)
const previewError = ref<string | null>(null)
const generating = ref(false)
const notification = ref<ZplDesignerNotification | null>(null)
const activePanel = ref('properties')
const guideVisible = ref(false)
const isFullscreen = ref(false)

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

async function toggleFullscreen(): Promise<void> {
  try {
    if (document.fullscreenElement) await document.exitFullscreen()
    else await mainElement.value?.requestFullscreen()
  } catch {
    showNotification('error', 'Unable to change fullscreen mode.')
  }
}

function onFullscreenChange(): void {
  isFullscreen.value = document.fullscreenElement === mainElement.value
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
  document.addEventListener('fullscreenchange', onFullscreenChange)
  await componentService.load()
  fit()
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  window.removeEventListener('keydown', onKeydown)
  document.removeEventListener('fullscreenchange', onFullscreenChange)
})
</script>
<template>
  <main
    ref="mainElement"
    class="flex h-screen w-full flex-col overflow-hidden bg-surface-muted text-content"
  >
    <ZplToolbar
      :label="designer.state.value.label"
      :grid="designer.state.value.grid"
      :zoom="designer.state.value.zoom"
      :can-undo="designer.canUndo.value"
      :can-redo="designer.canRedo.value"
      :generating="generating"
      :has-output="generatedZpl !== ''"
      :active-tool="designer.state.value.activeTool"
      :fullscreen="isFullscreen"
      @update:label="designer.updateLabel"
      @update:grid="designer.updateGrid"
      @set-tool="designer.setActiveTool"
      @zoom-in="designer.setZoom(designer.state.value.zoom + 0.1)"
      @zoom-out="designer.setZoom(designer.state.value.zoom - 0.1)"
      @fit="fit"
      @fullscreen="toggleFullscreen"
      @undo="designer.undo"
      @redo="designer.redo"
      @generate="generate"
      @copy="copyOutput"
      @download="downloadOutput"
    />

    <div v-if="notification" class="border-b border-border bg-surface px-4 py-2">
      <BaseAlert
        :variant="notification.type === 'success' ? 'success' : 'danger'"
        :description="notification.message"
        :show-icon="true"
      />
    </div>

    <div class="grid min-h-0 flex-1 grid-cols-[250px_minmax(0,1fr)_360px]">
      <aside class="flex min-h-0 flex-col gap-3 border-e border-border bg-surface p-3">
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

      <section ref="viewport" class="relative min-h-0 min-w-0">
        <ZplCanvas
          :label="designer.state.value.label"
          :grid="designer.state.value.grid"
          :zoom="designer.state.value.zoom"
          :components="designer.state.value.components"
          :definitions="componentService.components.value"
          :selected-component-id="designer.state.value.selectedComponentId"
          :active-tool="designer.state.value.activeTool"
          @set-tool="designer.setActiveTool"
          @update:grid="designer.updateGrid"
          @add="addByType"
          @begin-change="designer.recordHistory"
          @select="designer.selectComponent"
          @move="designer.moveComponent"
          @resize="designer.resizeComponent"
          @rotate="designer.rotateComponent"
          @zoom="designer.setZoom"
        />
        <BaseTooltip content="ZPL designer guide" placement="left">
          <button
            type="button"
            class="absolute bottom-3 right-3 flex h-8 w-8 items-center justify-center rounded-pill border border-border bg-surface text-content-muted shadow-control transition hover:border-primary hover:text-primary"
            aria-label="ZPL designer guide"
            @click="guideVisible = true"
          >
            <HelpCircle class="h-4 w-4" />
          </button>
        </BaseTooltip>
      </section>

      <aside class="flex min-h-0 flex-col gap-3 border-s border-border bg-surface p-3">
        <BaseTab
          v-model="activePanel"
          :items="[
            { value: 'properties', label: 'Properties' },
            { value: 'output', label: 'Output' },
          ]"
          :classes="{
            panelContainer: 'min-h-0 w-full flex-1',
            panel: 'flex min-h-0 flex-1 flex-col',
            tabContent: 'flex min-h-0 flex-1 flex-col',
          }"
        >
          <template #panel-properties>
            <ZplPropertiesPanel
              :instance="designer.selectedComponent.value"
              :definition="selectedDefinition"
              @update-attribute="designer.updateAttribute"
              @update-geometry="designer.updateGeometry"
            />
          </template>

          <template #panel-output>
            <section class="flex min-h-0 flex-1 flex-col">
              <img
                v-if="generatedPreview"
                :src="generatedPreview"
                alt="Backend-generated label preview"
                class="mb-3 max-h-72 w-full rounded-control border border-border bg-white object-contain"
              />
              <BaseAlert
                v-else-if="previewError"
                variant="danger"
                :description="previewError"
                class="mb-3"
              />
              <pre
                class="min-h-40 flex-1 overflow-auto rounded-control border border-border bg-surface-muted p-3 text-xs text-content"
                >{{ generatedZpl || 'Generated ZPL will appear here.' }}</pre>
            </section>
          </template>
        </BaseTab>
      </aside>
    </div>

    <BaseModal v-model="guideVisible" title="ZPL designer guide" :max-width="560">
      <div class="space-y-4 p-4 text-sm text-content">
        <section>
          <h3 class="font-semibold text-content">Canvas</h3>
          <ul class="mt-1 list-disc space-y-1 ps-5 text-content-muted">
            <li>Drag a component from the left panel onto the label.</li>
            <li>Use Hand for click-and-drag panning; middle-drag also pans in any mode.</li>
            <li>Hold Ctrl or ⌘ while scrolling to zoom; the Fit button restores the full label.</li>
          </ul>
        </section>
        <section>
          <h3 class="font-semibold text-content">Editing</h3>
          <ul class="mt-1 list-disc space-y-1 ps-5 text-content-muted">
            <li>Drag components to move them; drag the corner handle to resize.</li>
            <li>The circular rotate icon appears only for components that support rotation.</li>
            <li>Use the compact layer rows to reorder, lock, hide, duplicate, or delete items.</li>
          </ul>
        </section>
        <section>
          <h3 class="font-semibold text-content">Tables and ZPL</h3>
          <ul class="mt-1 list-disc space-y-1 ps-5 text-content-muted">
            <li>Set the column count first; header and data cells fill automatically.</li>
            <li>Use multiline cell text; the table renders each line as a separate field.</li>
            <li>Generate, then switch to Output to preview or copy the resulting ZPL.</li>
          </ul>
        </section>
        <section>
          <h3 class="font-semibold text-content">Shortcuts</h3>
          <ul class="mt-1 list-disc space-y-1 ps-5 text-content-muted">
            <li>Delete or Backspace removes the selected component.</li>
            <li>Ctrl/⌘+Z and Ctrl/⌘+Shift+Z undo and redo.</li>
            <li>Ctrl/⌘+D duplicates the selected component.</li>
          </ul>
        </section>
      </div>
    </BaseModal>
  </main>
</template>
