import type { BaseClasses, BaseIcon, OverlayPosition } from '../shared'

export type DrawerSize = 'sm' | 'md' | 'lg' | 'full'

export interface DrawerProps {
  modelValue?: boolean
  position?: OverlayPosition
  size?: DrawerSize
  title?: string
  icon?: BaseIcon
  closeIcon?: BaseIcon
  closable?: boolean
  closeOnEsc?: boolean
  closeOnBackdrop?: boolean
  lockScroll?: boolean
  overlayBlur?: boolean
  draggable?: boolean
  dragThreshold?: number
  transitionDuration?: number
  width?: string | number
  height?: string | number
  zIndex?: number
  classes?: DrawerClasses
}

export interface DrawerClasses extends BaseClasses {
  root?: BaseClasses['root']
  panel?: BaseClasses['panel']
  header?: BaseClasses['header']
  dragHandle?: BaseClasses['dragHandle']
  dragIndicator?: BaseClasses['dragIndicator']
  icon?: BaseClasses['icon']
  title?: BaseClasses['title']
  close?: BaseClasses['close']
  closeIcon?: BaseClasses['closeIcon']
  content?: BaseClasses['content']
  footer?: BaseClasses['footer']
}
