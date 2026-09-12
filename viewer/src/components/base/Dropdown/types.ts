import type {BaseClasses, BaseIcon, BaseSize, BaseVariant} from '../shared'

export type DropdownValue = string | number
export type DropdownPlacement = 'bottom-start' | 'bottom-end' | 'top-start' | 'top-end'

export interface DropdownOption {
  value: DropdownValue
  label: string
  icon?: BaseIcon
  disabled?: boolean
}

export interface DropdownProps {
  modelValue?: DropdownValue | null
  options?: DropdownOption[]
  variant?: BaseVariant
  size?: BaseSize
  placement?: DropdownPlacement
  icon?: BaseIcon
  loadingIcon?: BaseIcon
  clearIcon?: BaseIcon
  iconPosition?: 'left' | 'right'
  placeholder?: string
  label?: string
  disabled?: boolean
  loading?: boolean
  clearable?: boolean
  closeOnSelect?: boolean
  transitionDuration?: number
  name?: string
  id?: string
  classes?: DropdownClasses
}

export interface DropdownClasses extends BaseClasses {
  root?: BaseClasses['root']
  label?: BaseClasses['label']
  triggerWrapper?: BaseClasses['triggerWrapper']
  trigger?: BaseClasses['trigger']
  icon?: BaseClasses['icon']
  value?: BaseClasses['value']
  placeholder?: BaseClasses['placeholder']
  loadingIcon?: BaseClasses['loadingIcon']
  clearButton?: BaseClasses['clearButton']
  clearIcon?: BaseClasses['clearIcon']
  triggerIcon?: BaseClasses['triggerIcon']
  menu?: BaseClasses['menu']
  option?: BaseClasses['option']
  optionIcon?: BaseClasses['optionIcon']
  optionLabel?: BaseClasses['optionLabel']
  selectedIcon?: BaseClasses['selectedIcon']
}
