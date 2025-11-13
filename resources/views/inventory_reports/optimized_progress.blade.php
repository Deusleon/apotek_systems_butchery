<div class="optimized-progress-container" id="optimized-progress-container" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-cog fa-spin"></i> 
                Generating Optimized Report
            </h5>
        </div>
        <div class="card-body">
            <div class="progress-info">
                <div class="d-flex justify-content-between mb-2">
                    <span class="progress-status">Preparing report...</span>
                    <span class="progress-percentage">0%</span>
                </div>
                
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: 0%" 
                         data-progress="0">
                    </div>
                </div>
                
                <div class="progress-details">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-database"></i>
                                Records processed: <span id="records-processed">0</span>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                ETA: <span id="eta">Calculating...</span>
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="optimization-info mt-3">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-rocket"></i> Optimizations Active:</h6>
                        <ul class="mb-0">
                            <li>Streaming data processing</li>
                            <li>Memory management</li>
                            <li>Chunked database queries</li>
                            <li>Progress tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="actions mt-3">
                <button type="button" class="btn btn-secondary" id="cancel-generation">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="download-report" style="display: none;">
                    <i class="fas fa-download"></i> Download Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
class OptimizedPDFGenerator {
    constructor() {
        this.jobId = null;
        this.checkInterval = null;
        this.maxAttempts = 300; // 5 minutes timeout
        this.attempts = 0;
    }
    
    startGeneration(reportType, parameters, callbackUrl) {
        this.showProgress();
        
        // Start background generation
        fetch(`/optimized-reports/${reportType}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ...parameters,
                callback_url: callbackUrl
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.jobId = data.job_id;
                this.startProgressMonitoring();
            } else {
                this.hideProgress();
                alert('Failed to start report generation: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error starting generation:', error);
            this.hideProgress();
            alert('Failed to start report generation');
        });
    }
    
    startProgressMonitoring() {
        this.checkInterval = setInterval(() => {
            this.checkProgress();
        }, 1000); // Check every second
    }
    
    checkProgress() {
        if (this.attempts >= this.maxAttempts) {
            this.hideProgress();
            clearInterval(this.checkInterval);
            alert('Report generation timed out. Please try again.');
            return;
        }
        
        fetch(`/api/optimized-reports/progress/${this.jobId}`)
            .then(response => response.json())
            .then(data => {
                this.attempts++;
                this.updateProgress(data);
                
                if (data.status === 'completed') {
                    this.onComplete(data);
                } else if (data.status === 'failed') {
                    this.onFailure(data);
                }
            })
            .catch(error => {
                console.error('Error checking progress:', error);
            });
    }
    
    updateProgress(data) {
        const progressBar = document.querySelector('.progress-bar');
        const percentage = document.querySelector('.progress-percentage');
        const status = document.querySelector('.progress-status');
        const recordsProcessed = document.getElementById('records-processed');
        const eta = document.getElementById('eta');
        
        if (data.progress !== undefined) {
            progressBar.style.width = data.progress + '%';
            progressBar.dataset.progress = data.progress;
            percentage.textContent = data.progress + '%';
        }
        
        if (data.status_message) {
            status.textContent = data.status_message;
        }
        
        if (data.processed !== undefined) {
            recordsProcessed.textContent = data.processed.toLocaleString();
        }
        
        if (data.eta) {
            eta.textContent = data.eta;
        }
    }
    
    onComplete(data) {
        clearInterval(this.checkInterval);
        
        document.querySelector('.progress-status').textContent = 'Report generated successfully!';
        document.querySelector('.progress-percentage').textContent = '100%';
        document.querySelector('.progress-bar').style.width = '100%';
        
        // Show download button
        const downloadBtn = document.getElementById('download-report');
        downloadBtn.style.display = 'inline-block';
        downloadBtn.onclick = () => {
            if (data.download_url) {
                window.open(data.download_url, '_blank');
            }
        };
        
        // Hide cancel button
        document.getElementById('cancel-generation').style.display = 'none';
    }
    
    onFailure(data) {
        clearInterval(this.checkInterval);
        this.hideProgress();
        alert('Report generation failed: ' + (data.message || 'Unknown error'));
    }
    
    showProgress() {
        document.getElementById('optimized-progress-container').style.display = 'block';
        document.querySelector('.progress-bar').style.width = '0%';
        document.querySelector('.progress-bar').dataset.progress = '0';
    }
    
    hideProgress() {
        document.getElementById('optimized-progress-container').style.display = 'none';
        
        // Reset elements
        document.querySelector('.progress-bar').style.width = '0%';
        document.querySelector('.progress-bar').dataset.progress = '0';
        document.querySelector('.progress-percentage').textContent = '0%';
        document.querySelector('.progress-status').textContent = 'Preparing report...';
        document.getElementById('records-processed').textContent = '0';
        document.getElementById('eta').textContent = 'Calculating...';
        
        // Hide download button
        document.getElementById('download-report').style.display = 'none';
        
        // Show cancel button
        document.getElementById('cancel-generation').style.display = 'inline-block';
    }
    
    cancel() {
        if (this.jobId && this.checkInterval) {
            fetch(`/optimized-reports/background-jobs/${this.jobId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(() => {
                clearInterval(this.checkInterval);
                this.hideProgress();
            });
        }
    }
}

// Initialize the PDF generator
const pdfGenerator = new OptimizedPDFGenerator();

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cancel button
    document.getElementById('cancel-generation').addEventListener('click', () => {
        pdfGenerator.cancel();
    });
    
    // Download button is set dynamically when generation completes
});

// Usage examples for different reports
function generateOptimizedProductLedgerReport(productId) {
    const callbackUrl = window.location.origin + '/api/reports/callback';
    pdfGenerator.startGeneration('product-ledger', {
        product: productId
    }, callbackUrl);
}

function generateOptimizedInventoryCountSheet() {
    const callbackUrl = window.location.origin + '/api/reports/callback';
    pdfGenerator.startGeneration('inventory-count-sheet', {}, callbackUrl);
}

function generateOptimizedProductDetails(categoryId) {
    const callbackUrl = window.location.origin + '/api/reports/callback';
    pdfGenerator.startGeneration('product-details', {
        category_name_detail: categoryId
    }, callbackUrl);
}
</script>

<style>
.optimized-progress-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 400px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-header h5 {
    margin: 0;
    color: #495057;
}

.progress-bar {
    background-color: #007bff;
    transition: width 0.3s ease;
}

.progress-details .row {
    font-size: 0.875rem;
}

.optimization-info .alert {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.optimization-info ul {
    padding-left: 1.2rem;
}

.actions .btn {
    margin-right: 0.5rem;
}

.fa-spin {
    animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>