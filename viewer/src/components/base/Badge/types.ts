import type {BaseClasses, BaseIcon, BaseSize, BaseVariant} from '../shared'

export interface BadgeProps {
  variant?: BaseVariant
  size?: BaseSize
  icon?: BaseIcon
  iconPosition?: 'left' | 'right'
  classes?: BadgeClasses
}

export interface BadgeClasses extends BaseClasses {
  root?: BaseClasses['root']
  icon?: BaseClasses['icon']
  label?: BaseClasses['label']
}
