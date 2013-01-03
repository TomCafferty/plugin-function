<?php
/**
 * PHP Includes via Syntax
 *
 * Put your php files in /functions folder
 * Use the filename without .php extension inside the function tag:
 *
 * <function=filename />
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
        $this->Lexer->addSpecialPattern('<function[= ].*?/>',$mode,'plugin_function');
    }
    
    function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_SPECIAL :
            return array($state, $match);          
          default:
            return array($state);
        }
    }
 
    function render($mode, &$renderer, $indata) {
        global $funcTable;	// the function to class name mapping table
		if (empty($funcTable))
			$funcTable = array();

		if($mode == 'xhtml'){
          list($state, $data) = $indata;

         switch ($state) {
            case DOKU_LEXER_SPECIAL :
              preg_match("#^<function[= ](.+)/>$#", $data, $matches);
              $func = $matches[1];
              $a = explode('?', $func);
              $func = trim($a[0]);
              // this checks also that no relative path can be used
              if(preg_match("#^[a-z0-9\-_]+$#i", $func)) {
                    // critical part, this include and run shall be guarded
                    // against misuse and the check on the line before is
                    // extremely important (no dots allowed in the name, only
                    // alphanumeric, - and _ The files must be located in the
                    // functions directory in a 'flat' way
                    $renderer->info['cache'] = FALSE;
					
					if (empty($funcTable[$func])) {
						$filename = DOKU_PLUGIN . 'function/functions/' . $func . '.php';
						$className = include_once ($filename);
						if (!empty($className)) {
							$funcTable[$func] = $className;
						}
					}
					$object = new $funcTable[$func]();
					if (!empty($a[1])) {
						parse_str($a[1], $params); 
					} else {
						$params = '';
					}
                    $renderer->doc .= $object->run($params);
              }
              else {
                    $renderer->doc .= $renderer->_xmlEntities($data);
			  }
              break;
          }
          return true;
        }
        
        // unsupported $mode
        return false;
    } 
}
 
?>