import type {BaseClasses} from '../shared'

export type TooltipPlacement = 'top' | 'right' | 'bottom' | 'left'

export interface TooltipProps {
  content?: string
  placement?: TooltipPlacement
  delay?: number
  transitionDuration?: number
  disabled?: boolean
  showArrow?: boolean
  id?: string
  classes?: TooltipClasses
}

export interface TooltipClasses extends BaseClasses {
  root?: BaseClasses['root']
  trigger?: BaseClasses['trigger']
  content?: BaseClasses['content']
  arrow?: BaseClasses['arrow']
}
