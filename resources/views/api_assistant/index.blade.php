@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>AI Assistant - Pharmacy Management</h4>
                </div>
                <div class="card-body">
                    <!-- Connection Test -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>API Connection Test</h5>
                                </div>
                                <div class="card-body">
                                    <button id="testConnection" class="btn btn-primary">Test API Connection</button>
                                    <div id="connectionResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Message -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Send General Message</h5>
                                </div>
                                <div class="card-body">
                                    <form id="messageForm">
                                        <div class="form-group">
                                            <label for="message">Message:</label>
                                            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Enter your message here..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="systemPrompt">System Prompt (Optional):</label>
                                            <input type="text" class="form-control" id="systemPrompt" name="system_prompt" placeholder="Optional system prompt">
                                        </div>
                                        <button type="submit" class="btn btn-success">Send Message</button>
                                    </form>
                                    <div id="messageResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Drug Information -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Drug Information</h5>
                                </div>
                                <div class="card-body">
                                    <form id="drugInfoForm">
                                        <div class="form-group">
                                            <label for="drugName">Drug Name:</label>
                                            <input type="text" class="form-control" id="drugName" name="drug_name" placeholder="Enter drug name">
                                        </div>
                                        <button type="submit" class="btn btn-info">Get Drug Information</button>
                                    </form>
                                    <div id="drugInfoResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Drug Interactions -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Drug Interactions Check</h5>
                                </div>
                                <div class="card-body">
                                    <form id="interactionsForm">
                                        <div class="form-group">
                                            <label for="drugs">Drug Names (comma separated):</label>
                                            <input type="text" class="form-control" id="drugs" name="drugs" placeholder="e.g., Aspirin, Ibuprofen, Warfarin">
                                        </div>
                                        <button type="submit" class="btn btn-warning">Check Interactions</button>
                                    </form>
                                    <div id="interactionsResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pharmacy Questions -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Pharmacy Questions</h5>
                                </div>
                                <div class="card-body">
                                    <form id="pharmacyForm">
                                        <div class="form-group">
                                            <label for="question">Question:</label>
                                            <textarea class="form-control" id="question" name="question" rows="3" placeholder="Ask a pharmacy-related question..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-secondary">Ask Question</button>
                                    </form>
                                    <div id="pharmacyResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Test Connection
    $('#testConnection').click(function() {
        const button = $(this);
        const resultDiv = $('#connectionResult');
        
        button.prop('disabled', true).text('Testing...');
        resultDiv.html('<div class="alert alert-info">Testing connection...</div>');
        
        $.get('/api/assistant/test')
            .done(function(response) {
                if (response.success) {
                    resultDiv.html('<div class="alert alert-success">✓ ' + response.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger">✗ ' + response.message + '</div>');
                }
            })
            .fail(function(xhr) {
                resultDiv.html('<div class="alert alert-danger">✗ Connection failed: ' + (xhr.responseJSON?.message || 'Unknown error') + '</div>');
            })
            .always(function() {
                button.prop('disabled', false).text('Test API Connection');
            });
    });

    // Send Message
    $('#messageForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const resultDiv = $('#messageResult');
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).text('Sending...');
        resultDiv.html('<div class="alert alert-info">Sending message...</div>');
        
        $.post('/api/assistant/message', {
            message: $('#message').val(),
            system_prompt: $('#systemPrompt').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                resultDiv.html('<div class="alert alert-success"><strong>Response:</strong><br>' + response.response + '</div>');
            } else {
                resultDiv.html('<div class="alert alert-danger">✗ ' + response.message + '</div>');
            }
        })
        .fail(function(xhr) {
            resultDiv.html('<div class="alert alert-danger">✗ Error: ' + (xhr.responseJSON?.message || 'Unknown error') + '</div>');
        })
        .always(function() {
            submitBtn.prop('disabled', false).text('Send Message');
        });
    });

    // Drug Information
    $('#drugInfoForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const resultDiv = $('#drugInfoResult');
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).text('Getting Info...');
        resultDiv.html('<div class="alert alert-info">Retrieving drug information...</div>');
        
        $.post('/api/assistant/drug-info', {
            drug_name: $('#drugName').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                resultDiv.html('<div class="alert alert-success"><strong>Drug:</strong> ' + response.drug_name + '<br><strong>Information:</strong><br>' + response.information + '</div>');
            } else {
                resultDiv.html('<div class="alert alert-danger">✗ ' + response.message + '</div>');
            }
        })
        .fail(function(xhr) {
            resultDiv.html('<div class="alert alert-danger">✗ Error: ' + (xhr.responseJSON?.message || 'Unknown error') + '</div>');
        })
        .always(function() {
            submitBtn.prop('disabled', false).text('Get Drug Information');
        });
    });

    // Drug Interactions
    $('#interactionsForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const resultDiv = $('#interactionsResult');
        const submitBtn = form.find('button[type="submit"]');
        
        const drugs = $('#drugs').val().split(',').map(drug => drug.trim()).filter(drug => drug);
        
        if (drugs.length < 2) {
            resultDiv.html('<div class="alert alert-warning">Please enter at least 2 drug names separated by commas.</div>');
            return;
        }
        
        submitBtn.prop('disabled', true).text('Checking...');
        resultDiv.html('<div class="alert alert-info">Checking drug interactions...</div>');
        
        $.post('/api/assistant/drug-interactions', {
            drugs: drugs,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                resultDiv.html('<div class="alert alert-success"><strong>Drugs:</strong> ' + response.drugs.join(', ') + '<br><strong>Interactions:</strong><br>' + response.interactions + '</div>');
            } else {
                resultDiv.html('<div class="alert alert-danger">✗ ' + response.message + '</div>');
            }
        })
        .fail(function(xhr) {
            resultDiv.html('<div class="alert alert-danger">✗ Error: ' + (xhr.responseJSON?.message || 'Unknown error') + '</div>');
        })
        .always(function() {
            submitBtn.prop('disabled', false).text('Check Interactions');
        });
    });

    // Pharmacy Questions
    $('#pharmacyForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const resultDiv = $('#pharmacyResult');
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).text('Asking...');
        resultDiv.html('<div class="alert alert-info">Processing question...</div>');
        
        $.post('/api/assistant/pharmacy-question', {
            question: $('#question').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                resultDiv.html('<div class="alert alert-success"><strong>Question:</strong> ' + response.question + '<br><strong>Answer:</strong><br>' + response.answer + '</div>');
            } else {
                resultDiv.html('<div class="alert alert-danger">✗ ' + response.message + '</div>');
            }
        })
        .fail(function(xhr) {
            resultDiv.html('<div class="alert alert-danger">✗ Error: ' + (xhr.responseJSON?.message || 'Unknown error') + '</div>');
        })
        .always(function() {
            submitBtn.prop('disabled', false).text('Ask Question');
        });
    });
});
</script>
@endsection 