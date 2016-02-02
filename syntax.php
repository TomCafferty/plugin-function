<?php
/**
 * PHP Includes via Syntax
 *
 * Put your php files in /functions folder and add the path and function
 * identifier to the /conf/default.php filr
 *
 * <function=functionId>
 * 
 * The syntax includes the PHP file per include an puts the result into
 * the wiki page.
 *
 * @license    GNU_GPL_v2
 * @author     Markus Frosch <markus [at] lazyfrosch [dot] de>
 * @author     Tom Cafferty <tcafferty [at] glocalfocal [dot] com>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');


class syntax_plugin_function extends DokuWiki_Syntax_Plugin {
 
    function getType(){ return 'substition'; }
    function getPType(){ return 'normal'; }
    function getAllowedTypes() { 
        return array('substition','protected','disabled');
    }
    function getSort(){ return 195; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<function=.*?>',$mode,'plugin_function');
    }
    
    function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
          case DOKU_LEXER_SPECIAL :
            return array($state, $match);          
          default:
            return array($state);
        }
    }
 
    function render($mode, Doku_Renderer $renderer, $indata) {
        global $conf;
        if($mode == 'xhtml'){
          list($state, $data) = $indata;

          switch ($state) {
            case DOKU_LEXER_SPECIAL :
              preg_match("#^<function=(.+)>$#", $data, $matches);
              $func = $matches[1];
              $a = explode('?', $func);
              $func = $a[0];
              if (!empty($a[1])) { parse_str($a[1], $params); }
              else { $params[0] = ''; }
              if(preg_match("#^[a-z0-9\-_ \./]+$#i", $func)) {
                    $renderer->info['cache'] = FALSE;
                    $filename = DOKU_PLUGIN . 'function/functions/' . $this->getConf($func);
                    include ($filename);
                    $renderer->doc .= $thisfunction($params);
              }
              else
                    $renderer->doc .= $renderer->_xmlEntities($data);
              break;
  
          }
          return true;
        }
        
        // unsupported $mode
        return false;
    } 
}
 
?>