<?php
	function stylesheet_tag($filename, $id = false) {
		$timestamp = filemtime($filename);

		$id_part = $id ? "id=\"$id\"" : "";

		return "<link rel=\"stylesheet\" $id_part type=\"text/css\" href=\"$filename?$timestamp\"/>\n";
	}

	function javascript_tag($filename) {
		$query = "";

		if (!(strpos($filename, "?") === FALSE)) {
			$query = substr($filename, strpos($filename, "?")+1);
			$filename = substr($filename, 0, strpos($filename, "?"));
		}

		$timestamp = filemtime($filename);

		if ($query) $timestamp .= "&$query";

		return "<script type=\"text/javascript\" charset=\"utf-8\" src=\"$filename?$timestamp\"></script>\n";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Agriget - Installer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css">
		textarea { font-size : 12px; }
	</style>
	<?php
		echo stylesheet_tag("../css/default.css");
		echo javascript_tag("../lib/prototype.js");
		echo javascript_tag("../lib/dojo/dojo.js");
		echo javascript_tag("../lib/dojo/tt-rss-layer.js");
	?>
</head>
<body class="flat ttrss_utility installer">

<script type="text/javascript">
	require(['dojo/parser', "dojo/ready", 'dijit/form/Button','dijit/form/CheckBox', 'dijit/form/Form',
		'dijit/form/Select','dijit/form/TextBox','dijit/form/ValidationTextBox'],function(parser, ready){
		ready(function() {
			parser.parse();
		});
	});
</script>

<?php

	// could be needed because of existing config.php
	function define_default($param, $value) {
		//
	}

	function make_password($length = 12) {
		$password = "";
		$possible = "0123456789abcdfghjkmnpqrstvwxyzABCDFGHJKMNPQRSTVWXYZ*%+^";

		$i = 0;

		while ($i < $length) {

			try {
				$idx = function_exists("random_int") ? random_int(0, strlen($possible) - 1) : mt_rand(0, strlen($possible) - 1);
			} catch (Exception $e) {
				$idx = mt_rand(0, strlen($possible) - 1);
			}

			$char = substr($possible, $idx, 1);

			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}

		return $password;
	}


	function sanity_check($db_type) {
		$errors = array();

		if (version_compare(PHP_VERSION, '5.6.0', '<')) {
			array_push($errors, "PHP version 5.6.0 or newer required. You're using " . PHP_VERSION . ".");
		}

		if (!function_exists("curl_init") && !ini_get("allow_url_fopen")) {
			array_push($errors, "PHP configuration option allow_url_fopen is disabled, and CURL functions are not present. Either enable allow_url_fopen or install PHP extension for CURL.");
		}

		if (!function_exists("json_encode")) {
			array_push($errors, "PHP support for JSON is required, but was not found.");
		}

		if (!class_exists("PDO")) {
			array_push($errors, "PHP support for PDO is required but was not found.");
		}

		if (!function_exists("mb_strlen")) {
			array_push($errors, "PHP support for mbstring functions is required but was not found.");
		}

		if (!function_exists("hash")) {
			array_push($errors, "PHP support for hash() function is required but was not found.");
		}

		if (!function_exists("iconv")) {
			array_push($errors, "PHP support for iconv is required to handle multiple charsets.");
		}

		if (ini_get("safe_mode")) {
			array_push($errors, "PHP safe mode setting is obsolete and not supported by agriget.");
		}

		if (!class_exists("DOMDocument")) {
			array_push($errors, "PHP support for DOMDocument is required, but was not found.");
		}

		return $errors;
	}

	function print_error($msg) {
		print "<div class='alert alert-error'>$msg</div>";
	}

	function print_notice($msg) {
		print "<div class=\"alert alert-info\">$msg</div>";
	}

	function pdo_connect($host, $user, $pass, $db, $type, $port = false) {

		$db_port = $port ? ';port=' . $port : '';
		$db_host = $host ? ';host=' . $host : '';

		try {
			$pdo = new PDO($type . ':dbname=' . $db . $db_host . $db_port,
				$user,
				$pass);

			return $pdo;
		} catch (Exception $e) {
		    print "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
		    return null;
        }
	}

	function make_config($DB_TYPE, $DB_HOST, $DB_USER, $DB_NAME, $DB_PASS,
			$DB_PORT, $SELF_URL_PATH) {

		$data = explode("\n", file_get_contents("../config.php-dist"));

		$rv = "";

		$finished = false;

		foreach ($data as $line) {
			if (preg_match("/define\('DB_TYPE'/", $line)) {
				$rv .= "\tdefine('DB_TYPE', '$DB_TYPE');\n";
			} else if (preg_match("/define\('DB_HOST'/", $line)) {
				$rv .= "\tdefine('DB_HOST', '$DB_HOST');\n";
			} else if (preg_match("/define\('DB_USER'/", $line)) {
				$rv .= "\tdefine('DB_USER', '$DB_USER');\n";
			} else if (preg_match("/define\('DB_NAME'/", $line)) {
				$rv .= "\tdefine('DB_NAME', '$DB_NAME');\n";
			} else if (preg_match("/define\('DB_PASS'/", $line)) {
				$rv .= "\tdefine('DB_PASS', '$DB_PASS');\n";
			} else if (preg_match("/define\('DB_PORT'/", $line)) {
				$rv .= "\tdefine('DB_PORT', '$DB_PORT');\n";
			} else if (preg_match("/define\('SELF_URL_PATH'/", $line)) {
				$rv .= "\tdefine('SELF_URL_PATH', '$SELF_URL_PATH');\n";
			} else if (!$finished) {
				$rv .= "$line\n";
			}

			if (preg_match("/\?\>/", $line)) {
				$finished = true;
			}
		}

		return $rv;
	}

	function is_server_https() {
		return (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
	}

	function make_self_url_path() {
		$url_path = (is_server_https() ? 'https://' :  'http://') . $_SERVER["HTTP_HOST"] . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

		return $url_path;
	}

?>

<h1>Agriget Installer</h1>

<div class='content'>

<?php

	if (file_exists("../data/config.php")) {
		require "../data/config.php";

		if (!defined('_INSTALLER_IGNORE_CONFIG_CHECK')) {
			print_error("Error: config.php already exists in data directory; aborting.");

			print "<form method='GET' action='../index.php'>
				<button type='submit' dojoType='dijit.form.Button' class='alt-primary'>Return to Agriget</button>
				</form>";
			exit;
		}
	}

	@$op = $_REQUEST['op'];

	@$DB_HOST = strip_tags($_POST['DB_HOST']);
	@$DB_TYPE = strip_tags($_POST['DB_TYPE']);
	@$DB_USER = strip_tags($_POST['DB_USER']);
	@$DB_NAME = strip_tags($_POST['DB_NAME']);
	@$DB_PASS = strip_tags($_POST['DB_PASS']);
	@$DB_PORT = strip_tags($_POST['DB_PORT']);
	@$SELF_URL_PATH = strip_tags($_POST['SELF_URL_PATH']);

	if (!$SELF_URL_PATH) {
		$SELF_URL_PATH = preg_replace("/\/install\/$/", "/", make_self_url_path());
	}
?>

<form action="" method="post">
	<input type="hidden" name="op" value="testconfig">

	<h2>Database settings</h2>

	<?php
		$issel_pgsql = $DB_TYPE == "pgsql" ? "selected='selected'" : "";
		$issel_mysql = $DB_TYPE == "mysql" ? "selected='selected'" : "";
	?>

	<fieldset>
		<label>Database type:</label>
		<select name="DB_TYPE" dojoType="dijit.form.Select">
			<option <?php echo $issel_pgsql ?> value="pgsql">PostgreSQL</option>
			<option <?php echo $issel_mysql ?> value="mysql">MySQL</option>
		</select>
	</fieldset>

	<fieldset>
		<label>Username:</label>
		<input dojoType="dijit.form.TextBox" required name="DB_USER" size="20" value="<?php echo $DB_USER ?>"/>
	</fieldset>

	<fieldset>
		<label>Password:</label>
		<input dojoType="dijit.form.TextBox" name="DB_PASS" size="20" type="password" value="<?php echo $DB_PASS ?>"/>
	</fieldset>

	<fieldset>
		<label>Database name:</label>
		<input dojoType="dijit.form.TextBox" required name="DB_NAME" size="20" value="<?php echo $DB_NAME ?>"/>
	</fieldset>

	<fieldset>
		<label>Host name:</label>
		<input dojoType="dijit.form.TextBox" name="DB_HOST" size="20" value="<?php echo $DB_HOST ?>"/>
		<span class="hint">If needed</span>
	</fieldset>

	<fieldset>
		<label>Port:</label>
		<input dojoType="dijit.form.TextBox" name="DB_PORT" type="number" size="20" value="<?php echo $DB_PORT ?>"/>
		<span class="hint">Usually 3306 for MySQL or 5432 for PostgreSQL</span>
	</fieldset>

	<h2>Other settings</h2>

	<p>This should be set to the location your Agriget will be available on.</p>

	<fieldset>
		<label>Agriget URL:</label>
		<input dojoType="dijit.form.TextBox" type="url" name="SELF_URL_PATH" placeholder="<?php echo $SELF_URL_PATH; ?>" value="<?php echo $SELF_URL_PATH ?>"/>
	</fieldset>

	<p><button type="submit" dojoType="dijit.form.Button" class="alt-primary">Test configuration</button></p>
</form>

<?php if ($op == 'testconfig') { ?>

	<h2>Checking configuration</h2>

	<?php
		$errors = sanity_check($DB_TYPE);

		if (count($errors) > 0) {
			print "<p>Some configuration tests failed. Please correct them before continuing.</p>";

			print "<ul>";

			foreach ($errors as $error) {
				print "<li style='color : red'>$error</li>";
			}

			print "</ul>";

			exit;
		}

		$notices = array();

		if (!function_exists("curl_init")) {
			array_push($notices, "It is highly recommended to enable support for CURL in PHP.");
		}

		if (function_exists("curl_init") && ini_get("open_basedir")) {
			array_push($notices, "CURL and open_basedir combination breaks support for HTTP redirects. See the FAQ for more information.");
		}

		if (!function_exists("idn_to_ascii")) {
			array_push($notices, "PHP support for Internationalization Functions is required to handle Internationalized Domain Names.");
		}

        if ($DB_TYPE == "mysql" && !function_exists("mysqli_connect")) {
            array_push($notices, "PHP extension for MySQL (mysqli) is missing. This may prevent legacy plugins from working.");
        }

        if ($DB_TYPE == "pgsql" && !function_exists("pg_connect")) {
			array_push($notices, "PHP extension for PostgreSQL is missing. This may prevent legacy plugins from working.");
        }

		if (count($notices) > 0) {
			print_notice("Configuration check succeeded with minor problems:");

			print "<ul>";

			foreach ($notices as $notice) {
				print "<li>$notice</li>";
			}

			print "</ul>";
		} else {
			print_notice("Configuration check succeeded.");
		}

	?>

	<h2>Checking database</h2>

	<?php
		$pdo = pdo_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_TYPE, $DB_PORT);

		if (!$pdo) {
			print_error("Unable to connect to database using specified parameters (driver: $DB_TYPE).");
			exit;
		}

		print_notice("Database test succeeded.");
	?>

	<h2>Initialize database</h2>

	<p>Before you can start using agriget, database needs to be initialized. Click on the button below to do that now.</p>

	<?php
		$res = $pdo->query("SELECT true FROM ttrss_feeds");

		if ($res && $res->fetch()) {
			print_error("Some agriget data already exists in this database. If you continue with database initialization your current data <b>WILL BE LOST</b>.");
			$need_confirm = true;
		} else {
			$need_confirm = false;
		}
	?>

	<table><tr><td>
	<form method="post">
		<input type="hidden" name="op" value="installschema">

		<input type="hidden" name="DB_USER" value="<?php echo $DB_USER ?>"/>
		<input type="hidden" name="DB_PASS" value="<?php echo $DB_PASS ?>"/>
		<input type="hidden" name="DB_NAME" value="<?php echo $DB_NAME ?>"/>
		<input type="hidden" name="DB_HOST" value="<?php echo $DB_HOST ?>"/>
		<input type="hidden" name="DB_PORT" value="<?php echo $DB_PORT ?>"/>
		<input type="hidden" name="DB_TYPE" value="<?php echo $DB_TYPE ?>"/>
		<input type="hidden" name="SELF_URL_PATH" value="<?php echo $SELF_URL_PATH ?>"/>

		<p>
		<?php if ($need_confirm) { ?>
			<button onclick="return confirm('Please read the warning above. Continue?')" type="submit"
					   class="alt-danger" dojoType="dijit.form.Button">Initialize database</button>
		<?php } else { ?>
			<button type="submit" class="alt-danger" dojoType="dijit.form.Button">Initialize database</button>
		<?php } ?>
		</p>
	</form>

	</td><td>
	<form method="post">
		<input type="hidden" name="DB_USER" value="<?php echo $DB_USER ?>"/>
		<input type="hidden" name="DB_PASS" value="<?php echo $DB_PASS ?>"/>
		<input type="hidden" name="DB_NAME" value="<?php echo $DB_NAME ?>"/>
		<input type="hidden" name="DB_HOST" value="<?php echo $DB_HOST ?>"/>
		<input type="hidden" name="DB_PORT" value="<?php echo $DB_PORT ?>"/>
		<input type="hidden" name="DB_TYPE" value="<?php echo $DB_TYPE ?>"/>
		<input type="hidden" name="SELF_URL_PATH" value="<?php echo $SELF_URL_PATH ?>"/>

		<input type="hidden" name="op" value="skipschema">

		<p><button type="submit" dojoType="dijit.form.Button">Skip initialization</button></p>
	</form>

	</td></tr></table>

	<?php

		} else if ($op == 'installschema' || $op == 'skipschema') {

			$pdo = pdo_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_TYPE, $DB_PORT);

			if (!$pdo) {
				print_error("Unable to connect to database using specified parameters.");
				exit;
			}

			if ($op == 'installschema') {

				print "<h2>Initializing database...</h2>";

				$lines = explode(";", preg_replace("/[\r\n]/", "",
                    file_get_contents("../schema/ttrss_schema_".basename($DB_TYPE).".sql")));

				foreach ($lines as $line) {
					if (strpos($line, "--") !== 0 && $line) {
						$res = $pdo->query($line);

						if (!$res) {
							print_notice("Query: $line");
							print_error("Error: " . implode(", ", $this->pdo->errorInfo()));
                        }
					}
				}

				print_notice("Database initialization completed.");

			} else {
				print_notice("Database initialization skipped.");
			}

			print "<h2>Generated configuration file</h2>";

			print "<p>Copy following text and save as <code>config.php</code> in the data directory. It is suggested to read through the file to the end in case you need any options changed fom default values.</p>";

			print "<p>After copying the file, you will be able to login with default username and password combination: <code>admin</code> and <code>password</code>. Don't forget to change the password immediately!</p>"; ?>

			<form action="" method="post">
				<input type="hidden" name="op" value="saveconfig">
				<input type="hidden" name="DB_USER" value="<?php echo $DB_USER ?>"/>
				<input type="hidden" name="DB_PASS" value="<?php echo $DB_PASS ?>"/>
				<input type="hidden" name="DB_NAME" value="<?php echo $DB_NAME ?>"/>
				<input type="hidden" name="DB_HOST" value="<?php echo $DB_HOST ?>"/>
				<input type="hidden" name="DB_PORT" value="<?php echo $DB_PORT ?>"/>
				<input type="hidden" name="DB_TYPE" value="<?php echo $DB_TYPE ?>"/>
				<input type="hidden" name="SELF_URL_PATH" value="<?php echo $SELF_URL_PATH ?>"/>
			<?php print "<textarea rows='20' style='width : 100%'>";
			echo make_config($DB_TYPE, $DB_HOST, $DB_USER, $DB_NAME, $DB_PASS,
				$DB_PORT, $SELF_URL_PATH);
			print "</textarea>"; ?>

			<hr/>

			<?php if (is_writable("../data")) { ?>
				<p>We can also try saving the file automatically now.</p>

				<p><button type="submit" dojoType='dijit.form.Button' class='alt-primary'>Save configuration</button></p>
				</form>
			<?php } else {
				print_error("Unfortunately, data directory is not writable, so we're unable to save config.php automatically.");
			}

		   print_notice("You can generate the file again by changing the form above.");

		} else if ($op == "saveconfig") {

			print "<h2>Saving configuration file to parent directory...</h2>";

			if (!file_exists("../data/config.php")) {

				$fp = fopen("../data/config.php", "w");

				if ($fp) {
					$written = fwrite($fp, make_config($DB_TYPE, $DB_HOST,
						$DB_USER, $DB_NAME, $DB_PASS,
						$DB_PORT, $SELF_URL_PATH));

					if ($written > 0) {
						print_notice("Successfully saved config.php. You can try <a href=\"..\">loading agriget now</a>.");

					} else {
						print_notice("Unable to write into config.php in data directory.");
					}

					fclose($fp);
				} else {
					print_error("Unable to open config.php in data directory for writing.");
				}
			} else {
				print_error("config.php already present in data directory, refusing to overwrite.");
			}
		}
	?>

</div>

</body>
</html>
