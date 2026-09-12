import type {ZplAttributeDefinition, ZplComponentDefinition, ZplComponentInstance} from '@/types/zpl'

export interface ZplAttributeEditorProps {
  definition: ZplAttributeDefinition
  modelValue: unknown
}

export interface ZplAttributeEditorEmits {
  'update:modelValue': [value: unknown]
}

export interface ZplPropertiesPanelProps {
  instance: ZplComponentInstance | null
  definition: ZplComponentDefinition | null
}

export interface ZplPropertiesPanelEmits {
  'update-attribute': [id: string, name: string, value: unknown]
  'update-geometry': [
    id: string,
    geometry: Partial<Pick<ZplComponentInstance, 'x' | 'y' | 'width' | 'height' | 'rotation'>>,
  ]
}
