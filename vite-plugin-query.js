import { Plugin } from 'vite';

export default function jQueryPlugin() {
  return {
    name: 'vite-plugin-jquery',
    transformIndexHtml(html) {
      return html.replace(
        '</head>',
        `
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        </head>`
      );
    },
  };
}