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
 * Singleton. 'Language Prefix' Data Access Object
 *
 * @author Aybat Duyshokov <duyshokov@gmail.com>
 * @package sc_system_plugins.base.bol
 * @since 1.0
 */
class BOL_LanguagePrefixDao extends SC_BaseDao
{

    /**
     * Class constructor
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * Class instance
     *
     * @var BOL_LanguagePrefixDao
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return BOL_LanguagePrefixDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @see SC_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'BOL_LanguagePrefix';
    }

    /**
     * @see SC_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return SC_DB_PREFIX . 'base_language_prefix';
    }

    public function findAllWithKeyCount()
    {
        return $this->dbo->queryForList(
            'SELECT `p`.*, COUNT(`k`.`id`) as keyCount FROM ' . $this->getTableName()
            . ' AS `p` LEFT JOIN ' . BOL_LanguageKeyDao::getInstance()->getTableName()
            . ' AS `k` ON `p`.`id` = `k`.`prefixId` GROUP BY `k`.`prefixId` '
        );
    }

    public function findPrefixId( $prefix )
    {
        $query = "SELECT `id` FROM `" . $this->getTableName() . "` WHERE `prefix`=?";

        return $this->dbo->queryForColumn($query, array($prefix));
    }

    public function findByPrefix( $prefix )
    {
        $ex = new SC_Example();

        $ex->andFieldEqual('prefix', $prefix);

        return $this->findObjectByExample($ex);
    }
}
