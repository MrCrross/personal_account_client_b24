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

            if (node.nodeType === Node.ELEMENT_NODE) {
                const element = node as HTMLElement;
                const tagName = element.tagName.toLowerCase();
                const children = Array.from(element.childNodes)
                    .map(processNode)
                    .join('');

                switch (tagName) {
                    case 'h1':
                        return `[H1]${children}[/H1]\n`;
                    case 'h2':
                        return `[H2]${children}[/H2]\n`;
                    case 'h3':
                        return `[H3]${children}[/H3]\n`;
                    case 'p':
                        return `${children}\n`;
                    case 'strong':
                    case 'b':
                        return `[B]${children}[/B]`;
                    case 'em':
                    case 'i':
                        return `[I]${children}[/I]`;
                    case 'ol':
                        return `[OL]\n${children}[/OL]\n`;
                    case 'ul':
                        return `[UL]\n${children}[/UL]\n`;
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
            }

            return '';
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

        // Заголовки
        html = html.replace(/\[P\](.*?)\[\/P\]/gis, '<p>$1</p>');
        html = html.replace(/\[H1\](.*?)\[\/H1\]/gis, '<h1>$1</h1>');
        html = html.replace(/\[H2\](.*?)\[\/H2\]/gis, '<h2>$1</h2>');
        html = html.replace(/\[H3\](.*?)\[\/H3\]/gis, '<h3>$1</h3>');

        // Жирный и курсив
        html = html.replace(/\[B\](.*?)\[\/B\]/gis, '<strong>$1</strong>');
        html = html.replace(/\[I\](.*?)\[\/I\]/gis, '<em>$1</em>');

        // Списки
        html = html.replace(/\[OL\](.*?)\[\/OL\]/gis, (match: string, content: string) => {
            const items = content.split(/\[\*\]/).filter(item => item.trim());
            const listItems = items.map(item => `<li>${item.trim()}</li>`).join('');
            return `<ol>${listItems}</ol>`;
        });

        html = html.replace(/\[UL\](.*?)\[\/UL\]/gis, (match: string, content: string) => {
            const items = content.split(/\[\*\]/).filter(item => item.trim());
            const listItems = items.map(item => `<li>${item.trim()}</li>`).join('');
            return `<ul>${listItems}</ul>`;
        });

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
}
