<script setup lang="ts">
import {
  Accessibility,
  Blocks,
  Code,
  ExternalLink,
  LayoutGrid,
  Moon,
  MousePointerClick,
  Move,
  Palette,
  PanelRight,
  PanelTop,
  Save,
  Search,
  ShieldCheck,
  Sparkles,
  Sun,
} from '@lucide/vue'
import {computed, ref} from 'vue'
import {
  type AccordionItemValue,
  applyBaseTheme,
  BaseAccordion,
  BaseButton,
  BaseCard,
  BaseCheckbox,
  BaseDrawer,
  BaseDropdown,
  BaseInput,
  BaseModal,
  BaseRadio,
  BaseSwitch,
  BaseTab,
  BaseTextarea,
  type BaseTheme,
  BaseTooltip,
  type DropdownValue,
  type OverlayPosition,
  type RadioValue,
  type TabItemValue,
} from '@/components/base'
import {useI18n} from '@/i18n'

const {locale, direction, t, setLocale} = useI18n()
const inputValue = ref('')
const singleAccordion = ref<AccordionItemValue>('design')
const multipleAccordion = ref<AccordionItemValue[]>(['accessibility'])
const activeTab = ref<TabItemValue>('overview')
const dropdownValue = ref<DropdownValue | null>('design')
const checkboxValues = ref<Array<string>>(['quality', 'accessibility'])
const textareaValue = ref('')
const radioValue = ref<RadioValue>('compact')
const switchValue = ref(true)
const isModalVisible = ref(false)
const isDrawerVisible = ref(false)
const activeTheme = ref<BaseTheme>('light')
const overlayPositions: OverlayPosition[] = [
  'center',
  'top',
  'right',
  'bottom',
  'left',
  'top-right',
  'top-bottom',
  'top-left',
  'right-bottom',
  'right-left',
  'bottom-left',
  'top-right-bottom',
  'top-right-left',
  'top-bottom-left',
  'right-bottom-left',
  'top-right-bottom-left',
]
const overlayPositionIndex = ref(0)
const modalPosition = computed(() => overlayPositions[overlayPositionIndex.value] ?? 'center')
const drawerPosition = computed(() => overlayPositions[overlayPositionIndex.value] ?? 'right')
const tabs = computed(() => [
  {value: 'overview', label: t('app.overview'), icon: LayoutGrid, content: t('app.worm')},
  {value: 'api', label: t('app.api'), icon: Code, content: t('app.typedComponents')},
  {
    value: 'a11y',
    label: t('app.accessibility'),
    icon: Accessibility,
    content: t('app.ariaRoles'),
  },
])
const dropdownOptions = computed(() => [
  {value: 'design', label: t('app.designFirst'), icon: Palette},
  {value: 'code', label: t('app.codeFirst'), icon: Code},
])
const singleItems = computed(() => [
  {
    value: 'design',
    header: t('app.designTokens'),
    icon: Palette,
    content: t('app.designContent'),
  },
  {
    value: 'behavior',
    header: t('app.behavior'),
    icon: MousePointerClick,
    content: t('app.behaviorContent'),
  },
  {value: 'slots', header: t('app.slots'), icon: Blocks, content: t('app.slotsContent')},
])
const multipleItems = computed(() => [
  {value: 'typing', header: t('app.typescript'), icon: Code, content: t('app.typescriptContent')},
  {
    value: 'accessibility',
    header: t('app.accessibility'),
    icon: ShieldCheck,
    content: t('app.accessibilityContent'),
  },
])

function nextOverlayPosition(): void {
  overlayPositionIndex.value = (overlayPositionIndex.value + 1) % overlayPositions.length
}

function toggleTheme(): void {
  activeTheme.value = activeTheme.value === 'light' ? 'dark' : 'light'
  applyBaseTheme(activeTheme.value)
}

