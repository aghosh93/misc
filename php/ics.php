<?php
// use "/r/n" for CRLF terminator

/**
 * Returns string representing an event component according to iCalendar
 * protocol
 */
function vEvent($startDate, $endDate, $name, $description, $location,
		$lat, $lng, $timestamp, $eid) {

	$eol = PHP_EOL;
	$result = <<<EO
BEGIN:VEVENT
DTSTART:$startDate
DTEND:$endDate
SUMMARY:$name
DESCRIPTION:$description
LOCATION:$location
GEO:$lat;$lng
DTSTAMP:$timestamp
UID:$eid@test.website.com
END:VEVENT$eol
EO;
	return $result;

}

/**
 * Returns string representing an all day event component according to
 * iCalendar protocol
 */
function vEventAllDay($date, $name, $description, $location, $lat, $lng,
		$timestamp, $eid) {

	$eol = PHP_EOL;
	$result = <<<EO
BEGIN:VEVENT
DTSTART;VALUE=DATE:$date
DTEND;VALUE=DATE:$date
SUMMARY:$name
DESCRIPTION:$description
LOCATION:$location
GEO:$lat;$lng
DTSTAMP:$timestamp
UID:$eid-$date@test.website.com
END:VEVENT$eol
EO;
	return $result;

}

/**
 * Creates all event components from database
 */
function getEvents() {
	$result = "";
	$db = connectToDB();
	$query = "select * from events;";
	$data = mysqli_query($db, $query);
	if (mysqli_num_rows($data) != 0) {
		while ($row = mysqli_fetch_array($data, MYSQLI_ASSOC)) {
			// print_r($row);
			$result .= parseRow($row);
		}
	}
	mysqli_close($db);
	return $result;
}

/**
 * Parse sql date/time format to ical format
 */
function parseDateTime($string) {
	$dateTime = new DateTime($string);
	return $dateTime->format('Ymd').'T'.$dateTime->format('His');
}

/**
 * Parse sql date format to ical format (used for all day event)
 */
function parseDate($string) {
	$dateTime = new DateTime($string);
	return $dateTime->format('Ymd');
}

function multDays($startDate, $endDate) {
	$result = array();
	$sDateTime = new DateTime($startDate);
	$eDateTime = (new DateTime($endDate))->add(new DateInterval("P1D"));
	$diff = $eDateTime->diff($sDateTime);
	for ($i = new DateTime($startDate); $i != $eDateTime; $i->add(new DateInterval("P1D"))) {
		array_push($result, $i->format('Ymd'));
	}
	// print_r($result);
	return $result;
}

function encodeString($string) {
	if (strlen($string) < 70) {
		return $string;
	}

	$result = '';
	for ($i = 0; $substring = substr($string, $i, 70); $i += 70) {
		$result .= $substring . PHP_EOL . ' ';
	}
	return trim($result);
}

/**
 * Looks at row given by db query
 */
function parseRow($row) {
	$startDate = parseDateTime($row['start_date']);
	$endDate = parseDateTime($row['end_date']);
	$timestamp = parseDateTime($row['created']);
	$name = encodeString($row['name']);
	$description = encodeString($row['description']);
	$location = encodeString($row['location']);

	if ($row['all_day'] == 0) {
		return vEvent($startDate, $endDate, $name, $description,
			$location, $row['lat'], $row['lng'], $timestamp, $row['eid']);
	} else {
		$days = multDays($row['start_date'], $row['end_date']);
		$result = "";
		foreach ($days as $day) {
			$result .= vEventAllDay($day, $name, $description,
				$location, $row['lat'], $row['lng'], $timestamp, $row['eid']);
		}
		return $result;
	}
}

/**
 * Creates .ics file with one calendar object.
 */
function createICS() {
	$eol = PHP_EOL;
	$icsBody = "";
	$icsBody .= "BEGIN:VCALENDAR".$eol;
	// ics vCalendar object properties
	$icsBody .= "PRODID:-//UMD//geodude//EN".$eol;
	$icsBody .= "VERSION:2.0".$eol;
	// events components
	$icsBody .= getEvents();
	// $icsBody .= vEvent("20140213T080000", "20140213T090000",
	// 	"Test Description", "Test Location", 0, 0, "20140213T010700", 10);
	// closing vCalendar object
	$icsBody .= "END:VCALENDAR".$eol;
	// creating and saving file
	$filePath = "files/"; // change the directory of stored file Ex: files/
	$filename = 'calendar.ics'; // filename for ics file. Ex: *.ics or *.ical
	$file = fopen($filePath.$filename, 'w');
	if ($file) {
		fwrite($file, $icsBody);
		fclose($file);
	}
// // TO DO delete on completion
// 		echo "<h1>the file is saved in $filePath$filename</h1>";
// 	} else {
// // TO DO delete on completion
// 		echo "Could not create $filePath$filename file <br>";
// 		echo "Current user: " . get_current_user();
// 	}

}

// TO DO delete on completion
// createICS();
