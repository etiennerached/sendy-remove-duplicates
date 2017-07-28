<?php 
/**
* ***** BEGIN LICENSE BLOCK *****
* Version: MPL 2.0
*
* The contents of this file are subject to the Mozilla Public License Version
* 2.0 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Initial Developer of the Original Code is
* Etienne Rached.
* Portions created by the Initial Developer are Copyright (C) 2017
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
* Etienne Rached
* https://github.com/etiennerached
* http://www.tech-and-dev.com/2017/07/removing-duplicates-from-different-lists-sendy.html
*
* ***** END LICENSE BLOCK *****
**/
?>
<?php include('includes/header.php');?>         
<?php include('includes/login/auth.php');?>     
<?php include('includes/create/main.php');?>    
<?php include('includes/helpers/short.php');?>
<?php include('includes/create/timezone.php');?>
<?php include('js/create/main.php');?>          

<?php                                   
if(isset($_POST['mainList']) && is_numeric($_POST['mainList']) && isset($_POST['compareList']) && is_numeric($_POST['compareList']))
{                               
        $mainList = mysqli_real_escape_string($mysqli, $_POST['mainList']);
        $compareList = mysqli_real_escape_string($mysqli, $_POST['compareList']);

        if($compareList == 0)
        {       
                $q = 'SELECT id,email FROM `subscribers` WHERE email IN (SELECT email FROM subscribers WHERE list<>' . $mainList . ' AND userID = ' . get_app_info('main_userID') .')  AND list='.$mainList. ' AND userID = ' . get_app_info('main_userID');
        }                       
        else                            
        {                               
                $q = 'SELECT id,email FROM `subscribers` WHERE email IN (SELECT email FROM subscribers WHERE list<>' . $mainList . ' AND list=' . $compareList .' AND userID = ' . get_app_info('main_userID') .')  AND list='.$mainList.' AND userID = ' . get_app_info('main_userID');
        }                                       
                                                
        $r = mysqli_query($mysqli, $q);                 
        if ($r)                                         
        {                                               
                $count = 0;                             
                while($row = mysqli_fetch_array($r))    
                {
                        //Storing for future use
                        $id = $row['id'];
                        $email = $row['email'];
                        
                        //Delete from DB
                        $query = "DELETE FROM subscribers WHERE id=" . $id . ' AND userID = ' . get_app_info('main_userID');
                        mysqli_query($mysqli, $query);
                        $count++;
                }
                echo '<h2 style="color:red">' . $count . ' ' . _('emails has been found & removed') . '</h2>';
        }
        else
        {
                echo '<h2 style="color:red">' . _('No duplicates were found') . '</h2>';
        }
}

?>


<link rel="stylesheet" type="text/css" href="css/datepicker.css" />
<div class="span2">
        <?php include('includes/sidebar.php');?>
</div>

<form action="<?php echo get_app_info('path')?>/remove-duplicates-diff" method="POST" accept-charset="utf-8" class="form-vertical" id="remove-duplicates-diff">
        <div class="control-group">
                <label class="control-label"><?php echo _('Remove Duplicates From');?></label>
                <div class="controls">

                        <select id="mainList" name="mainList">
                        <?php
                                $q = 'SELECT * FROM lists WHERE  userID = '.get_app_info('main_userID').' ORDER BY name ASC';
                                $r = mysqli_query($mysqli, $q);
                                if ($r && mysqli_num_rows($r) > 0)
                                {
                                        while($row = mysqli_fetch_array($r))
                                        {
                                                $list_id = stripslashes($row['id']);
                                                $list_name = stripslashes($row['name']);
                                                $list_selected = '';


                                                echo '<option value="'.$list_id.'" data-quantity="'.get_list_quantity($list_id).'" id="'.$list_id.'" '.$list_selected.'>'.$list_name.'</option>';
                                        }
                                }
                                ?>
                        </select>
                </div>

                <label class="control-label"><?php echo _('Compare with');?></label>
                <div class="controls">
                        <select id="compareList" name="compareList">
                                <option value="0">All</option>
                                <?php
                                        $q = 'SELECT * FROM lists WHERE  userID = '.get_app_info('main_userID').' ORDER BY name ASC';
                                        $r = mysqli_query($mysqli, $q);
                                        if ($r && mysqli_num_rows($r) > 0)
                                        {
                                                while($row = mysqli_fetch_array($r))
                                                {
                                                        $list_id = stripslashes($row['id']);
                                                        $list_name = stripslashes($row['name']);
                                                        $list_selected = '';

                                                        echo '<option value="'.$list_id.'" data-quantity="'.get_list_quantity($list_id).'" id="'.$list_id.'" '.$list_selected.'>'.$list_name.'</option>';
                                                }
                                        }
                                        ?>
                        </select>
                </div>
                <button type="submit" class="btn" id="remove-duplicates-diff-btn"><?php echo _('Remove Duplicates');?></button>
        </div>
</form>
<?php include('includes/footer.php');?>


