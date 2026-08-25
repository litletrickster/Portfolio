<?php
include 'db.php';

// Get selected year (default: current year)
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Set headers to download the file as CSV
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=BnB_Sales_Report_$selectedYear.csv");

$output = fopen('php://output', 'w');

// Write CSV column headers
fputcsv($output, ['Date', 'Daily Gross Sale', 'Detergents', 'Electricity', 'Water', 'Gas', 'Internet', 'Rent', 'Miscellaneous', 'Total Expenses', 'Net Daily Income', 'Remarks']);

// Fetch sales and expenses for the selected year
$query = "
    WITH OrderSums AS (
        SELECT DATE(Date_Created) AS Order_Date, 
               SUM(Total_Price) AS Daily_Gross
        FROM Orders
        WHERE YEAR(Date_Created) = ?
        GROUP BY Order_Date
    ),
    ExpenseSums AS (
        SELECT DATE(Date) AS Expense_Date,
               SUM(CASE WHEN Type = 'Detergents' THEN Amount ELSE 0 END) AS Detergents,
               SUM(CASE WHEN Type = 'Electricity' THEN Amount ELSE 0 END) AS Electricity,
               SUM(CASE WHEN Type = 'Water' THEN Amount ELSE 0 END) AS Water,
               SUM(CASE WHEN Type = 'Gas' THEN Amount ELSE 0 END) AS Gas,
               SUM(CASE WHEN Type = 'Internet' THEN Amount ELSE 0 END) AS Internet,
               SUM(CASE WHEN Type = 'Rent' THEN Amount ELSE 0 END) AS Rent,
               SUM(CASE WHEN Type = 'Miscellaneous' THEN Amount ELSE 0 END) AS Miscellaneous,
               SUM(Amount) AS Total_Expenses
        FROM Expense
        WHERE YEAR(Date) = ?
        GROUP BY Expense_Date
    )
    SELECT OrderSums.Order_Date, 
           OrderSums.Daily_Gross, 
           COALESCE(ExpenseSums.Detergents, 0) AS Detergents,
           COALESCE(ExpenseSums.Electricity, 0) AS Electricity,
           COALESCE(ExpenseSums.Water, 0) AS Water,
           COALESCE(ExpenseSums.Gas, 0) AS Gas,
           COALESCE(ExpenseSums.Internet, 0) AS Internet,
           COALESCE(ExpenseSums.Rent, 0) AS Rent,
           COALESCE(ExpenseSums.Miscellaneous, 0) AS Miscellaneous,
           COALESCE(ExpenseSums.Total_Expenses, 0) AS Total_Expenses,
           (OrderSums.Daily_Gross - COALESCE(ExpenseSums.Total_Expenses, 0)) AS Net_Daily_Income
    FROM OrderSums
    LEFT JOIN ExpenseSums ON OrderSums.Order_Date = ExpenseSums.Expense_Date
    ORDER BY OrderSums.Order_Date ASC;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $selectedYear, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        date('M d, Y', strtotime($row['Order_Date'])), // Date formatted
        '₱' . number_format($row['Daily_Gross'], 2), // Daily Gross Sales
        '₱' . number_format($row['Detergents'], 2), // Detergents Expense
        '₱' . number_format($row['Electricity'], 2), // Electricity
        '₱' . number_format($row['Water'], 2), // Water
        '₱' . number_format($row['Gas'], 2), // Gas
        '₱' . number_format($row['Internet'], 2), // Internet
        '₱' . number_format($row['Rent'], 2), // Rent
        '₱' . number_format($row['Miscellaneous'], 2), // Miscellaneous
        '₱' . number_format($row['Total_Expenses'], 2), // Total Expenses
        '₱' . number_format($row['Net_Daily_Income'], 2), // Net Daily Income
        '' // Placeholder for Remarks
    ]);
}

fclose($output);
exit();
?>
