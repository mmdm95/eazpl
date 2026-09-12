import { describe, it, expect } from 'vitest'

import { mount } from '@vue/test-utils'
import App from '../App.vue'

describe('App', () => {
  it('mounts the ZPL designer', () => {
    const wrapper = mount(App)
    expect(wrapper.text()).toContain('Components')
    expect(wrapper.text()).toContain('Properties')
  })
})
