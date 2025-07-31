'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (() => {
    const renderPage = (pageNumber, canvas, pdf) => {
      pdf.getPage(pageNumber).then(page => {
        let viewport = page.getViewport({ scale: 1.5 });
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        page.render({
          canvasContext: canvas.getContext('2d'),
          viewport: viewport
        });
      });
    };

    const showPdf = viewer => {
      pdfjsLib.getDocument(viewer.getAttribute('data-get-url')).promise.then(pdf => {
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
          downloadBtn.setAttribute('class', 'pdf-download btn btn-success');
          downloadBtn.innerText = 'Download';
          downloadBtn.setAttribute('href', URL.createObjectURL(blob));
          downloadBtn.setAttribute('download', viewer.getAttribute('data-name'));
          viewer.prepend(downloadBtn);
        });
      });
    };

    const checkPdf = async viewer => {
      const res = await fetch(viewer.getAttribute('data-check-url'), {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json'
        }
      }).then(res => {
        return res.json();
      });

      if (res['error']) viewer.innerHTML = 'Xatolik yuz berdi. Iltimos, sahifani yangilang';

      if (res['endConvert']) {
        showPdf(viewer);
      } else {
        setTimeout(() => checkPdf(viewer), 2000);
      }
    };

    const pdfViewers = document.querySelectorAll('.pdf-viewer');

    if (pdfViewers && typeof pdfViewers !== 'undefined') {
      pdfViewers.forEach(pdfViewer => checkPdf(pdfViewer));
    }
  })();
});
