import type { BaseClasses, BaseIcon } from '../shared'

export type CheckboxValue = string | number
export type CheckboxModelValue = boolean | CheckboxValue[]

export interface CheckboxProps {
  modelValue?: CheckboxModelValue
  value?: CheckboxValue
  label?: string
  description?: string
  error?: string
  name?: string
  id?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  indeterminate?: boolean
  icon?: BaseIcon
  classes?: CheckboxClasses
}

export interface CheckboxClasses extends BaseClasses {
  root?: BaseClasses['root']
  labelWrapper?: BaseClasses['labelWrapper']
  inputWrapper?: BaseClasses['inputWrapper']
  input?: BaseClasses['input']
  iconWrapper?: BaseClasses['iconWrapper']
  icon?: BaseClasses['icon']
  label?: BaseClasses['label']
  description?: BaseClasses['description']
  error?: BaseClasses['error']
}
