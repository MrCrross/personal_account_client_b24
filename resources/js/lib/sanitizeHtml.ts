// Безопасный рендер пользовательского HTML (сообщения чата — их пишут клиенты).
// Оставляем только инлайновые теги форматирования из Tiptap и ПОЛНОСТЬЮ вырезаем атрибуты —
// это исключает onerror/onclick, javascript:-ссылки, <script>/<iframe> и прочие XSS-векторы.
// DOMParser('text/html') разбирает разметку без исполнения скриптов и загрузки ресурсов.
const ALLOWED_TAGS = new Set([
    'P', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'S',
    'H1', 'H2', 'H3', 'UL', 'OL', 'LI', 'BLOCKQUOTE', 'CODE', 'PRE',
])

function sanitizeNode(node: Node): Node[] {
    if (node.nodeType === Node.TEXT_NODE) {
        return [node]
    }

    if (node.nodeType !== Node.ELEMENT_NODE) {
        // Комментарии и прочие узлы выкидываем
        return []
    }

    const element = node as Element
    // Сначала чистим потомков (глубина), чтобы не пропустить вложенный опасный тег
    const cleanChildren: Node[] = []
    Array.from(element.childNodes).forEach((child) => {
        cleanChildren.push(...sanitizeNode(child))
    })

    if (ALLOWED_TAGS.has(element.tagName)) {
        Array.from(element.attributes).forEach((attr) => element.removeAttribute(attr.name))
        element.replaceChildren(...cleanChildren)

        return [element]
    }

    // Неразрешённый тег убираем, но сохраняем его (уже очищенное) содержимое
    return cleanChildren
}

export function sanitizeHtml(html: string): string {
    if (!html) {
        return ''
    }

    const doc = new DOMParser().parseFromString(html, 'text/html')
    const container = doc.createElement('div')
    Array.from(doc.body.childNodes).forEach((child) => {
        container.append(...sanitizeNode(child))
    })

    return container.innerHTML
}
