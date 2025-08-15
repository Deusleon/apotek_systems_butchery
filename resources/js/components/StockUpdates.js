import Echo from 'laravel-echo';

class StockUpdates {
    constructor(storeId) {
        this.storeId = storeId;
        this.initializeEcho();
        this.listenForUpdates();
    }

    initializeEcho() {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            encrypted: true
        });
    }

    listenForUpdates() {
        window.Echo.private(`stock.updates.${this.storeId}`)
            .listen('StockUpdated', (e) => {
                this.updateTableRow(e);
            });
    }

    updateTableRow(data) {
        const table = document.getElementById('fixed-header-main');
        if (!table) return;

        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            if (cells[0] && cells[0].textContent === data.product_name) {
                // Update quantity
                cells[2].textContent = data.quantity;
                
                // Update stock value
                cells[3].textContent = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(data.stock_value);

                // Update status badge
                const statusCell = cells[8];
                const statusSpan = statusCell.getElementsByTagName('span')[0];
                if (statusSpan) {
                    statusSpan.className = `badge badge-${this.getStatusClass(data.status)}`;
                    statusSpan.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                }

                // Highlight the updated row temporarily
                row.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 2000);

                break;
            }
        }
    }

    getStatusClass(status) {
        const classes = {
            'critical': 'danger',
            'low': 'warning',
            'normal': 'success'
        };
        return classes[status] || 'secondary';
    }
}

export default StockUpdates; 