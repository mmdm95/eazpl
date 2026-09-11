export type BaseTheme = 'light' | 'dark'

export const baseRadiusTokens = {
  control: '0.5rem',
  card: '1rem',
  cardLg: '1.5rem',
  overlay: '1rem',
  pill: '9999px',
} as const

export const baseSpacingTokens = {
  icon: { xs: '0.75rem', sm: '1rem', md: '1.25rem' },
  overlay: { padding: '1rem', gap: '0.5rem' },
  menu: { padding: '0.25rem', maxHeight: '15rem' },
  fieldAction: { offset: '0.5rem', padding: '0.25rem' },
  fieldLabelGap: '0.25rem',
  dropdownClearSpace: '3rem',
  dragHandle: { area: '1.5rem', length: '5rem', thickness: '0.375rem' },
} as const

export const baseLayoutTokens = {
  contentGap: '1.5rem',
  pagePadding: '2.5rem',
  pageGap: '2.5rem',
} as const
export const baseTypographyTokens = {
  caption: '0.75rem',
  control: { sm: '0.875rem', md: '0.875rem', lg: '1rem' },
  title: '1rem',
} as const
export const baseMotionTokens = { lift: '0.125rem', press: 0.98 } as const
export const baseSizeTokens = {
  control: {
    height: { sm: '2rem', md: '2.5rem', lg: '3rem' },
    text: baseTypographyTokens.control,
    icon: { sm: '1rem', md: '1.25rem' },
  },
  switch: {
    height: { sm: '1.25rem', md: '1.5rem', lg: '1.75rem' },
    width: { sm: '2.25rem', md: '2.75rem', lg: '3rem' },
    knob: { sm: '0.875rem', md: '1.125rem', lg: '1.25rem' },
  },
} as const
export const basePaddingTokens = {
  control: {
    x: { sm: '0.75rem', md: '1rem', lg: '1.5rem' },
    y: { sm: '0.5rem', md: '1rem', lg: '1.5rem' },
    gap: { sm: '0.375rem', md: '0.5rem', lg: '0.625rem' },
  },
  content: { sm: '0.75rem', md: '1.25rem', lg: '1.75rem' },
} as const

export const baseColorTokens = {
  primary: 'oklch(54.6% 0.245 262.881)',
  primaryHover: 'oklch(48.8% 0.243 264.376)',
  primarySoft: 'oklch(93.2% 0.032 255.585)',
  primarySoftHover: 'oklch(88.2% 0.059 254.128)',
  secondary: 'oklch(20.8% 0.042 265.755)',
  secondaryHover: 'oklch(27.9% 0.041 260.031)',
  danger: 'oklch(57.7% 0.245 27.325)',
  dangerHover: 'oklch(50.5% 0.213 27.518)',
  dangerSoft: 'oklch(93.6% 0.032 17.717)',
  success: 'oklch(59.6% 0.145 163.225)',
  successHover: 'oklch(50.8% 0.118 165.612)',
  successSoft: 'oklch(92.5% 0.084 155.995)',
  surface: 'oklch(100% 0 0)',
  surfaceMuted: 'oklch(96.8% 0.007 247.896)',
  surfaceHover: 'oklch(92.9% 0.013 255.508)',
  surfaceRaised: 'oklch(100% 0 0)',
  overlay: 'oklch(20.49% 0.0428 277.449 / 0.6)',
  border: 'oklch(92.9% 0.013 255.508)',
  borderStrong: 'oklch(86.9% 0.022 252.894)',
  content: 'oklch(20.8% 0.042 265.755)',
  contentMuted: 'oklch(37.3% 0.034 259.733)',
  contentSubtle: 'oklch(55.4% 0.046 257.417)',
  contentInverted: 'oklch(100% 0 0)',
  shadow: 'oklch(20.49% 0.0428 277.449 / 0.05)',
} as const

const darkColorTokens = {
  primary: 'oklch(70.7% 0.165 254.624)',
  primaryHover: 'oklch(80.9% 0.105 251.813)',
  primarySoft: 'oklch(37.9% 0.146 265.522)',
  primarySoftHover: 'oklch(48.8% 0.243 264.376)',
  secondary: 'oklch(92.9% 0.013 255.508)',
  secondaryHover: 'oklch(86.9% 0.022 252.894)',
  danger: 'oklch(70.4% 0.191 22.216)',
  dangerHover: 'oklch(80.8% 0.114 19.571)',
  dangerSoft: 'oklch(37.1% 0.155 27.325)',
  success: 'oklch(76.5% 0.177 163.223)',
  successHover: 'oklch(84.5% 0.143 164.978)',
  successSoft: 'oklch(38.5% 0.11 167.87)',
  surface: 'oklch(20.8% 0.042 265.755)',
  surfaceMuted: 'oklch(27.9% 0.041 260.031)',
  surfaceHover: 'oklch(37.3% 0.034 259.733)',
  surfaceRaised: 'oklch(21% 0.034 264.665)',
  overlay: 'oklch(12.615% 0.0432 275.767 / 0.72)',
  border: 'oklch(37.3% 0.034 259.733)',
  borderStrong: 'oklch(44.6% 0.043 257.281)',
  content: 'oklch(98.4% 0.003 247.858)',
  contentMuted: 'oklch(86.9% 0.022 252.894)',
  contentSubtle: 'oklch(70.4% 0.04 256.788)',
  contentInverted: 'oklch(20.8% 0.042 265.755)',
  shadow: 'oklch(0% 0 0 / 0.35)',
} as const

