import type { InputHTMLAttributes } from 'vue'
import type { BaseClasses, BaseIcon, BaseSize } from '../shared'

export type InputType = NonNullable<InputHTMLAttributes['type']>
export type InputVariant = 'outline' | 'filled'

export interface InputProps {
  modelValue?: string | number | null
  type?: InputType
  label?: string
  name?: string
  placeholder?: string
  autocomplete?: string
  description?: string
  error?: string
  size?: BaseSize
  variant?: InputVariant
  icon?: BaseIcon
  loadingIcon?: BaseIcon
  clearIcon?: BaseIcon
  iconPosition?: 'left' | 'right'
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  invalid?: boolean
  loading?: boolean
  clearable?: boolean
  id?: string
  classes?: InputClasses
}

export interface InputClasses extends BaseClasses {
  root?: BaseClasses['root']
  label?: BaseClasses['label']
  inputWrapper?: BaseClasses['inputWrapper']
  input?: BaseClasses['input']
  icon?: BaseClasses['icon']
  clearButton?: BaseClasses['clearButton']
  clearIcon?: BaseClasses['clearIcon']
  loadingIcon?: BaseClasses['loadingIcon']
  description?: BaseClasses['description']
  error?: BaseClasses['error']
}
