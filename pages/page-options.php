<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="wrap">

    <?php screen_icon(Easy_Pie_Maintenance_Mode::PAGE_KEY); ?>
    <h2>Easy Pie Maintenance Mode</h2>
    <?php
    if (isset($_GET['settings-updated'])) {
        echo "<div class='updated'><p>" . __('If you have a caching plugin, be sure to clear the cache!', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG) . "</p></div>";        
    }
    ?>
    <div class="inside">
        <form method="post" action="options.php"> 
            <?php
            settings_fields(Easy_Pie_Maintenance_Mode::OPTIONS_GROUP_NAME);
            do_settings_sections(Easy_Pie_Maintenance_Mode::PAGE_KEY);
            
            submit_button();
            ?>
        </form>
    </div>
</div>