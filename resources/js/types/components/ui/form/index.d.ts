export type FieldType =
    | 'text'
    | 'email'
    | 'password'
    | 'number'
    | 'phone'
    | 'textarea'
    | 'select'
    | 'autocomplete'
    | 'checkbox'
    | 'file'
    | 'editor'
    | 'custom'

export interface FormFieldConfig {
    name: string
    label: string
    type: FieldType
    required?: boolean
    placeholder?: string
    disabled?: boolean
    // Для select/autocomplete
    options?: any[]
    apiFeature?: any
    // Для file
    multiple?: boolean
    maxFiles?: number
    maxSize?: number
    // Валидация
    pattern?: string
    min?: number
    max?: number
    // Условное отображение
    showIf?: (formData: any) => boolean
    // Кастомный компонент
    component?: any
    // Дополнительные props для компонента
    props?: Record<string, any>
}
