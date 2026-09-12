import {computed, ref} from 'vue'
import {useZplHistory} from './useZplHistory'
import type {
  ZplComponentDefinition,
  ZplComponentInstance,
  ZplDesignerState,
} from '@/types/zpl'

function createId(): string {
  if (typeof crypto.randomUUID === 'function') return crypto.randomUUID()
  return `zpl-${Date.now()}-${Math.random().toString(16).slice(2)}`
}

function defaultState(): ZplDesignerState {
  return {
    label: {width: 812, height: 500, dpi: 203, orientation: 'portrait'},
    components: [],
    selectedComponentId: null,
    zoom: 1,
    grid: {enabled: true, size: 10, snap: true},
  }
}

export function useZplDesigner() {
  const state = ref<ZplDesignerState>(defaultState())
  const history = useZplHistory<ZplDesignerState>()

  const selectedComponent = computed<ZplComponentInstance | null>(
    () => state.value.components.find((component) => component.id === state.value.selectedComponentId) ?? null,
  )

  function recordHistory(): void {
    history.record(state.value)
  }

  function addComponent(definition: ZplComponentDefinition, x: number, y: number): ZplComponentInstance {
    recordHistory()

    const instance: ZplComponentInstance = {
      id: createId(),
      type: definition.type,
      x: Math.max(0, Math.round(x)),
      y: Math.max(0, Math.round(y)),
      width: definition.defaultWidth ?? 100,
      height: definition.defaultHeight ?? 30,
      rotation: 0,
      attributes: Object.fromEntries(
        definition.attributes.map((attribute) => [attribute.name, attribute.default ?? null]),
      ),
    }

    state.value.components.push(instance)
    state.value.selectedComponentId = instance.id
    return instance
  }

  function selectComponent(id: string | null): void {
    state.value.selectedComponentId = id
  }

  function moveComponent(id: string, x: number, y: number, record = true): void {
    if (record) recordHistory()
    const component = state.value.components.find((item) => item.id === id)
    if (!component) return
    component.x = Math.max(0, Math.round(x))
    component.y = Math.max(0, Math.round(y))
  }

  function resizeComponent(
    id: string,
    width: number,
    height: number,
    record = true,
  ): void {
    if (record) recordHistory()
    const component = state.value.components.find((item) => item.id === id)
    if (!component) return
    component.width = Math.max(1, Math.round(width))
    component.height = Math.max(1, Math.round(height))
  }

  function rotateComponent(id: string, rotation: number, record = true): void {
    if (record) recordHistory()
    const component = state.value.components.find((item) => item.id === id)
    if (!component) return
    component.rotation = Math.max(0, Math.min(359, Math.round(rotation)))
  }

  function updateGeometry(
    id: string,
    geometry: Partial<Pick<ZplComponentInstance, 'x' | 'y' | 'width' | 'height' | 'rotation'>>,
  ): void {
    recordHistory()
    const component = state.value.components.find((item) => item.id === id)
    if (!component) return
    Object.assign(component, geometry)
  }

  function updateAttribute(id: string, name: string, value: unknown): void {
    recordHistory()
    const component = state.value.components.find((item) => item.id === id)
    if (!component) return
    component.attributes[name] = value
  }

  function duplicateComponent(id: string): void {
    recordHistory()
    const index = state.value.components.findIndex((item) => item.id === id)
    if (index === -1) return
    const source = state.value.components[index]
    if (!source) return
    const duplicate: ZplComponentInstance = {
      ...JSON.parse(JSON.stringify(source)),
      id: createId(),
      x: source.x + 10,
      y: source.y + 10,
    }
    state.value.components.splice(index + 1, 0, duplicate)
    state.value.selectedComponentId = duplicate.id
  }

  function removeComponent(id: string): void {
    recordHistory()
    state.value.components = state.value.components.filter((component) => component.id !== id)
    if (state.value.selectedComponentId === id) state.value.selectedComponentId = null
  }

  function moveLayer(id: string, direction: -1 | 1): void {
    recordHistory()
    const index = state.value.components.findIndex((component) => component.id === id)
    const target = index + direction
    if (index === -1 || target < 0 || target >= state.value.components.length) return
    const components = state.value.components
    const component = components[index]
    const other = components[target]
    if (!component || !other) return
    components[index] = other
    components[target] = component
  }

  function updateLabel(label: Partial<ZplDesignerState['label']>): void {
    recordHistory()
    Object.assign(state.value.label, label)
  }

  function updateGrid(grid: Partial<ZplDesignerState['grid']>): void {
    recordHistory()
    Object.assign(state.value.grid, grid)
  }

  function setZoom(zoom: number): void {
    state.value.zoom = Math.max(0.1, Math.min(4, zoom))
  }

  function fitToViewport(width: number, height: number): void {
    const padding = 48
    const zoom = Math.min(
      (width - padding) / state.value.label.width,
      (height - padding) / state.value.label.height,
    )
    setZoom(Math.max(0.1, Math.min(1, zoom)))
  }

  function undo(): void {
    state.value = history.undo(state.value)
  }

  function redo(): void {
    state.value = history.redo(state.value)
  }

  return {
    state,
    selectedComponent,
    recordHistory,
    addComponent,
    duplicateComponent,
    fitToViewport,
    moveComponent,
    moveLayer,
    redo,
    removeComponent,
    resizeComponent,
    rotateComponent,
    selectComponent,
    setZoom,
    undo,
    updateAttribute,
    updateGeometry,
    updateGrid,
    updateLabel,
    canUndo: history.canUndo,
    canRedo: history.canRedo,
  }
}
