import type {LucideIcon} from '@lucide/vue'
import type {ClassValue} from 'clsx'
import {cn} from '@/utils'

export type BaseSize = 'sm' | 'md' | 'lg'
export type BaseVariant = 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger' | 'success'

export type BaseIcon = LucideIcon

export interface OverlayDragOffset {
  x: number
  y: number
}

export type BaseClassValue = ClassValue
export type BaseClasses = Record<string, BaseClassValue>
export type OverlayPosition =
  | 'center'
  | 'top'
  | 'right'
  | 'bottom'
  | 'left'
  | 'top-right'
  | 'top-bottom'
  | 'top-left'
  | 'right-bottom'
  | 'right-left'
  | 'bottom-left'
  | 'top-right-bottom'
  | 'top-right-left'
  | 'top-bottom-left'
  | 'right-bottom-left'
  | 'top-right-bottom-left'

export const sizeClasses: Record<BaseSize, string> = {
  sm: 'h-control-height-sm px-control-padding-x-sm text-control-sm gap-control-gap-sm',
  md: 'h-control-height-md px-control-padding-x-md text-control-md gap-control-gap-md',
  lg: 'h-control-height-lg px-control-padding-x-lg text-control-lg gap-control-gap-lg',
}

export const variantClasses: Record<BaseVariant, string> = {
  primary:
    'border-transparent bg-primary text-content-inverted hover:bg-primary-hover focus-visible:ring-primary',
  secondary:
    'border-transparent bg-secondary text-content-inverted hover:bg-secondary-hover focus-visible:ring-secondary',
  outline:
    'border-border-strong bg-surface text-content hover:border-border-strong hover:bg-surface-muted focus-visible:ring-border-strong',
  ghost: 'border-transparent text-content hover:bg-surface-muted focus-visible:ring-border-strong',
  danger:
    'border-transparent bg-danger text-content-inverted hover:bg-danger-hover focus-visible:ring-danger',
  success:
    'border-transparent bg-success text-content-inverted hover:bg-success-hover focus-visible:ring-success',
}

const overlayPositionClasses: Record<Exclude<OverlayPosition, 'center'>, string> = {
  top: 'items-start justify-center',
  right: 'items-center justify-end rtl:justify-start',
  bottom: 'items-end justify-center',
  left: 'items-center justify-start rtl:justify-end',
  'top-right': 'items-start justify-end rtl:justify-start',
  'top-bottom': 'items-stretch justify-center',
  'top-left': 'items-start justify-start rtl:justify-end',
  'right-bottom': 'items-end justify-end rtl:justify-start',
  'right-left': 'items-center justify-center',
  'bottom-left': 'items-end justify-start rtl:justify-end',
  'top-right-bottom': 'items-stretch justify-end rtl:justify-start',
  'top-right-left': 'items-start justify-center',
  'top-bottom-left': 'items-stretch justify-center',
  'right-bottom-left': 'items-end justify-center',
  'top-right-bottom-left': 'items-stretch justify-center',
}

const overlayTransitionClasses: Record<Exclude<OverlayPosition, 'center'>, string> = {
  top: '-translate-y-8 opacity-0',
  right: 'translate-x-8 opacity-0',
  bottom: 'translate-y-8 opacity-0',
  left: '-translate-x-8 opacity-0',
  'top-right': '-translate-y-8 translate-x-8 opacity-0',
  'top-bottom': 'opacity-0',
  'top-left': '-translate-y-8 -translate-x-8 opacity-0',
  'right-bottom': 'translate-x-8 translate-y-8 opacity-0',
  'right-left': 'opacity-0',
  'bottom-left': 'translate-y-8 -translate-x-8 opacity-0',
  'top-right-bottom': '-translate-y-8 translate-x-8 opacity-0',
  'top-right-left': '-translate-y-8 opacity-0',
  'top-bottom-left': '-translate-y-8 opacity-0',
  'right-bottom-left': 'translate-y-8 opacity-0',
  'top-right-bottom-left': 'opacity-0',
}

export function overlayContainerClass(position: OverlayPosition): string {
  return position === 'center' ? 'items-center justify-center' : overlayPositionClasses[position]
}

export function overlayPanelEnterClass(position: OverlayPosition): string {
  return position === 'center' ? 'scale-95 opacity-0' : overlayTransitionClasses[position]
}

export function resolveClasses(
  userClasses: BaseClasses | undefined,
  key: string,
  fallback: BaseClassValue,
): string {
  return cn(fallback, userClasses?.[key])
}
