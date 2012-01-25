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

use Fuel\Core\Str;

use Fuel\Core\Inflector;

use Fuel\Core\Asset, Fuel\Core\Format, Fuel\Core\Uri, Fuel\Core\Input, Fuel\Core\DB, Fuel\Core\View;

class Controller_Generator_Model extends Controller_Generic_Admin {

    public function before() {
        parent::before();

        $this->auto_render = false;
    }

    public function after($response) {

        $this->template->set('base', Uri::base(false), false);

		\Asset::add_path('static/cms/');
		\Asset::add_path('static/cms/js/vendor/wijmo/');
		\Asset::css('aristo/jquery-wijmo.css', array(), 'css');
		\Asset::css('jquery.wijmo-complete.all.2.0.0b2.min.css', array(), 'css');
		\Asset::css('base.css', array(), 'css');

        return parent::after($response);
    }

    public function action_index()
    {
        $view = View::forge('generator/model', array(
            'tables' => DB::list_tables(),
            'url'    => str_replace('/index', '', Uri::current()),
        ));
        $this->template->body = $view;
        $this->response->body = $this->template;
    }

    public function action_submit()
    {
        $subdir        = Input::post('subdir');
        $namespace     = Input::post('namespace');
        $namespace     = substr($namespace, -1) == '\\' ? substr($namespace, 0, -1) : $namespace;
        $namespace     = substr($namespace, 0, 1) == '\\' ? substr($namespace, 1) : $namespace;
        $table         = Input::post('table');
        $classname     = Input::post('name');
        if ($classname == '') {
            $classname = 'Model_'.Inflector::classify($table);
        }
        if (substr($classname, 0, 6) != 'Model_') {
            $classname = 'Model_'.$classname;
        }
        $filename      = str_replace('model_', '', Str::lower($classname));
        if ($subdir && substr($classname, 0, 6 + strlen($subdir) + 1) != 'Model_'.ucfirst($subdir).'_') {
            $classname = str_replace('Model_', 'Model_'.ucfirst($subdir).'_', $classname);
        }
        $props         = $this->_columns($table);
        $columns       = preg_replace(array('/\)$/', '/=> \n  array/', '/\n  /', '/\n\t\t  /', '/ NULL\,/'), array("\t)", '=> array', "\n\t\t", "\n\t\t\t", ' null,'), var_export($props['columns'], true));
        $pk            = $props['primary'];

        $manytomany          = '';
        $belongsto           = '';
        $hasone              = '';
        $hasmany             = '';
        $relationships_model = Input::post('relationships_model', array());
        if (count($relationships_model)) {
            foreach (Input::post('relationships_name', array()) as $i => $name) {
                $model  = Input::post('relationships_model.'.$i);
                $table2 = Input::post('relationships_table.'.$i);
                if ($name == '') {
                    $name = $table2;
                }
                if ($model == '') {
                    $model = 'Model_'.Inflector::classify($table2);
                }
                if (substr($model, 0, 6) != 'Model_') {
                    $model = 'Model_'.$model;
                }
                if ($subdir && substr($model, 0, 6 + strlen($subdir) + 1) != 'Model_'.ucfirst($subdir).'_') {
                    $model = str_replace('Model_', 'Model_'.ucfirst($subdir).'_', $model);
                }
                $model = '\\'.$namespace.'\\'.$model;

                switch (Input::post('relationships_type.'.$i)) {
                    case 'has-many':
                        if ($hasmany === '') {
                            $hasmany = "\n\tprotected static \$_has_many = array(\n";
                        }

                        $hasmany .= "\t\t'".$name."' => array(\n";
                        $hasmany .= "\t\t\t'key_from'       => '".$props['primary']."',\n";
                        $hasmany .= "\t\t\t'model_to'       => '".$model."',\n";
                        $hasmany .= "\t\t\t'key_to'         => '".Input::post('relationships_foreignkey.'.$i)."',\n";
                        $hasmany .= "\t\t\t'cascade_save'   => false,\n";
                        $hasmany .= "\t\t\t'cascade_delete' => false,\n";
                        $hasmany .= "\t\t),\n";
                        break;

                    case 'has-one':
                        if ($hasone === '') {
                            $hasone = "\n\tprotected static \$_has_one = array(\n";
                        }
                        $props2 = $this->_columns(Input::post('relationships_table.'.$i));

                        $hasone .= "\t\t'".$name."' => array(\n";
                        $hasone .= "\t\t\t'key_from'       => '".$pk."',\n";
                        $hasone .= "\t\t\t'model_to'       => '".$model."',\n";
                        $hasone .= "\t\t\t'key_to'         => '".Input::post('relationships_foreignkey.'.$i)."',\n";
                        $hasone .= "\t\t\t'cascade_save'   => false,\n";
                        $hasone .= "\t\t\t'cascade_delete' => false,\n";
                        $hasone .= "\t\t),\n";
                        break;

                    case 'belongs-to':
                        if ($belongsto === '') {
                            $belongsto = "\n\tprotected static \$_belongs_to = array(\n";
                        }
                        $props2 = $this->_columns(Input::post('relationships_table.'.$i));
                        $pk2     = $props2['primary'];

                        $belongsto .= "\t\t'".$name."' => array(\n";
                        $belongsto .= "\t\t\t'key_from'       => '".Input::post('relationships_foreignkey.'.$i)."',\n";
                        $belongsto .= "\t\t\t'model_to'       => '".$model."',\n";
                        $belongsto .= "\t\t\t'key_to'         => '".$pk2."',\n";
                        $belongsto .= "\t\t\t'cascade_save'   => false,\n";
                        $belongsto .= "\t\t\t'cascade_delete' => false,\n";
                        $belongsto .= "\t\t),\n";
                        break;

                    case 'many-to-many':
                        if ($manytomany === '') {
                            $manytomany = "\n\tprotected static \$_many_many = array(\n";
                        }
                        $props2 = $this->_columns(Input::post('relationships_table.'.$i));
                        $pk2     = $props2['primary'];

                        $manytomany .= "\t\t'".$name."' => array(\n";
                        $manytomany .= "\t\t\t'key_from'         => '".$pk."',\n";
                        $manytomany .= "\t\t\t'key_through_from' => '".Input::post('relationship_foreignkey_through_from.'.$i)."',\n";
                        $manytomany .= "\t\t\t'table_through'    => '".Input::post('relationship_table_through.'.$i)."',\n";
                        $manytomany .= "\t\t\t'key_through_to'   => '".Input::post('relationship_foreignkey_through_to.'.$i)."',\n";
                        $manytomany .= "\t\t\t'model_to'         => '".$model."',\n";
                        $manytomany .= "\t\t\t'key_to'           => '".$pk2."',\n";
                        $manytomany .= "\t\t\t'cascade_save'     => false,\n";
                        $manytomany .= "\t\t\t'cascade_delete'   => false,\n";
                        $manytomany .= "\t\t),\n";
                        break;
                }
            }
            if ($manytomany !== '') {
                $manytomany .= "\t);\n";
            }
            if ($belongsto !== '') {
                $belongsto .= "\t);\n";
            }
            if ($hasone !== '') {
                $hasone .= "\t);\n";
            }
            if ($hasmany !== '') {
                $hasmany .= "\t);\n";
            }
        }
        $namespace     = $namespace ? "namespace ".$namespace.";\n\n" : '';

        $model = <<<MODEL
<?php
{$namespace}class $classname extends \Orm\Model {
    protected static \$_table_name = '$table';
    protected static \$_primary_key = array('{$props['primary']}');

    protected static \$_properties = $columns;
{$hasone}{$hasmany}{$belongsto}{$manytomany}}
MODEL;

        header('Content-Disposition: attachment; filename='.$filename.'.php');
        $this->response->body = $model;
    }

