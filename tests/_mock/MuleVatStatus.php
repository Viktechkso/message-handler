<?php

/* mocked entry point for mule vat status check */

echo json_encode([
    'total' => 1,
    'success' => 2,
    'duplicate' => 3,
    'error' => 4,
    'unknown' => 1
]);