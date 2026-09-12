import type {ZplComponentDefinition} from '@/types/zpl'

export interface ZplComponentPaletteProps {
  definitions: ZplComponentDefinition[]
  loading: boolean
  error: string | null
}

export interface ZplComponentPaletteEmits {
  add: [definition: ZplComponentDefinition]
}
