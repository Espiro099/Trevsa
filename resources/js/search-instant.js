/**
 * Búsqueda instantánea para tablas
 * Filtra filas de tabla en tiempo real mientras el usuario escribe
 */
document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los inputs de búsqueda con atributo data-search-table
    const searchInputs = document.querySelectorAll('[data-search-table]');
    
    searchInputs.forEach(input => {
        const tableId = input.getAttribute('data-search-table');
        const table = document.getElementById(tableId);
        
        if (!table) return;
        
        // Agregar evento de input para búsqueda instantánea
        input.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                
                row.style.display = isVisible ? '' : 'none';
                
                if (isVisible) {
                    visibleCount++;
                    // Agregar animación de entrada
                    row.style.animation = 'fade-in 0.2s ease-out';
                }
            });
            
            // Mostrar mensaje si no hay resultados
            let noResultsMsg = table.parentElement.querySelector('.no-results-message');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results-message text-center py-8 text-brand-gray-500';
                    noResultsMsg.innerHTML = `
                        <svg class="w-12 h-12 mx-auto mb-3 text-brand-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-medium">No se encontraron resultados</p>
                        <p class="text-sm mt-1">Intenta con otros términos de búsqueda</p>
                    `;
                    table.parentElement.appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = 'block';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        });
    });
});

/**
 * Filtrado avanzado con múltiples campos
 */
function initAdvancedFilter(filterContainer) {
    const filterInputs = filterContainer.querySelectorAll('[data-filter]');
    const tableId = filterContainer.getAttribute('data-filter-table');
    const table = document.getElementById(tableId);
    
    if (!table) return;
    
    function applyFilters() {
        const rows = table.querySelectorAll('tbody tr');
        const filters = {};
        
        filterInputs.forEach(input => {
            const filterKey = input.getAttribute('data-filter');
            const filterValue = input.value.toLowerCase().trim();
            if (filterValue) {
                filters[filterKey] = filterValue;
            }
        });
        
        rows.forEach(row => {
            let matches = true;
            
            for (const [key, value] of Object.entries(filters)) {
                const cell = row.querySelector(`[data-filter-key="${key}"]`);
                if (!cell || !cell.textContent.toLowerCase().includes(value)) {
                    matches = false;
                    break;
                }
            }
            
            row.style.display = matches ? '' : 'none';
            if (matches) {
                row.style.animation = 'fade-in 0.2s ease-out';
            }
        });
    }
    
    filterInputs.forEach(input => {
        input.addEventListener('input', applyFilters);
        input.addEventListener('change', applyFilters);
    });
}

// Inicializar filtros avanzados
document.addEventListener('DOMContentLoaded', function() {
    const filterContainers = document.querySelectorAll('[data-filter-table]');
    filterContainers.forEach(container => {
        initAdvancedFilter(container);
    });
});

