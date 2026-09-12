import {describe, expect, it} from 'vitest'

import {useZplDesigner} from '../composables/zpl/useZplDesigner'
import type {ZplComponentDefinition} from '../types/zpl'

const definition: ZplComponentDefinition = {
  type: 'text',
  name: 'Text',
  attributes: [
    {
      name: 'text',
      type: 'text',
      label: 'Text',
      required: true,
      default: 'Hello',
    },
  ],
  defaultWidth: 100,
  defaultHeight: 30,
}

describe('useZplDesigner', () => {
  it('adds and duplicates reactive component instances safely', () => {
    const designer = useZplDesigner()
    const component = designer.addComponent(definition, 10, 20)

    expect(designer.state.value.components).toHaveLength(1)
    expect(component.attributes.text).toBe('Hello')

    designer.duplicateComponent(component.id)

    expect(designer.state.value.components).toHaveLength(2)
    expect(designer.canUndo.value).toBe(true)
  })

  it('supports canvas tools and layer visibility and locking', () => {
    const designer = useZplDesigner()
    const component = designer.addComponent(definition, 10, 20)

    designer.setActiveTool('hand')
    expect(designer.state.value.activeTool).toBe('hand')

    designer.toggleComponentLock(component.id)
    expect(component.locked).toBe(true)

    designer.toggleComponentVisibility(component.id)
    expect(component.visible).toBe(false)
    expect(designer.state.value.selectedComponentId).toBeNull()
  })
})
