import type {BaseClasses} from '../shared'

export type TextareaVariant = 'outline' | 'filled'
export type TextareaResize = 'none' | 'horizontal' | 'vertical' | 'both'

export interface TextareaProps {
  modelValue?: string | number | null
  label?: string
  name?: string
  placeholder?: string
  description?: string
  error?: string
  rows?: number
  maxLength?: number
  resize?: TextareaResize
  variant?: TextareaVariant
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  invalid?: boolean
  clearable?: boolean
  autosize?: boolean
  id?: string
  classes?: TextareaClasses
}

export interface TextareaClasses extends BaseClasses {
  root?: BaseClasses['root']
  label?: BaseClasses['label']
  wrapper?: BaseClasses['wrapper']
  textarea?: BaseClasses['textarea']
  clearButton?: BaseClasses['clearButton']
  clearIcon?: BaseClasses['clearIcon']
  description?: BaseClasses['description']
  error?: BaseClasses['error']
}
