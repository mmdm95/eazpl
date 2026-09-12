export type ZplAttributeType =
  'string' | 'text' | 'number' | 'boolean' | 'select' | 'color' | 'image' | 'json'

export interface ZplAttributeOption {
  label: string
  value: unknown
}

export interface ZplAttributeDefinition {
  name: string
  type: ZplAttributeType
  label: string
  required?: boolean
  default?: unknown
  options?: ZplAttributeOption[]
}

export interface ZplComponentDefinition {
  type: string
  name: string
  icon?: string
  description?: string
  category?: string
  preview?: 'text' | 'code' | 'shape' | 'image' | 'table'
  attributes: ZplAttributeDefinition[]
  defaultWidth?: number
  defaultHeight?: number
  resizable?: boolean
  rotatable?: boolean
}

export interface ZplComponentInstance {
  id: string
  type: string
  x: number
  y: number
  width?: number
  height?: number
  rotation?: number
  visible?: boolean
  locked?: boolean
  attributes: Record<string, unknown>
}

export interface ZplLabelConfig {
  width: number
  height: number
  dpi: number
  orientation?: string
}

export interface ZplGridConfig {
  enabled: boolean
  size: number
  snap: boolean
}

export interface ZplDesignerState {
  label: ZplLabelConfig
  components: ZplComponentInstance[]
  selectedComponentId: string | null
  activeTool: ZplDesignerTool
  zoom: number
  grid: ZplGridConfig
}

export type ZplDesignerTool = 'selection' | 'hand'

export interface ZplApiError {
  message: string
  errors?: Record<string, unknown>
}
