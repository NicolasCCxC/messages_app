# PowerShell script to call the Extract API

# Set variables
$baseUrl = "http://localhost:8087/core"
$endpoint = "/extract/generate"

# Set JSON data with required fields for ExtractDto
$jsonData = @{
    productId = "00000000-0000-4000-a000-000000000000"  # Replace with a valid UUID
    period = "20250804"  # Format: YYYYMMDD (8 characters)
} | ConvertTo-Json -Compress

# Display information
Write-Host "Calling Extract API..."
Write-Host "URL: $baseUrl$endpoint"
Write-Host "Request Body: $jsonData"

# Make the API call using Invoke-RestMethod
try {
    $response = Invoke-RestMethod -Uri "$baseUrl$endpoint" -Method Post -ContentType "application/json" -Body $jsonData

    # Display the response
    Write-Host "`nAPI Response:"
    Write-Host "Product ID: $($response.data.productId)"
    Write-Host "Period: $($response.data.period)"
    Write-Host "State: $($response.data.state)"
    Write-Host "Progress: $($response.data.progress)"
    Write-Host "User ID: $($response.data.userId)"
    Write-Host "Message: $($response.message)"
    Write-Host "Service: $($response.service)"
}
catch {
    Write-Host "`nError calling API:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    if ($_.ErrorDetails.Message) {
        Write-Host "Details: $($_.ErrorDetails.Message)" -ForegroundColor Red
    }
}

# Pause to see the results
Write-Host "`nAPI call completed."
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")