import type {BaseClasses} from '../shared'

export type RadioValue = string | number
export type RadioOrientation = 'horizontal' | 'vertical'

export interface RadioOption {
  value: RadioValue
  label: string
  description?: string
  disabled?: boolean
}

export interface RadioProps {
  modelValue?: RadioValue
  options?: RadioOption[]
  orientation?: RadioOrientation
  label?: string
  description?: string
  error?: string
  name?: string
  disabled?: boolean
  required?: boolean
  classes?: RadioClasses
}

export interface RadioClasses extends BaseClasses {
  root?: BaseClasses['root']
  groupLabel?: BaseClasses['groupLabel']
  group?: BaseClasses['group']
  option?: BaseClasses['option']
  input?: BaseClasses['input']
  optionLabel?: BaseClasses['optionLabel']
  optionDescription?: BaseClasses['optionDescription']
  description?: BaseClasses['description']
  error?: BaseClasses['error']
}
