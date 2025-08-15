# Anthropic API Integration for Pharmacy Management System

This integration adds AI-powered assistance to your Laravel pharmacy management system using Anthropic's Claude API.

## Features

- **API Connection Testing**: Verify your API key and connection
- **General AI Chat**: Send custom messages to Claude
- **Drug Information**: Get detailed information about medications
- **Drug Interactions**: Check for potential interactions between multiple drugs
- **Pharmacy Questions**: Ask pharmacy-related questions with professional context
- **Web Interface**: User-friendly web interface for testing and using the API
- **RESTful API Endpoints**: Programmatic access to all features

## Setup Instructions

### 1. Environment Configuration

Add your Anthropic API key to your `.env` file:

```env
ANTHROPIC_API_KEY=your_api_key_here
```

If no API key is set in the environment, the system will use the default key provided in the PowerShell script.

### 2. Service Registration (Optional)

If you want to use dependency injection, you can register the service in `app/Providers/AppServiceProvider.php`:

```php
public function register()
{
    $this->app->singleton(AnthropicApiService::class, function ($app) {
        return new AnthropicApiService();
    });
}
```

### 3. Testing the Integration

#### Option A: Web Interface
1. Navigate to `/ai-assistant` in your browser
2. Use the web interface to test all features

#### Option B: Command Line Test
Run the test script:

```bash
php test_anthropic_api.php
```

#### Option C: API Endpoints
Test the REST API endpoints directly:

```bash
# Test connection
curl -X GET http://your-domain/api/assistant/test

# Send a message
curl -X POST http://your-domain/api/assistant/message \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello Claude!", "system_prompt": "You are a helpful assistant."}'

# Get drug information
curl -X POST http://your-domain/api/assistant/drug-info \
  -H "Content-Type: application/json" \
  -d '{"drug_name": "Aspirin"}'

# Check drug interactions
curl -X POST http://your-domain/api/assistant/drug-interactions \
  -H "Content-Type: application/json" \
  -d '{"drugs": ["Aspirin", "Warfarin"]}'

# Ask pharmacy question
curl -X POST http://your-domain/api/assistant/pharmacy-question \
  -H "Content-Type: application/json" \
  -d '{"question": "What are the common side effects of antibiotics?"}'
```

## API Endpoints

### 1. Test Connection
- **URL**: `GET /api/assistant/test`
- **Description**: Test the API connection
- **Response**: JSON with success status and message

### 2. Send Message
- **URL**: `POST /api/assistant/message`
- **Body**:
  ```json
  {
    "message": "Your message here",
    "system_prompt": "Optional system prompt"
  }
  ```
- **Response**: JSON with AI response

### 3. Get Drug Information
- **URL**: `POST /api/assistant/drug-info`
- **Body**:
  ```json
  {
    "drug_name": "Drug Name"
  }
  ```
- **Response**: JSON with drug information

### 4. Check Drug Interactions
- **URL**: `POST /api/assistant/drug-interactions`
- **Body**:
  ```json
  {
    "drugs": ["Drug 1", "Drug 2", "Drug 3"]
  }
  ```
- **Response**: JSON with interaction information

### 5. Ask Pharmacy Question
- **URL**: `POST /api/assistant/pharmacy-question`
- **Body**:
  ```json
  {
    "question": "Your pharmacy question"
  }
  ```
- **Response**: JSON with AI answer

## Usage Examples

### In Controllers

```php
use App\Services\AnthropicApiService;

class YourController extends Controller
{
    private $anthropicService;

    public function __construct(AnthropicApiService $anthropicService)
    {
        $this->anthropicService = $anthropicService;
    }

    public function someMethod()
    {
        // Get drug information
        $drugInfo = $this->anthropicService->getDrugInfo('Aspirin');
        
        // Check interactions
        $interactions = $this->anthropicService->checkDrugInteractions(['Aspirin', 'Warfarin']);
        
        // Ask a question
        $answer = $this->anthropicService->askPharmacyQuestion('What are the storage requirements for insulin?');
    }
}
```

### In Blade Views

```php
@php
    $anthropicService = app(App\Services\AnthropicApiService::class);
    $drugInfo = $anthropicService->getDrugInfo('Paracetamol');
@endphp

<div>
    <h3>Drug Information</h3>
    <p>{{ $drugInfo }}</p>
</div>
```

## Error Handling

The service includes comprehensive error handling:

- **API Connection Errors**: Logged and handled gracefully
- **Invalid Responses**: Null values returned with error logging
- **Rate Limiting**: Built-in timeout handling
- **Validation**: Input validation on all endpoints

## Security Considerations

1. **API Key Protection**: Store your API key in environment variables
2. **Input Validation**: All inputs are validated before processing
3. **Error Logging**: Sensitive information is not logged
4. **Rate Limiting**: Consider implementing rate limiting for production use

## Customization

### Adding New Features

You can extend the `AnthropicApiService` class to add new features:

```php
public function getDosageInfo($drugName, $age, $weight)
{
    $query = "Provide dosage information for {$drugName} for a patient aged {$age} weighing {$weight}kg.";
    return $this->askPharmacyQuestion($query);
}
```

### Modifying System Prompts

Update the system prompts in the service methods to customize the AI's behavior for your specific use case.

## Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check your API key in `.env`
   - Verify internet connectivity
   - Check Anthropic API status

2. **Timeout Errors**
   - Increase timeout in service configuration
   - Check network connectivity

3. **Invalid Responses**
   - Check API response format
   - Verify request payload structure

### Debug Mode

Enable debug logging by checking Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

## Support

For issues related to:
- **Anthropic API**: Contact Anthropic support
- **Laravel Integration**: Check Laravel documentation
- **Pharmacy System**: Contact your system administrator

## License

This integration is part of your pharmacy management system and follows the same licensing terms. 