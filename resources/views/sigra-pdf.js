/* ============================================================
   SIGRA — Generación de PDFs e impresión (jsPDF + autoTable)
   Requiere que jsPDF y jspdf-autotable estén cargados antes.
   ============================================================ */
(function () {
  'use strict';

  function getJsPDF() {
    if (window.jspdf && window.jspdf.jsPDF) return window.jspdf.jsPDF;
    if (window.jsPDF) return window.jsPDF;
    console.error('jsPDF no está cargado');
    return null;
  }

  function getConfig() {
    return (window.SIGRA_DATA && window.SIGRA_DATA.getConfig()) || {
      restaurante: { nombre: 'Restaurante La Antigua', direccion: '', telefono: '', nit: '', eslogan: '' },
      impuestos: { iva: 12, moneda: 'Q' },
    };
  }

  /* ── Encabezado común (logo + datos del restaurante) ── */
  function drawHeader(doc, cfg, titulo) {
    const r = cfg.restaurante || {};
    /* Banda navy superior */
    doc.setFillColor(26, 46, 74); // navy
    doc.rect(0, 0, 210, 28, 'F');

    /* Cuadro ámbar (logo) */
    doc.setFillColor(232, 160, 32); // amber
    doc.roundedRect(14, 8, 14, 14, 2, 2, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(14);
    doc.text('LA', 21, 17, { align: 'center' });

    /* Nombre + eslogan */
    doc.setFontSize(15);
    doc.text(r.nombre || 'Restaurante La Antigua', 32, 14);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.text(r.eslogan || '', 32, 19);
    doc.text(r.direccion || '', 32, 23);

    /* Título derecha */
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(14);
    doc.text(titulo, 196, 17, { align: 'right' });

    doc.setTextColor(40, 40, 40);
  }

  function drawFooter(doc, cfg) {
    const r = cfg.restaurante || {};
    const pageH = doc.internal.pageSize.getHeight();
    doc.setDrawColor(220, 220, 220);
    doc.line(14, pageH - 18, 196, pageH - 18);
    doc.setFont('helvetica', 'italic');
    doc.setFontSize(9);
    doc.setTextColor(120, 120, 120);
    doc.text(r.eslogan || '', 105, pageH - 12, { align: 'center' });
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8);
    doc.text(`${r.nombre || ''} · ${r.telefono || ''} · NIT ${r.nit || ''}`, 105, pageH - 7, { align: 'center' });
  }

  /* ============================================================
     FACTURA
     ============================================================ */
  function buildFacturaDoc(factura) {
    const jsPDFClass = getJsPDF();
    if (!jsPDFClass) return null;
    const cfg = getConfig();
    const doc = new jsPDFClass({ unit: 'mm', format: 'a4' });

    drawHeader(doc, cfg, factura.tipo === 'CREDITO' ? 'FACTURA · CRÉDITO' : 'FACTURA ELECTRÓNICA');

    /* Bloque: datos receptor + datos factura */
    let y = 38;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.text('Receptor', 14, y);
    doc.text('Documento', 120, y);
    y += 5;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text(factura.nombre, 14, y);
    doc.text(`Pedido: ${factura.pedidoId}`, 120, y);
    y += 5;
    doc.text(`NIT: ${factura.nit}`, 14, y);
    doc.text(`Fecha: ${factura.fecha} ${factura.hora}`, 120, y);
    y += 5;
    if (factura.ubicacion) doc.text(`Origen: ${factura.ubicacion}`, 14, y);
    doc.text(`Tipo: ${factura.tipo}`, 120, y);
    y += 5;
    doc.setFontSize(8);
    doc.setTextColor(120, 120, 120);
    doc.text('UUID SAT:', 14, y);
    doc.setFont('courier', 'normal');
    doc.text(factura.id, 30, y);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(40, 40, 40);

    /* Tabla de items */
    const body = factura.items.map(i => [
      `${i.cantidad}`,
      `${i.nombre}${i.notas ? `\n${i.notas}` : ''}`,
      `Q ${i.precio.toFixed(2)}`,
      `Q ${(i.precio * i.cantidad).toFixed(2)}`,
    ]);

    doc.autoTable({
      startY: y + 6,
      head: [['Cant.', 'Descripción', 'P. Unit.', 'Subtotal']],
      body,
      theme: 'grid',
      headStyles: { fillColor: [26, 46, 74], textColor: 255, fontSize: 10, halign: 'left' },
      bodyStyles:  { fontSize: 10, cellPadding: 3 },
      columnStyles: {
        0: { halign: 'center', cellWidth: 16 },
        2: { halign: 'right',  cellWidth: 26 },
        3: { halign: 'right',  cellWidth: 30 },
      },
      margin: { left: 14, right: 14 },
    });

    /* Totales */
    let endY = doc.lastAutoTable.finalY + 6;

    doc.setFontSize(10);
    doc.setDrawColor(220, 220, 220);
    doc.line(120, endY, 196, endY);
    endY += 6;
    doc.setFillColor(26, 46, 74);
    doc.rect(120, endY - 4, 76, 9, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.text('TOTAL:', 130, endY + 2);
    doc.text(`Q ${factura.total.toFixed(2)}`, 194, endY + 2, { align: 'right' });
    doc.setTextColor(40, 40, 40);
    doc.setFont('helvetica', 'normal');

    /* Nota de pago */
    endY += 16;
    doc.setFontSize(9);
    doc.setTextColor(80, 80, 80);
    if (factura.tipo === 'CREDITO') {
      doc.text('Esta factura fue emitida bajo convenio de crédito empresarial.', 14, endY);
      if (factura.ref) doc.text(`Referencia: ${factura.ref}`, 14, endY + 5);
    } else {
      doc.text('Gracias por su preferencia. Para reclamos conservar este documento.', 14, endY);
    }
    if (factura.offline) {
      doc.setTextColor(180, 60, 60);
      doc.text('*** Emitida en modo contingencia — pendiente de transmisión a SAT ***', 14, endY + 10);
    }

    drawFooter(doc, cfg);
    return doc;
  }

  function descargarFactura(factura) {
    const doc = buildFacturaDoc(factura);
    if (!doc) return;
    const filename = `Factura_${factura.pedidoId}_${factura.id.slice(0, 8)}.pdf`;
    doc.save(filename);
  }

  function imprimirFactura(factura) {
    const doc = buildFacturaDoc(factura);
    if (!doc) return;
    doc.autoPrint();
    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    const w = window.open(url);
    if (!w) { window.location.href = url; }
  }

  /* ============================================================
     PLANILLA DE NÓMINA
     ============================================================ */
  function buildPlanillaDoc(periodo, empleados, totales) {
    const jsPDFClass = getJsPDF();
    if (!jsPDFClass) return null;
    const cfg = getConfig();
    const doc = new jsPDFClass({ unit: 'mm', format: 'a4' });

    drawHeader(doc, cfg, 'PLANILLA DE NÓMINA');

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.text(`Período: ${periodo}`, 14, 38);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.text(`Generado: ${new Date().toLocaleString('es-GT')}`, 14, 43);
    doc.text(`${empleados.length} empleados`, 14, 48);

    const body = empleados.map(e => [
      e.nombre,
      e.puesto,
      `Q ${e.salarioBruto.toFixed(2)}`,
      `Q ${e.igss.toFixed(2)}`,
      e.isr > 0 ? `Q ${e.isr.toFixed(2)}` : '—',
      `Q ${(e.salarioBruto - e.igss - e.isr).toFixed(2)}`,
    ]);
    body.push([
      { content: 'TOTALES', colSpan: 2, styles: { fontStyle: 'bold', halign: 'right' } },
      { content: `Q ${totales.bruto.toFixed(2)}`, styles: { fontStyle: 'bold', halign: 'right' } },
      { content: `Q ${totales.igss.toFixed(2)}`,  styles: { fontStyle: 'bold', halign: 'right' } },
      { content: `Q ${totales.isr.toFixed(2)}`,   styles: { fontStyle: 'bold', halign: 'right' } },
      { content: `Q ${totales.neto.toFixed(2)}`,  styles: { fontStyle: 'bold', halign: 'right', fillColor: [232, 245, 233], textColor: [27, 94, 32] } },
    ]);

    doc.autoTable({
      startY: 54,
      head: [['Empleado', 'Puesto', 'S. Bruto', 'IGSS 4.83%', 'ISR', 'Neto']],
      body,
      theme: 'striped',
      headStyles: { fillColor: [26, 46, 74], textColor: 255, fontSize: 9 },
      bodyStyles:  { fontSize: 9, cellPadding: 2.4 },
      columnStyles: {
        0: { cellWidth: 50 },
        1: { cellWidth: 22 },
        2: { halign: 'right' },
        3: { halign: 'right' },
        4: { halign: 'right' },
        5: { halign: 'right', fontStyle: 'bold' },
      },
      margin: { left: 14, right: 14 },
    });

    let y = doc.lastAutoTable.finalY + 12;
    doc.setFontSize(9);
    doc.setTextColor(80, 80, 80);
    doc.text('IGSS (4.83%) calculado sobre salario bruto. ISR según escala SAT.', 14, y);
    y += 5;
    doc.text('Esta planilla cumple con el Código de Trabajo de Guatemala.', 14, y);

    /* Firmas */
    y += 30;
    doc.line(30, y, 90, y);
    doc.line(120, y, 180, y);
    y += 5;
    doc.text('Elaborado por', 60, y, { align: 'center' });
    doc.text('Autorizado por', 150, y, { align: 'center' });

    drawFooter(doc, cfg);
    return doc;
  }

  function descargarPlanilla(periodo, empleados, totales) {
    const doc = buildPlanillaDoc(periodo, empleados, totales);
    if (!doc) return;
    doc.save(`Planilla_${periodo.replace(/\s+/g, '_')}.pdf`);
  }

  function imprimirPlanilla(periodo, empleados, totales) {
    const doc = buildPlanillaDoc(periodo, empleados, totales);
    if (!doc) return;
    doc.autoPrint();
    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    const w = window.open(url);
    if (!w) { window.location.href = url; }
  }

  /* ============================================================
     REPORTE GENÉRICO
     ============================================================ */
  function descargarReporte(titulo, head, body, opts) {
    const jsPDFClass = getJsPDF();
    if (!jsPDFClass) return null;
    const cfg = getConfig();
    const doc = new jsPDFClass({ unit: 'mm', format: 'a4', orientation: (opts && opts.landscape) ? 'landscape' : 'portrait' });

    drawHeader(doc, cfg, 'REPORTE');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.text(titulo, 14, 38);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.text(`Generado: ${new Date().toLocaleString('es-GT')}`, 14, 43);
    if (opts && opts.subtitulo) doc.text(opts.subtitulo, 14, 48);

    doc.autoTable({
      startY: 54,
      head: [head],
      body,
      theme: 'striped',
      headStyles: { fillColor: [26, 46, 74], textColor: 255, fontSize: 9 },
      bodyStyles:  { fontSize: 9, cellPadding: 2.4 },
      margin: { left: 14, right: 14 },
    });

    drawFooter(doc, cfg);
    const filename = (opts && opts.filename) || `${titulo.replace(/\s+/g, '_')}.pdf`;
    doc.save(filename);
  }

  window.SIGRA_PDF = {
    descargarFactura, imprimirFactura,
    descargarPlanilla, imprimirPlanilla,
    descargarReporte,
  };
})();
