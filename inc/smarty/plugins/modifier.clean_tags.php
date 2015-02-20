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

    $search  = array("<br>", "<BR>", "</br>", "</BR>", "<STRONG>", "</STRONG>");
    $replace = array("</p><p>", "</p><p>", "</p><p>", "</p><p>", "<strong>", "</strong>");
    return "<p>".str_replace($search, $replace, $params)."</p>";
}