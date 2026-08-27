function exportCSV() {
    let csv = "Category,Type,Amount\n";
    
    // Add income data from the table
    const incomeRows = document.querySelectorAll('.report-section:first-child table tbody tr');
    incomeRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 2) {
            const category = cells[0].textContent.trim();
            const amount = cells[1].textContent.trim().replace('E', '').replace(/,/g, '');
            csv += `"${category}","Income",${amount}\n`;
        }
    });
    
    // Add expense data from the second table
    const expenseRows = document.querySelectorAll('.report-section:nth-child(2) table tbody tr');
    expenseRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 2) {
            const category = cells[0].textContent.trim();
            const amount = cells[1].textContent.trim().replace('E', '').replace(/,/g, '');
            csv += `"${category}","Expense",${amount}\n`;
        }
    });
    
    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `management_report_<?php echo date('Y-m-d'); ?>.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function getPaymentIcon(method) {
    const icons = {
        'cash': 'money-bill-wave',
        'bank_transfer': 'university',
        'mobile_money': 'mobile-alt',
        'credit_card': 'credit-card',
        'other': 'coins'
    };
    return icons[method] || 'coins';
}

// Initialize reports page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reports page loaded');
});