import { describe, expect, it } from 'vitest'

import { mount } from '@vue/test-utils'
import { Check } from '@lucide/vue'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { defineComponent, h, nextTick } from 'vue'

import {
  BaseAccordion,
  BaseAlert,
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseCheckbox,
  BaseDropdown,
  BaseDrawer,
  BaseModal,
  BaseRadio,
  BaseSwitch,
  BaseTab,
  BaseTabContent,
  BaseTabFooter,
  BaseTabHeader,
  BaseTabPanel,
  BaseTextarea,
  BaseTabs,
  baseColorTokens,
  baseMotionTokens,
  basePaddingTokens,
  baseRadiusTokens,
  baseSizeTokens,
  baseThemes,
  getBaseThemeVariables,
} from '../components/base'
import { overlayContainerClass } from '../components/base/shared'

describe('BaseAccordion', () => {
  it('supports component values for item header, content, and footer', () => {
    const HeaderComponent = defineComponent({ setup: () => () => h('strong', 'Component header') })
    const ContentComponent = defineComponent({ setup: () => () => h('p', 'Component content') })
    const FooterComponent = defineComponent({ setup: () => () => h('span', 'Component footer') })
    const wrapper = mount(BaseAccordion, {
      props: {
        modelValue: 'custom',
        items: [
          {
            value: 'custom',
            header: HeaderComponent,
            content: ContentComponent,
            footer: FooterComponent,
          },
        ],
      },
    })

    expect(wrapper.find('button').text()).toContain('Component header')
    expect(wrapper.find('[role="region"]').text()).toContain('Component content')
    expect(wrapper.find('[role="region"] footer').text()).toContain('Component footer')
  })

  it('supports keyed content and footer slots alongside item sections', () => {
    const wrapper = mount(BaseAccordion, {
      props: {
        modelValue: 'custom',
        items: [{ value: 'custom', header: 'Header', content: 'Fallback content' }],
      },
      slots: {
        'content-custom': () => 'Slotted content',
        'footer-custom': () => 'Slotted footer',
      },
    })

    expect(wrapper.find('[role="region"]').text()).toContain('Slotted content')
    expect(wrapper.find('[role="region"]').text()).not.toContain('Fallback content')
    expect(wrapper.find('[role="region"] footer').text()).toContain('Slotted footer')
  })
})

describe('BaseButton', () => {
  it('uses a consistent border and focus structure for every variant', () => {
    for (const variant of ['primary', 'outline', 'ghost'] as const) {
      const wrapper = mount(BaseButton, { props: { variant } })
      const classes = wrapper.classes()

      expect(classes).toContain('border')
      expect(classes).toContain('focus-visible:ring-2')
      expect(classes).toContain('focus-visible:ring-offset-2')
    }
  })

  it('does not emit a click while loading', async () => {
    const wrapper = mount(BaseButton, { props: { loading: true } })

    await wrapper.trigger('click')

    expect(wrapper.emitted('click')).toBeUndefined()
  })
})

describe('BaseBadge', () => {
  it('renders content, an icon, and class overrides', () => {
    const wrapper = mount(BaseBadge, {
      props: {
        variant: 'success',
        size: 'lg',
        icon: Check,
        classes: { root: 'custom-root', label: 'custom-label' },
      },
      slots: { default: 'Active' },
    })

    expect(wrapper.text()).toContain('Active')
    expect(wrapper.classes()).toContain('custom-root')
    expect(wrapper.find('span > span').classes()).toContain('custom-label')
  })
})

describe('BaseAlert', () => {
  it('renders content, a close action, and class overrides', async () => {
    const wrapper = mount(BaseAlert, {
      props: {
        variant: 'danger',
        title: 'Upload failed',
        description: 'The label could not be generated.',
        closable: true,
        classes: { root: 'custom-root', title: 'custom-title' },
      },
      slots: { default: 'Please try again.' },
    })

    expect(wrapper.attributes('role')).toBe('alert')
    expect(wrapper.text()).toContain('Upload failed')
    expect(wrapper.text()).toContain('The label could not be generated.')
    expect(wrapper.text()).toContain('Please try again.')
    expect(wrapper.classes()).toContain('custom-root')
    expect(wrapper.find('h3').classes()).toContain('custom-title')

    await wrapper.find('button').trigger('click')

    expect(wrapper.emitted('close')).toHaveLength(1)
  })
})

