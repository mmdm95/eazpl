import type { App } from 'vue'

import { BaseAccordion } from './Accordion'
import { BaseButton } from './Button'
import { BaseCard } from './Card'
import { BaseCheckbox } from './Checkbox'
import { BaseDrawer } from './Drawer'
import { BaseDropdown } from './Dropdown'
import { BaseLucideIcon } from './Icon'
import { BaseInput } from './Input'
import { BaseModal } from './Modal'
import { BaseRadio } from './Radio'
import { BaseSwitch } from './Switch'
import { BaseTooltip } from './Tooltip'
import {
  BaseTab,
  BaseTabContent,
  BaseTabFooter,
  BaseTabHeader,
  BaseTabPanel,
  BaseTabSection,
  BaseTabs,
} from './Tab'
import { BaseTextarea } from './Textarea'

export { BaseAccordion } from './Accordion'
export type {
  AccordionClasses,
  AccordionItem,
  AccordionItemValue,
  AccordionModelValue,
  AccordionProps,
  AccordionSection,
} from './Accordion'

export { BaseButton } from './Button'
export type { ButtonClasses, ButtonProps } from './Button'

export { BaseCard } from './Card'
export type { CardClasses, CardPadding, CardProps, CardRounded, CardVariant } from './Card'

export { BaseCheckbox } from './Checkbox'
export type { CheckboxClasses, CheckboxModelValue, CheckboxProps, CheckboxValue } from './Checkbox'

export { BaseDrawer } from './Drawer'
export type { DrawerClasses, DrawerProps, DrawerSize } from './Drawer'
export type { DrawerCloseEdge } from './useDrawerDismissDrag'

export { BaseDropdown } from './Dropdown'
export type {
  DropdownClasses,
  DropdownOption,
  DropdownPlacement,
  DropdownProps,
  DropdownValue,
} from './Dropdown'

export { BaseInput } from './Input'
export type { InputClasses, InputProps, InputType, InputVariant } from './Input'

export { BaseLucideIcon } from './Icon'
export type { IconClasses, IconProps } from './Icon'

export { BaseModal } from './Modal'
export type { ModalClasses, ModalProps } from './Modal'

export { BaseRadio } from './Radio'
export type { RadioClasses, RadioOption, RadioOrientation, RadioProps, RadioValue } from './Radio'

export { BaseSwitch } from './Switch'
export type { SwitchClasses, SwitchProps, SwitchSize } from './Switch'

export { BaseTooltip } from './Tooltip'
export type { TooltipClasses, TooltipPlacement, TooltipProps } from './Tooltip'

export {
  BaseTab,
  BaseTabContent,
  BaseTabFooter,
  BaseTabHeader,
  BaseTabPanel,
  BaseTabSection,
  BaseTabs,
} from './Tab'
export type {
  TabClasses,
  TabContentClasses,
  TabContentProps,
  TabFooterClasses,
  TabFooterProps,
  TabHeaderClasses,
  TabHeaderProps,
  TabItem,
  TabItemValue,
  TabOrientation,
  TabPanelClasses,
  TabPanelProps,
  TabProps,
  TabSection,
  TabSectionClasses,
  TabSectionProps,
  TabVariant,
  TabsClasses,
  TabsProps,
} from './Tab'

export { BaseTextarea } from './Textarea'
export type { TextareaClasses, TextareaProps, TextareaResize, TextareaVariant } from './Textarea'

export type {
  BaseClassValue,
  BaseClasses,
  BaseIcon,
  BaseSize,
  BaseVariant,
  OverlayDragOffset,
  OverlayPosition,
} from './shared'

export {
  baseColorTokens,
  baseLayoutTokens,
  baseMotionTokens,
  basePaddingTokens,
  baseRadiusTokens,
  baseSizeTokens,
  baseSpacingTokens,
  baseThemes,
  baseTypographyTokens,
  applyBaseTheme,
  getBaseThemeVariables,
} from './tokens'
export type { BaseTheme, BaseThemeVariables } from './tokens'

export const baseComponents = {
  BaseAccordion,
  BaseButton,
  BaseCard,
  BaseCheckbox,
  BaseDrawer,
  BaseDropdown,
  BaseInput,
  BaseLucideIcon,
  BaseModal,
  BaseRadio,
  BaseSwitch,
  BaseTab,
  BaseTabContent,
  BaseTabFooter,
  BaseTabHeader,
  BaseTabPanel,
  BaseTabSection,
  BaseTabs,
  BaseTextarea,
  BaseTooltip,
}

export default {
  install(app: App): void {
    for (const [name, component] of Object.entries(baseComponents)) {
      app.component(name, component)
    }
  },
}
