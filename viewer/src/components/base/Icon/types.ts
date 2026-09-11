import type { BaseClasses, BaseIcon } from '../shared'

export interface IconProps {
  icon: BaseIcon
  size?: number
  strokeWidth?: number | string
  classes?: IconClasses
}

export interface IconClasses extends BaseClasses {
  root?: BaseClasses['root']
}
