import type {ZplComponentDefinition, ZplComponentInstance} from '@/types/zpl'

export interface ZplLayersPanelProps {
  components: ZplComponentInstance[]
  definitions: ZplComponentDefinition[]
  selectedComponentId: string | null
}

export interface ZplLayersPanelEmits {
  select: [id: string]
  duplicate: [id: string]
  remove: [id: string]
  move: [id: string, direction: -1 | 1]
  'toggle-visibility': [id: string]
  'toggle-lock': [id: string]
}
