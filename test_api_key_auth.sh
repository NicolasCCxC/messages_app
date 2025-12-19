#!/bin/bash
# Script to generate extract/

# Variables
BASE_URL="http://192.168.1.70:8087/core"
ENDPOINT="/extract/generate"
API_KEY="sk-test-1234567890abcdef1234567890abcdef"

# JSON with the fields required for testing
JSON_DATA='{"productId":"00000000-0000-4000-a000-000000000000","period":"20250804"}'

# Display information
echo "Testing API Key Authentication..."
echo "URL: ${BASE_URL}${ENDPOINT}"
echo "API Key: ${API_KEY}"
echo "Request Body: ${JSON_DATA}"

# Make API call with API key in header
curl -v -X POST "${BASE_URL}${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -H "API-KEY: ${API_KEY}" \
  -d "${JSON_DATA}" 2>&1
