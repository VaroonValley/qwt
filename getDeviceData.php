<?php
    include 'conn.php';

	function getTodayAverage($device_id){
		$conn = connectDB();
		$sql = "SELECT DATE(date) AS day, AVG(voltage) AS avg_voltage, AVG(amp) AS avg_amp, device_id
            FROM q_power
            WHERE date = CURDATE() AND device_id = ?
            GROUP BY DATE(date), device_id";

		$stmt = $conn->prepare($sql);

		// Bind the device_id parameter to the prepared statement
		$stmt->bind_param("s", $device_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$stmt->close(); // Close the statement
		$conn->close(); // Close the DB connection

		return json_encode($data);
	}

	function getWeeklyData($device_id){
		$conn = connectDB();
		$sql = "SELECT voltage, amp, device_id, date
        FROM q_power
        WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE() AND device_id = ?";

		$stmt = $conn->prepare($sql);

		// Bind the device_id parameter to the prepared statement
		$stmt->bind_param("s", $device_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$stmt->close(); // Close the statement
		$conn->close(); // Close the DB connection

		return json_encode($data);
	}
?>