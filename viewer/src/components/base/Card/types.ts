import type {BaseClasses, BaseIcon} from '../shared'

export type CardVariant = 'plain' | 'outline' | 'elevated'
export type CardPadding = 'none' | 'sm' | 'md' | 'lg'
export type CardRounded = 'lg' | 'xl' | '2xl'

export interface CardProps {
  variant?: CardVariant
  padding?: CardPadding
  rounded?: CardRounded
  hoverable?: boolean
  title?: string
  description?: string
  icon?: BaseIcon
  classes?: CardClasses
}

export interface CardClasses extends BaseClasses {
  root?: BaseClasses['root']
  header?: BaseClasses['header']
  icon?: BaseClasses['icon']
  title?: BaseClasses['title']
  description?: BaseClasses['description']
  content?: BaseClasses['content']
  footer?: BaseClasses['footer']
}
