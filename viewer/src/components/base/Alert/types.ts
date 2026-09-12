import type { BaseClasses, BaseIcon, BaseVariant } from '../shared'

export interface AlertProps {
  variant?: BaseVariant
  title?: string
  description?: string
  icon?: BaseIcon
  showIcon?: boolean
  closable?: boolean
  closeIcon?: BaseIcon
  classes?: AlertClasses
}

export interface AlertClasses extends BaseClasses {
  root?: BaseClasses['root']
  icon?: BaseClasses['icon']
  content?: BaseClasses['content']
  title?: BaseClasses['title']
  description?: BaseClasses['description']
  message?: BaseClasses['message']
  close?: BaseClasses['close']
  closeIcon?: BaseClasses['closeIcon']
}
