import type { ZplDesignerTool, ZplGridConfig, ZplLabelConfig } from '@/types/zpl'

export interface ZplToolbarProps {
  label: ZplLabelConfig
  grid: ZplGridConfig
  zoom: number
  canUndo: boolean
  canRedo: boolean
  generating: boolean
  hasOutput: boolean
  activeTool: ZplDesignerTool
  fullscreen?: boolean
}

export interface ZplToolbarEmits {
  'update:label': [label: Partial<ZplLabelConfig>]
  'update:grid': [grid: Partial<ZplGridConfig>]
  'set-tool': [tool: ZplDesignerTool]
  'zoom-in': []
  'zoom-out': []
  fit: []
  fullscreen: []
  undo: []
  redo: []
  generate: []
  copy: []
  download: []
}
