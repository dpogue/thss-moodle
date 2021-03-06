<?php

/** This is the global search shortcut block - a single query can be entered, and
 * the user will be redirected to the query page where they can enter more
 *  advanced queries, and view the results of their search. When searching from
 *  this block, the broadest possible selection of documents is searched.
 *
 * @package search
 * @subpackage search block
 * @author: Michael Champanis (mchampan), reengineered by Valery Fremaux
 * @date: 2006 06 25
 */

class block_search extends block_base {

    function init() {
      $this->title = get_string('pluginname', 'block_search');
    } //init

    // only one instance of this block is required
    function instance_allow_multiple() {
      return false;
    } //instance_allow_multiple

    // label and button values can be set in admin
    function has_config() {
      return true;
    } //has_config

    function get_content() {
      global $CFG;

      if (empty($CFG->enableglobalsearch)) {
        return '';
      }

      //cache block contents
      if ($this->content !== NULL) {
        return $this->content;
      } //if

      $this->content = new stdClass;

      //lazy check for the moment
      //fetch values if defined in admin, otherwise use defaults
      $label  = (!empty($CFG->block_search_text)) ? $CFG->block_search_text : get_string('searchmoodle', 'block_search');
      $button = (!empty($CFG->block_search_button)) ? $CFG->block_search_button : get_string('go', 'block_search');

      //basic search form
      $this->content->text =
            '<form id="searchquery" method="get" action="'. $CFG->wwwroot .'/search/query.php"><div>'
          . '<label for="block_search_q">'. $label .'</label>'
          . '<input id="block_search_q" type="text" name="query_string" />'
          . '<input type="submit" value="'.$button.'" />'
          . '</div></form>';

      //no footer, thanks
      $this->content->footer = '';

      return $this->content;
    } //get_content

    function specialisation() {
      //empty!
    } //specialisation

    /**
     * wraps up to search engine cron
     */
    function cron(){
        global $CFG;

        include($CFG->dirroot.'/search/cron.php');
    }

  } //block_search

