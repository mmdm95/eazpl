import { computed, onBeforeUnmount, ref, type Ref } from 'vue'

import type { OverlayPosition } from './shared'

export type DrawerCloseEdge = 'top' | 'right' | 'bottom' | 'left'

interface UseDrawerDismissDragOptions {
  enabled: Ref<boolean>
  position: Ref<OverlayPosition>
  threshold: Ref<number>
  onDragStart?: (event: PointerEvent) => void
  onDrag?: (event: PointerEvent, progress: number) => void
  onDragEnd?: (event: PointerEvent, progress: number) => void
  onClose: () => void
}

export function getDrawerCloseEdge(position: OverlayPosition): DrawerCloseEdge {
  const edges = position.split('-')
  if (edges.includes('top')) return 'top'
  if (edges.includes('right')) return 'right'
  if (edges.includes('bottom')) return 'bottom'
  return 'left'
}

export function useDrawerDismissDrag({
  enabled,
  position,
  threshold,
  onDragStart,
  onDrag,
  onDragEnd,
  onClose,
}: UseDrawerDismissDragOptions) {
  const isDragging = ref(false)
  const dragProgress = ref(0)
  const dragDistance = ref(0)
  const closeEdge = computed(() => getDrawerCloseEdge(position.value))
  let startClient = { x: 0, y: 0 }
  let activeElement: HTMLElement | null = null
  let pointerId: number | null = null
  let suppressClick = false

  function cleanup(): void {
    if (activeElement && pointerId !== null) {
      activeElement.removeEventListener('pointermove', onPointerMove)
      activeElement.removeEventListener('pointerup', onPointerUp)
      activeElement.removeEventListener('pointercancel', onPointerUp)
      activeElement.releasePointerCapture?.(pointerId)
    }
    activeElement = null
    pointerId = null
    isDragging.value = false
  }

  function calculateDistance(event: PointerEvent): number {
    if (closeEdge.value === 'top') return startClient.y - event.clientY
    if (closeEdge.value === 'right') return event.clientX - startClient.x
    if (closeEdge.value === 'bottom') return event.clientY - startClient.y
    return startClient.x - event.clientX
  }

  function calculateProgress(distance: number): number {
    return Math.min(Math.max(distance / Math.max(1, threshold.value), 0), 1)
  }

  function onPointerMove(event: PointerEvent): void {
    if (!enabled.value || !isDragging.value) return

    event.preventDefault()
    const distance = calculateDistance(event)
    dragDistance.value = Math.min(Math.max(distance, 0), Math.max(1, threshold.value))
    dragProgress.value = calculateProgress(distance)
    onDrag?.(event, dragProgress.value)

    if (dragProgress.value >= 1) {
      const endEvent = event
      cleanup()
      onDragEnd?.(endEvent, 1)
      onClose()
    }
  }

  function onPointerUp(event: PointerEvent): void {
    if (!isDragging.value) return

    const progress = dragProgress.value
    cleanup()
    onDragEnd?.(event, progress)
    dragProgress.value = 0
    dragDistance.value = 0
  }

  function onPointerDown(event: PointerEvent): void {
    const element = event.currentTarget
    if (
      !enabled.value ||
      !(element instanceof HTMLElement) ||
      !event.isPrimary ||
      event.button !== 0
    ) {
      return
    }

    event.preventDefault()
    startClient = { x: event.clientX, y: event.clientY }
    activeElement = element
    pointerId = event.pointerId
    isDragging.value = true
    dragProgress.value = 0
    dragDistance.value = 0
    suppressClick = false

    try {
      element.setPointerCapture(event.pointerId)
    } catch {
      // Pointer capture is unavailable in some test environments and embedded browsers.
    }
    element.addEventListener('pointermove', onPointerMove)
    element.addEventListener('pointerup', onPointerUp)
    element.addEventListener('pointercancel', onPointerUp)
    onDragStart?.(event)
  }

  function onIndicatorClick(): void {
    if (suppressClick) {
      suppressClick = false
      return
    }
    onClose()
  }

  function markDragged(): void {
    suppressClick = dragProgress.value > 0.02
  }

  function resetDrag(): void {
    cleanup()
    dragProgress.value = 0
    dragDistance.value = 0
    suppressClick = false
  }

  onBeforeUnmount(resetDrag)

  return {
    closeEdge,
    isDragging,
    dragProgress,
    dragDistance,
    onPointerDown,
    onIndicatorClick,
    markDragged,
    resetDrag,
  }
}
