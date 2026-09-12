import {describe, expect, it, vi} from 'vitest'

import {mount} from '@vue/test-utils'
import {nextTick} from 'vue'

import {BaseTooltip} from '../components/base'

function rect(values: Partial<DOMRect>): DOMRect {
  return {
    bottom: 0,
    height: 0,
    left: 0,
    right: 0,
    top: 0,
    width: 0,
    x: 0,
    y: 0,
    toJSON: () => ({}),
    ...values,
  } as DOMRect
}

describe('BaseTooltip', () => {
  it('renders slotted content and links the tooltip to its trigger', async () => {
    vi.useFakeTimers()
    const wrapper = mount(BaseTooltip, {
      attachTo: document.body,
      props: {delay: 0},
      slots: {
        default: '<button type="button">Trigger</button>',
        content: '<span>Slotted tooltip</span>',
      },
    })
    const trigger = wrapper.find('button').element
    trigger.getBoundingClientRect = () =>
      rect({top: 400, bottom: 440, left: 500, right: 600, width: 100, height: 40})

    await wrapper.trigger('pointerenter')
    await vi.advanceTimersByTimeAsync(0)
    await nextTick()

    const tooltip = document.body.querySelector<HTMLElement>('[role="tooltip"]')
    expect(tooltip?.textContent).toContain('Slotted tooltip')
    expect(wrapper.find('button').attributes('aria-describedby')).toBe(tooltip?.id)
    expect(tooltip?.parentElement?.style.transform).toBe('translate3d(550px, 392px, 0)')
    const arrow = tooltip?.parentElement?.querySelector<HTMLElement>('[aria-hidden="true"]')
    expect(arrow?.style.boxShadow).toBe('0 0 0 1px var(--color-border)')
    expect(arrow?.className).toContain('z-0')
    expect(tooltip?.className).toContain('z-10')

    wrapper.unmount()
    vi.useRealTimers()
  })

  it('keeps a right tooltip inside the viewport and repositions it', async () => {
    vi.useFakeTimers()
    const wrapper = mount(BaseTooltip, {
      attachTo: document.body,
      props: {content: 'Viewport-aware tooltip', placement: 'right', delay: 0},
      slots: {default: '<button type="button">Trigger</button>'},
    })
    const trigger = wrapper.find('button').element
    trigger.getBoundingClientRect = () =>
      rect({top: 100, bottom: 140, left: 1000, right: 1100, width: 100, height: 40})

    await wrapper.trigger('pointerenter')
    await vi.advanceTimersByTimeAsync(0)
    await nextTick()

    const tooltip = document.body.querySelector<HTMLElement>('[role="tooltip"]')
    expect(tooltip).not.toBeNull()
    if (!tooltip) return
    tooltip.getBoundingClientRect = () => rect({width: 200, height: 40})
    window.dispatchEvent(new Event('resize'))
    await nextTick()

    const transform = tooltip.parentElement?.style.transform.match(/translate3d\((\d+)px, (\d+)px/)
    expect(transform).not.toBeNull()
    if (!transform) return

    const x = Number(transform[1])
    const y = Number(transform[2])
    expect(x).toBeGreaterThanOrEqual(8)
    expect(x + 200).toBeLessThanOrEqual(window.innerWidth - 8)
    expect(y).toBeGreaterThanOrEqual(8)
    expect(y + 40).toBeLessThanOrEqual(window.innerHeight - 8)

    wrapper.unmount()
    vi.useRealTimers()
  })

  it('mirrors right and left placements and their arrows in RTL', async () => {
    vi.useFakeTimers()
    const computedStyle = vi
      .spyOn(window, 'getComputedStyle')
      .mockReturnValue({direction: 'rtl'} as CSSStyleDeclaration)
    const wrapper = mount(BaseTooltip, {
      attachTo: document.body,
      props: {content: 'RTL tooltip', placement: 'right', delay: 0},
      slots: {default: '<button type="button">Trigger</button>'},
    })
    const trigger = wrapper.find('button').element
    trigger.getBoundingClientRect = () =>
      rect({top: 400, bottom: 440, left: 500, right: 600, width: 100, height: 40})

    await wrapper.trigger('pointerenter')
    await vi.advanceTimersByTimeAsync(0)
    await nextTick()

    const tooltip = document.body.querySelector<HTMLElement>('[role="tooltip"]')
    expect(tooltip?.parentElement?.style.transform).toBe('translate3d(492px, 420px, 0)')
    const arrow = tooltip?.parentElement?.querySelector<HTMLElement>('[aria-hidden="true"]')
    expect(arrow?.style.left).toBe('100%')
    expect(arrow?.style.transform).toBe('translate(50%, -50%) rotate(45deg)')

    wrapper.unmount()
    computedStyle.mockRestore()
    vi.useRealTimers()
  })
})