describe('BaseTab', () => {
  const items = [
    { value: 'overview', label: 'Overview', content: 'Overview content' },
    { value: 'settings', label: 'Settings', content: 'Settings content' },
  ]

  it('places horizontal content under the tab list', () => {
    const wrapper = mount(BaseTab, { props: { items, modelValue: 'overview' } })
    const root = wrapper.find('[role="tablist"]').element.parentElement

    expect(root?.className).toContain('flex-col')
    expect(wrapper.findAll('[role="tabpanel"]')).toHaveLength(1)
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Overview content')
  })

  it('renders only the active panel when the selected tab changes', async () => {
    const wrapper = mount(BaseTab, { props: { items, modelValue: 'overview' } })

    await wrapper.setProps({ modelValue: 'settings' })

    expect(wrapper.findAll('[role="tabpanel"]')).toHaveLength(1)
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Settings content')
    expect(wrapper.text()).not.toContain('Overview content')
  })

  it('positions the horizontal indicator from the inline start in RTL', async () => {
    const container = document.createElement('div')
    container.setAttribute('dir', 'rtl')
    document.body.appendChild(container)

    const wrapper = mount(BaseTab, {
      props: { items, modelValue: 'overview' },
      attachTo: container,
    })
    const nav = wrapper.find('[role="tablist"]').element
    const settingsTab = wrapper.findAll('[role="tab"]')[1]?.element

    Object.defineProperty(nav, 'getBoundingClientRect', {
      value: () => ({ left: 0, right: 300 }) as DOMRect,
    })
    Object.defineProperty(settingsTab, 'getBoundingClientRect', {
      value: () => ({ left: 90, right: 190 }) as DOMRect,
    })

    await wrapper.setProps({ modelValue: 'settings' })
    await nextTick()

    expect(wrapper.find('[aria-hidden="true"]').attributes('style')).toContain(
      'inset-inline-start: 110px',
    )

    wrapper.unmount()
    container.remove()
  })

  it('supports selecting a tab without a bound model', async () => {
    const wrapper = mount(BaseTab, { props: { items } })

    await wrapper.findAll('[role="tab"]')[1]?.trigger('click')

    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Settings content')
    expect(wrapper.findAll('[role="tabpanel"]')).toHaveLength(1)
  })

  it('keeps vertical content beside the tab list', () => {
    const wrapper = mount(BaseTab, {
      props: { items, modelValue: 'overview', orientation: 'vertical' },
    })
    const root = wrapper.find('[role="tablist"]').element.parentElement

    expect(root?.className).not.toContain('flex-col')
    expect(root?.className).toContain('items-start')
  })

  it('supports composable tabs, headers, panels, content, and footers', async () => {
    const wrapper = mount(BaseTab, {
      props: { modelValue: 'overview' },
      slots: {
        default: () => [
          h(BaseTabs, () => [
            h(BaseTabHeader, { value: 'overview', label: 'Overview' }),
            h(BaseTabHeader, { value: 'settings', label: 'Settings' }),
          ]),
          h(
            BaseTabPanel,
            { value: 'overview', header: 'Panel header' },
            {
              default: () => h(BaseTabContent, () => 'Panel content'),
              footer: () => h(BaseTabFooter, () => 'Panel footer'),
            },
          ),
          h(BaseTabPanel, { value: 'settings', content: 'Settings content' }),
        ],
      },
    })

    expect(wrapper.find('[role="tablist"]').exists()).toBe(true)
    expect(wrapper.findAll('[role="tab"]').map((tab) => tab.text())).toEqual([
      'Overview',
      'Settings',
    ])
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Panel header')
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Panel content')
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Panel footer')

    await wrapper.find('[role="tablist"]').trigger('keydown', { key: 'ArrowRight' })

    expect(wrapper.emitted('update:modelValue')).toEqual([['settings']])
    await wrapper.setProps({ modelValue: 'settings' })
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Settings content')
  })

  it('supports component values for tab label, header, content, and footer', () => {
    const LabelComponent = defineComponent({ setup: () => () => h('strong', 'Component label') })
    const HeaderComponent = defineComponent({ setup: () => () => h('header', 'Component header') })
    const ContentComponent = defineComponent({ setup: () => () => h('p', 'Component content') })
    const FooterComponent = defineComponent({ setup: () => () => h('footer', 'Component footer') })
    const wrapper = mount(BaseTab, {
      props: {
        modelValue: 'custom',
        items: [
          {
            value: 'custom',
            label: LabelComponent,
            header: HeaderComponent,
            content: ContentComponent,
            footer: FooterComponent,
          },
        ],
      },
    })

    expect(wrapper.find('[role="tab"]').text()).toContain('Component label')
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Component header')
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Component content')
    expect(wrapper.find('[role="tabpanel"]').text()).toContain('Component footer')
  })

  it('styles composed tab parts from the parent classes object', () => {
    const wrapper = mount(BaseTab, {
      props: {
        modelValue: 'overview',
        classes: {
          tabs: 'custom-tabs',
          tabHeader: 'custom-tab-header',
          tabPanel: 'custom-tab-panel',
          tabContent: 'custom-tab-content',
          tabFooter: 'custom-tab-footer',
        },
      },
      slots: {
        default: () => [
          h(BaseTabs, () => h(BaseTabHeader, { value: 'overview', label: 'Overview' })),
          h(
            BaseTabPanel,
            { value: 'overview', header: 'Panel header' },
            {
              default: () => h(BaseTabContent, () => 'Panel content'),
              footer: () => h(BaseTabFooter, () => 'Panel footer'),
            },
          ),
        ],
      },
    })

    expect(wrapper.find('[role="tablist"]').classes()).toContain('custom-tabs')
    expect(wrapper.find('[role="tab"]').classes()).toContain('custom-tab-header')
    expect(wrapper.find('[role="tabpanel"]').classes()).toContain('custom-tab-panel')
    expect(wrapper.find('[role="tabpanel"] .min-w-0.flex-1').classes()).toContain(
      'custom-tab-content',
    )
    expect(wrapper.find('[role="tabpanel"] footer').classes()).toContain('custom-tab-footer')
  })
})

