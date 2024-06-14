<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define('CORRECT', 1);
define('INCORRECT', -1);
define('NOT_ANS', 0);
class QuizzOver extends Exception
{
}
class QuizzComplete extends Exception
{
}
class Question
{
    public $question;
    public $ans;
    public $correct;
    public $state = NOT_ANS;

    public function Question($question, $ans, $correct)
    {
        if (!is_array($ans)) {
            throw new Exception('Invalid argument answars');
        }
        $this->ans = $ans;
        $this->question = $question;
        $this->correct = $correct;
    }

    public function answear($i)
    {
        if ($this->correct != $i) {
            $this->state = INCORRECT;
        } else {
            $this->state = CORRECT;
        }
    }
}
class Quizz
{
    public $questions = [];
    public $questionsDB = [];
    public $last_question = 0;

    public function Save()
    {
        $_SESSION['QUIZZ'] = serialize($this);
    }

    public static function ReturnItSelf($number_of_questions = 2)
    {
        $users = SUserFactory::getInstance()->getUser($_SESSION['id']);
        if ($users->quizz == CORRECT) {
            throw new QuizzComplete();
        }
        try {
            if (!isset($_SESSION['QUIZZ'])) {
                throw new Exception('Session not set');
            }
            $var = unserialize($_SESSION['QUIZZ']);
            if (!($var instanceof Quizz)) {
                throw new Exception('no type of quizz');
            }
            if (count($var->questions) == 0) {
                throw new Exception('Empty question');
            }
            if ($var->last_question >= count($var->questions)) {
                throw new Exception('Counter to big');
            }
        } catch (Exception $e) {
            $_SESSION['QUIZZ'] = serialize(new Quizz($number_of_questions));
        }

        return unserialize($_SESSION['QUIZZ']);
    }

    public function Quizz($number_of_questions = 2)
    {
        $this->updateDB();
        for ($i = 0; $i < $number_of_questions; ++$i) {
            $this->questions[] = $this->selectQuestion();
        }
    }

    public function updateDB()
    {
        $this->questionsDB[] = new Question(QUESTION_1, [ANS_1_1,ANS_1_2,ANS_1_3], 1);
        $this->questionsDB[] = new Question(QUESTION_2, [ANS_2_1,ANS_2_2,ANS_2_3,ANS_2_4], 1);
        $this->questionsDB[] = new Question(QUESTION_3, [ANS_3_1,ANS_3_2,ANS_3_3,ANS_3_4], 3);
        $this->questionsDB[] = new Question(QUESTION_4, [ANS_4_1,ANS_4_2,ANS_4_3,ANS_4_4], 2);
        $this->questionsDB[] = new Question(QUESTION_5, [ANS_5_1,ANS_5_2,ANS_5_3,ANS_5_4], 1);
        $this->questionsDB[] = new Question(QUESTION_6, [ANS_6_1,ANS_6_2,ANS_6_3,ANS_6_4], 2);
    }

    public function getQuestion()
    {
        return $this->questions[$this->last_question];
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function answear($ans)
    {
        $re = $this->questions[$this->last_question];
        $re->answear($ans);
        ++$this->last_question;
        $this->terminate_state();
        $this->Save();
    }

    public function is_finish()
    {
        $i = 0;
        foreach ($this->questions as $question) {
            if ($question->state == NOT_ANS) {
                return false;
            }
        }

        return true;
    }

    public function as_won()
    {
        foreach ($this->questions as $question) {
            if ($question->state != CORRECT) {
                return false;
            }
        }

        return true;
    }

    public function terminate_state()
    {
        if ($this->is_finish()) {
            if ($this->as_won()) {
                $user = SUserFactory::getInstance()->getUser($_SESSION['id']);
                $user->SetAttribute('quizz', CORRECT);
            }
            unset($_SESSION['QUIZZ']);
            throw new QuizzOver();
        }
    }

    public function selectQuestion()
    {
        $valid = false;
        while (!$valid) {
            $valid = true;
            $questao = $this->questionsDB[(rand() % count($this->questionsDB))];
            foreach ($this->questions as $question) {
                if ($questao->question == $question->question) {
                    $valid = false;
                }
            }
        }

        return $questao;
    }
}
