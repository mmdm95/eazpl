import {
  type ComponentPublicInstance,
  computed,
  type CSSProperties,
  onBeforeUnmount,
  ref,
  type Ref,
} from 'vue'
import type {OverlayDragOffset} from './shared'

interface UseOverlayDragOptions {
  enabled: Ref<boolean>
  onDragStart?: (event: PointerEvent) => void
  onDrag?: (event: PointerEvent, offset: OverlayDragOffset) => void
  onDragEnd?: (event: PointerEvent, offset: OverlayDragOffset) => void
}

export function useOverlayDrag({enabled, onDragStart, onDrag, onDragEnd}: UseOverlayDragOptions) {
  const panelRef = ref<HTMLElement>()
  const isDragging = ref(false)
  const dragOffset = ref<OverlayDragOffset>({x: 0, y: 0})
  let startClient = {x: 0, y: 0}
  let startOffset = {x: 0, y: 0}
  let dragBounds = {minX: 0, maxX: 0, minY: 0, maxY: 0}

  const dragStyle = computed<CSSProperties>(() =>
    dragOffset.value.x === 0 && dragOffset.value.y === 0
      ? {}
      : {
        transform: `translate3d(${dragOffset.value.x}px, ${dragOffset.value.y}px, 0)`,
      },
  )

  function isInteractiveTarget(target: EventTarget | null): boolean {
    return (
      target instanceof Element &&
      Boolean(
        target.closest(
          'button, a, input, textarea, select, [contenteditable="true"], [data-no-drag]',
        ),
      )
    )
  }

  function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), Math.max(min, max))
  }

  function onPointerMove(event: PointerEvent): void {
    const panel = panelRef.value
    if (!enabled.value || !panel || !isDragging.value) return

    event.preventDefault()
    dragOffset.value = {
      x: clamp(startOffset.x + event.clientX - startClient.x, dragBounds.minX, dragBounds.maxX),
      y: clamp(startOffset.y + event.clientY - startClient.y, dragBounds.minY, dragBounds.maxY),
    }
    onDrag?.(event, {...dragOffset.value})
  }

  function getViewportBounds(): { width: number; height: number } {
    if (typeof document === 'undefined') {
      return {width: window.innerWidth, height: window.innerHeight}
    }

    return {
      // The panel is fixed to the viewport, so the draggable area must not
      // expand to the document's scrollable content.
      width: window.innerWidth,
      height: window.innerHeight,
    }
  }

  function onPointerUp(event: PointerEvent): void {
    if (!isDragging.value) return

    isDragging.value = false
    panelRef.value?.releasePointerCapture?.(event.pointerId)
    panelRef.value?.removeEventListener('pointermove', onPointerMove)
    panelRef.value?.removeEventListener('pointerup', onPointerUp)
    panelRef.value?.removeEventListener('pointercancel', onPointerUp)
    onDragEnd?.(event, {...dragOffset.value})
  }

  function onPointerDown(event: PointerEvent): void {
    const panel = panelRef.value
    if (
      !enabled.value ||
      !panel ||
      !event.isPrimary ||
      event.button !== 0 ||
      isInteractiveTarget(event.target)
    ) {
      return
    }

    event.preventDefault()
    startOffset = {...dragOffset.value}
    const rect = panel.getBoundingClientRect()
    const viewport = getViewportBounds()

    dragBounds = {
      minX: startOffset.x - rect.left,
      maxX: startOffset.x + viewport.width - rect.right,
      minY: startOffset.y - rect.top,
      maxY: startOffset.y + viewport.height - rect.bottom,
    }
    startClient = {x: event.clientX, y: event.clientY}
    isDragging.value = true
    try {
      panel.setPointerCapture(event.pointerId)
    } catch {
      // Pointer capture is unavailable in some test environments and embedded browsers.
    }
    panel.addEventListener('pointermove', onPointerMove)
    panel.addEventListener('pointerup', onPointerUp)
    panel.addEventListener('pointercancel', onPointerUp)
    onDragStart?.(event)
  }

  function setPanelRef(element: Element | ComponentPublicInstance | null): void {
    panelRef.value = element instanceof HTMLElement ? element : undefined
  }

  function resetDrag(): void {
    dragOffset.value = {x: 0, y: 0}
    startOffset = {x: 0, y: 0}
    isDragging.value = false
    panelRef.value?.removeEventListener('pointermove', onPointerMove)
    panelRef.value?.removeEventListener('pointerup', onPointerUp)
    panelRef.value?.removeEventListener('pointercancel', onPointerUp)
  }

  onBeforeUnmount(resetDrag)

  return {
    panelRef,
    setPanelRef,
    isDragging,
    dragOffset,
    dragStyle,
    onPointerDown,
    resetDrag,
  }
}
