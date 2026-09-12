import type {Component} from 'vue'

import type {BaseClasses, BaseIcon} from '../shared'

export type AccordionItemValue = string | number
export type AccordionModelValue = AccordionItemValue | AccordionItemValue[] | null
export type AccordionSection = string | Component

export interface AccordionItem {
  value: AccordionItemValue
  header: AccordionSection
  content?: AccordionSection
  footer?: AccordionSection
  icon?: BaseIcon
  disabled?: boolean
}

export interface AccordionProps {
  items?: AccordionItem[]
  modelValue?: AccordionModelValue
  multiple?: boolean
  transitionDuration?: number
  classes?: AccordionClasses
}

export interface AccordionClasses extends BaseClasses {
  root?: BaseClasses['root']
  item?: BaseClasses['item']
  headerWrapper?: BaseClasses['headerWrapper']
  header?: BaseClasses['header']
  icon?: BaseClasses['icon']
  headerTitle?: BaseClasses['headerTitle']
  headerIcon?: BaseClasses['headerIcon']
  contentWrapper?: BaseClasses['contentWrapper']
  content?: BaseClasses['content']
  contentInner?: BaseClasses['contentInner']
  footer?: BaseClasses['footer']
}
