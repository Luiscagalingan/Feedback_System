<?php
$four = ['Excellent' => 4, 'Good' => 4, 'Fair' => 3, 'Poor' => 2];
$satisfied = ['5 - Very Satisfied' => 5, '4 - Satisfied' => 4, '3 - Neutral' => 3, '2 - Dissatisfied' => 2, '1 - Very Dissatisfied' => 1];
$helpful = ['5 - Very Helpful' => 5, '4 - Helpful' => 4, '3 - Neutral' => 3, '2 - Unhelpful' => 2, '1 - Very Unhelpful' => 1];
$fast = ['5 - Very Fast' => 5, '4 - Fast' => 4, '3 - Average' => 3, '2 - Slow' => 2, '1 - Very Slow' => 1];
$responsive = ['5 - Very Responsive' => 5, '4 - Responsive' => 4, '3 - Neutral' => 3, '2 - Unresponsive' => 2, '1 - Very Unresponsive' => 1];
return [
  'library' => ['name' => 'Library Office', 'section' => 'Library Service Experience', 'questions' => [
    ['How would you rate the availability of books/references you needed?', $four],
    ['How helpful were the library staff when you asked for assistance?', $helpful],
    ["How satisfied are you with the library's borrowing/reservation process?", $satisfied],
    ['How would you rate the comfort and condition of the library study spaces?', $four],
  ]],
  'college-enrollment' => ['name' => 'College Enrollment Offices (per College)', 'section' => 'College-Level Enrollment Experience', 'questions' => [
    ['How would you rate your enrollment experience within your specific college?', $four],
    ['How satisfied are you with subject scheduling and adviser support?', $satisfied],
    ['How quickly were enrollment-related issues resolved?', $fast],
    ['How clear was the information given about requirements/deadlines?', $four],
  ]],
  'teacher-consultation' => ['name' => 'Faculty / Teacher Consultation Services', 'section' => 'Teacher Consultation Experience', 'questions' => [
    ['How would you rate the availability of your teachers for consultation?', $four],
    ['How satisfied are you with the quality of guidance given during consultations?', $satisfied],
    ['How responsive are teachers to consultation requests (in person or online)?', $responsive],
    ['How would you rate the overall helpfulness of consultation sessions to your learning?', $four],
  ]],
];
