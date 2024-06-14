<?php
//CREATE TABLE battle_ladder (
//    id INT AUTO_INCREMENT PRIMARY KEY,
//    name VARCHAR(255) NOT NULL,
//    level_requirements TEXT
//);
//
//CREATE TABLE battle_stats (
//    id INT AUTO_INCREMENT PRIMARY KEY,
//    user_id INT NOT NULL,
//    ladder_name VARCHAR(255) NOT NULL,
//    points INT NOT NULL,
//    num_attacks INT NOT NULL,
//    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id)
//);
class BattleLadder extends BaseObject
{
    const FIRST_PLACE_PRIZE = 500;
    const SECOND_PLACE_PRIZE = 250;
    const THIRD_PLACE_PRIZE = 100;

    /**
     * Get all the current ladders from the database.
     *
     * @return array|null Returns an array of ladders or null if there are no ladders.
     */
    public static function getCurrentLadders()
    {
        $query = "SELECT * FROM battle_ladder";
        $result = DBi::$conn->query($query);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        $ladders = array();
        while ($row = $result->fetch_assoc()) {
            $ladders[] = $row;
        }

        return $ladders;
    }


    /**
     * Get the level requirements for joining a ladder.
     *
     * @param int $userLevel The user's level.
     * @return int|null Returns the level requirement if the user can join a ladder,
     *                 null otherwise.
     */
    public static function getLevelRequirements($user)
    {
        $user = new User($user);
        $userLevel = $user->id;
        // Perform the database query to retrieve the level requirements
        $query = "SELECT level_requirements FROM battle_ladder WHERE level_requirements >= $userLevel ORDER BY level_requirements ASC LIMIT 1";
        $stmt = DBi::$conn->query($query);


        // Check if a level requirement is found
        if (mysqli_num_rows($stmt) > 0) {
            return true;
        }

        return null;
    }


    /**
     * Check if two users are in the same battle ladder.
     *
     * @param int $userId1 The ID of the first user.
     * @param int $userId2 The ID of the second user.
     * @return bool Returns true if both users are in the same ladder, false otherwise.
     */
    public static function checkSameLadder($userId1, $userId2)
    {
        $query = "SELECT COUNT(*) AS count FROM battle_stats WHERE user_id = ? 
                  AND ladder_name IN (SELECT ladder_name FROM battle_stats WHERE user_id = ?)";
        $stmt = DBi::$conn->prepare($query);
        $stmt->bind_param("ii", $userId1, $userId2);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            return false;
        }

        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    /**
     * Add a point to the battle_stats table for the winner and subtract a point from the loser.
     *
     * @param int $winnerId The ID of the winner.
     * @param int $loserId The ID of the loser.
     * @return bool Returns true if the points were updated successfully, false otherwise.
     */
    public static function addPointToStats($winnerId, $loserId)
    {
        if (!self::checkSameLadder($winnerId, $loserId)) {
            return false;
        }

        $query = "UPDATE battle_stats SET points = points + 1 WHERE user_id = ?; 
                  UPDATE battle_stats SET points = points - 1 WHERE user_id = ? AND points > 0";
        $stmt = DBi::$conn->prepare($query);
        $stmt->bind_param("ii", $winnerId, $loserId);
        $stmt->execute();
        $stmt->next_result();  // Move to the next result for the second query
        $affectedRows = $stmt->affected_rows;

        return $affectedRows > 0;
    }

    /**
     * Join a ladder.
     *
     * @param int    $userId      The ID of the user joining the ladder.
     * @param string $ladderName  The name of the ladder to join.
     * @param int    $userLevel   The level of the user joining the ladder.
     * @return bool Returns true if the user successfully joins the ladder, false otherwise.
     */
    public static function joinLadder($userId, $ladderName)
    {
        // Check if the user's level meets the ladder's level requirements
        if (self::getLevelRequirements($userId) !== true) {
            return false;
        }

        // Check if the user is already registered in a ladder
        if (self::checkLadderRegistration($userId)) {
            return false;
        }

        // Perform the database query to join the ladder
        $query = "INSERT INTO battle_stats (user_id, ladder_name, points, num_attacks) VALUES ($userId, '$ladderName', 0, 0)";
        $stmt = DBi::$conn->query($query);


        // Check if the ladder registration was successful
        if (DBi::$conn->affected_rows === 1) {
            return true;
        }

    }

