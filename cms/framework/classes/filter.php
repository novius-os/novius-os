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

use Input;

class Filter {

    /** Apply all filtering sent by wijmo grid
     *
     * @static
     * @param $query : Query instanciation
     * @param $config : Controller configuration
     */
    static public function apply($query, $config) {
        $sorting = Input::get('sorting', array());
        $filtering = Input::get('filtering', array());
        self::applySorting($query, $sorting, $config);
        self::applyFiltering($query, $filtering, $config);
    }


    /** Get the database column from a configuration key and join
     *  the table containing the value if necessary
     *
     * @static
     * @param $query : Query instanciation
     * @param $key : configuration key
     * @return column name (string)
     */
    static public function getColumnFromKey($query, $key, $config) {
        if (is_array($config['dataset'][$key])) {
            if (!empty($config['dataset'][$key]['search_relation'])) {
                $query->related($config['dataset'][$key]['search_relation']);
            }
            $column = $config['dataset'][$key]['search_column'];
        } else {
            $column = $config['dataset'][$key];
        }
        return $column;
    }

    /** Apply sorting on list from parameters sent by wijmo grid
     *
     * @static
     * @param $query : Query instanciation
     * @param $sorting : sorting parameters sent by wijmo
     */
    static public function applySorting($query, $sorting, $config) {
        for ($i = 0; $i < count($sorting); $i++) {
            $key = $sorting[$i]['dataKey'];
            $column = self::getColumnFromKey($query, $key, $config);
            if ($column != null) {
                $query->order_by($column, $sorting[$i]['sortDirection'] == 'ascending' ? 'ASC' : 'DESC');
            }
        }
    }

    /** Apply filtering on list from parameters sent by wijmo grid
     *
     * @static
     * @param $query : Query instanciation
     * @param $filtering : filtering parameters sent by wijmo
     */
    static public function applyFiltering($query, $filtering, $config) {
        for ($i = 0; $i < count($filtering); $i++) {
            $key = $filtering[$i]['dataKey'];
            $column = self::getColumnFromKey($query, $key, $config);
            $value = $filtering[$i]['filterValue'];
            $operator = '=';
            $mustFilter = true;
            switch ($filtering[$i]['filterOperator']) {
                case 'Contains':
                    $operator = 'LIKE';
                    $value = '%'.$value.'%';
                    break;
                case 'NotContains':
                    $operator = 'NOT LIKE';
                    $value = '%'.$value.'%';
                    break;
                case 'BeginsWith':
                    $operator = 'LIKE';
                    $value = $value.'%';
                    break;
                case 'EndsWith':
                    $operator = 'LIKE';
                    $value = '%'.$value;
                    break;
                case 'Equals':
                    $operator = '=';
                    $value = $value;
                    break;
                case 'Greater':
                    $operator = '>';
                    $value = $value;
                    break;
                case 'Less':
                    $operator = '<';
                    $value = $value;
                    break;
                case 'GreaterOrEqual':
                    $operator = '>=';
                    $value = $value;
                    break;
                case 'LessOrEqual':
                    $operator = '<=';
                    $value = $value;
                    break;
                case 'IsEmpty':
                    $operator = 'IS NULL';
                    $value = '';
                    break;
                case 'NotIsEmpty':
                    $operator = 'IS NOT NULL';
                    $value = '';
                    break;
                default:
                    $mustFilter = false;
                    break;
            }
            if ($mustFilter) {
                $query->where($column, $operator, $value);
            }
        }
    }

}