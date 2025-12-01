<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];
$format = $_GET['format'] ?? 'pdf';

try {
    // Check if student has approved registrations
    $stmt = $pdo->prepare("SELECT cr.*, s.*, c.course_code, c.course_title, c.course_unit, c.semester as course_semester
                          FROM course_regtbl cr 
                          JOIN studenttbl s ON cr.student_id = s.student_id 
                          JOIN coursetbl c ON cr.course_id = c.course_id 
                          WHERE cr.student_id = ? AND cr.approval_status = 'approved'
                          ORDER BY c.semester, c.course_code");
    $stmt->execute([$student_id]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($registrations)) {
        echo "<script>alert('No approved course registrations found.'); window.history.back();</script>";
        exit();
    }

    $student = $registrations[0]; // Get student info from first record

    // Generate content based on format
    switch ($format) {
        case 'pdf':
            generatePDF($registrations, $student);
            break;
        case 'doc':
            generateDOC($registrations, $student);
            break;
        case 'docx':
            generateDOCX($registrations, $student);
            break;
        case 'xls':
            generateXLS($registrations, $student);
            break;
        case 'xlsx':
            generateXLSX($registrations, $student);
            break;
        case 'txt':
            generateTXT($registrations, $student);
            break;
        case 'csv':
            generateCSV($registrations, $student);
            break;
        default:
            generateHTML($registrations, $student);
    }

} catch (PDOException $e) {
    echo "<script>alert('Database error: " . $e->getMessage() . "'); window.history.back();</script>";
}

function generateHTML($registrations, $student) {
    $html = generateFormHTML($registrations, $student);
    header('Content-Type: text/html');
    echo $html;
}

function generatePDF($registrations, $student) {
    $html = generateFormHTML($registrations, $student);
    
    // Simple PDF generation using HTML to PDF conversion
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="course_registration_' . $student['Matric_No'] . '.pdf"');
    
    // For basic PDF, we'll use HTML with print styles
    echo "<!DOCTYPE html><html><head><style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .student-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        @media print { body { margin: 0; } }
    </style></head><body>$html</body></html>";
}

function generateDOC($registrations, $student) {
    $content = generateFormContent($registrations, $student);
    
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="course_registration_' . $student['Matric_No'] . '.doc"');
    
    echo "<html><body>$content</body></html>";
}

function generateDOCX($registrations, $student) {
    // For DOCX, we'll use DOC format as fallback
    generateDOC($registrations, $student);
}

function generateXLS($registrations, $student) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="course_registration_' . $student['Matric_No'] . '.xls"');
    
    echo "<table border='1'>";
    echo "<tr><th colspan='6'>COURSE REGISTRATION FORM</th></tr>";
    echo "<tr><td>Name:</td><td colspan='2'>" . $student['first_name'] . " " . $student['last_name'] . "</td><td>Matric No:</td><td colspan='2'>" . $student['Matric_No'] . "</td></tr>";
    echo "<tr><td>Department:</td><td colspan='2'>" . $student['Department'] . "</td><td>Level:</td><td colspan='2'>" . $student['Level'] . "</td></tr>";
    echo "<tr><th>S/N</th><th>Course Code</th><th>Course Title</th><th>Units</th><th>Semester</th><th>Status</th></tr>";
    
    foreach ($registrations as $index => $reg) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>" . $reg['course_code'] . "</td>";
        echo "<td>" . $reg['course_title'] . "</td>";
        echo "<td>" . $reg['course_unit'] . "</td>";
        echo "<td>" . $reg['course_semester'] . "</td>";
        echo "<td>Approved</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function generateXLSX($registrations, $student) {
    // For XLSX, we'll use XLS format as fallback
    generateXLS($registrations, $student);
}

function generateTXT($registrations, $student) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="course_registration_' . $student['Matric_No'] . '.txt"');
    
    $content = "COURSE REGISTRATION FORM\n";
    $content .= "========================\n\n";
    $content .= "Student Name: " . $student['first_name'] . " " . $student['last_name'] . "\n";
    $content .= "Matric Number: " . $student['Matric_No'] . "\n";
    $content .= "Department: " . $student['Department'] . "\n";
    $content .= "Level: " . $student['Level'] . "\n";
    $content .= "Academic Year: " . $student['academic_year'] . "\n\n";
    $content .= "REGISTERED COURSES:\n";
    $content .= "==================\n\n";
    
    foreach ($registrations as $index => $reg) {
        $content .= ($index + 1) . ". " . $reg['course_code'] . " - " . $reg['course_title'] . " (" . $reg['course_unit'] . " units)\n";
    }
    
    echo $content;
}

