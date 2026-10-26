document.addEventListener("DOMContentLoaded", function () {
    function flipArrows(node) {
        if (node.nodeType === Node.TEXT_NODE) {
            node.nodeValue = node.nodeValue
                .replace(/▶/g, 'TEMP_ARROW')
                .replace(/◀/g, '▶')
                .replace(/TEMP_ARROW/g, '◀');
        } else {
            node.childNodes.forEach(flipArrows);
        }
    }
    flipArrows(document.body);

    document.querySelectorAll('input[type="submit"], input[type="button"], input[type="text"]').forEach((el) => {
        el.value = el.value
            .replace(/▶/g, 'TEMP_ARROW')
            .replace(/◀/g, '▶')
            .replace(/TEMP_ARROW/g, '◀');
    });
});
