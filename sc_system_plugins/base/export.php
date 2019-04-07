<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.scene.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Scene software.
 * The Initial Developer of the Original Code is Scene Foundation (http://www.scene.org/foundation).
 * All portions of the code written by Scene Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Scene Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Scene community software
 * Attribution URL: http://www.scene.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */
class BASE_Export extends DATAEXPORTER_CLASS_Export
{
    public $configs = array();

    public function excludeTableList()
    {
        return array(
            SC_DB_PREFIX . 'base_config',
            SC_DB_PREFIX . 'base_theme',
            SC_DB_PREFIX . 'base_plugin',
            SC_DB_PREFIX . 'base_theme_control',
            SC_DB_PREFIX . 'base_theme_control_value',
            SC_DB_PREFIX . 'base_component_place_cache'
        );
    }

    public function includeTableList()
    {
        return array();
    }

    public function export( $params )
    {
        /* @var $za ZipArchives */
        $za = $params['zipArchive'];
        $archiveDir = $params['archiveDir'];

        // theme
        $this->exportThemes($za, $archiveDir);


        // configs
        $this->exportConfigs($za, $archiveDir);

        $this->configs['media_panel_url'] = SC::getStorage()->getFileUrl(SC::getPluginManager()->getPlugin('base')->getUserFilesDir());

        $string = json_encode($this->configs);
        $za->addFromString($archiveDir . '/' . 'config.txt', $string);
    }

    private function exportConfigs( ZipArchive $za, $archiveDir )
    {
        $this->configs['avatarUrl'] = SC::getStorage()->getFileUrl(BOL_AvatarService::getInstance()->getAvatarsDir());

        $tableName = SC::getDbo()->escapeString(str_replace(SC_DB_PREFIX, '%%TBL-PREFIX%%', BOL_ConfigDao::getInstance()->getTableName()));

        $query = " SELECT `key`, `name`, `value`, `description` FROM " . BOL_ConfigDao::getInstance()->getTableName() . " WHERE name NOT IN ( 'maintenance', 'update_soft', 'site_installed', 'soft_build', 'soft_version' )
                    AND `key` NOT IN ( 'dataimporter', 'dataexporter' ) ";

        $sql = DATAEXPORTER_BOL_ExportService::getInstance()->exportTableToSql(SC_DB_PREFIX . 'base_config', false, false, true, $query);

        $za->addFromString($archiveDir . '/configs.sql', $sql);
    }

    private function exportThemes( ZipArchive $za, $archiveDir )
    {
        $currentTheme = SC::getThemeManager()->getSelectedTheme()->getDto();
        $currentThemeDir = SC::getThemeManager()->getSelectedTheme()->getRootDir();
        $currentThemeUserfilesDir = SC_DIR_THEME_USERFILES;

        $this->configs['currentTheme'] = array(
            'name' => $currentTheme->name,
            'customCss' => $currentTheme->customCss,
            'customCssFileName' => $currentTheme->customCssFileName,
            'description' => $currentTheme->description,
            'isActive' => $currentTheme->isActive,
            'sidebarPosition' => $currentTheme->sidebarPosition,
            'title' => $currentTheme->title
        );

        $controlValueList = SC::getDbo()->queryForList(" SELECT * FROM " . BOL_ThemeControlValueDao::getInstance()->getTableName() . " WHERE themeId = :themeId ", array('themeId' => $currentTheme->id));

        foreach ( $controlValueList as $controlValue )
        {
            $this->configs['controlValue'][$controlValue['themeControlKey']] = $controlValue['value'];
        }

        $za->addEmptyDir($archiveDir . '/' . $currentTheme->getKey());
        $this->zipFolder($za, $currentThemeDir, $archiveDir . '/' . $currentTheme->getKey() . '/');

        $themesDir = Sc::getPluginManager()->getPlugin('dataexporter')->getPluginFilesDir(). 'themes' . DS;

        UTIL_File::copyDir(SC_DIR_THEME_USERFILES, $themesDir);

        $fileList = Sc::getStorage()->getFileNameList(SC_DIR_THEME_USERFILES);
        
        mkdir($themesDir, 0777);
        
        foreach($fileList as $file)
        {
            if ( Sc::getStorage()->isFile($file) )
            {
                Sc::getStorage()->copyFileToLocalFS($file, $themesDir . mb_substr($file, mb_strlen(SC_DIR_THEME_USERFILES)));
            }
        }
        
        $za->addEmptyDir($archiveDir . '/themes');
        
        $this->zipFolder($za, $themesDir, $archiveDir . '/themes/');
    }

    private function zipFolder( ZipArchive $zipArchive, $localDir, $archiveDir )
    {
        if ( $handle = opendir($localDir) )
        {
            while ( false !== ($file = readdir($handle)) )
            {
                if ( is_file($localDir . $file) )
                {
                    $zipArchive->addFile($localDir . $file, $archiveDir . $file);
                }
                elseif ( $file != '.' and $file != '..' and is_dir($localDir . $file) )
                {
                    $zipArchive->addEmptyDir($archiveDir . $file);
                    $this->zipFolder($zipArchive, $localDir . $file . DS, $archiveDir . $file . '/');
                }
            }
        }
        closedir($handle);
    }
}

?>