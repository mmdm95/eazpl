import { describe, it, expect } from 'vitest'

import { mount } from '@vue/test-utils'
import App from '../App.vue'

describe('App', () => {
  it('mounts the base component library demo', () => {
    const wrapper = mount(App)
    expect(wrapper.text()).toContain('Base component library')
    expect(wrapper.text()).toContain('Tabs with moving worm')
  })
})
