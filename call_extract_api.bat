@echo off
REM Batch file to call the Extract API

REM Set variables
SET BASE_URL=https://calidad-api-gateway-avvillas.ccxc.co/core
SET ENDPOINT=/extract/generate
SET API_KEY=sk-test-1234567890abcdef1234567890abcdef

REM Set JSON data with required fields for ExtractDto
SET JSON_DATA={"productId":"00000000-0000-4000-a000-000000000000","period":"20250804"}

REM Display information
echo Calling Extract API...
echo URL: %BASE_URL%%ENDPOINT%
echo Request Body: %JSON_DATA%

REM Make the API call using curl
curl -X POST "${BASE_URL}${ENDPOINT}" \
     -H "Content-Type: application/json" \
     -H "APY-KEY: ${API_KEY}" \
     -d "${JSON_DATA}" \
     -v
REM Pause to see the results
echo.
echo API call completed.
pause