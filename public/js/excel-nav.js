/**
 * Excel-like Keyboard Navigation for Tables
 * Allows navigating input fields inside tables using arrow keys.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('keydown', (e) => {
        // Only trigger on arrow keys
        if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            return;
        }

        const currentInput = e.target;
        
        // Ensure the active element is an input inside a table
        if (currentInput.tagName !== 'INPUT' || !currentInput.closest('.table-container table')) {
            return;
        }

        // Optional: only trigger for number or text inputs
        if (currentInput.type !== 'number' && currentInput.type !== 'text') {
            return;
        }

        const currentTd = currentInput.closest('td');
        const currentTr = currentInput.closest('tr');
        const table = currentInput.closest('table');

        if (!currentTd || !currentTr || !table) return;

        let targetRowIndex = currentTr.rowIndex;
        let targetCellIndex = currentTd.cellIndex;
        let nextInput = null;

        // Determine direction
        const rows = Array.from(table.rows);

        while (!nextInput) {
            if (e.key === 'ArrowUp') {
                targetRowIndex--;
            } else if (e.key === 'ArrowDown') {
                targetRowIndex++;
            } else if (e.key === 'ArrowLeft') {
                targetCellIndex--;
            } else if (e.key === 'ArrowRight') {
                targetCellIndex++;
            }

            // Check boundaries
            if (targetRowIndex < 0 || targetRowIndex >= rows.length) break;
            
            const targetRow = rows[targetRowIndex];
            if (!targetRow) break;

            if (targetCellIndex < 0 || targetCellIndex >= targetRow.cells.length) {
                // For left/right, if we hit the edge, maybe wrap to next/prev row?
                // Standard Excel doesn't wrap on arrow keys, so we just break.
                break;
            }

            const targetCell = targetRow.cells[targetCellIndex];
            if (!targetCell) break;

            // Find an input in the target cell that is not disabled, not readonly, and not tabindex="-1"
            const inputsInCell = Array.from(targetCell.querySelectorAll('input'));
            nextInput = inputsInCell.find(inp => 
                !inp.disabled && 
                !inp.readOnly && 
                inp.tabIndex !== -1 && 
                (inp.type === 'number' || inp.type === 'text')
            );
        }

        if (nextInput) {
            e.preventDefault(); // Prevent default scrolling or number incrementing
            
            // For number inputs, if we want to immediately select all text (like Excel)
            nextInput.focus();
            
            // select() works well on text inputs, but can sometimes fail on number inputs in certain browsers.
            // Using a try-catch just in case.
            try {
                nextInput.select();
            } catch (err) {}
        } else if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            // Even if we couldn't move to another input, prevent default for Up/Down on number inputs
            // so we don't accidentally change the value when the user meant to navigate but hit the top/bottom edge.
            if (currentInput.type === 'number') {
                e.preventDefault();
            }
        }
    });
});
