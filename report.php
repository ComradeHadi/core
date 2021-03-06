<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//Gibbon system-wide includes
include './functions.php';
include './config.php';
include './version.php';

//New PDO DB connection
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

@session_start();

//Check to see if system settings are set from databases
if ($_SESSION[$guid]['systemSettingsSet'] == false) {
    getSystemSettings($guid, $connection2);
}
//If still false, show warning, otherwise display page
if ($_SESSION[$guid]['systemSettingsSet'] == false) {
    echo __($guid, 'System Settings are not set: the system cannot be displayed');
} else {
    ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title><?php echo $_SESSION[$guid]['organisationNameShort'].' - '.$_SESSION[$guid]['systemName'] ?></title>
			<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
			<meta http-equiv="content-language" content="en"/>
			<meta name="author" content="Ross Parker, International College Hong Kong"/>
			<meta name="robots" content="none"/>

			<?php
            //Set theme
            $themeCSS = "<link rel='stylesheet' type='text/css' href='./themes/Default/css/main.css' />";
			if ($_SESSION[$guid]['i18n']['rtl'] == 'Y') {
				$themeCSS .= "<link rel='stylesheet' type='text/css' href='./themes/Default/css/main_rtl.css' />";
			}
			$themeJS = "<script type='text/javascript' src='./themes/Default/js/common.js'></script>";
			$_SESSION[$guid]['gibbonThemeID'] = '001';
			$_SESSION[$guid]['gibbonThemeName'] = 'Default';

			if ($_SESSION[$guid]['gibbonThemeIDPersonal'] != null) {
				$dataTheme = array('gibbonThemeIDPersonal' => $_SESSION[$guid]['gibbonThemeIDPersonal']);
				$sqlTheme = 'SELECT * FROM gibbonTheme WHERE gibbonThemeID=:gibbonThemeIDPersonal';
			} else {
				$dataTheme = array();
				$sqlTheme = "SELECT * FROM gibbonTheme WHERE active='Y'";
			}
			$resultTheme = $connection2->prepare($sqlTheme);
			$resultTheme->execute($dataTheme);
			if ($resultTheme->rowCount() == 1) {
				$rowTheme = $resultTheme->fetch();
				$themeCSS = "<link rel='stylesheet' type='text/css' href='./themes/".$rowTheme['name']."/css/main.css' />";
				if ($_SESSION[$guid]['i18n']['rtl'] == 'Y') {
					$themeCSS .= "<link rel='stylesheet' type='text/css' href='./themes/".$rowTheme['name']."/css/main_rtl.css' />";
				}
				$themeCJS = "<script type='text/javascript' src='./themes/".$rowTheme['name']."/js/common.js'></script>";
				$_SESSION[$guid]['gibbonThemeID'] = $rowTheme['gibbonThemeID'];
				$_SESSION[$guid]['gibbonThemeName'] = $rowTheme['name'];
			}
			echo $themeCSS;
			echo $themeJS;

            //Set module CSS & JS
            $moduleCSS = "<link rel='stylesheet' type='text/css' href='./modules/".$_SESSION[$guid]['module']."/css/module.css' />";
			$moduleJS = "<script type='text/javascript' src='./modules/".$_SESSION[$guid]['module']."/js/module.js'></script>";
			echo $moduleCSS;
			echo $moduleJS;

            //Set timezone from session variable
            date_default_timezone_set($_SESSION[$guid]['timezone']);
   			?>

			<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico"/>
			<script type="text/javascript" src="./lib/LiveValidation/livevalidation_standalone.compressed.js"></script>

			<?php
            if ($_SESSION[$guid]['analytics'] != '') {
                echo $_SESSION[$guid]['analytics'];
            }
   			 ?>
		</head>
		<body style="background: none">
			<div id="wrap-report" style="width:750px">
				<div id="header-report">
					<div style="width:400px; font-size: 100%; float: right">
						<?php
                        echo "<div style='padding-top: 10px'>";
						echo "<p style='margin-bottom: 0; padding-bottom: 0'>";
						echo sprintf(__($guid, 'This printout contains information that is the property of %1$s. If you find this report, and do not have permission to read it, please return it to %2$s (%3$s). In the event that it cannot be returned, please destroy it.'), $_SESSION[$guid]['organisationName'], $_SESSION[$guid]['organisationAdministratorName'], $_SESSION[$guid]['organisationAdministratorEmail']);
						echo '</p>';
						echo '</div>';
						?>
					</div>
					<div id="header-logo-report" style="text-align: right">
						<img height='75px' width='300px' alt="Logo" src="<?php echo $_SESSION[$guid]['absoluteURL'].'/'.$_SESSION[$guid]['organisationLogo'];?>"/>
					</div>
				</div>
				<div id="content-wrap-report" style="min-height: 500px">

					<?php
                    $_SESSION[$guid]['address'] = $_GET['q'];
					$_SESSION[$guid]['module'] = getModuleName($_SESSION[$guid]['address']);
					$_SESSION[$guid]['action'] = getActionName($_SESSION[$guid]['address']);

					if (strstr($_SESSION[$guid]['address'], '..') != false) {
						echo "<div class='error'>";
						echo __($guid, 'Illegal address detected: access denied.');
						echo '</div>';
					} else {
						if (is_file('./'.$_SESSION[$guid]['address'])) {
							include './'.$_SESSION[$guid]['address'];
						} else {
							include './error.php';
						}
					}
					?>
				</div>
				<div id="footer-report">
					<?php echo sprintf(__($guid, 'Created by %1$s (%2$s) at %3$s on %4$s.'), $_SESSION[$guid]['username'], $_SESSION[$guid]['organisationNameShort'], date('H:i'), date($_SESSION[$guid]['i18n']['dateFormatPHP']));?>
				</div>
			</div>
		</body>
	</html>
	<?php
}
?>
