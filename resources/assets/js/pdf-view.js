'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (() => {
    const renderPage = (pageNumber, canvas, pdf) =>
      pdf.getPage(pageNumber).then(page => {
        let viewport = page.getViewport({ scale: 1.5 });
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        page.render({
          canvasContext: canvas.getContext('2d'),
          viewport: viewport
        });
      });

    const showPdf = viewer =>
      pdfjsLib.getDocument(viewer.getAttribute('data-url')).promise.then(pdf => {
        viewer.innerHTML = '';
        for (let page = 1; page <= pdf.numPages; page++) {
          let canvas = document.createElement('canvas');
          viewer.appendChild(canvas);
          renderPage(page, canvas, pdf);
        }
        pdf.getData().then(function (data) {
          let pdfData = new Uint8Array(data);
          let blob = new Blob([pdfData], { type: 'application/pdf' });
          let downloadBtn = document.createElement('a');
          downloadBtn.setAttribute('id', `download-virtual-${viewer.getAttribute('id')}`);
          downloadBtn.setAttribute('class', 'hidden');
          downloadBtn.setAttribute('href', URL.createObjectURL(blob));
          downloadBtn.setAttribute('download', viewer.getAttribute('name'));
          viewer.appendChild(downloadBtn);
        });
      });

    const pdfViewers = document.querySelectorAll('.pdf-viewer');

    if (pdfViewers && typeof pdfViewers !== 'undefined')
      pdfViewers.forEach(pdfViewer => showPdf(pdfViewer));

  })();
});
