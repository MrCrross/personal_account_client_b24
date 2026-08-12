// utils/bbcodeConverter.ts
export class BBCodeConverter {
    /**
     * HTML → BBCode
     */
    static toBBCode(html: string): string {
        if (!html) return '';

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();

        const processNode = (node: ChildNode): string => {
            if (node.nodeType === Node.TEXT_NODE) {
                return node.textContent || '';
            }

            if (node.nodeType !== Node.ELEMENT_NODE) return '';

            const element = node as HTMLElement;
            const tagName = element.tagName.toLowerCase();

            if (tagName === 'ol' || tagName === 'ul') {
                const wrapTag = tagName === 'ol' ? 'OL' : 'UL';
                return `[${wrapTag}]\n${formatListItems(element, 0)}[/${wrapTag}]\n`;
            }

            const children = Array.from(element.childNodes).map(processNode).join('');

            switch (tagName) {
                // Bitrix не поддерживает теги [H1]/[H2]/[H3] — они остаются
                // видны как сырой текст, поэтому заголовки конвертируем в [B]
                case 'h1':
                case 'h2':
                case 'h3':
                    return `[B]${children}[/B]\n`;
                case 'p':
                    return `${children}\n`;
                case 'strong':
                case 'b':
                    return `[B]${children}[/B]`;
                case 'em':
                case 'i':
                    return `[I]${children}[/I]`;
                case 'li':
                    return `[*]${children}\n`;
                case 'a': {
                    const href = element.getAttribute('href');
                    return href ? `[URL=${href}]${children}[/URL]` : children;
                }
                case 'br':
                    return '\n';
                default:
                    return children;
            }
        };

        // Ни Bitrix, ни наш toHTML не умеют парсить вложенные [UL]/[OL] —
        // вложенный список обрывает закрывающий тег родительского, и остаток
        // описания "протекает" сырым BBCode. Поэтому вложенные списки
        // разворачиваем в один уровень, помечая глубину отступом-дефисом.
        const formatListItems = (list: HTMLElement, depth: number): string => {
            return Array.from(list.children)
                .filter((child) => child.tagName.toLowerCase() === 'li')
                .flatMap((li) => {
                    const nested = Array.from(li.children).find((child) => ['ol', 'ul'].includes(child.tagName.toLowerCase()));

                    const ownText = Array.from(li.childNodes)
                        .filter((child) => child !== nested)
                        .map(processNode)
                        .join('')
                        .trim();
                    const prefix = depth > 0 ? '— '.repeat(depth) : '';
                    const own = `[*]${prefix}${ownText}\n`;

                    return nested ? [own, formatListItems(nested as HTMLElement, depth + 1)] : [own];
                })
                .join('');
        };

        const result = Array.from(tempDiv.childNodes)
            .map(processNode)
            .join('')
            .replace(/\n{3,}/g, '\n\n') // Убираем лишние переносы
            .trim();

        return result;
    }

    /**
     * BBCode → HTML
     */
    static toHTML(bbcode: string): string {
        if (!bbcode) return '';

        let html = bbcode;

        // Bitrix иногда сохраняет скобки нераспознанных тегов ([UL]/[OL]) как HTML-сущности
        // (&#91;UL&#93;), из-за чего они не парсятся и остаются видны как текст
        html = html.replace(/&#91;/gi, '[').replace(/&#93;/gi, ']');

        // Заголовки
        html = html.replace(/\[P\](.*?)\[\/P\]/gis, '<p>$1</p>');
        html = html.replace(/\[H1\](.*?)\[\/H1\]/gis, '<h1>$1</h1>');
        html = html.replace(/\[H2\](.*?)\[\/H2\]/gis, '<h2>$1</h2>');
        html = html.replace(/\[H3\](.*?)\[\/H3\]/gis, '<h3>$1</h3>');

        // Жирный и курсив
        html = html.replace(/\[B\](.*?)\[\/B\]/gis, '<strong>$1</strong>');
        html = html.replace(/\[I\](.*?)\[\/I\]/gis, '<em>$1</em>');

        // Списки, в том числе вложенные (старые описания могли быть сохранены
        // до того, как toBBCode начал разворачивать вложенность в один уровень)
        html = BBCodeConverter.convertLists(html);

        // Ссылки
        html = html.replace(/\[URL=(.*?)\](.*?)\[\/URL\]/gis, '<a href="$1" target="_blank">$2</a>');
        html = html.replace(/\[URL\](.*?)\[\/URL\]/gis, '<a href="$1" target="_blank">$1</a>');

        // Переносы строк
        html = html.replace(/\n/g, '<br>');

        // Оборачиваем в параграф если нет блочных элементов
        if (!html.match(/<(H1|H2|H3|OL|UL|DIV|P)/i) && html.includes('<br>')) {
            html = `<p>${html.replace(/<br>/g, '</p><p>')}</p>`;
        }
        html = html.replace(/<p><\/p>/g, ''); // Убираем пустые параграфы
        html = html.replace(/<br>/g, ''); // Убираем отступы

        return html;
    }

    /**
     * Конвертирует BBCode-списки ([OL]/[UL]/[LIST=1]/[LIST]) в HTML.
     * Каждый паттерн ищет блок, не содержащий внутри себя открывающего тега
     * другого списка (innermost-first), и повторяет проходы, пока такие
     * находятся — иначе нежадный regex обрывается на первом же закрывающем
     * теге вложенного списка, и остаток описания "протекает" сырым BBCode.
     */
    private static convertLists(html: string): string {
        const patterns: { re: RegExp; tag: 'ol' | 'ul' }[] = [
            { re: /\[OL\]((?:(?!\[OL\]|\[UL\]|\[LIST(?:=1)?\]|\[\/OL\])[\s\S])*)\[\/OL\]/gi, tag: 'ol' },
            { re: /\[UL\]((?:(?!\[OL\]|\[UL\]|\[LIST(?:=1)?\]|\[\/UL\])[\s\S])*)\[\/UL\]/gi, tag: 'ul' },
            { re: /\[LIST=1\]((?:(?!\[OL\]|\[UL\]|\[LIST(?:=1)?\]|\[\/LIST\])[\s\S])*)\[\/LIST\]/gi, tag: 'ol' },
            { re: /\[LIST\]((?:(?!\[OL\]|\[UL\]|\[LIST(?:=1)?\]|\[\/LIST\])[\s\S])*)\[\/LIST\]/gi, tag: 'ul' },
        ];

        let changed = true;
        while (changed) {
            changed = false;
            for (const { re, tag } of patterns) {
                html = html.replace(re, (_match: string, content: string) => {
                    changed = true;
                    const items = content.split(/\[\*\]/).filter((item) => item.trim());
                    const listItems = items.map((item) => `<li>${item.trim()}</li>`).join('');
                    return `<${tag}>${listItems}</${tag}>`;
                });
            }
        }

        return html;
    }
}
