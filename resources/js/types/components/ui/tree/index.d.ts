export interface PanelMenuItemType {
    id?: string
    label: string
    icon?: any // Любой компонент иконки
    href?: string
    badge?: string | number
    children?: PanelMenuItemType[]
    expanded?: boolean
    disabled?: boolean
    target?: string
    only?: string[]
    method?: string
}
