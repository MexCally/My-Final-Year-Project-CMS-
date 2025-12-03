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
                          WHERE cr.student_id = ? AND cr.approval_status = 'Registered'
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
    // For now, generate HTML that can be printed as PDF
    $html = generateFormHTML($registrations, $student);
    
    header('Content-Type: text/html');
    header('Content-Disposition: inline; filename="course_registration_' . $student['Matric_No'] . '.html"');
    
    echo "<!DOCTYPE html><html><head>
    <title>Course Registration Form - " . $student['Matric_No'] . "</title>
    <style>
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
            @page { size: A4; margin: 0.5in; }
        }
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
    </style>
    <script>
        window.onload = function() {
            document.getElementById('printBtn').onclick = function() {
                window.print();
            };
        };
    </script>
    </head><body>
    <div class='no-print' style='text-align: center; margin-bottom: 20px;'>
        <button id='printBtn' style='background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;'>Print as PDF</button>
        <p style='font-size: 12px; color: #666;'>Click 'Print as PDF' and select 'Save as PDF' in the print dialog</p>
    </div>
    $html
    </body></html>";
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
    $totalUnits = array_sum(array_column($registrations, 'course_unit'));
    $matric = str_split($student['Matric_No']);
    
    $html = '
    <div style="max-width: 800px; margin: 0 auto; padding: 10px; font-family: Arial, sans-serif; font-size: 11px; line-height: 1.2;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: flex; justify-content: center; align-items: center;">
                <img src="../assets/img/logo1.jpg" alt="College Logo" style="width: 120px; height: 120px; border: 2px solid #000;">
            </div>
            <h3 style="margin: 20px 0 10px 0; font-size: 14px; font-weight: bold;">COURSE REGISTRATION FORM</h3>
            <p style="margin: 0; font-size: 12px;">PLEASE COMPLETE THE FORM NEATLY</p>
        </div>

        <!-- Student Info Section -->
        <div style="margin-bottom: 20px;">
            <div style="display: flex; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">DEPARTMENT:</label>
                    <div style="border-bottom: 1px solid #000; padding: 2px 5px; margin-top: 2px; min-height: 18px;">' . htmlspecialchars($student['Department']) . '</div>
                </div>
            </div>
            
            <div style="display: flex; margin-bottom: 10px;">
                <div style="flex: 1; margin-right: 20px;">
                    <label style="font-size: 12px; font-weight: bold;">STUDENT REGISTRATION NUMBER/MATRIC NUMBER</label>
                    <div style="display: flex; margin-top: 5px;">';
    
    // Matric number boxes
    for ($i = 0; $i < 6; $i++) {
        $char = isset($matric[$i]) ? $matric[$i] : '';
        $html .= '<div style="width: 30px; height: 30px; border: 1px solid #000; margin-right: 2px; text-align: center; line-height: 28px; font-weight: bold;">' . $char . '</div>';
    }
    
    $html .= '
                    </div>
                </div>
            </div>
            
            <div style="display: flex; margin-bottom: 15px;">
                <div style="flex: 1; margin-right: 20px;">
                    <div style="display: flex; margin-bottom: 10px;">
                        <div style="flex: 1; margin-right: 10px;">
                            <label style="font-size: 12px; font-weight: bold;">SURNAME</label>
                            <div style="border: 1px solid #000; padding: 5px; margin-top: 2px; min-height: 20px;">' . htmlspecialchars($student['last_name']) . '</div>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                    <label style="font-size: 12px; font-weight: bold;">SEMESTER</label>
                                    <div style="margin-top: 5px;">
                                        <label style="font-size: 11px;">FIRST <input type="checkbox" ' . (strpos($registrations[0]['semester'] ?? '', 'First') !== false ? 'checked' : '') . ' style="margin-left: 5px;"></label>
                                        <label style="font-size: 11px; margin-left: 15px;">SECOND <input type="checkbox" ' . (strpos($registrations[0]['semester'] ?? '', 'Second') !== false ? 'checked' : '') . ' style="margin-left: 5px;"></label>
                                    </div>
                                </div>
                                <div style="margin-left: 20px;">
                                    <label style="font-size: 12px; font-weight: bold;">BLOCK</label>
                                    <div style="margin-top: 5px;">
                                        <label style="font-size: 11px;">FIRST <input type="checkbox" style="margin-left: 5px;"></label>
                                        <label style="font-size: 11px; margin-left: 15px;">SECOND <input type="checkbox" style="margin-left: 5px;"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label style="font-size: 12px; font-weight: bold;">OTHER NAMES</label>
                        <div style="border: 1px solid #000; padding: 5px; margin-top: 2px; min-height: 20px;">' . htmlspecialchars($student['first_name']) . '</div>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; margin-bottom: 20px;">
                <div style="flex: 1; margin-right: 20px;">
                    <label style="font-size: 12px; font-weight: bold;">LEVEL (ND1/ND2):</label>
                    <div style="border-bottom: 1px solid #000; padding: 2px 5px; margin-top: 2px; min-height: 18px; display: inline-block; min-width: 200px;">' . htmlspecialchars($student['Level'] . ' - ' . $registrations[0]['semester']) . '</div>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">SESSION:</label>
                    <div style="border-bottom: 1px solid #000; padding: 2px 5px; margin-top: 2px; min-height: 18px; display: inline-block; min-width: 150px;">' . htmlspecialchars($student['academic_year']) . '</div>
                </div>
            </div>
        </div>

        <!-- Course Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 8px; font-size: 11px; font-weight: bold; text-align: center; background-color: #f0f0f0;">COURSE<br>CODE</th>
                    <th style="border: 1px solid #000; padding: 8px; font-size: 11px; font-weight: bold; text-align: center; background-color: #f0f0f0;">COURSE TITLE</th>
                    <th style="border: 1px solid #000; padding: 8px; font-size: 11px; font-weight: bold; text-align: center; background-color: #f0f0f0;">NO. OF<br>UNITS</th>
                    <th style="border: 1px solid #000; padding: 8px; font-size: 11px; font-weight: bold; text-align: center; background-color: #f0f0f0;">STUDENT\'S<br>SIGNATURE</th>
                    <th style="border: 1px solid #000; padding: 8px; font-size: 11px; font-weight: bold; text-align: center; background-color: #f0f0f0;">LECTURER\'S<br>SIGNATURE &<br>DATE</th>
                </tr>
            </thead>
            <tbody>';
    
    // Course rows
    foreach ($registrations as $reg) {
        $html .= '
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">' . htmlspecialchars($reg['course_code']) . '</td>
                    <td style="border: 1px solid #000; padding: 4px; font-size: 10px;">' . htmlspecialchars($reg['course_title']) . '</td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px;">' . htmlspecialchars($reg['course_unit']) . '</td>
                    <td style="border: 1px solid #000; padding: 4px; height: 25px;"></td>
                    <td style="border: 1px solid #000; padding: 4px; height: 25px;"></td>
                </tr>';
    }
    
    // Empty rows to fill the form (reduced to fit one page)
    for ($i = count($registrations); $i < 8; $i++) {
        $html .= '
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 4px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 4px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 4px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 4px; height: 20px;"></td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <!-- Total Units -->
        <div style="text-align: center; margin-bottom: 20px;">
            <strong style="font-size: 12px;">SEMESTER TOTAL UNITS = ' . $totalUnits . '</strong>
        </div>
        
        <!-- Official Use Section -->
        <div style="border: 2px solid #000; padding: 15px;">
            <div style="font-size: 12px; font-weight: bold; margin-bottom: 15px;">FOR OFFICIAL USE:</div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <span style="font-size: 11px;">BURSAR\'S SIGNATURE:</span>
                    <div style="border-bottom: 1px solid #000; margin-top: 5px; height: 20px; width: 200px;"></div>
                </div>
                <div style="flex: 1; text-align: right;">
                    <span style="font-size: 11px;">DATE:</span>
                    <div style="border-bottom: 1px solid #000; margin-top: 5px; height: 20px; width: 150px; display: inline-block;"></div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <div style="flex: 1;">
                    <span style="font-size: 11px;">DEAN\'S SIGNATURE:</span>
                    <div style="border-bottom: 1px solid #000; margin-top: 5px; height: 20px; width: 200px;"></div>
                </div>
                <div style="flex: 1; text-align: right;">
                    <span style="font-size: 11px;">DATE:</span>
                    <div style="border-bottom: 1px solid #000; margin-top: 5px; height: 20px; width: 150px; display: inline-block;"></div>
                </div>
            </div>
        </div>
    </div>';
    
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