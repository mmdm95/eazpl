import {describe, expect, it} from 'vitest'
import {mount} from '@vue/test-utils'

import ZplTableRowsEditor from '../components/zpl/properties/ZplTableRowsEditor.vue'

describe('ZplTableRowsEditor', () => {
  it('restores the original header after all data rows are removed', async () => {
    const wrapper = mount(ZplTableRowsEditor, {
      props: {
        columns: 2,
        modelValue: JSON.stringify([
          ['Header 1', 'Header 2'],
          ['Value 1', 'Value 2'],
        ]),
      },
    })

    await wrapper.find('[aria-label="Remove row 1"]').trigger('click')
    const rowEvents = wrapper.emitted('update:modelValue')
    const withoutRow = rowEvents?.[rowEvents.length - 1]?.[0] as string
    expect(JSON.parse(withoutRow)).toEqual([['Header 1', 'Header 2']])

    await wrapper.setProps({modelValue: withoutRow})
    await wrapper.find('input[type="checkbox"]').setValue(false)
    const headerEvents = wrapper.emitted('update:modelValue')
    const withoutHeader = headerEvents?.[headerEvents.length - 1]?.[0] as string
    expect(JSON.parse(withoutHeader)).toEqual([])

    await wrapper.setProps({modelValue: withoutHeader})
    await wrapper.find('input[type="checkbox"]').setValue(true)
    const restoreEvents = wrapper.emitted('update:modelValue')
    const restoredHeader = restoreEvents?.[restoreEvents.length - 1]?.[0] as string
    expect(JSON.parse(restoredHeader)).toEqual([['Header 1', 'Header 2']])
  })
})