function toggleLocale(): void {
  setLocale(locale.value === 'en' ? 'fa' : 'en')
}
</script>
<template>
  <main
    :dir="direction"
    class="mx-auto flex min-h-screen w-full max-w-5xl flex-col gap-page-gap bg-surface-muted p-page-padding"
  >
    <header class="flex flex-wrap items-start justify-between gap-content-gap">
      <div class="space-y-2">
        <h1 class="text-3xl font-semibold text-content">{{ t('app.title') }}</h1>
        <p class="text-sm text-content-muted">{{ t('app.intro') }}</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <BaseButton
          variant="outline"
          :aria-label="locale === 'en' ? t('common.switchToPersian') : t('common.switchToEnglish')"
          @click="toggleLocale"
        >{{ locale === 'en' ? t('common.persian') : t('common.english') }}
        </BaseButton
        >
        <BaseButton
          variant="outline"
          :icon="activeTheme === 'light' ? Moon : Sun"
          :aria-label="activeTheme === 'light' ? t('app.switchToDark') : t('app.switchToLight')"
          @click="toggleTheme"
        >{{ activeTheme === 'light' ? t('app.dark') : t('app.light') }}
        </BaseButton
        >
      </div>
    </header>
    <section class="grid gap-content-gap md:grid-cols-2">
      <BaseCard variant="elevated"
      ><h2 class="mb-content-md text-lg font-semibold text-content">
        {{ t('app.buttonsInput') }}
      </h2>
        <div class="flex flex-wrap items-center gap-3">
          <BaseButton variant="primary" :icon="Save">{{ t('app.primary') }}
          </BaseButton
          >
          <BaseButton variant="outline" size="sm" :icon="Move">{{ t('app.outline') }}
          </BaseButton
          >
          <BaseButton variant="ghost" size="lg" :icon="Sparkles">{{ t('app.ghost') }}
          </BaseButton
          >
          <BaseButton variant="danger" loading>{{ t('app.loading') }}</BaseButton>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3">
          <BaseButton variant="primary" size="sm" :icon="Save">{{ t('app.smSize') }}
          </BaseButton
          >
          <BaseButton variant="primary" :icon="Save">{{ t('app.defaultSize') }}
          </BaseButton
          >
          <BaseButton variant="primary" size="lg" :icon="Save">{{ t('app.lgSize') }}</BaseButton>
        </div>
        <div class="mt-5">
          <BaseInput
            v-model="inputValue"
            :label="t('app.search')"
            :icon="Search"
            :placeholder="t('app.typeClear')"
            :description="t('app.inputDescription')"
            clearable
          />
        </div>
      </BaseCard
      >
      <BaseCard variant="elevated"
      ><h2 class="mb-content-md text-lg font-semibold text-content">{{ t('app.tabsWorm') }}</h2>
        <BaseTab v-model="activeTab" :items="tabs"
        />
      </BaseCard>
    </section>
    <section class="grid gap-content-gap md:grid-cols-2">
      <BaseCard variant="elevated"
      ><h2 class="mb-content-md text-lg font-semibold text-content">
        {{ t('app.tooltips') }}
      </h2>
        <div class="flex flex-wrap items-center gap-3">
          <BaseTooltip :content="t('app.tooltipPrimary')">
            <BaseButton variant="primary">{{ t('app.top') }}</BaseButton>
          </BaseTooltip>
          <BaseTooltip placement="right" :content="t('app.tooltipRight')">
            <BaseButton variant="outline">{{ t('app.right') }}</BaseButton>
          </BaseTooltip>
          <BaseTooltip placement="bottom" :delay="0">
            <BaseButton variant="secondary">{{ t('app.slotContent') }}</BaseButton>
            <template #content>
              <span class="font-medium text-primary">{{ t('app.tooltipSlot') }}</span>
            </template>
          </BaseTooltip>
        </div>
        <p class="mt-4 text-sm text-content-muted">{{ t('app.tooltipDescription') }}</p>
      </BaseCard>
    </section>
    <section class="grid gap-content-gap md:grid-cols-2">
      <BaseCard variant="elevated"
      ><h2 class="mb-content-md text-lg font-semibold text-content">{{ t('app.selection') }}</h2>
        <div class="grid gap-5">
          <BaseDropdown
            v-model="dropdownValue"
            :label="t('app.layout')"
            clearable
            :options="dropdownOptions"
          />
          <div class="grid gap-2">
            <BaseCheckbox
              v-model="checkboxValues"
              value="quality"
              :label="t('app.highQuality')"
            />
            <BaseCheckbox
              v-model="checkboxValues"
              value="accessibility"
              :label="t('app.accessible')"
            />
            <BaseCheckbox v-model="checkboxValues" value="performance" :label="t('app.fast')"/>
          </div>
          <BaseRadio
            v-model="radioValue"
            :label="t('app.density')"
            :options="[
              { value: 'compact', label: t('app.compact') },
              { value: 'comfortable', label: t('app.comfortable') },
            ]"
          />
        </div>
      </BaseCard>
      <BaseCard variant="elevated">
        <h2 class="mb-content-md text-lg font-semibold text-content">
          {{ t('app.textSwitches') }}
        </h2>
        <div class="grid gap-5">
          <BaseTextarea
            v-model="textareaValue"
            :label="t('app.notes')"
            :placeholder="t('app.writeMultiline')"
            clearable
            autosize
          />
          <BaseSwitch
            v-model="switchValue"
            :label="t('app.animations')"
            :description="t('app.animationDescription')"
            size="sm"
          />
          <BaseSwitch
            v-model="switchValue"
            :label="t('app.animations')"
            :description="t('app.animationDescription')"
          />
          <BaseSwitch
            v-model="switchValue"
            :label="t('app.animations')"
            :description="t('app.animationDescription')"
            size="lg"
          />
        </div>
      </BaseCard>
    </section>
    <section class="grid gap-content-gap md:grid-cols-2">
      <BaseCard variant="elevated"
      ><h2 class="mb-content-md text-lg font-semibold text-content">
        {{ t('app.accordionSingle') }}
      </h2>
        <BaseAccordion v-model="singleAccordion" :items="singleItems"/>
      </BaseCard
      >
      <BaseCard variant="elevated"
      ><h2 class="mb-content-md text-lg font-semibold text-content">
        {{ t('app.accordionMultiple') }}
      </h2>
        <BaseAccordion v-model="multipleAccordion" multiple :items="multipleItems"
        />
      </BaseCard>
    </section>
    <BaseCard :title="t('app.overlays')" :description="t('app.overlayDescription')" :icon="PanelTop"
    >
      <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="mt-1 text-sm text-content-muted">
          {{ t('app.currentPosition') }}
          <code class="font-mono text-primary">{{ modalPosition }}</code>
        </p>
        <div class="flex flex-wrap gap-3">
          <BaseButton variant="outline" :icon="Move" @click="nextOverlayPosition">{{
              t('app.changePosition')
            }}
          </BaseButton
          >
          <BaseButton :icon="ExternalLink" @click="isModalVisible = true">{{
              t('app.openModal')
            }}
          </BaseButton
          >
          <BaseButton variant="secondary" :icon="PanelRight" @click="isDrawerVisible = true">{{
              t('app.openDrawer')
            }}
          </BaseButton>
        </div>
      </div>
      <template #footer
      ><span class="text-caption text-content-subtle">{{ t('app.dragHint') }}</span></template
      >
    </BaseCard
    >
    <BaseModal
      v-model="isModalVisible"
      :position="modalPosition"
      :title="t('app.animatedModal')"
      :icon="PanelTop"
      draggable
      :classes="{ title: 'text-primary', content: 'bg-surface-muted' }"
    ><p>{{ t('app.modalContent') }}</p>
      <template #footer
      >
        <BaseButton variant="ghost" size="sm" @click="isModalVisible = false">{{
            t('app.close')
          }}
        </BaseButton>
      </template
      >
    </BaseModal
    >
    <BaseDrawer
      v-model="isDrawerVisible"
      :position="drawerPosition"
      :title="t('app.animatedDrawer')"
      :icon="PanelRight"
      draggable
      :classes="{ content: 'bg-surface-muted' }"
    ><p>{{ t('app.drawerContent') }}</p></BaseDrawer
    >
  </main>
</template>
