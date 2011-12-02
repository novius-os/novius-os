<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

class Controller_Doc extends Controller_Generic_Admin {
    public function router() {
        $args = func_get_args();
        $callback = array($this, 'action_'.$args[0]);
        if (is_callable($callback)) {
            return call_user_func_array($callback, $args[1]);
        }
        
        try {
            $this->template->body = \View::forge('docs/'.$args[0].'.md');
            return $this->template;
        } catch (\Exception $e) {
            
        }
        
        return false;
    }
    
    // Index
    public function action_doc() {
        $this->template->body = \View::forge('docs/index.md');
        return $this->template;
    }
}