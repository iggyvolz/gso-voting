<?php
header("Content-Type: text/plain");
// Obtain current voter from Shibboleth
$voter="rjv8806";
// Only allow one voter at a time
flock($lock=fopen(__DIR__."/data/lock", "r+"), LOCK_EX);
// If the backup files exist, we have experienced a previous crash and we need to manually recover
if(is_file(__DIR__."/data/voters.bkp") || is_file(__DIR__."/data/votes.bkp")) {
	http_response_code(500);
	echo "The server has experienced a crash and your vote has not been recorded.  Please contact the system administrator immediately.";
	die();
}
// Create a backup file in case of crash
copy(__DIR__."/data/voters", __DIR__."/data/voters.bkp");
copy(__DIR__."/data/votes", __DIR__."/data/votes.bkp");
// Get list of remaining voters and current votes as array
$votes=array_map("trim", file(__DIR__."/data/votes"));
$voters=array_map("trim", file(__DIR__."/data/voters"));
// Find the current voter in the list of voters
$voter_key=array_search($voter, $voters);
if($voter_key === false) {
	http_response_code(403);
	echo "The user '$voter' is not on the list of valid voters.";
	unlink(__DIR__."/data/voters.bkp");
	unlink(__DIR__."/data/votes.bkp");
	die();
}
// Remove voter from list of voters
unset($voters[$voter_key]);
// Obtain the vote from php://input (base64 to ensure no file issues)
$votes[]=base64_encode($_POST["content"]);
// Sort the votes so we don't know which came from who
sort($votes);
// Ensure that empty string is not placed in the array
$votes=array_filter($votes, function(string $s):bool{return !empty($s);});
// Place the lists to the filesystem and remove backups
file_put_contents(__DIR__."/data/votes", implode(PHP_EOL, $votes));
file_put_contents(__DIR__."/data/voters", implode(PHP_EOL, $voters));
unlink(__DIR__."/data/voters.bkp");
unlink(__DIR__."/data/votes.bkp");
http_response_code(200);
echo "Your vote has been successfully cast.";