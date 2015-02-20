<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty clean_tags modifier plugin
 * Type:     modifier<br>
 * Name:     clean_tags<br>
 * Purpose:  clean html tags from text
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifier_clean_tags($params) {

    $params = strip_tags($params, "<br><strong>");

    $search  = array("<br>");
    $replace = array("</p><p>");
    return "<p>".str_replace($search, $replace, $params)."</p>";
}