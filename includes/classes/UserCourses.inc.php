<?php

class UserCourses extends BaseObject
{
    const READING = 1;

    const STUDIED = 2;

    public static $idField = '';

    public static $dataTable = 'courses_user';

    public function __construct($id)
    {
        parent::__construct($id);

        $this->name = constant($this->name);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function Get($userId, $courseId)
    {
        $courses = self::GetAll('user_id=' . $userId . ' AND course_id=' . $courseId);

        return $courses[0];
    }

    public static function GetAllAttending($userId)
    {
        return self::GetAll('user_id=' . $userId . ' AND status=' . self::READING);
    }

    public static function GetAllStudied($userId)
    {
        return self::GetAll('user_id=' . $userId . ' AND status=' . self::STUDIED);
    }

    public static function GetAllForUser($userId)
    {
        return self::GetAll('user_id=' . $userId);
    }

    public static function IsAttendingAnyCourse($userId)
    {
        $courses = self::GetAllAttending($userId);

        return count($courses) > 0;
    }

    public static function IsDependent($userId, $courseId)
    {
        $course = new Course($courseId);

        if (empty($course->predecessors)) {
            return false;
        }

        $pCoursesCount = MySQL::GetSingle('SELECT COUNT(*) FROM ' . self::GetDataTable() . ' WHERE course_id IN (' . $course->predecessors . ') AND user_id = ' . $userId . ' AND ( status = ' . self::STUDIED . ' OR count > 0)');

        return $pCoursesCount <= 0;
    }

    public static function FinishAttending($userId, $courseId)
    {

        $user = UserFactory::getInstance()->getUser($userId);
        if($user->lastactive < (time() - 86400)) {
            return;
        }

        if (!self::IsAttendingAnyCourse($userId)) {
            throw new FailedResult(COURSES_NOT_ATTENDING);
        }
        $data = [
            'status' => self::STUDIED,

            'count' => 'count + 1',
        ];

        try {
            $course = new Course($courseId);

            $user = UserFactory::getInstance()->getUser($userId);

            $user->AddToAttribute($course->stat, $course->statamt);

            $user->Notify(sprintf(COURSES_FINISHED, '<b>' . $course->name . '</b>', number_format($course->statamt), $course->stat), BOOKS);

            return self::sUpdate(self::GetDataTable(), $data, ['user_id' => $userId, 'course_id' => $courseId], false);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function FinishAllAttending()
    {
        $courses = self::GetAll('status = ' . self::READING . ' AND finished <= ' . time());

        foreach ($courses as $course) {
            self::FinishAttending($course->user_id, $course->course_id);
        }
    }

    public static function UserHasCourse($userId, $courseId)
    {
        $course = self::Get($userId, $courseId);

        return !empty($course);
    }

    public static function UserHasStudied($userId, $courseId)
    {
        $course = self::Get($userId, $courseId);

        return $course->status == self::STUDIED;
    }

    public static function UserHasStudiedAny($userId, array $courseIds = [])
    {
        $courses = self::GetAllStudied($userId);

        foreach ($courses as $course) {
            if (in_array($course->course_id, $courseIds)) {
                return true;
            }
        }

        return false;
    }

    public static function Start(User $user, $courseId)
    {
        if (self::IsAttendingAnyCourse($user->id, $courseId)) {
            throw new FailedResult(COURSES_ALREADY_ATTENDING);
        }
        if (self::IsDependent($user->id, $courseId)) {
            throw new FailedResult(BOOK_ALREADY_EXISTS);
        }
        $course = new Course($courseId);

        $error = false;

        if ($user->money < $course->money) {
            throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
        }
        $user->RemoveFromAttribute('money', $course->money);

        $userCourse = self::Get($user->id, $courseId);

        if (!empty($userCourse)) {
            $data = [
                'status' => self::READING,

                'start' => time(),

                'finished' => time() + $course->duration * DAY_SEC,
            ];

            parent::sUpdate(self::GetDataTable(), $data, ['course_id' => $courseId, 'user_id' => $user->id]);
        } else {
            $data = [
                'course_id' => $courseId,

                'user_id' => $user->id,

                'status' => self::READING,

                'start' => time(),

                'finished' => time() + $course->duration * DAY_SEC,
            ];

            parent::AddRecords($data, self::GetDataTable());
        }

        throw new SuccessResult(sprintf(COURSES_STARTED, '<b>' . $course->name . '</b>', $course->duration));
    }

    public static function Cancel(User $user, $courseId)
    {
        if (!self::IsAttendingAnyCourse($user->id, $courseId)) {
            throw new FailedResult('Not attending any course');
        }
        $course = new Course($courseId);

        if (!($user->AddToAttribute('bank', ((int) $course->money / 2)))) {
            $user->AddToAttribute('money', ((int) $course->money / 2));
        }

        $userCourse = self::Get($user->id, $courseId);

        $data = ['status' => self::STUDIED];

        if ($userCourse->count == 0) {
            parent::sDelete(self::GetDataTable(), ['course_id' => $courseId, 'user_id' => $user->id]);
        } else {
            parent::sUpdate(self::GetDataTable(), $data, ['course_id' => $courseId, 'user_id' => $user->id]);
        }

        throw new SuccessResult(sprintf(COURSES_CANCELLED));
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'course_id',

            'user_id',

            'status',

            'start',

            'finished',

            'count',
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

?>

