<?php

/**
 * discription: This class is used to generate html for table.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: HTMLTable
 * @package: includes
 * @subpackage: classes
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
final class HTMLTable
{
    public $size = '95%';

    private $id = '';        //Table Id

    private $headerData = [];  //Header data container

    private $actionData = [];    //Action data container

    private $buttonData = [];    //Action data container

    private $rowData = [];    //Table Rows data container

    private $form = [];    //Form name

    private $paginator = null;        //Paginator object

    private $nodatamsg = '';        //No data message

    /**
     * Function used to create object ot class.
     *
     * @param string $id
     */
    private static $counterID = 0;

    public function __construct($id = '')
    {
        $this->id = $id;

        $this->nodatamsg = NO_RECORDS_FOUND;
    }

    /**
     * Add header data.
     *
     * @param $filed String Database field related
     * @param $text  String Text to be shown
     * @param $options Array Options for header.
     *            field    :  If sort field is diffrent from shown field.
     *            sortable:  Wheter to show sort icon to short the table on field
     *            qryStr  :  If any query string to be passed to the sort link
     *            default :  is this default field to be sorted
     *            sort    :  Sort type i.e ASC or DESC
     *            align    :  alignment of column
     */
    public function addHeaderColumn($field, $text, array $options = [])
    {
        $this->headerData[$field] = [
            'text' => $text,

            'field' => isset($options['field']) ? $options['field'] : $field,

            'sortable' => isset($options['sortable']) ? $options['sortable'] : false,

            'qryStr' => isset($options['qryStr']) ? $options['qryStr'] : '',

            'default' => isset($options['default']) ? $options['default'] : false,

            'sort' => isset($options['sort']) ? $options['sort'] : 'DESC',

            'align' => isset($options['align']) ? $options['align'] : 'center',

            'type' => isset($options['type']) ? $options['type'] : '',

            'width' => isset($options['width']) ? $options['width'] : 'auto',

            'refdata' => isset($options['refdata']) ? $options['refdata'] : null,

            'tooltip' => isset($options['tooltip']) ? $options['tooltip'] : null,

            'tooltipfield' => isset($options['tooltipfield']) ? $options['tooltipfield'] : null,
        ];
    }

    /**
     * Function used to set wheter to show serial number column or not.
     *
     * @param string $text
     */
    public function addSerialNoHeaderColumn($text = 'S.No.')
    {
        $this->addHeaderColumn('SNO', $text);
    }

    /**
     * Function used to set checkbox column.
     *
     * @param string $text
     */
    public function addCheckBoxHeaderColumn($field)
    {
        if (!isset($this->form['name']) || empty($this->form['name'])) {
            throw new SoftException('Form must be added before this.');
        }
        $this->addActionColumn('checkbox', '<input type=\'checkbox\' onclick=\'checkall(this,"' . $field . '[]","' . $this->form['name'] . '")\' title=\'Select/Deselect All\'>');
    }

    /**
     * Function used to set wheter to show chekbox header to select all rows or not.
     *
     * @param string $text
     */
    public function addCheckBoxRowColumn($name, $field)
    {
        if (!isset($this->form['name']) || empty($this->form['name'])) {
            throw new SoftException('Form must be added before this.');
        }
        $this->addActionString('checkbox', '<input type="checkbox" name="' . $name . '[]" value="' . $field . '">');
    }

    /**
     * Function used to add button to form at last.
     *
     * @param string $text
     */
    public function addButton($name, $label, $onclick = '', $type = 'button')
    {
        if (!isset($this->form['name']) || empty($this->form['name'])) {
            throw new SoftException('Form must be added before this.');
        }
        $this->buttonData[$name] = ['label' => $label, 'onclick' => $onclick, 'type' => $type];
    }

    /**
     * Function used to add button to form at last.
     *
     * @param string $text
     */
    public function addSubmitButton($name, $label, $onlick = '')
    {
        $this->addButton($name, $label, $onlick, 'submit');
    }

    /**
     * Add action column to table rows.
     *
     * @param string $field any valid name
     * @param string $text  Text to be shown
     */
    public function addActionColumn($field, $text)
    {
        $this->actionData[$field] = ['text' => $text];
    }

    /**
     * Add the action string used in loop for each row.
     *
     * @param string $field
     * @param string $string
     *                       like for button <input type="button" value="Submit" onclick="document.myform.contract_id.value=\'<!-_id_-!>\'; document.myform.submit();">
     *                       or <a  href="abc.php?id=<!-_id_-!>&name=<!-_name_-!>'>Link</a>
     *                       <!-_valid_table_field_name_-!>
     */
    public function addActionString($field, $string)
    {
        $this->actionData[$field]['string'][] = $string;
    }

    /**
     * Add records coming from database.
     */
    public function addRowData(array $data)
    {
        if (!is_array($data)) {
            throw new SoftException('Invalid data!');
        }
        $this->rowData = $data;
    }

    /**
     * Add form if you need.
     *
     * @param string $form
     * @param string $action
     * @param string $isMultiPart
     */
    public function addForm($form, $action = '', $isMultiPart = false)
    {
        if (empty($form)) {
            throw new SoftException('Invalid form name passed.');
        }
        $this->form['name'] = $form;

        $this->form['action'] = $action;

        $this->form['isMultipart'] = $isMultiPart;
    }

    /**
     * Add hidden fields to form.
     *
     * @param string $field
     * @param string $value
     */
    public function addHiddenField($field, $value)
    {
        if (empty($field)) {
            throw new SoftException('Invalid form field name passed.');
        }
        $this->form['fields'][$field] = $value;
    }

    /**
     * Set paginator object if paging is used.
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Set the messsage if no data coming from database.
     *
     * @param string $msg
     */
    public function setNoDataMessage($msg)
    {
        $this->nodatamsg = $msg;
    }

    /**
     * Function to draw the table.
     *
     * @param bool $return wheter to return the generated html or to show to browser directly from function
     *
     * @return string
     */
    public function draw($return = false)
    {
        $html = '';

        $formend = '';

        $showsno = false;

        $sno = 1;

        $jstimecnt = 1;

        $totalcol = 0;

        $formatedNames = [];

        $formatedGang = [];

        if (count($this->rowData) == 0) {
            if ($return) {
                return $this->nodatamsg;
            }

            echo $this->nodatamsg;

            return;
        }

        /**check for form to be used or not **/

        if (!empty($this->form['name'])) {
            $html .= '<form method="POST" id="' . $this->form['name'] . '" name="' . $this->form['name'] . '" action="' . $this->form['action'] . '" ' . ($this->form['isMultipart'] ? 'enctype="multipart/form-data"' : '') . '>' . "\n";

            if (is_array($this->form['fields'])) {
                foreach ($this->form['fields'] as $field => $value) {
                    $html .= '<input type="hidden" name="' . $field . '" value="' . $value . '">' . "\n";
                }
            }

            $formend = '</form>' . "\n";
        }

        $html .= '<table class="cleanTable"  width="' . $this->size . '" id="' . $this->id . '">' . "\n";

        $html .= '<tr>';

        if (isset($this->headerData['SNO'])) { //if have to show serial number before each record
            $showsno = true;
        }

        foreach ($this->headerData as $field => $data) {
            if ($data['sortable']) {
                $html .= '<th class="headerCell" align="' . $data['align'] . '">' . self::FieldSortLink($data['text'], $data['field'], $data['qryStr'], $data['default'], $data['sort']) . '</th>' . "\n";
            } else {
                $html .= '<th class="headerCell" align="' . $data['align'] . '">' . $this->headerData[$field]['text'] . '</th>' . "\n";
            }
        }

        foreach ($this->actionData as $field => $data) {
            $html .= '<th class="headerCell" align="center">' . $this->actionData[$field]['text'] . '</th>' . "\n";
        }

        $html .= '</tr>' . "\n";

        if ($showsno && is_object($this->paginator)) {
            $sno = 1 + (($this->paginator->currentPage() - 1) * Paginator::$recordsOnPage);
        }

        foreach ($this->rowData as $row) {
            $html .= '<tr id="row' . ($this->counterID++) . '">' . "\n";

            if ($showsno) {
                $html .= '<td class="dottedRow">' . $sno . '</td>' . "\n";

                ++$sno;
            }

            foreach ($this->headerData as $field => $data) {
                //$field = $data['field'];

                if ($field == 'SNO') {
                    continue;
                }

                $html .= '<td class="dottedRow" align="' . $data['align'] . '" width="' . $data['width'] . '">';

                switch ($data['type']) {
                    case 'user':

                        if (!isset($formatedNames[$row->$field])) {
                            if (is_numeric($row->$field)) {
                                if ($row->$field > 0) {
                                    $formatedNames[$row->$field] = User::SGetFormattedName($row->$field);
                                } else {
                                    $formatedNames[$row->$field] = '-';
                                }
                            } else {
                                $formatedNames[$row->$field] = $row->$field;
                            }
                        }

                        $html .= $formatedNames[$row->$field];

                        break;

                    case 'gang':

                        if (!isset($formatedGang[$row->$field])) {
                            if (is_numeric($row->$field)) {
                                if ($row->$field > 0) {
                                    $gang = new Gang($row->$field);

                                    $formatedGang[$row->$field] = $gang->GetPublicFormattedName();
                                } else {
                                    $formatedGang[$row->$field] = '-';
                                }
                            } else {
                                $formatedGang[$row->$field] = $row->$field;
                            }
                        }

                        $html .= $formatedGang[$row->$field];

                        break;

                    case 'number':

                        $html .= number_format((float) ($row->$field));

                        break;

                    case 'money':

                        $html .= '$' . number_format((float) ($row->$field));

                        break;

                    case 'shorttime':

                        $html .= date('d/m/y g:i:sa', $row->$field);

                        break;

                    case 'time':

                        $html .= date('F d, Y g:i:sa', $row->$field);

                        break;

                    case 'jstime':

                        $html .= '<div id="active-' . $jstimecnt . '"></div><script>countdown(' . ($row->$field) . ', \'active-' . $jstimecnt . '\', \'%%D%% Days <br> %%H%% Hours <br>%%M%% Minutes <br> %%S%% Seconds\');</script>';

                        ++$jstimecnt;

                        break;

                    case 'ref':

                        $html .= $data['refdata'][$row->$field];

                        break;

                    default:

                        $html .= $row->$field;

                        break;
                }

                $html .= '</td>' . "\n";
            }

            foreach ($this->actionData as $field => $data) {
                $html .= '<td class="dottedRow" align="center">';
                $seprator = '';
                foreach ($data['string'] as $string) {
                    $text = preg_replace_callback('/\<!-_(\w+)_-!>/', function ($matches) use ($row) {
                        return $row->{$matches[1]};
                    }, $string);
                    $html .= $seprator . $text;
                    $seprator = ' ';
                }
                $html .= '</td>' . "\n";
            }
            $html .= '</tr>';
        }

        if (is_object($this->paginator)) {
            $paging = $this->paginator->getPageNav('', true);
            if ($paging) {
                $html .= '<tr><td align="center" colspan="' . (count($this->headerData) + count($this->actionData) + 1) . '"><br>' . $paging . '</td></tr>' . "\n";
            }
        }

        if (is_array($this->buttonData)) {
            if (!empty($this->buttonData)) {
                $html .= '<tr><td align="center" colspan="' . (count($this->headerData) + count($this->actionData) + 1) . '"><br>';
                foreach ($this->buttonData as $name => $data) {
                    $html .= '<input type="' . $data['type'] . '" name="' . $name . '" id="' . $name . '" value="' . $data['label'] . '" ' . (!empty($data['onclick']) ? 'onclick=\'' . $data['onclick'] . '\'' : '') . ' class="button"> ';
                }
                $html .= '</td></tr>' . "\n";
            }
        }

        $html .= '</table>';

        $html .= $formend;

        if ($return) {
            return $html;
        }

        echo $html;
    }

    /**
     * Function is used for sorting utility.
     *
     * @param $title String Text Header
     * @param $field String Table fiedld
     * @param $qryStr String If any query string to be passed to the sort link
     * @param $default Boolean  is this default field to be sorted
     * @param $sort    String Sort type i.e ASC or DESC
     *
     * @return string
     */
    private static function FieldSortLink($title, $field, $qryStr = '', $default = false, $fsort = 'desc', $jsHandler = '')
    {
        $sort = 'asc';

        $qryStr = preg_replace('/(oby|sort)=[^&]*&?/', '', $qryStr);

        if ($default && empty($_REQUEST['oby'])) {
            $_REQUEST['oby'] = $field;

            $_REQUEST['sort'] = $fsort;

            $sort = $fsort;
        }

        if (trim($_REQUEST['oby']) == $field && ($_REQUEST['sort'] == 'asc' || empty($_REQUEST['sort']))) {
            $sort = 'desc';
        }

        $return .= "$title &nbsp;";

        if (!empty($jsHandler)) {
            $return .= "<a href='#' onclick='$jsHandler(\"$field\",\"$sort\",\"$qryStr\"); return false;'>";
        } else {
            $return .= "<a href='" . $_SERVER['PHP_SELF'] . "?oby=$field&sort=$sort&$qryStr'>";
        }

        $return .= "<img src='images/buttons/sort_" . $sort . ".gif' border=0>";

        return $return;
    }
}
