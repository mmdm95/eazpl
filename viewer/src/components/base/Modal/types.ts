import type {BaseClasses, BaseIcon, OverlayPosition} from '../shared'

export interface ModalProps {
  modelValue?: boolean
  position?: OverlayPosition
  title?: string
  icon?: BaseIcon
  closeIcon?: BaseIcon
  closable?: boolean
  closeOnEsc?: boolean
  closeOnBackdrop?: boolean
  lockScroll?: boolean
  overlayBlur?: boolean
  draggable?: boolean
  transitionDuration?: number
  width?: string | number
  maxWidth?: string | number
  zIndex?: number
  classes?: ModalClasses
}

export interface ModalClasses extends BaseClasses {
  root?: BaseClasses['root']
  panel?: BaseClasses['panel']
  header?: BaseClasses['header']
  dragHandle?: BaseClasses['dragHandle']
  icon?: BaseClasses['icon']
  title?: BaseClasses['title']
  close?: BaseClasses['close']
  closeIcon?: BaseClasses['closeIcon']
  content?: BaseClasses['content']
  footer?: BaseClasses['footer']
}
