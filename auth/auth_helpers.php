<?php
declare(strict_types=1);

function normalize_college(?string $college): string
{
    $value = strtoupper(trim((string) $college));
    $compact = preg_replace('/[^A-Z0-9]+/', '', $value);
    $aliases = [
        'BSA' => 'BSA', 'CBA' => 'BSA', 'COLLEGEOFBUSINESSANDACCOUNTANCY' => 'BSA',
        'CAS' => 'CAS', 'COLLEGEOFARTSANDSCIENCES' => 'CAS',
        'CCS' => 'CCS', 'COLLEGEOFCOMPUTERSTUDIES' => 'CCS',
        'CIHM' => 'CIHM', 'COLLEGEOFHOSPITALITYMANAGEMENT' => 'CIHM',
        'COE' => 'COE', 'COLLEGEOFENGINEERING' => 'COE',
        'COED' => 'COED', 'COLLEGEOFEDUCATION' => 'COED',
        'CON' => 'CON', 'COLLEGEOFNURSING' => 'CON',
    ];

    return $aliases[$compact] ?? '';
}

function student_dashboard_file(?string $college): ?string
{
    $files = [
        'BSA' => 'dashboard_BSA.html', 'CAS' => 'dashbaord_CAS.html',
        'CCS' => 'dashboard_CCS.html', 'CIHM' => 'dashboard_CIHM.html',
        'COE' => 'dashboard_COE.html', 'COED' => 'dashboard_COED.html',
        'CON' => 'dashboard_CON.html',
    ];

    return $files[normalize_college($college)] ?? null;
}

function student_dashboard_path(?string $college): ?string
{
    $file = student_dashboard_file($college);
    return $file ? 'Student Dashboard/' . $file : null;
}
