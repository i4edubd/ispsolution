#!/bin/bash

# Load Testing Script for ISP Solution
# Uses Apache Bench (ab) to test common endpoints

echo "==================================="
echo "ISP Solution Load Testing"
echo "==================================="
echo ""

# Configuration
BASE_URL="${BASE_URL:-http://localhost}"
CONCURRENCY="${CONCURRENCY:-10}"
REQUESTS="${REQUESTS:-100}"
AUTH_TOKEN="${AUTH_TOKEN:-}"

# Test endpoints
declare -a ENDPOINTS=(
    "/api/v1/network-users"
    "/api/v1/invoices"
    "/api/v1/payments"
    "/panel/customer/dashboard"
)

# Create results directory
RESULTS_DIR="storage/performance-tests/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$RESULTS_DIR"

echo "Configuration:"
echo "  Base URL: $BASE_URL"
echo "  Concurrency: $CONCURRENCY"
echo "  Requests: $REQUESTS"
echo "  Results: $RESULTS_DIR"
echo ""

# Function to run load test
run_test() {
    local endpoint=$1
    local name=$(echo $endpoint | sed 's/\//-/g' | sed 's/^-//')
    local output_file="$RESULTS_DIR/$name.txt"
    
    echo "Testing: $endpoint"
    
    if [ -n "$AUTH_TOKEN" ]; then
        ab -n $REQUESTS -c $CONCURRENCY \
           -H "Authorization: Bearer $AUTH_TOKEN" \
           -H "Accept: application/json" \
           "${BASE_URL}${endpoint}" > "$output_file" 2>&1
    else
        ab -n $REQUESTS -c $CONCURRENCY \
           -H "Accept: application/json" \
           "${BASE_URL}${endpoint}" > "$output_file" 2>&1
    fi
    
    # Extract key metrics
    local requests_per_sec=$(grep "Requests per second" "$output_file" | awk '{print $4}')
    local time_per_request=$(grep "Time per request.*mean\)" "$output_file" | awk '{print $4}')
    local failed_requests=$(grep "Failed requests" "$output_file" | awk '{print $3}')
    
    echo "  Requests/sec: $requests_per_sec"
    echo "  Time/request: ${time_per_request}ms"
    echo "  Failed: $failed_requests"
    echo ""
}

# Run tests on all endpoints
echo "Starting load tests..."
echo ""

for endpoint in "${ENDPOINTS[@]}"; do
    run_test "$endpoint"
done

# Generate summary report
SUMMARY_FILE="$RESULTS_DIR/summary.txt"
echo "==================================="  > "$SUMMARY_FILE"
echo "Load Testing Summary"                >> "$SUMMARY_FILE"
echo "Date: $(date)"                       >> "$SUMMARY_FILE"
echo "==================================="  >> "$SUMMARY_FILE"
echo ""                                    >> "$SUMMARY_FILE"

for endpoint in "${ENDPOINTS[@]}"; do
    local name=$(echo $endpoint | sed 's/\//-/g' | sed 's/^-//')
    local output_file="$RESULTS_DIR/$name.txt"
    
    if [ -f "$output_file" ]; then
        echo "Endpoint: $endpoint"           >> "$SUMMARY_FILE"
        grep "Requests per second" "$output_file" >> "$SUMMARY_FILE"
        grep "Time per request.*mean\)" "$output_file" | head -1 >> "$SUMMARY_FILE"
        grep "Failed requests" "$output_file" >> "$SUMMARY_FILE"
        echo ""                              >> "$SUMMARY_FILE"
    fi
done

echo "==================================="
echo "Load testing completed!"
echo "Results saved to: $RESULTS_DIR"
echo "==================================="
echo ""
echo "Summary:"
cat "$SUMMARY_FILE"
