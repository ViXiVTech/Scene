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

/**
 * @author Podyachev Evgeny <joker.SC2@gmail.com>
 * @package sc_system_plugins.base.components
 * @since 1.0
 */
class BASE_MCMP_JoinNowWidget extends BASE_CLASS_Widget
{
    public function __construct( BASE_CLASS_WidgetParameter $paramObject )
    {
        parent::__construct();

        $this->assign('url', SC::getRouter()->urlForRoute('base_join'));
        $this->assign('label', !empty($paramObject->customParamList['buttonLabel']) ? $paramObject->customParamList['buttonLabel'] : SC::getLanguage()->text('base', 'join_index_join_button'));
        $this->setTemplate(SC::getPluginManager()->getPlugin('base')->getMobileCmpViewDir().'join_now_widget.html');
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => SC::getLanguage()->text('base', 'join_index_join_button'),
            self::SETTING_SHOW_TITLE => false,
            self::SETTING_ICON => self::ICON_INFO
        );
    }

    public static function getSettingList()
    {
        $lang = SC::getLanguage();
        $settingList = array();
        
        $settingList['buttonLabel'] = array(
            'presentation' => self::PRESENTATION_TEXT,
            'label' => SC::getLanguage()->text('base', 'join_index_join_button'),
            'value' => ''
        );
        
        return $settingList;
    }
    
    public static function getAccess()
    {
        return self::ACCESS_GUEST;
    }
}
