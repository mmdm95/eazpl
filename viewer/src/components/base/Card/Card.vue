<script setup lang="ts">
import {computed} from 'vue'
import {cn} from '@/utils'

import {resolveClasses} from '../shared'
import BaseLucideIcon from '../Icon/Icon.vue'
import type {CardProps} from './types'

defineOptions({name: 'BaseCard'})

const props = withDefaults(defineProps<CardProps>(), {
  variant: 'outline',
  padding: 'md',
  rounded: 'xl',
  hoverable: false,
  title: undefined,
  description: undefined,
  icon: undefined,
  classes: undefined,
})

const variantClass = computed(
  () =>
    ({
      plain: 'bg-surface',
      outline: 'border border-border bg-surface',
      elevated: 'border border-transparent bg-surface-raised shadow-lg shadow-shadow',
    })[props.variant],
)

const paddingClass = computed(
  () =>
    ({
      none: '',
      sm: 'p-content-sm',
      md: 'p-content-md',
      lg: 'p-content-lg',
    })[props.padding],
)

const roundedClass = computed(
  () =>
    ({
      lg: 'rounded-control',
      xl: 'rounded-card',
      '2xl': 'rounded-card-lg',
    })[props.rounded],
)

const rootClass = computed(() =>
  resolveClasses(
    props.classes,
    'root',
    cn(
      'flex w-full flex-col transition-all duration-200',
      variantClass.value,
      paddingClass.value,
      roundedClass.value,
      props.hoverable && 'hover:-translate-y-motion-lift hover:shadow-lg hover:shadow-shadow',
    ),
  ),
)
</script>

<template>
  <div :class="rootClass">
    <header
      v-if="props.title || props.description || props.icon || $slots.header"
      :class="
        resolveClasses(props.classes, 'header', 'mb-content-sm flex items-start gap-control-gap-md')
      "
    >
      <slot name="header" :title="props.title" :description="props.description">
        <BaseLucideIcon
          v-if="props.icon"
          :icon="props.icon"
          :classes="{
            root: resolveClasses(
              props.classes,
              'icon',
              'mt-field-label-gap h-icon-md w-icon-md shrink-0 text-primary',
            ),
          }"
        />
        <div class="min-w-0 flex-1">
          <h3
            :class="resolveClasses(props.classes, 'title', 'text-title font-semibold text-content')"
          >
            <slot name="title">{{ props.title }}</slot>
          </h3>
          <p
            v-if="props.description || $slots.description"
            :class="
              resolveClasses(
                props.classes,
                'description',
                'mt-1 text-control-sm text-content-muted',
              )
            "
          >
            <slot name="description">{{ props.description }}</slot>
          </p>
        </div>
      </slot>
    </header>

    <div
      :class="resolveClasses(props.classes, 'content', 'flex-1 text-control-sm text-content-muted')"
    >
      <slot/>
    </div>

    <footer
      v-if="$slots.footer"
      :class="
        resolveClasses(
          props.classes,
          'footer',
          'mt-content-sm flex items-center gap-control-gap-md border-t border-border pt-content-sm',
        )
      "
    >
      <slot name="footer"/>
    </footer>
  </div>
</template>
