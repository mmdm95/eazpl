import { inject, provide, type ComputedRef, type CSSProperties, type InjectionKey } from 'vue'
import { cn } from '@/utils'

import type { BaseClasses } from '../shared'
import type { TabClasses, TabItem, TabItemValue, TabOrientation, TabVariant } from './types'

export interface BaseTabContext {
  activeValue: ComputedRef<TabItemValue | undefined>
  orientation: ComputedRef<TabOrientation>
  variant: ComputedRef<TabVariant>
  transitionDuration: ComputedRef<number>
  classes: ComputedRef<TabClasses | undefined>
  indicatorStyle: ComputedRef<CSSProperties>
  items: ComputedRef<TabItem[]>
  select: (value: TabItemValue) => void
  moveSelection: (direction: 1 | -1) => void
  registerTab: (item: TabItem) => void
  unregisterTab: (value: TabItemValue) => void
  registerElement: (value: TabItemValue, element: HTMLElement | null) => void
  updateIndicator: () => void
  tabId: (value: TabItemValue) => string
  panelId: (value: TabItemValue) => string
}

export const baseTabContextKey: InjectionKey<BaseTabContext> = Symbol('BaseTabContext')

export function provideBaseTabContext(context: BaseTabContext): void {
  provide(baseTabContextKey, context)
}

export function useBaseTabContext(): BaseTabContext {
  const context = inject(baseTabContextKey)
  if (!context) {
    throw new Error('BaseTab child components must be placed inside a BaseTab component.')
  }

  return context
}

export function mergeTabClasses(
  localClasses: BaseClasses | undefined,
  contextClasses: TabClasses | undefined,
  key: string,
  fallbackKey: string | undefined,
  fallback: BaseClasses[string],
): string {
  const contextClassesForKey = resolveContextClasses(contextClasses, key, fallback)
  const contextFallback =
    fallbackKey === undefined
      ? contextClassesForKey
      : resolveContextClasses(contextClasses, fallbackKey, contextClassesForKey)

  return resolveContextClasses(localClasses, key, contextFallback)
}

function resolveContextClasses(
  classes: BaseClasses | undefined,
  key: string,
  fallback: BaseClasses[string],
): string {
  const value = classes?.[key]
  return cn(fallback, value)
}
