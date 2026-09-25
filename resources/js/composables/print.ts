/**
 * Prints a single element through a hidden iframe instead of window.print(),
 * so the app shell (header, sidebar, filters) never leaks into the printout
 * and each report can pick its own page orientation without global @page
 * rules colliding. The page's stylesheets are copied over, so Tailwind and
 * scoped component styles still apply to the cloned markup.
 */
export function usePrintElement() {
    const waitForStylesheets = (doc: Document) =>
        Promise.all(
            Array.from(doc.querySelectorAll<HTMLLinkElement>('link[rel="stylesheet"]')).map(
                (link) =>
                    new Promise<void>((resolve) => {
                        if (link.sheet) return resolve();
                        link.addEventListener("load", () => resolve());
                        link.addEventListener("error", () => resolve());
                    })
            )
        );

    const printElement = async (element: HTMLElement | null, options: { title?: string; orientation?: "portrait" | "landscape" } = {}) => {
        if (!element) return;

        const iframe = document.createElement("iframe");
        iframe.setAttribute("aria-hidden", "true");
        Object.assign(iframe.style, { position: "fixed", right: "0", bottom: "0", width: "0", height: "0", border: "0" });
        document.body.appendChild(iframe);

        const doc = iframe.contentDocument!;
        const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
            .map((node) => node.outerHTML)
            .join("\n");

        doc.open();
        doc.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>${options.title ?? document.title}</title>
<base href="${document.baseURI}">
${styles}
<style>
  @page { size: ${options.orientation ?? "portrait"}; margin: 10mm; }
  html, body { background: #fff !important; margin: 0; }
  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>
</head>
<body>${element.outerHTML}</body>
</html>`);
        doc.close();

        await waitForStylesheets(doc);
        await doc.fonts?.ready;

        const cleanup = () => setTimeout(() => iframe.remove(), 500);
        iframe.contentWindow!.addEventListener("afterprint", cleanup);
        iframe.contentWindow!.focus();
        iframe.contentWindow!.print();
        // Some browsers never fire afterprint for iframes; print() blocks until the dialog closes.
        cleanup();
    };

    return { printElement };
}
