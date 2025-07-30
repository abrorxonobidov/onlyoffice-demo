import { PDFWorker } from 'pdfjs-dist/build/pdf.mjs';

try {
  window.PDFWorker = PDFWorker;
} catch (e) {}

export { PDFWorker };
