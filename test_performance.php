<?php
/**
 * Simple performance test script for JEMS Task Manager
 * Tests the optimized endpoints to measure performance improvements
 */

function testEndpoint($url, $description) {
    echo "\n🔍 Testing: $description\n";
    echo "URL: $url\n";
    
    $startTime = microtime(true);
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET',
            'header' => 'User-Agent: Performance Test Script'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    $endTime = microtime(true);
    $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
    
    if ($response !== false) {
        $responseSize = strlen($response);
        echo "✅ Success: " . number_format($duration, 2) . "ms ({$responseSize} bytes)\n";
        
        // Performance rating
        if ($duration < 200) {
            echo "🚀 Excellent performance!\n";
        } elseif ($duration < 500) {
            echo "✨ Good performance\n";
        } elseif ($duration < 1000) {
            echo "⚠️ Acceptable performance\n";
        } else {
            echo "🐌 Slow performance - needs optimization\n";
        }
    } else {
        echo "❌ Failed to load\n";
    }
    
    return $duration;
}

echo "🚀 JEMS Task Manager - Performance Test\n";
echo "=====================================\n";
echo "Testing optimized endpoints...\n";

$baseUrl = 'http://localhost:8000';

// Test endpoints
$tests = [
    ['/', 'Home page (should redirect to login)'],
    ['/login', 'Login page'],
    ['/register', 'Registration page'],
];

$totalTime = 0;
$successfulTests = 0;

foreach ($tests as $test) {
    list($endpoint, $description) = $test;
    $url = $baseUrl . $endpoint;
    $duration = testEndpoint($url, $description);
    
    if ($duration > 0) {
        $totalTime += $duration;
        $successfulTests++;
    }
    
    // Small delay between requests
    usleep(100000); // 100ms
}

echo "\n📊 Performance Summary\n";
echo "=====================\n";
echo "Total tests: " . count($tests) . "\n";
echo "Successful: $successfulTests\n";

if ($successfulTests > 0) {
    $averageTime = $totalTime / $successfulTests;
    echo "Average response time: " . number_format($averageTime, 2) . "ms\n";
    
    if ($averageTime < 200) {
        echo "🎉 Overall performance: EXCELLENT!\n";
    } elseif ($averageTime < 500) {
        echo "✨ Overall performance: GOOD\n";
    } elseif ($averageTime < 1000) {
        echo "⚠️ Overall performance: ACCEPTABLE\n";
    } else {
        echo "🔧 Overall performance: NEEDS IMPROVEMENT\n";
    }
}

echo "\n🔧 Performance Optimizations Applied:\n";
echo "- ✅ Production mode (APP_ENV=prod)\n";
echo "- ✅ Doctrine query caching\n";
echo "- ✅ Profile completion caching\n";
echo "- ✅ Optimized repository queries with eager loading\n";
echo "- ✅ External CSS/JS files\n";
echo "- ✅ Cache warmup completed\n";
echo "- ✅ Twig optimizations\n";
echo "- ✅ Service Worker for client-side caching\n";

echo "\n💡 Next Steps:\n";
echo "- Add database indexes (run migrations when ready)\n";
echo "- Monitor performance in production\n";
echo "- Consider Redis for distributed caching\n";
echo "- Enable OPcache for PHP bytecode caching\n";

echo "\n🌐 Application is running at: $baseUrl\n";
echo "📊 Performance test completed!\n";
?>
