<?php

final class GamePolls extends BaseObject
{
    public static $idField = 'id';
        public static $dataTable = 'GamePolls';
    public $votes;
    public $chart;

    public function __construct($id)
    {
        parent::__construct($id);
        for ($i = 1; $i < 6; ++$i) {
            $field = 'answer' . $i;
            if ($this->$field == '') {
                continue;
            }
            $this->votes[$i] = 0;
        }

        $sql = 'select answear, count(id) number from pollvotes where pool=' . $id . ' group by answear';
        $res =DBi::$conn->query($sql);
        while ($obj = mysqli_fetch_object($res)) {
            $this->votes[$obj->answear] = $obj->number;
        }
        $data = [];
        $i = 0;
        foreach ($this->votes as $key => $value) {
            //if($value==0)continue;
            $field = 'answer' . $key;
            $data[++$i] = $value;
        }
        if (count($data) == 0) {
            return;
        }
        $barChart = new gGroupedBarChart();
        $barChart->width = 150;
        $barChart->height = 150;
        $title = [];
        foreach ($data as $key => $dat) {
            $barChart->addDataSet([$dat]);
            $title[] = $key;
        }

        $barChart->dataColors = ['ff3344', '11ff11', '22aacc', '3333aa'];
        $barChart->setHorizontal(false);
        $barChart->valueLabels = $title;
        $this->chart = '<img src="' . $barChart->getUrl() . '">';
    }

    public static function GetCharts()
    {
        $sql = 'select id from  GamePolls WHERE visible=1';
        $res =DBi::$conn->query($sql);
        $pools = [];
        while ($row = mysqli_fetch_object($res)) {
            $pools[] = new GamePolls($row->id);
        }

        return $pools;
    }

    public function Already_voted(User $user)
    {
        $sql = 'select id from pollvotes where pool=' . $this->id . ' and userid=' . $user->id;
        $res =DBi::$conn->query($sql);
        if (mysqli_num_rows($res) > 0) {
            return 1;
        }

        return 0;
    }

    public function Vote(User $user, $answear)
    {
        $sql = 'select id from pollvotes where pool=' . $this->id . ' and userid=' . $user->id;
        $res =DBi::$conn->query($sql);
        if (mysqli_num_rows($res) > 0) {
            throw new FailedResult('You already voted');
        }
        $values = ['pool' => $this->id,'userid' => $user->id,'answear' => $answear];
        GamePolls::AddRecords($values, 'pollvotes');
        echo HTML::ShowSuccessMessage("You have voted!");
    }

    public static function Create($userid, $question, $description, $answers)
    {
        $values = ['userid' => $userid, 'question' => $question,'description' => $description,'visible' => 1];
        for ($i = 1; $i < 6; ++$i) {
            $field = 'answer' . $i;
            if ($answers[$field] == '') {
                continue;
            }
            $values[$field] = $answers[$field];
        }
        GamePolls::AddRecords($values, GamePolls::$dataTable);
        echo HTML::ShowSuccessMessage("You have added the poll");
    }

    public function Delete()
    {
        $this->SetAttribute('visible', '0');
        echo HTML::ShowSuccessMessage('You have deleted this poll, this wil still be available to view in the admin panel');
    }

    public static function GetAll($where = '')
    {
        $objs = parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable(), $where);

        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $obj->desc = constant($obj->desc);
            $objs[$key] = $obj;
        }

        return $objs;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'userid','question','description','answer1','answer2','answer3','answer4','answer5','visible',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