    /**
     * Distribute prizes to the correct places for each ladder.
     *
     * @return bool Returns true if the prizes were successfully distributed, false otherwise.
     */
    public static function distributePrizes()
    {
        // Get a list of ladder names
        $query = "SELECT DISTINCT ladder_name FROM battle_stats";
        $stmt = DBi::$conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            return false;
        }

        // Loop through each ladder
        while ($row = $result->fetch_assoc()) {
            $ladderName = $row['ladder_name'];

            // Get the top three rankings for the ladder
            $query = "SELECT user_id, points FROM battle_stats WHERE ladder_name = ? 
                      AND points >= 1 ORDER BY points DESC LIMIT 3";
            $stmt = DBi::$conn->prepare($query);
            $stmt->bind_param("s", $ladderName);
            $stmt->execute();
            $rankingResult = $stmt->get_result();

            if (!$rankingResult || $rankingResult->num_rows === 0) {
                continue;
            }

            // Distribute prizes to the top three rankings and send notifications
            $prizes = [
                self::FIRST_PLACE_PRIZE,
                self::SECOND_PLACE_PRIZE,
                self::THIRD_PLACE_PRIZE
            ];

            $place = 0;
            while ($rankingRow = $rankingResult->fetch_assoc()) {
                $userId = $rankingRow['user_id'];
                $points = $rankingRow['points'];

                // Update the prize for the user
                $query = "UPDATE battle_stats SET points = points + ? WHERE user_id = ?";
                $stmt = DBi::$conn->prepare($query);
                $stmt->bind_param("ii", $prizes[$place], $userId);
                $stmt->execute();

                // Send notification to the user about their prize
                $message = "Congratulations! You have received " . $prizes[$place] . " points as a prize.";
                User::SNotify($userId, $message);

                $place++;
            }
        }

        return true;
    }

    /**
     * Check if a user is already registered in a ladder.
     *
     * @param int $userId The ID of the user to check.
     * @return bool Returns true if the user is already registered in a ladder, false otherwise.
     */
    private static function checkLadderRegistration($userId)
    {
        // Perform the database query to check ladder registration
        $query = "SELECT COUNT(*) AS num_rows FROM battle_stats WHERE user_id = $userId";
        $stmt= DBi::$conn->query($query);


        if(mysqli_num_rows($stmt) > 0) {
            $row = mysqli_fetch_assoc($stmt);
            return $row['num_rows'] > 0;
        }

        return false;
    }

    /**
     * Get the ladder the user is registered into.
     *
     * @param int $userId The ID of the user to check.
     * @return bool Returns true if the user is already registered in a ladder, false otherwise.
     */
    public static function GetUserLadder($userId)
    {
        // Perform the database query to check ladder registration
        $query = "SELECT * FROM battle_stats WHERE user_id = $userId";
        $stmt= DBi::$conn->query($query);


        if(mysqli_num_rows($stmt) > 0) {
            $row = mysqli_fetch_assoc($stmt);
            return $row;
        }

        return false;
    }

    protected function GetIdentifierFieldName()
    {
        // TODO: Implement GetIdentifierFieldName() method.
    }

    protected function GetClassName()
    {
        // TODO: Implement GetClassName() method.
    }

    protected static function GetDataTable()
    {
        // TODO: Implement GetDataTable() method.
    }

    protected static function GetDataTableFields()
    {
        // TODO: Implement GetDataTableFields() method.
    }
}
