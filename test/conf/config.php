<?php
    // Copy and rename to config.include

	// Set the global configuration for accessing the REST-API
	$config = array(
		"BASE_URI" => "https://api.maileon.com/1.0",
		"API_KEY" => "XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
		"PROXY_HOST" => "",
		"PROXY_PORT" => "",
		"THROW_EXCEPTION" => true,
		"TIMEOUT" => 5, // 5 seconds
		"DEBUG" => "false" // NEVER enable on production
	);
	