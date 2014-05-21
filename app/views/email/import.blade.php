<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>
		<h1>Madison Import Log</h1>
		<p>New import run on {{ $date }}</p>
		<hr>
		<p><strong>Results</strong></p>
		<p>Successes: {{ $success }}</p>
		<p>Skipped: {{ $skipped }}</p>
		<p>Errors: {{ $error }}</p>
		<p>Old Files: {{ $old_files }}</p>
		<hr>
		<strong>Any relevant logs have been attached</strong>
	</body>
</html>