import type {BaseClasses, BaseIcon, BaseSize} from '../shared'

export type SwitchSize = Extract<BaseSize, 'sm' | 'md' | 'lg'>

export interface SwitchProps {
  modelValue?: boolean
  size?: SwitchSize
  label?: string
  description?: string
  error?: string
  name?: string
  value?: string
  disabled?: boolean
  readonly?: boolean
  loading?: boolean
  onIcon?: BaseIcon
  offIcon?: BaseIcon
  loadingIcon?: BaseIcon
  id?: string
  classes?: SwitchClasses
}

export interface SwitchClasses extends BaseClasses {
  root?: BaseClasses['root']
  control?: BaseClasses['control']
  knob?: BaseClasses['knob']
  onIcon?: BaseClasses['onIcon']
  offIcon?: BaseClasses['offIcon']
  loadingIcon?: BaseClasses['loadingIcon']
  label?: BaseClasses['label']
  description?: BaseClasses['description']
  error?: BaseClasses['error']
}