type CssVariableMap = Record<`--${string}`, string>
function kebabCase(value: string): string {
  return value.replace(/[A-Z]/g, (character) => `-${character.toLowerCase()}`)
}
function colorVariables(colors: Record<string, string>): CssVariableMap {
  return Object.fromEntries(
    Object.entries(colors).map(([name, value]) => [`--ui-color-${kebabCase(name)}`, value]),
  ) as CssVariableMap
}

const sharedVariables: CssVariableMap = {
  '--ui-radius-control': baseRadiusTokens.control,
  '--ui-radius-card': baseRadiusTokens.card,
  '--ui-radius-card-lg': baseRadiusTokens.cardLg,
  '--ui-radius-overlay': baseRadiusTokens.overlay,
  '--ui-radius-pill': baseRadiusTokens.pill,
  '--ui-control-height-sm': baseSizeTokens.control.height.sm,
  '--ui-control-height-md': baseSizeTokens.control.height.md,
  '--ui-control-height-lg': baseSizeTokens.control.height.lg,
  '--ui-control-padding-x-sm': basePaddingTokens.control.x.sm,
  '--ui-control-padding-x-md': basePaddingTokens.control.x.md,
  '--ui-control-padding-x-lg': basePaddingTokens.control.x.lg,
  '--ui-control-padding-y-sm': basePaddingTokens.control.y.sm,
  '--ui-control-padding-y-md': basePaddingTokens.control.y.md,
  '--ui-control-padding-y-lg': basePaddingTokens.control.y.lg,
  '--ui-control-gap-sm': basePaddingTokens.control.gap.sm,
  '--ui-control-gap-md': basePaddingTokens.control.gap.md,
  '--ui-control-gap-lg': basePaddingTokens.control.gap.lg,
  '--ui-icon-sm': baseSpacingTokens.icon.sm,
  '--ui-icon-md': baseSpacingTokens.icon.md,
  '--ui-icon-xs': baseSpacingTokens.icon.xs,
  '--ui-overlay-padding': baseSpacingTokens.overlay.padding,
  '--ui-overlay-gap': baseSpacingTokens.overlay.gap,
  '--ui-menu-padding': baseSpacingTokens.menu.padding,
  '--ui-menu-max-height': baseSpacingTokens.menu.maxHeight,
  '--ui-field-action-offset': baseSpacingTokens.fieldAction.offset,
  '--ui-field-action-padding': baseSpacingTokens.fieldAction.padding,
  '--ui-field-label-gap': baseSpacingTokens.fieldLabelGap,
  '--ui-dropdown-clear-space': baseSpacingTokens.dropdownClearSpace,
  '--ui-drag-handle-area': baseSpacingTokens.dragHandle.area,
  '--ui-drag-handle-length': baseSpacingTokens.dragHandle.length,
  '--ui-drag-handle-thickness': baseSpacingTokens.dragHandle.thickness,
  '--ui-switch-height-sm': baseSizeTokens.switch.height.sm,
  '--ui-switch-height-md': baseSizeTokens.switch.height.md,
  '--ui-switch-height-lg': baseSizeTokens.switch.height.lg,
  '--ui-switch-width-sm': baseSizeTokens.switch.width.sm,
  '--ui-switch-width-md': baseSizeTokens.switch.width.md,
  '--ui-switch-width-lg': baseSizeTokens.switch.width.lg,
  '--ui-switch-knob-sm': baseSizeTokens.switch.knob.sm,
  '--ui-switch-knob-md': baseSizeTokens.switch.knob.md,
  '--ui-switch-knob-lg': baseSizeTokens.switch.knob.lg,
  '--ui-content-padding-sm': basePaddingTokens.content.sm,
  '--ui-content-padding-md': basePaddingTokens.content.md,
  '--ui-content-padding-lg': basePaddingTokens.content.lg,
  '--ui-content-gap': baseLayoutTokens.contentGap,
  '--ui-page-padding': baseLayoutTokens.pagePadding,
  '--ui-page-gap': baseLayoutTokens.pageGap,
  '--ui-text-control-sm': baseTypographyTokens.control.sm,
  '--ui-text-control-md': baseTypographyTokens.control.md,
  '--ui-text-control-lg': baseTypographyTokens.control.lg,
  '--ui-text-caption': baseTypographyTokens.caption,
  '--ui-text-title': baseTypographyTokens.title,
  '--ui-motion-lift': baseMotionTokens.lift,
  '--ui-motion-press': String(baseMotionTokens.press),
}

export const baseThemes = {
  light: { ...sharedVariables, ...colorVariables(baseColorTokens) },
  dark: { ...sharedVariables, ...colorVariables(darkColorTokens) },
} as const satisfies Record<BaseTheme, CssVariableMap>
export type BaseThemeVariables = (typeof baseThemes)[BaseTheme]
export function getBaseThemeVariables(theme: BaseTheme): BaseThemeVariables {
  return baseThemes[theme]
}
export function applyBaseTheme(theme: BaseTheme, element?: HTMLElement): void {
  if (typeof document === 'undefined' && !element) return
  const root = element ?? document.documentElement
  root.dataset.theme = theme
  root.classList.toggle('dark', theme === 'dark')
  for (const [name, value] of Object.entries(getBaseThemeVariables(theme)))
    root.style.setProperty(name, value)
}
