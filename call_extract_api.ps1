# PowerShell script to call the Extract API

# Set variables
$baseUrl = "http://192.168.1.83:8087/core"
$endpoint = "/extract/generate"
$apiKey = "sk-test-1234567890abcdef1234567890abc" # <--- ADDED API KEY

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
    $response = Invoke-RestMethod -Uri "$baseUrl$endpoint" `
        -Method Post `
        -ContentType "application/json" `
        -Headers @{ "API-KEY" = $apiKey } `
        -Body $jsonData

    # Display the response
    Write-Host "`nAPI Response:" -ForegroundColor Green
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
   if ($_.ErrorDetails) {
        Write-Host "Details: $($_.ErrorDetails.Message)" -ForegroundColor Red
       } elseif ($_.Exception.Response) {
           $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
           Write-Host "Server Response: $($reader.ReadToEnd())" -ForegroundColor Red
       }
}

# Pause to see the results
Write-Host "`nAPI call completed."
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")