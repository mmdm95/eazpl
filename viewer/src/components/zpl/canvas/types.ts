import type {
  ZplComponentDefinition,
  ZplComponentInstance,
  ZplDesignerTool,
  ZplGridConfig,
  ZplLabelConfig,
} from '@/types/zpl'

export interface ZplCanvasProps {
  label: ZplLabelConfig
  grid: ZplGridConfig
  zoom: number
  components: ZplComponentInstance[]
  definitions: ZplComponentDefinition[]
  selectedComponentId: string | null
  activeTool: ZplDesignerTool
}

export interface ZplCanvasEmits {
  add: [type: string, x: number, y: number]
  beginChange: []
  select: [id: string | null]
  move: [id: string, x: number, y: number]
  resize: [id: string, width: number, height: number]
  rotate: [id: string, rotation: number]
  zoom: [zoom: number]
}

export interface ZplCanvasComponentProps {
  instance: ZplComponentInstance
  definition: ZplComponentDefinition
  zoom: number
  selected: boolean
  locked: boolean
}

export interface ZplCanvasComponentEmits {
  select: [id: string]
  beginChange: []
  move: [id: string, x: number, y: number]
  resize: [id: string, width: number, height: number]
  rotate: [id: string, rotation: number]
}
