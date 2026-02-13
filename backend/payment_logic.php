<?php

if (!function_exists("normalizePaymentType")) {
    function normalizePaymentType($rawType)
    {
        $type = strtolower(trim((string)$rawType));
        $allowed = ["project", "advance", "bonus", "extra"];

        if (!in_array($type, $allowed, true)) {
            return "project";
        }

        return $type;
    }
}

if (!function_exists("isProjectPaymentType")) {
    function isProjectPaymentType($paymentType)
    {
        return normalizePaymentType($paymentType) === "project";
    }
}

if (!function_exists("extractEmployeeDue")) {
    function extractEmployeeDue($employeeId, $employeesText, $rate = 0.0, $surface = 0.0)
    {
        $employeeId = (int)$employeeId;
        $employeesText = (string)$employeesText;
        $rate = (float)$rate;
        $surface = (float)$surface;
        $due = 0.0;

        if ($employeeId > 0 && $employeesText !== "") {
            $pattern = '/\[' . preg_quote((string)$employeeId, '/') . '\][^\(]*\(([^)]*)\)/u';
            if (preg_match($pattern, $employeesText, $match)) {
                preg_match_all('/-?\d+(?:[.,]\d+)?/u', $match[1], $numMatches);
                $values = array_map(static function ($val) {
                    return (float)str_replace(",", ".", $val);
                }, $numMatches[0] ?? []);

                if (count($values) >= 2) {
                    $first = $values[0];
                    $second = $values[1];
                    $eps = 0.01;

                    if ($rate > 0 && abs($first - $rate) <= $eps && abs($second - $rate) > $eps) {
                        $due = $second;
                    } elseif ($rate > 0 && abs($second - $rate) <= $eps && abs($first - $rate) > $eps) {
                        $due = $first;
                    } else {
                        $due = max($first, $second);
                    }
                } elseif (count($values) === 1) {
                    $due = $values[0];
                }
            }
        }

        // Fallback when text format is inconsistent.
        if ($due <= 0 && $rate > 0 && $surface > 0) {
            $due = $rate * $surface;
        }

        return round(max(0.0, $due), 2);
    }
}
