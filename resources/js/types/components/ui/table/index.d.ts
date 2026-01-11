export interface TableColumn {
    key: string
    label: string
    sortable?: boolean
    headerClass?: string
    cellClass?: string
    slot?: boolean
    align?: 'left' | 'center' | 'right'
    width?: string
}