describe('BaseDropdown', () => {
  const options = [
    { value: 'blue', label: 'Blue' },
    { value: 'green', label: 'Green' },
  ]

  it('opens, selects an option, and clears the value', async () => {
    const wrapper = mount(BaseDropdown, {
      props: { options, modelValue: null, clearable: true },
      attachTo: document.body,
    })

    await wrapper.find('[aria-haspopup="listbox"]').trigger('click')
    expect(wrapper.find('[role="listbox"]').exists()).toBe(true)

    await wrapper.findAll('[role="option"]')[1]?.trigger('click')
    const updates = wrapper.emitted('update:modelValue')
    expect(updates?.[updates.length - 1]).toEqual(['green'])
    await wrapper.setProps({ modelValue: 'green' })
    expect(wrapper.find('[aria-label="Clear selected option"]').exists()).toBe(true)

    await wrapper.find('[aria-label="Clear selected option"]').trigger('click')
    expect(updates?.[updates.length - 1]).toEqual([null])
    expect(wrapper.emitted('clear')).toHaveLength(1)

    wrapper.unmount()
  })

  it('supports keyboard navigation', async () => {
    const wrapper = mount(BaseDropdown, { props: { options, modelValue: null } })
    const trigger = wrapper.find('[aria-haspopup="listbox"]')

    await trigger.trigger('keydown', { key: 'ArrowDown' })
    await trigger.trigger('keydown', { key: 'ArrowDown' })
    await trigger.trigger('keydown', { key: 'Enter' })

    expect(wrapper.emitted('update:modelValue')).toEqual([['blue']])
  })

  it('renders a fixed menu and keeps it within the viewport', async () => {
    const wrapper = mount(BaseDropdown, {
      props: { options, modelValue: null },
      attachTo: document.body,
    })
    const trigger = wrapper.find('[aria-haspopup="listbox"]')

    Object.defineProperty(trigger.element, 'getBoundingClientRect', {
      value: () =>
        ({
          left: 1000,
          top: 100,
          right: 1080,
          bottom: 140,
          width: 80,
          height: 40,
        }) as DOMRect,
    })

    await trigger.trigger('click')

    const menuStyle = wrapper.find('[role="listbox"]').attributes('style')
    expect(menuStyle).toContain('position: fixed')
    expect(menuStyle).toContain('left: 936px')
    expect(menuStyle).toContain('top: 148px')

    wrapper.unmount()
  })
})

