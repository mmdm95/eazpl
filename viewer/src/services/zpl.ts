import type {ZplComponentDefinition, ZplDesignerState} from '@/types/zpl'

interface ApiEnvelope<T> {
  data?: T
  error?: {
    message?: string
    errors?: Record<string, unknown>
  }
}

export class ZplApiError extends Error {
  constructor(
    message: string,
    readonly errors?: Record<string, unknown>,
  ) {
    super(message)
    this.name = 'ZplApiError'
  }
}

const API_BASE_URL = import.meta.env.VITE_ZPL_API_URL ?? '/api'

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  try {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      ...init,
      headers: {
        ...(init?.body ? {'Content-Type': 'application/json'} : {}),
        ...init?.headers,
      },
    })
    const payload = (await response.json().catch(() => null)) as ApiEnvelope<T> | null

    if (!response.ok) {
      throw new ZplApiError(
        payload?.error?.message ?? `The ZPL API request failed with status ${response.status}.`,
        payload?.error?.errors,
      )
    }

    if (!payload?.data) {
      throw new ZplApiError('The ZPL API returned an invalid response.')
    }

    return payload.data
  } catch (error) {
    if (error instanceof ZplApiError) throw error
    throw new ZplApiError('Unable to reach the ZPL API. Start the PHP API and refresh the designer.')
  }
}

export function getZplComponents(): Promise<ZplComponentDefinition[]> {
  return request<ZplComponentDefinition[]>('/zpl/components')
}

export function generateZpl(state: ZplDesignerState): Promise<string> {
  return request<{zpl: string}>('/zpl/generate', {
    method: 'POST',
    body: JSON.stringify(state),
  }).then((payload) => payload.zpl)
}
