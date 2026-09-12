import type {BaseClasses, BaseIcon, BaseSize, BaseVariant} from '../shared'

export interface ButtonProps {
  type?: 'button' | 'submit' | 'reset'
  variant?: BaseVariant
  size?: BaseSize
  icon?: BaseIcon
  loadingIcon?: BaseIcon
  iconPosition?: 'left' | 'right'
  loading?: boolean
  disabled?: boolean
  block?: boolean
  rounded?: boolean
  classes?: ButtonClasses
}

export interface ButtonClasses extends BaseClasses {
  root?: BaseClasses['root']
  icon?: BaseClasses['icon']
  label?: BaseClasses['label']
}
