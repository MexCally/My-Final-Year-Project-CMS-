<?php
require_once 'config/db.php';

try {
    // Test fetching degree requirements
    $degreeRequirementsStmt = $pdo->prepare("SELECT category, required_credits FROM degree_requirementstbl");
    $degreeRequirementsStmt->execute();
    $degree_requirements = $degreeRequirementsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    echo "Degree requirements fetched successfully:\n";
    foreach ($degree_requirements as $category => $credits) {
        echo "$category: $credits credits\n";
    }

    // Calculate total degree credits
    $degree_total_credits = array_sum($degree_requirements);
    echo "\nTotal degree credits: $degree_total_credits\n";

    // Separate core and general education requirements
    $core_requirements = [];
    $gen_ed_requirements = [];

    foreach ($degree_requirements as $category => $credits) {
        if (in_array($category, ['Computer Science Core', 'Mathematics', 'Science Requirements'])) {
            $core_requirements[$category] = $credits;
        } elseif (in_array($category, ['English & Communication', 'Liberal Arts', 'Electives'])) {
            $gen_ed_requirements[$category] = $credits;
        }
    }

    echo "\nCore Requirements:\n";
    foreach ($core_requirements as $category => $credits) {
        echo "$category: $credits credits\n";
    }

    echo "\nGeneral Education Requirements:\n";
    foreach ($gen_ed_requirements as $category => $credits) {
        echo "$category: $credits credits\n";
    }

    echo "\nTest completed successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
