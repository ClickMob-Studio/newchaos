<?php

class ContactSendGrid
{
    /**
     * @var SendGrid
     */
    private $sendGrid;

    public function __construct()
    {
        $this->sendGrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
    }

    /**
     * Update individual user on SendGrid.
     *
     * @param User $user
     *
     * @return bool
     */
    public function updateUser(User $user)
    {
        try {
            $payload = [
                'contacts' => [
                    $this->buildUserPayload(
                        $user->username,
                        $user->email,
                        $user->level,
                        $user->avatar !== '',
                        $user->signuptime,
                        $user->lastactive,
                        $user->validC !== 0
                    ),
                ],
            ];
            $response = $this->sendGrid->client->marketing()->contacts()->put($payload);

            return $response->statusCode() === 202;
        } catch (\Exception $e) {
            // If this fails we shouldn't interrupt the player
            Logger::error($e);
        }
    }

    /**
     * Update all users in database.
     *
     * @return bool
     */
    public function updateAllUsers()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $users = $queryBuilder->select(['username', 'email', 'level', 'avatar', 'signuptime', 'lastactive', 'validC'])
            ->from('grpgusers');

        $contacts = [];
        foreach ($users->execute()->fetchAll(PDO::FETCH_OBJ) as $user) {
            $contacts[] = $this->buildUserPayload(
                $user->username,
                $user->email,
                $user->level,
                $user->avatar !== '',
                $user->signuptime,
                $user->lastactive,
                $user->validC === 0
            );
        }

        $response = $this->sendGrid->client->marketing()->contacts()->put([
            'list_ids' => ['91030f3a-0e69-42ac-8ba1-65055a7ad841'],
            'contacts' => $contacts,
        ]);

        return $response->statusCode() === 202;
    }

    /**
     * Create a user payload for SendGrid.
     *
     * @param string $username
     * @param string $email
     * @param int    $level
     * @param bool   $loggedIn
     * @param int    $signUpTime
     * @param int    $lastActive
     * @param bool   $validatedEmail
     *
     * @return array
     */
    private function buildUserPayload(
        string $username,
        string $email,
        int $level,
        bool $loggedIn,
        int $signUpTime,
        int $lastActive,
        bool $validatedEmail
    ) {
        return [
            'first_name' => $username,
            'email' => $email,
            'custom_fields' => [
                /* Level */
                'e4_N' => (int) $level,
                /* Logged in */
                'e3_T' => $loggedIn ? 'Yes' : 'No',
                /* Player Name */
                'e1_T' => $username,
                /* Sign up date */
                'e2_D' => date('c', $signUpTime),
                /* Last Active */
                'e5_D' => date('c', $lastActive),
                /* Validated Email */
                'e6_T' => $validatedEmail ? 'Yes' : 'No',
            ],
        ];
    }
}