function generateCSV($registrations, $student) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="course_registration_' . $student['Matric_No'] . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Student info header
    fputcsv($output, ['Student Name', $student['first_name'] . ' ' . $student['last_name']]);
    fputcsv($output, ['Matric Number', $student['Matric_No']]);
    fputcsv($output, ['Department', $student['Department']]);
    fputcsv($output, ['Level', $student['Level']]);
    fputcsv($output, []);
    
    // Course headers
    fputcsv($output, ['S/N', 'Course Code', 'Course Title', 'Units', 'Semester', 'Status']);
    
    foreach ($registrations as $index => $reg) {
        fputcsv($output, [
            $index + 1,
            $reg['course_code'],
            $reg['course_title'],
            $reg['course_unit'],
            $reg['course_semester'],
            'Approved'
        ]);
    }
    
    fclose($output);
}

function generateFormHTML($registrations, $student) {
    $html = '<div class="header">';
    $html .= '<h2>COURSE REGISTRATION FORM</h2>';
    $html .= '<p>Academic Year: ' . $student['academic_year'] . '</p>';
    $html .= '</div>';
    
    $html .= '<div class="student-info">';
    $html .= '<p><strong>Student Name:</strong> ' . $student['first_name'] . ' ' . $student['last_name'] . '</p>';
    $html .= '<p><strong>Matric Number:</strong> ' . $student['Matric_No'] . '</p>';
    $html .= '<p><strong>Department:</strong> ' . $student['Department'] . '</p>';
    $html .= '<p><strong>Level:</strong> ' . $student['Level'] . '</p>';
    $html .= '</div>';
    
    $html .= '<table>';
    $html .= '<thead><tr><th>S/N</th><th>Course Code</th><th>Course Title</th><th>Units</th><th>Semester</th><th>Status</th></tr></thead>';
    $html .= '<tbody>';
    
    foreach ($registrations as $index => $reg) {
        $html .= '<tr>';
        $html .= '<td>' . ($index + 1) . '</td>';
        $html .= '<td>' . $reg['course_code'] . '</td>';
        $html .= '<td>' . $reg['course_title'] . '</td>';
        $html .= '<td>' . $reg['course_unit'] . '</td>';
        $html .= '<td>' . $reg['course_semester'] . '</td>';
        $html .= '<td><span style="color: green;">Approved</span></td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '<p style="margin-top: 30px;"><strong>Date Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>';
    
    return $html;
}

function generateFormContent($registrations, $student) {
    $content = '<h2 style="text-align: center;">COURSE REGISTRATION FORM</h2>';
    $content .= '<p style="text-align: center;">Academic Year: ' . $student['academic_year'] . '</p>';
    
    $content .= '<p><strong>Student Name:</strong> ' . $student['first_name'] . ' ' . $student['last_name'] . '</p>';
    $content .= '<p><strong>Matric Number:</strong> ' . $student['Matric_No'] . '</p>';
    $content .= '<p><strong>Department:</strong> ' . $student['Department'] . '</p>';
    $content .= '<p><strong>Level:</strong> ' . $student['Level'] . '</p>';
    
    $content .= '<table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
    $content .= '<tr><th>S/N</th><th>Course Code</th><th>Course Title</th><th>Units</th><th>Semester</th><th>Status</th></tr>';
    
    foreach ($registrations as $index => $reg) {
        $content .= '<tr>';
        $content .= '<td>' . ($index + 1) . '</td>';
        $content .= '<td>' . $reg['course_code'] . '</td>';
        $content .= '<td>' . $reg['course_title'] . '</td>';
        $content .= '<td>' . $reg['course_unit'] . '</td>';
        $content .= '<td>' . $reg['course_semester'] . '</td>';
        $content .= '<td>Approved</td>';
        $content .= '</tr>';
    }
    
    $content .= '</table>';
    $content .= '<p style="margin-top: 30px;"><strong>Date Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>';
    
    return $content;
}
?>