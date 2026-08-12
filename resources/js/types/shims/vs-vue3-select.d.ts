// У пакета vs-vue3-select есть index.d.ts, но он не объявлен в package.json#exports,
// поэтому moduleResolution: "bundler" его не находит. Делаем минимальный shim,
// которого достаточно для импортов в app.ts и BaseSelect.vue.
declare module 'vs-vue3-select' {
    import type { Component } from 'vue'

    const VsSelect: Component
    export default VsSelect

    export type VSelectProps = Record<string, unknown>
    export type VSelectEvents = Record<string, unknown>
    export type VSelectSlots = Record<string, unknown>
}

declare module 'vs-vue3-select/dist/vs-vue3-select.css'
