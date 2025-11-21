/**
 * Convierte tablas con clase .responsive-table en tarjetas móviles.
 */
document.addEventListener('DOMContentLoaded', () => {
    const tables = document.querySelectorAll('table.responsive-table');

    tables.forEach((table) => {
        try {
            const headers = Array.from(
                table.querySelectorAll('thead th')
            ).map((header) => header.textContent.trim());

            if (!headers.length) {
                return;
            }

            const rows = table.querySelectorAll('tbody tr');
            rows.forEach((row) => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (!cell.hasAttribute('data-label')) {
                        const label = headers[index] || '';
                        cell.setAttribute('data-label', label);
                    }
                });
            });
        } catch (error) {
            console.warn('ResponsiveTable: no se pudo procesar la tabla.', error);
        }
    });
});

