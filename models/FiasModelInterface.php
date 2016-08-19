<?php


namespace solbianca\fias\models;


interface FiasModelInterface
{
    /**
     * Описание соотношения данных в документе XML со свойствами модели.
     * Отношение описывается в массиве где ключ это XML аттрибут а значение свойство модели: [XML аттрибут => свойство модли]
     *
     * @return array
     */
    public static function getXmlAttributes();

    /**
     * Имя временной таблицы
     *
     * @return string
     */
    public static function temporaryTableName();

    /**
     * Аттрибуьы для фильтрации входящих из XML данных
     *
     * @return array
     */
    public static function getXmlFilters();
}