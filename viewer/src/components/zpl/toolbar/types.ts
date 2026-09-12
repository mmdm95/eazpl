import type {ZplGridConfig, ZplLabelConfig} from '@/types/zpl'

export interface ZplToolbarProps {
  label: ZplLabelConfig
  grid: ZplGridConfig
  zoom: number
  canUndo: boolean
  canRedo: boolean
  generating: boolean
  hasOutput: boolean
}

export interface ZplToolbarEmits {
  'update:label': [label: Partial<ZplLabelConfig>]
  'update:grid': [grid: Partial<ZplGridConfig>]
  'zoom-in': []
  'zoom-out': []
  fit: []
  undo: []
  redo: []
  generate: []
  copy: []
  download: []
}
