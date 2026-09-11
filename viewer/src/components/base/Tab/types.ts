import type { Component } from 'vue'

import type { BaseClasses, BaseIcon } from '../shared'

export type TabItemValue = string | number
export type TabOrientation = 'horizontal' | 'vertical'
export type TabVariant = 'underline' | 'pill'
export type TabSection = string | Component

export interface TabItem {
  value: TabItemValue
  label: TabSection
  header?: TabSection
  content?: TabSection
  footer?: TabSection
  description?: string
  icon?: BaseIcon
  disabled?: boolean
}

export interface TabProps {
  items?: TabItem[]
  modelValue?: TabItemValue
  orientation?: TabOrientation
  variant?: TabVariant
  transitionDuration?: number
  idPrefix?: string
  classes?: TabClasses
}

export interface TabClasses extends BaseClasses {
  root?: BaseClasses['root']
  nav?: BaseClasses['nav']
  tab?: BaseClasses['tab']
  icon?: BaseClasses['icon']
  label?: BaseClasses['label']
  indicator?: BaseClasses['indicator']
  panelContainer?: BaseClasses['panelContainer']
  panel?: BaseClasses['panel']
  tabs?: BaseClasses['tabs']
  tabHeader?: BaseClasses['tabHeader']
  tabHeaderIcon?: BaseClasses['tabHeaderIcon']
  tabHeaderLabel?: BaseClasses['tabHeaderLabel']
  tabPanel?: BaseClasses['tabPanel']
  tabPanelHeader?: BaseClasses['tabPanelHeader']
  tabPanelIcon?: BaseClasses['tabPanelIcon']
  tabPanelTitle?: BaseClasses['tabPanelTitle']
  tabPanelDescription?: BaseClasses['tabPanelDescription']
  tabContent?: BaseClasses['tabContent']
  tabFooter?: BaseClasses['tabFooter']
}

export interface TabsProps {
  classes?: TabsClasses
}

export interface TabsClasses extends BaseClasses {
  root?: BaseClasses['root']
}

export interface TabHeaderProps {
  value: TabItemValue
  label?: TabSection
  icon?: BaseIcon
  disabled?: boolean
  classes?: TabHeaderClasses
}

export interface TabHeaderClasses extends BaseClasses {
  root?: BaseClasses['root']
  icon?: BaseClasses['icon']
  label?: BaseClasses['label']
}

export interface TabPanelProps {
  value: TabItemValue
  header?: TabSection
  description?: string
  icon?: BaseIcon
  content?: TabSection
  footer?: TabSection
  classes?: TabPanelClasses
}

export interface TabPanelClasses extends BaseClasses {
  root?: BaseClasses['root']
  header?: BaseClasses['header']
  icon?: BaseClasses['icon']
  title?: BaseClasses['title']
  description?: BaseClasses['description']
  content?: BaseClasses['content']
  footer?: BaseClasses['footer']
}

export interface TabContentProps {
  content?: TabSection
  classes?: TabContentClasses
}

export interface TabContentClasses extends BaseClasses {
  root?: BaseClasses['root']
}

export interface TabFooterProps {
  content?: TabSection
  classes?: TabFooterClasses
}

export interface TabFooterClasses extends BaseClasses {
  root?: BaseClasses['root']
}

export interface TabSectionProps {
  section?: TabSection
  classes?: TabSectionClasses
}

export interface TabSectionClasses extends BaseClasses {
  root?: BaseClasses['root']
}
