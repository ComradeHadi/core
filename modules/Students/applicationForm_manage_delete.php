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

@session_start();

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Students/applicationForm_manage_delete.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Students/applicationForm_manage.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID']."'>".__($guid, 'Manage Applications')."</a> > </div><div class='trailEnd'>".__($guid, 'Delete Form').'</div>';
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $gibbonApplicationFormID = $_GET['gibbonApplicationFormID'];
    $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
    $search = $_GET['search'];
    if ($gibbonApplicationFormID == '' or $gibbonSchoolYearID == '') {
        echo "<div class='error'>";
        echo __($guid, 'You have not specified one or more required parameters.');
        echo '</div>';
    } else {
        try {
            $data = array('gibbonApplicationFormID' => $gibbonApplicationFormID);
            $sql = 'SELECT * FROM gibbonApplicationForm WHERE gibbonApplicationFormID=:gibbonApplicationFormID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo __($guid, 'The selected record does not exist, or you do not have access to it.');
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();

            echo "<div class='linkTop'>";
            if ($search != '') {
                echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Students/applicationForm_manage.php&gibbonSchoolYearID=$gibbonSchoolYearID&search=$search'>".__($guid, 'Back to Search Results').'</a>';
            }
            echo '</div>'; ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/applicationForm_manage_deleteProcess.php?gibbonApplicationFormID=$gibbonApplicationFormID&search=$search" ?>">
				<table class='smallIntBorder fullWidth' cellspacing='0'>	
					<tr>
						<td> 
							<b><?php echo __($guid, 'Are you sure you want to delete this record?'); ?></b><br/>
							<span style="font-size: 90%; color: #cc0000"><i><?php echo __($guid, 'This operation cannot be undone, and may lead to loss of vital data in your system. PROCEED WITH CAUTION!'); ?></span>
						</td>
						<td class="right">
							
						</td>
					</tr>
					<tr>
						<td> 
							<input name="gibbonSchoolYearID" id="gibbonSchoolYearID" value="<?php echo $gibbonSchoolYearID ?>" type="hidden">
							<input name="gibbonApplicationFormID" id="gibbonApplicationFormID" value="<?php echo $gibbonApplicationFormID ?>" type="hidden">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="submit" value="<?php echo __($guid, 'Yes'); ?>">
						</td>
						<td class="right">
							
						</td>
					</tr>
				</table>
			</form>
			<?php

        }
    }
}
?>