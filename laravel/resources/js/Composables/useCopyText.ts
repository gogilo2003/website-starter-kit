// useCopyText.js
import { ref } from 'vue';

export default function useCopyText() {
    const copiedText = ref('');

    async function copyText(text: string) {
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(text);
                copiedText.value = text;
                console.log('Text copied to clipboard using navigator.clipboard.writeText:', text);
            } else if (document.execCommand) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                copiedText.value = text;
                console.log('Text copied to clipboard using document.execCommand:', text);
                document.body.removeChild(textarea);
            } else {
                throw new Error('Clipboard API and execCommand are not supported.');
            }
        } catch (error) {
            console.error('Failed to copy text:', error);
        }
    }

    return { copiedText, copyText };
}
