import {ref} from 'vue'
import {getZplComponents} from '@/services/zpl'
import type {ZplComponentDefinition} from '@/types/zpl'

export function useZplComponents() {
  const components = ref<ZplComponentDefinition[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function load(): Promise<void> {
    loading.value = true
    error.value = null

    try {
      components.value = await getZplComponents()
    } catch (caught) {
      error.value = caught instanceof Error ? caught.message : 'Unable to load ZPL components.'
    } finally {
      loading.value = false
    }
  }

  return {components, loading, error, load}
}
