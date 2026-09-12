import type {Ref} from 'vue'
import {computed, ref} from 'vue'

export function useZplHistory<T>() {
  const past = ref<T[]>([]) as Ref<T[]>
  const future = ref<T[]>([]) as Ref<T[]>

  function clone(value: T): T {
    return JSON.parse(JSON.stringify(value)) as T
  }

  function record(value: T): void {
    past.value.push(clone(value))
    if (past.value.length > 100) past.value.shift()
    future.value = []
  }

  function undo(current: T): T {
    const previous = past.value.pop()
    if (previous === undefined) return current
    future.value.push(clone(current))
    return previous
  }

  function redo(current: T): T {
    const next = future.value.pop()
    if (next === undefined) return current
    past.value.push(clone(current))
    return next
  }

  function reset(value: T): void {
    past.value = []
    future.value = []
    record(value)
  }

  return {
    canUndo: computed(() => past.value.length > 0),
    canRedo: computed(() => future.value.length > 0),
    record,
    redo,
    reset,
    undo,
  }
}
