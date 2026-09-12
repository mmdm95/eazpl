import { computed, ref, watch, type ComputedRef, type Ref } from 'vue'

export type Locale = 'en' | 'fa'

const messages = {
  en: {
    common: {
      language: 'Language',
      english: 'English',
      persian: 'Persian',
      switchToEnglish: 'Switch to English',
      switchToPersian: 'Switch to Persian',
      clearInput: 'Clear input',
      clearTextarea: 'Clear textarea',
      clearOption: 'Clear selected option',
      closeModal: 'Close modal',
      closeDrawer: 'Close drawer',
      closeAlert: 'Close alert',
      dragToClose: 'Drag to close',
      selectOption: 'Select an option',
    },
    app: {
      title: 'Base component library',
      intro:
        'Typed, Tailwind-styled primitives with slots, animations, and element-level class overrides.',
      dark: 'Dark',
      light: 'Light',
      switchToDark: 'Switch to dark theme',
      switchToLight: 'Switch to light theme',
      buttonsInput: 'Buttons and input',
      primary: 'Primary',
      outline: 'Outline',
      ghost: 'Ghost',
      loading: 'Loading',
      smSize: 'Sm size',
      defaultSize: 'Default size',
      lgSize: 'Lg size',
      search: 'Search',
      typeClear: 'Type and clear me',
      inputDescription: 'Supports icons, validation, loading, and clearable states.',
      tabsWorm: 'Tabs with moving worm',
      tooltips: 'Tooltips',
      top: 'Top',
      right: 'Right',
      slotContent: 'Slot content',
      tooltipPrimary: 'Appears above the trigger.',
      tooltipRight: 'Appears on the right side.',
      tooltipSlot: 'Rich content through the content slot.',
      tooltipDescription: 'Hover or focus a trigger to see delayed, accessible tooltips.',
      overview: 'Overview',
      api: 'API',
      accessibility: 'Accessibility',
      worm: 'The worm measures the active tab and follows it.',
      typedComponents: 'Each component exposes typed props, emits, slots, and class hooks.',
      ariaRoles: 'Tabs, panels, modals, drawers, and accordions use ARIA roles.',
      selection: 'Selection controls',
      layout: 'Layout',
      designFirst: 'Design-first',
      codeFirst: 'Code-first',
      highQuality: 'High quality',
      accessible: 'Accessible',
      fast: 'Fast',
      density: 'Density',
      compact: 'Compact',
      comfortable: 'Comfortable',
      textSwitches: 'Text and switches',
      notes: 'Notes',
      writeMultiline: 'Write multi-line content',
      animations: 'Enable polished animations',
      animationDescription: 'Controls are animated with Tailwind transitions.',
      accordionSingle: 'Accordion: single open',
      designTokens: 'Design tokens',
      designContent: 'Variants, sizes, and class overrides stay consistent.',
      behavior: 'Behavior',
      behaviorContent: 'One panel can stay open at a time.',
      slots: 'Slots',
      slotsContent: 'Each content panel has a dynamic keyed slot.',
      accordionMultiple: 'Accordion: multiple open',
      typescript: 'TypeScript',
      typescriptContent: 'Model values are narrowed by the multiple prop.',
      accessibilityContent: 'Headers and regions are connected with ARIA attributes.',
      overlays: 'Overlays',
      overlayDescription: 'Modal and drawer share a typed position API.',
      currentPosition: 'Current position:',
      changePosition: 'Change position',
      openModal: 'Open modal',
      openDrawer: 'Open drawer',
      dragHint: 'Drag the modal header, or drag the drawer indicator to close it.',
      animatedModal: 'Animated modal',
      modalContent: 'This modal supports every edge, corner, and full-edge position.',
      close: 'Close',
      animatedDrawer: 'Animated drawer',
      drawerContent: 'This drawer shares the same typed position API as the modal.',
    },
  },
  fa: {
    common: {
      language: 'زبان',
      english: 'انگلیسی',
      persian: 'فارسی',
      switchToEnglish: 'تغییر به انگلیسی',
      switchToPersian: 'تغییر به فارسی',
      clearInput: 'پاک کردن ورودی',
      clearTextarea: 'پاک کردن متن',
      clearOption: 'پاک کردن گزینه انتخاب‌شده',
      closeModal: 'بستن پنجره',
      closeDrawer: 'بستن کشو',
      closeAlert: 'بستن هشدار',
      dragToClose: 'برای بستن بکشید',
      selectOption: 'یک گزینه انتخاب کنید',
    },
    app: {
      title: 'کتابخانه کامپوننت‌های پایه',
      intro: 'کامپوننت‌های تایپ‌شده با Tailwind، اسلات، انیمیشن و امکان بازنویسی کلاس‌ها.',
      dark: 'تیره',
      light: 'روشن',
      switchToDark: 'تغییر به پوسته تیره',
      switchToLight: 'تغییر به پوسته روشن',
      buttonsInput: 'دکمه‌ها و ورودی',
      primary: 'اصلی',
      outline: 'خطی',
      ghost: 'شبح',
      loading: 'در حال بارگذاری',
      smSize: 'اندازه کوچک',
      defaultSize: 'اندازه پیش‌فرض',
      lgSize: 'اندازه بزرگ',
      search: 'جست‌وجو',
      typeClear: 'برای پاک‌کردن تایپ کنید',
      inputDescription: 'پشتیبانی از آیکون، اعتبارسنجی، بارگذاری و پاک‌سازی.',
      tabsWorm: 'تب‌ها با حرکت دنبال‌کننده',
      tooltips: 'راهنماها',
      top: 'بالا',
      right: 'راست',
      slotContent: 'محتوای اسلات',
      tooltipPrimary: 'در بالای راه‌انداز نمایش داده می‌شود.',
      tooltipRight: 'در سمت راست راه‌انداز نمایش داده می‌شود.',
      tooltipSlot: 'محتوای غنی از طریق اسلات محتوا.',
      tooltipDescription:
        'برای دیدن راهنماهای تأخیری و دسترس‌پذیر، روی راه‌انداز شوید یا آن را فعال کنید.',
      overview: 'نمای کلی',
      api: 'API',
      accessibility: 'دسترس‌پذیری',
      worm: 'دنبال‌کننده اندازه تب فعال را می‌سنجد و آن را دنبال می‌کند.',
      typedComponents: 'هر کامپوننت پراپ‌ها، رویدادها، اسلات‌ها و نقاط اتصال کلاس تایپ‌شده دارد.',
      ariaRoles: 'تب‌ها، پنل‌ها، مودال‌ها، کشوها و آکاردئون‌ها از نقش‌های ARIA استفاده می‌کنند.',
      selection: 'کنترل‌های انتخاب',
      layout: 'چیدمان',
      designFirst: 'ابتدا طراحی',
      codeFirst: 'ابتدا کد',
      highQuality: 'کیفیت بالا',
      accessible: 'دسترس‌پذیر',
      fast: 'سریع',
      density: 'تراکم',
      compact: 'فشرده',
      comfortable: 'راحت',
      textSwitches: 'متن و کلیدها',
      notes: 'یادداشت‌ها',
      writeMultiline: 'متن چندخطی بنویسید',
      animations: 'فعال‌سازی انیمیشن‌های زیبا',
      animationDescription: 'کنترل‌ها با انتقال‌های Tailwind متحرک می‌شوند.',
      accordionSingle: 'آکاردئون: یک مورد باز',
      designTokens: 'توکن‌های طراحی',
      designContent: 'گونه‌ها، اندازه‌ها و بازنویسی کلاس‌ها هماهنگ می‌مانند.',
      behavior: 'رفتار',
      behaviorContent: 'در هر لحظه فقط یک پنل باز می‌ماند.',
      slots: 'اسلات‌ها',
      slotsContent: 'هر پنل محتوا یک اسلات پویا با کلید اختصاصی دارد.',
      accordionMultiple: 'آکاردئون: چند مورد باز',
      typescript: 'TypeScript',
      typescriptContent: 'نوع مقادیر مدل با پراپ multiple محدود می‌شود.',
      accessibilityContent: 'سربرگ‌ها و ناحیه‌ها با ویژگی‌های ARIA به هم متصل هستند.',
      overlays: 'لایه‌ها',
      overlayDescription: 'مودال و کشو از API تایپ‌شده موقعیت مشترک استفاده می‌کنند.',
      currentPosition: 'موقعیت فعلی:',
      changePosition: 'تغییر موقعیت',
      openModal: 'باز کردن مودال',
      openDrawer: 'باز کردن کشو',
      dragHint: 'برای بستن، سربرگ مودال یا نشانگر کشو را بکشید.',
      animatedModal: 'مودال متحرک',
      modalContent: 'این مودال از همه لبه‌ها، گوشه‌ها و حالت تمام‌صفحه پشتیبانی می‌کند.',
      close: 'بستن',
      animatedDrawer: 'کشوی متحرک',
      drawerContent: 'این کشو همان API تایپ‌شده موقعیت مودال را به اشتراک می‌گذارد.',
    },
  },
} as const

export const locale: Ref<Locale> = ref('en')
export const direction: ComputedRef<'ltr' | 'rtl'> = computed(() =>
  locale.value === 'fa' ? 'rtl' : 'ltr',
)

export function setLocale(value: Locale): void {
  locale.value = value
  if (typeof window !== 'undefined') window.localStorage.setItem('eazpl-locale', value)
}

export function t(key: string): string {
  const value = key
    .split('.')
    .reduce<unknown>(
      (current, part) => (current as Record<string, unknown>)?.[part],
      messages[locale.value],
    )
  return typeof value === 'string' ? value : key
}

export function useI18n() {
  if (typeof window !== 'undefined') {
    const saved = window.localStorage.getItem('eazpl-locale')
    if (saved === 'en' || saved === 'fa') locale.value = saved
  }
  watch(
    direction,
    (dir) => {
      if (typeof document !== 'undefined') {
        document.documentElement.lang = locale.value
        document.documentElement.dir = dir
      }
    },
    { immediate: true },
  )
  return { locale, direction, t, setLocale }
}