describe('Base design tokens', () => {
  it('exposes the default theme, color, radius, size, and padding tokens', () => {
    expect(getBaseThemeVariables('light')['--ui-color-primary']).toBe(baseColorTokens.primary)
    expect(baseThemes.dark['--ui-color-surface']).not.toBe(baseColorTokens.surface)
    expect(baseRadiusTokens.control).toBe('0.5rem')
    expect(baseSizeTokens.control.height.md).toBe('2.5rem')
    expect(basePaddingTokens.control.x.md).toBe('1rem')
    expect(baseMotionTokens.press).toBe(0.98)
    expect(baseColorTokens.overlay).toMatch(/^oklch\(/)
    expect(baseColorTokens.shadow).toMatch(/^oklch\(/)
  })

  it('maps semantic CSS variables into Tailwind theme utilities', () => {
    const stylesheet = readFileSync(resolve('src/assets/css/main.css'), 'utf8')

    expect(stylesheet).toContain('--color-primary: var(--ui-color-primary)')
    expect(stylesheet).toContain('--color-surface: var(--ui-color-surface)')
    expect(stylesheet).toContain('--radius-control: var(--ui-radius-control)')
    expect(stylesheet).toContain('--spacing-control-height-md: var(--ui-control-height-md)')
    expect(stylesheet).toContain('--spacing-control-padding-x-md: var(--ui-control-padding-x-md)')
    expect(stylesheet).toContain("[data-theme='dark']")
    expect(stylesheet).not.toMatch(/\brgba?\(/i)
  })
})

describe('BaseCheckbox', () => {
  it('updates a boolean model', async () => {
    const wrapper = mount(BaseCheckbox, { props: { modelValue: false, label: 'Accept' } })

    await wrapper.find('input[type="checkbox"]').setValue(true)

    expect(wrapper.emitted('update:modelValue')).toEqual([[true]])
  })

  it('updates an array model', async () => {
    const wrapper = mount(BaseCheckbox, {
      props: { modelValue: [], value: 'news', label: 'News' },
    })

    await wrapper.find('input[type="checkbox"]').setValue(true)

    expect(wrapper.emitted('update:modelValue')).toEqual([[['news']]])
  })
})

describe('BaseTextarea', () => {
  it('updates the model and clears the value', async () => {
    const wrapper = mount(BaseTextarea, {
      props: { modelValue: '', label: 'Notes', clearable: true },
    })

    await wrapper.find('textarea').setValue('Hello')
    expect(wrapper.emitted('update:modelValue')).toEqual([['Hello']])
    await wrapper.setProps({ modelValue: 'Hello' })

    await wrapper.find('[aria-label="Clear textarea"]').trigger('click')
    const updates = wrapper.emitted('update:modelValue')
    expect(updates?.[updates.length - 1]).toEqual([''])
    expect(wrapper.emitted('clear')).toHaveLength(1)
  })
})

describe('BaseRadio', () => {
  it('updates the selected option', async () => {
    const wrapper = mount(BaseRadio, {
      props: {
        modelValue: 'a',
        options: [
          { value: 'a', label: 'Option A' },
          { value: 'b', label: 'Option B' },
        ],
      },
    })

    await wrapper.findAll('input[type="radio"]')[1]?.setValue(true)

    expect(wrapper.emitted('update:modelValue')).toEqual([['b']])
  })
})

describe('BaseSwitch', () => {
  it('toggles the model and exposes switch semantics', async () => {
    const wrapper = mount(BaseSwitch, { props: { modelValue: false, label: 'Notifications' } })
    const control = wrapper.find('[role="switch"]')

    expect(control.attributes('aria-checked')).toBe('false')

    await control.trigger('click')

    expect(wrapper.emitted('update:modelValue')).toEqual([[true]])
    expect(wrapper.emitted('change')).toEqual([[true]])
  })
})

describe('RTL support', () => {
  it('inherits direction and uses logical utilities for vertical tabs', () => {
    const wrapper = mount(BaseTab, {
      props: {
        orientation: 'vertical',
        modelValue: 'overview',
        items: [{ value: 'overview', label: 'Overview', content: 'Content' }],
      },
      attrs: { dir: 'rtl' },
    })

    expect(wrapper.attributes('dir')).toBe('rtl')
    expect(wrapper.find('[role="tablist"]').classes()).toContain('border-s')
    expect(wrapper.find('[role="tab"]').classes()).toContain('text-start')
    expect(wrapper.find('[role="tabpanel"]').classes()).toContain('ps-content-sm')
  })

  it('moves the switch knob toward the inline end in RTL', () => {
    const wrapper = mount(BaseSwitch, {
      props: { modelValue: true, label: 'Notifications' },
      attrs: { dir: 'rtl' },
    })
    const knob = wrapper.find('[role="switch"]').find('span')

    expect(wrapper.attributes('dir')).toBe('rtl')
    expect(knob.classes()).toContain('rtl:-translate-x-5')
  })

  it('uses logical placement and clearing positions for dropdown and textarea', () => {
    const dropdown = mount(BaseDropdown, {
      props: {
        placement: 'bottom-end',
        clearable: true,
        modelValue: 'blue',
        options: [{ value: 'blue', label: 'Blue' }],
      },
      attrs: { dir: 'rtl' },
    })
    const textarea = mount(BaseTextarea, {
      props: { modelValue: 'Hello', clearable: true },
      attrs: { dir: 'rtl' },
    })

    expect(dropdown.attributes('dir')).toBe('rtl')
    expect(dropdown.find('[aria-haspopup="listbox"]').classes()).toContain(
      'pe-dropdown-clear-space',
    )
    expect(dropdown.find('[aria-label="Clear selected option"]').classes()).toContain(
      'dropdown-clear-position',
    )
    expect(textarea.find('[aria-label="Clear textarea"]').classes()).toContain(
      'end-field-action-offset',
    )
  })

  it('keeps physical left and right drawer edges in RTL', () => {
    expect(overlayContainerClass('left')).toContain('rtl:justify-end')
    expect(overlayContainerClass('right')).toContain('rtl:justify-start')
  })
})

describe('Draggable overlays', () => {
  function createPointerEvent(
    type: string,
    {
      button = 0,
      clientX = 0,
      clientY = 0,
    }: { button?: number; clientX?: number; clientY?: number } = {},
  ): Event {
    const event = new Event(type, { bubbles: true, cancelable: true })
    Object.defineProperties(event, {
      button: { value: button },
      clientX: { value: clientX },
      clientY: { value: clientY },
      isPrimary: { value: true },
      pointerId: { value: 1 },
    })
    return event
  }

  async function dragOverlay(header: Element): Promise<void> {
    header.dispatchEvent(createPointerEvent('pointerdown', { clientX: 100, clientY: 100 }))
    await nextTick()
    header.dispatchEvent(createPointerEvent('pointermove', { clientX: 130, clientY: 120 }))
    await nextTick()
    header.dispatchEvent(createPointerEvent('pointerup', { clientX: 130, clientY: 120 }))
    await nextTick()
  }

  it('drags a modal from its header and emits drag lifecycle events', async () => {
    const wrapper = mount(BaseModal, {
      props: { modelValue: true, title: 'Drag me', draggable: true },
      global: { stubs: { teleport: true } },
    })

    expect(wrapper.find('header').classes()).toContain('cursor-move')
    await dragOverlay(wrapper.find('header').element)

    expect(wrapper.emitted('drag-start')).toHaveLength(1)
    const dragEvents = wrapper.emitted('drag')
    expect(dragEvents?.[dragEvents.length - 1]).toEqual([expect.any(Event), { x: 30, y: 20 }])
    expect(wrapper.emitted('drag-end')).toHaveLength(1)
  })

  it("starts a new drag from the modal's current offset", async () => {
    const wrapper = mount(BaseModal, {
      props: { modelValue: true, title: 'Drag me', draggable: true },
      global: { stubs: { teleport: true } },
    })
    const header = wrapper.find('header').element

    await dragOverlay(header)

    header.dispatchEvent(createPointerEvent('pointerdown', { clientX: 200, clientY: 200 }))
    await nextTick()
    header.dispatchEvent(createPointerEvent('pointermove', { clientX: 220, clientY: 210 }))
    await nextTick()

    const dragEvents = wrapper.emitted('drag')
    expect(dragEvents?.[dragEvents.length - 1]).toEqual([expect.any(Event), { x: 50, y: 30 }])
  })

  it('moves a drawer panel with its drag indicator and closes at the threshold', async () => {
    const wrapper = mount(BaseDrawer, {
      props: {
        modelValue: true,
        title: 'Drag me',
        draggable: true,
        dragThreshold: 40,
        position: 'right',
      },
      global: { stubs: { teleport: true } },
    })

    const indicator = wrapper.find('[title="Drag to close"]')
    const panel = wrapper.find('[role="dialog"]')
    expect(indicator.exists()).toBe(true)
    expect(panel.attributes('style')).not.toContain('transform')

    indicator.element.dispatchEvent(
      createPointerEvent('pointerdown', { clientX: 100, clientY: 100 }),
    )
    await nextTick()
    indicator.element.dispatchEvent(
      createPointerEvent('pointermove', { clientX: 120, clientY: 100 }),
    )
    await nextTick()

    expect(panel.attributes('style')).toContain('transform: translate3d(20px, 0px, 0)')

    indicator.element.dispatchEvent(createPointerEvent('pointerup', { clientX: 120, clientY: 100 }))
    await nextTick()

    expect(panel.attributes('style')).not.toContain('transform')
    expect(wrapper.emitted('update:modelValue')).toBeUndefined()

    indicator.element.dispatchEvent(
      createPointerEvent('pointerdown', { clientX: 100, clientY: 100 }),
    )
    await nextTick()
    indicator.element.dispatchEvent(
      createPointerEvent('pointermove', { clientX: 150, clientY: 100 }),
    )
    await nextTick()

    const updates = wrapper.emitted('update:modelValue')
    expect(updates?.[updates.length - 1]).toEqual([false])
    expect(panel.attributes('style')).toContain('transform: translate3d(40px, 0px, 0)')
  })

  it('does not drag when draggable is disabled', async () => {
    const wrapper = mount(BaseModal, {
      props: { modelValue: true, title: 'Static' },
      global: { stubs: { teleport: true } },
    })

    await dragOverlay(wrapper.find('header').element)

    expect(wrapper.emitted('drag-start')).toBeUndefined()
    expect(wrapper.emitted('drag')).toBeUndefined()
  })

  it('allows modal movement toward the top-left within page boundaries', async () => {
    const originalBoundingClientRect = Element.prototype.getBoundingClientRect
    Element.prototype.getBoundingClientRect = () =>
      ({
        x: 100,
        y: 100,
        top: 100,
        right: 200,
        bottom: 200,
        left: 100,
        width: 100,
        height: 100,
      }) as DOMRect

    try {
      const wrapper = mount(BaseModal, {
        props: { modelValue: true, title: 'Drag me', draggable: true },
        global: { stubs: { teleport: true } },
      })
      const header = wrapper.find('header').element

      header.dispatchEvent(createPointerEvent('pointerdown', { clientX: 100, clientY: 100 }))
      await nextTick()
      header.dispatchEvent(createPointerEvent('pointermove', { clientX: 80, clientY: 90 }))
      await nextTick()

      const dragEvents = wrapper.emitted('drag')
      expect(dragEvents?.[dragEvents.length - 1]).toEqual([expect.any(Event), { x: -20, y: -10 }])
    } finally {
      Element.prototype.getBoundingClientRect = originalBoundingClientRect
    }
  })

  it('clamps modal movement to the visible viewport', async () => {
    const originalBoundingClientRect = Element.prototype.getBoundingClientRect
    Element.prototype.getBoundingClientRect = () =>
      ({
        x: 100,
        y: 100,
        top: 100,
        right: 200,
        bottom: 200,
        left: 100,
        width: 100,
        height: 100,
      }) as DOMRect

    try {
      const wrapper = mount(BaseModal, {
        props: { modelValue: true, title: 'Drag me', draggable: true },
        global: { stubs: { teleport: true } },
      })
      const header = wrapper.find('header').element

      header.dispatchEvent(createPointerEvent('pointerdown', { clientX: 100, clientY: 100 }))
      await nextTick()
      header.dispatchEvent(createPointerEvent('pointermove', { clientX: 5000, clientY: 5000 }))
      await nextTick()

      const dragEvents = wrapper.emitted('drag')
      expect(dragEvents?.[dragEvents.length - 1]).toEqual([
        expect.any(Event),
        { x: window.innerWidth - 200, y: window.innerHeight - 200 },
      ])
    } finally {
      Element.prototype.getBoundingClientRect = originalBoundingClientRect
    }
  })
})

describe('BaseCard', () => {
  it('renders header, content, footer, and class overrides', () => {
    const wrapper = mount(BaseCard, {
      props: {
        title: 'Card title',
        description: 'Card description',
        variant: 'elevated',
        padding: 'lg',
        rounded: '2xl',
        hoverable: true,
        classes: { root: 'custom-root', title: 'custom-title' },
      },
      slots: {
        default: 'Card content',
        footer: '<button>Footer action</button>',
      },
    })

    expect(wrapper.text()).toContain('Card title')
    expect(wrapper.text()).toContain('Card description')
    expect(wrapper.text()).toContain('Card content')
    expect(wrapper.text()).toContain('Footer action')
    expect(wrapper.classes()).toContain('custom-root')
    expect(wrapper.classes()).toContain('p-content-lg')
    expect(wrapper.classes()).toContain('rounded-card-lg')
    expect(wrapper.find('h3').classes()).toContain('custom-title')
  })
})