    private function _columns($table) {
        $columns = DB::list_columns($table);
        $props = array(
            'columns'       => array(),
            'prefixe'       => '',
            'primary'       => '',
        );
        foreach ($columns as $column => $item) {
            $props['columns'][$column] = $item;
            if ($item['key'] === 'PRI') {
                $props['primary'] = $column;
                $props['prefixe'] = str_replace('id', '', $column);
            }
        }
        return $props;
    }

    public function action_json_table()
    {
        $this->response->body = Format::forge($this->_columns(Input::get('table')))->to_json();
        $this->response->set_header('Content-Type', 'application/json');
    }

    public function action_json_model()
    {
        try {
            $model     = Input::get('model');
            if (substr($model, 0, 6) == 'Model_') {
                $model = substr($model, 6);
            }
            $namespace = Input::get('namespace');
            $namespace = substr($namespace, -1) == '\\' ? substr($namespace, 0, -1) : $namespace;
            $namespace = substr($namespace, 0, 1) != '\\' ? '\\'.$namespace : $namespace;
            $subdir    = Input::get('subdir');

            if (class_exists(($namespace ? $namespace.'\\' : '').'Model_'.($subdir ? ucfirst($subdir) : '').$model)) {
                $model = ($namespace ? $namespace.'\\' : '').'Model_'.($subdir ? ucfirst($subdir) : '').$model;
                $table = $model::table();
                $this->response->body = Format::forge($table)->to_json();
            } else if (class_exists(($namespace ? $namespace.'\\' : '').'Model_'.$model)) {
                $model = ($namespace ? $namespace.'\\' : '').'Model_'.$model;
                $table = $model::table();
                $this->response->body = Format::forge($table)->to_json();
            } else if (class_exists('Model_'.$model)) {
                $model = 'Model_'.$model;
                $table = $model::table();
                $this->response->body = Format::forge($table)->to_json();
            }
        } catch (Exception $e) {}
        $this->response->set_header('Content-Type', 'application/json');
    }
}

/* End of file model.php */