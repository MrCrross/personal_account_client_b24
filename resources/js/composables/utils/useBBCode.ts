import { ref, computed, type Ref } from 'vue';
import {BBCodeConverter} from "@/lib/bbCodeConverter";

interface UseBBCodeReturn {
    htmlContent: Ref<string>;
    bbcodeContent: Ref<string>;
    convertToBBCode: (html: string) => string;
    convertToHTML: (bbcode: string) => string;
    bbcodeFromHtml: Readonly<Ref<string>>;
    htmlFromBBCode: Readonly<Ref<string>>;
}

export function useBBCode(): UseBBCodeReturn {
    const htmlContent = ref<string>('');
    const bbcodeContent = ref<string>('');

    // HTML → BBCode
    const convertToBBCode = (html: string): string => {
        return BBCodeConverter.toBBCode(html);
    };

    // BBCode → HTML
    const convertToHTML = (bbcode: string): string => {
        return BBCodeConverter.toHTML(bbcode);
    };

    // Реактивные преобразования
    const bbcodeFromHtml = computed(() => convertToBBCode(htmlContent.value));
    const htmlFromBBCode = computed(() => convertToHTML(bbcodeContent.value));

    return {
        htmlContent,
        bbcodeContent,
        convertToBBCode,
        convertToHTML,
        bbcodeFromHtml,
        htmlFromBBCode
    };
}
