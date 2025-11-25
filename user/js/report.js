class IncomeReport {
    constructor() {
        this.apiUrl = 'controllers/reports.php';
        this.init();
    }

    init() {
        this.setDefaultDates();
        this.bindEvents();
    }

    setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        document.getElementById('start_date').value = this.formatDate(firstDay);
        document.getElementById('end_date').value = this.formatDate(today);
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    bindEvents() {
        document.getElementById('reportFilter').addEventListener('submit', (e) => {
            e.preventDefault();
            this.generateReport();
        });

        document.getElementById('exportPdf').addEventListener('click', () => this.exportPDF());
        document.getElementById('exportExcel').addEventListener('click', () => this.exportExcel());
    }

    async generateReport() {
        const formData = new FormData(document.getElementById('reportFilter'));
        const data = {
            action: 'get_report',
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date'),
            vehicle_reg: formData.get('vehicle_reg'),
            customer_name: formData.get('customer_name'),
            report_type: formData.get('report_type')
        };

        this.showLoading(true);

        try {
            const response = await this.apiCall(data);
            if (response.success) {
                this.displayReport(response.data);
                await this.loadSummary(data);
            } else {
                this.showError('Failed to generate report');
            }
        } catch (err) {
            this.showError('Error: ' + err.message);
        } finally {
            this.showLoading(false);
        }
    }

    async loadSummary(data) {
        const summaryData = { ...data, action: 'get_summary' };
        try {
            const response = await this.apiCall(summaryData);
            if (response.success) this.displaySummary(response.summary);
        } catch (err) { console.error(err); }
    }

    displayReport(data) {
        const tbody = document.getElementById('reportData');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="12" class="text-center">No records found</td></tr>';
            document.getElementById('summarySection').style.display = 'none';
            return;
        }

        data.forEach(item => {
            console.log(item);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.invoice_no}</td>
                <td>${item.invoice_date}</td>
                <td>${item.customer_name || 'N/A'}</td>
                <td>${item.vehicle_reg ? `${item.vehicle_reg} (${item.make})` : 'N/A'}</td>
                <td>
                    ${item.write_off == 1 ? '<span class="status-badge status-writeoff">WRITE-OFF</span>' : '<span class="status-badge status-' + item.status + '">' + item.status.toUpperCase() + '</span>'}
                </td>
                <td>£${parseFloat(item.subtotal).toFixed(2)}</td>
                <td>£${parseFloat(item.tax_amount).toFixed(2)}</td>
                <td>£${parseFloat(item.discount).toFixed(2)}</td>
                <td>£${parseFloat(item.total_amount).toFixed(2)}</td>
                <td>£${parseFloat(item.paid_amount).toFixed(2)}</td>
                <td>£${parseFloat(item.due_amount).toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary view-details" data-id="${item.id}"><i class="fas fa-eye"></i></button>
                </td>
            `;
            tbody.appendChild(row);
        });

        this.initializeDataTable();
    }

    displaySummary(summary) {
        const summarySection = document.getElementById('summarySection');
        const summaryCards = document.getElementById('summaryCards');
        summarySection.style.display = 'block';
        summaryCards.innerHTML = '';

        summary.forEach(item => {

            const cardHtml = `
                <div class="col-md-3 mb-3">
                    <div class="card summary-card bg-primary">
                        <div class="card-body">
                            <h4 class="text-dark">${item.total_invoices} Invoices</h4>
                            <p class="text-dark">Total Amount: £${parseFloat(item.total_amount).toFixed(2)}</p>
                            <p class="text-dark">Total Paid: £${parseFloat(item.total_paid).toFixed(2)}</p>
                            <p class="text-dark">Total Due: £${parseFloat(item.total_due).toFixed(2)}</p>
                            <p class="text-dark">Write off: £${item.write_off == 1 ? parseFloat(item.total_due).toFixed(2) : 0}</p>
                        </div>
                    </div>
                </div>
            `;
            summaryCards.innerHTML += cardHtml;
        });
    }

    initializeDataTable() {
        if ($.fn.DataTable.isDataTable('#reportTable')) {
            $('#reportTable').DataTable().destroy();
        }

        $('#reportTable').DataTable({
            pageLength: 25,
            order: [[1, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tip',
            language: { search: "_INPUT_", searchPlaceholder: "Search records..." }
        });
    }

    async apiCall(data) {
        const res = await fetch(this.apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (!res.ok) throw new Error('Network error');
        return await res.json();
    }

    showLoading(show) { document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none'; }
    showError(msg) { alert(msg); }

    exportPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const rows = [];
        document.querySelectorAll('#reportData tr').forEach(tr => {
            const row = Array.from(tr.querySelectorAll('td')).map(td => td.innerText);
            rows.push(row);
        });
        doc.autoTable({
            head: [['Invoice No', 'Date', 'Customer', 'Vehicle', 'Status', 'Subtotal', 'Tax', 'Discount', 'Total', 'Paid', 'Due']],
            body: rows
        });
        doc.save('income-report.pdf');
    }

    exportExcel() {
        const table = document.getElementById('reportTable');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Income Report');
        XLSX.writeFile(wb, 'income-report.xlsx');
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => { new IncomeReport(); });
